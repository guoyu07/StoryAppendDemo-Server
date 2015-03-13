<?php

/**
 * Class Notify
 */
class SmsVerify
{
    const PERIOD = 120;

    public function init()
    {
        return true;
    }

    public function sendVerificationCode($phone_number)
    {
        $verify = HtSmsVerify::model()->findByAttributes(['phone_number'=>$phone_number]);
        if (time() - strtotime($verify['insert_time']) < self::PERIOD){
            return ['code' => 301, 'msg' => '间隔时间太短，请稍等几分钟后重新获取！'];
        }

        $verify = new HtSmsVerify();
        $session_id = Yii::app()->session->sessionID;
        $verify['session_id'] = $session_id;
        $verify['phone_number'] = $phone_number;
        $verify['sms_code'] = $this->generateVerifyCode($session_id, $phone_number);
        if ($verify->save()) {
            $sms = new Sms();
            $sms->send($phone_number, sprintf('您的短信验证码是:' . $verify['sms_code']));
            return ['code' => 200, 'msg' => '短信验证码发送成功！'];
        }
        return ['code' => 400, 'msg' => '短信验证码发送失败！'];
    }

    public function generateVerifyCode()
    {
        $auth_num = '';
        srand((double)microtime() * 10000000000);//create a random number feed.
        $list = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        for ($i = 0; $i < 6; $i++) {
            $rand_num = rand(0, 9999) % 10;
            $auth_num .= $list[$rand_num];
        }
        return $auth_num;
    }

    /**
     * @param $type
     * @param $phone_number
     * @param $code
     * @return bool
     */
    public function verify($phone_number,$code)
    {
        $result = ['code' => 200, 'msg' => 'OK'];
        $session_id = Yii::app()->session->sessionID;
//        $criteria = new CDbCriteria(array('order' => 'id DESC'));
        $verify = HtSmsVerify::model()->findByAttributes(['session_id' => $session_id,'phone_number'=>$phone_number]);
        if (empty($verify)) {
            $result['code'] = 404;
            $result['msg'] = '验证失败，未曾获取验证码！';
        } else if ($verify['sms_code'] != $code) {
            $result['code'] = 400;
            $result['msg'] = '验证失败，验证码错误！';
        } else if ($verify['verify_time'] > 0) {
            $result['code'] = 401;
            $result['msg'] = '验证失败，该验证码已经验证！';
        } else if (time() - strtotime($verify['insert_time']) > self::PERIOD) {
            $result['code'] = 300;
            $result['msg'] = '验证码已过期，请重新获取！';
        } else {
            $verify['verify_time'] = date('Y-m-d H:i:s.u');
            if (!$verify->update()) {
                $result['code'] = 301;
                $result['msg'] = '验证失败！';
            }
        }

        return $result;
    }

}