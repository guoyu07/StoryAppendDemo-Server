<?php
/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 8/29/14
 * Time: 11:50 AM
 */

require_once "AccountHelper.php";

class LoginToGetCouponAction extends CAction
{

    public function run()
    {
        $openid = $this->controller->getParam('openid');
        $unionid = $this->controller->getParam('unionid');
        $nickname = $this->controller->getParam('nickname');
        $avatar_url = $this->controller->getParam('avatar_url');

        $email = $this->controller->getParam('email');
        $password = $this->controller->getParam('password');

        if(empty($unionid) || empty($email) || empty($password)) {
            EchoUtility::echoCommonFailed('请求参数不完整。');

            return;
        }

        $customer = HtCustomer::model()->getCustomer($email);
        if (empty($customer)) {
            EchoUtility::echoCommonFailed('用户名或密码不正确。');

            return;
        } else {
            $customer['wx_openid'] = $openid;
            $customer->update();
            // TODO check whether customer has bind to openid
            $customer_id = $customer['customer_id'];


            list($code, $msg) = AccountHelper::bindToWX($customer_id, $unionid, $nickname, $avatar_url);
            if($code != 200) {
                EchoUtility::echoCommonFailed($msg);
                return;
            }
        }

        $result = AccountHelper::doLogin($email, $password, false);

        EchoUtility::echoMsgTF($result, '登录');
    }

} 