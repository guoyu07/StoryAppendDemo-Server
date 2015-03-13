<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 8/29/14
 * Time: 11:09 AM
 */
class AccountHelper
{

    public static function doLogin($email, $password, $auto_login = false, $override = false)
    {
        // to using Customer for login action
        $result = Yii::app()->customer->login($email, $password, $override);

        if ($result && $auto_login) {
            $login_data['auto'] = true;
            $login_data['email'] = $email;
            $login_data['password'] = $password;

            $encryption = new Encryption(Setting::instance()->get('config_encryption'));
            setcookie('customer', $encryption->encrypt(serialize($login_data)), time() + 3600 * 24 * 90, '/',
                      $_SERVER['HTTP_HOST'], false, true);
        }

        return $result;
    }

    public static function getCustomer($wx_unionid)
    {
        $customer_third = HtCustomerThird::model()->getThirdAccount(HtCustomerThird::WEIXIN, $wx_unionid);
        if (!empty($customer_third)) {
            $customer = HtCustomer::model()->findByPk($customer_third['customer_id']);

            return array(true, !empty($customer), $customer, $customer_third);
        } else {
            return array(false, false, null, null);
        }
    }

    public static function bindToWX($customer_id, $unionid, $nickname, $avatar_url)
    {
        $customer_third_customer_id = 0;
        $customer_third = HtCustomerThird::model()->getThirdAccount(HtCustomerThird::WEIXIN, $unionid);
        if (!empty($customer_third)) {
            $customer_third_customer_id = $customer_third['customer_id'];
        }

        if ($customer_third_customer_id != $customer_id) {
            if ($customer_third_customer_id > 0) {
                return array(400, '微信已绑定其它账号。');
            } else {
                $customer_third = HtCustomerThird::model()->getThirdAccount(HtCustomerThird::WEIXIN, '', $customer_id);
                if (!empty($customer_third)) {
                    return array(400, '该账号已绑定其它微信。');
                } else {
                    $result = HtCustomerThird::model()->saveThirdAccount(HtCustomerThird::WEIXIN, $unionid, '', '',
                                                                         $nickname, $avatar_url, $customer_id);
                    if (!$result) {
                        return array(400, '绑定微信账号失败。');
                    }
                }
            }
        }

        return array(200, 'Ok');
    }

    public static function checkPickup($did, $customer_id)
    {
        $item = HtDandelionPickup::model()->findByAttributes(array('did' => $did, 'customer_id' => $customer_id));
        if (!empty($item)) {

            return true;
        }

        return false;
    }

    public static function validateDandelion($dandelion)
    {
        if ($dandelion['use_limit'] == 1) {
            return array(400, '不是分享券。');
        }
        $share_date_limit = $dandelion['share_date_limit'];
        if ($share_date_limit <= date('Y-m-d')) {
            return array(401, '分享券已过期，不能再分享。');
        }

        // TODO check share_max_time

        return array(200, 'Ok');
    }

    public static function addPhoneCustomer($phone_no, $nickname = '')
    {
        //Phone number validation
        $phone_no = trim($phone_no);
        if (substr($phone_no, 0, 1) == '1' && in_array(substr($phone_no, 1, 1),
                                                       array('3', '4', '5', '7', '8')) && strlen($phone_no) == 11
        ) {
            // already have user?
            $customer = HtCustomer::model()->getCustomer($phone_no);
            if (!empty($customer)) {
                return array('customer' => $customer, 'password' => '', 'msg' => '用户已注册。');
            }

            // add customer and customer_third
            $nickname = $nickname == '' ? substr($phone_no, -6) : $nickname;
            $password = mt_rand(100000, 999999);
            $customer = HtCustomer::model()->addCustomerByPhone($phone_no, $password, $nickname);
            // notify user by SMS
            $sms = new Sms();
            $sms->send($phone_no, sprintf('您玩途账号已创建，用户名为手机号，密码:' . $password));

            if (empty($customer)) {
                return false;
            }

            return array(
                'customer' => $customer,
                'password' => $password
            );
        } else {
            return false;
        }
    }

    public static function addCustomerByWX($ouid, $openid, $nick_name, $avatar_url)
    {
        $result = HtCustomerThird::model()->saveThirdAccount(HtCustomerThird::WEIXIN, $ouid, '', '', $nick_name,
                                                                     $avatar_url, 0, $openid);
        if ($result) {
            $customer_third = HtCustomerThird::model()->getThirdAccount(HtCustomerThird::WEIXIN, $ouid);
            $customer = HtCustomer::model()->addThirdCustomer($ouid, $nick_name);
            if (!empty($customer)) {
                $customer_third['customer_id'] = $customer['customer_id'];
                $customer_third->update();

                return $customer;
            }
        }

        return array();
    }
}
