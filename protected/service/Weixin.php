<?php

/**
 * @project hitour.server
 * @file Sms.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 14-8-6 下午2:03
 **/
class Weixin
{
    public function __construct()
    {
    }

    public function sendTemplateMsg($to,$data = array()){

        if(empty($data) || empty($to)){
            Yii::log('');
            $result['code'] = 400;
            $result['msg'] = 'Wrong weixin data!';
            return $result;
        }
//        $to = 'oyuIfuKPEl33meef9qEucI6DDYmc';//Songsong's weixin id
        $data['to'] = $to;

        $gate = Yii::app()->params['WEIXIN_GATE'];
        $result = HTTPRequest::request($gate, 30, null, http_build_query($data));
        if ($result['Status'] == 'OK' && !empty($result['content'])) {
            if (strtoupper($result['content']) != 'OK') {
                $result['code'] = 501;
                $result['msg'] = $result['content'];
            }else{
                $result['code'] = 200;
                $result['msg'] = 'OK!';
            }
        } else {
            $result['code'] = 400;
            $result['msg'] = 'Send Weixin Failed!';
        }

        return $result;
    }

}