<?php
/**
 * Created by hitour.server.
 * User: xingminglister
 * Date: 9/15/14
 * Time: 3:25 PM
 */

require_once "AccountHelper.php";

class UpdateOrRegisterWXAction extends CAction
{

    public function run()
    {
        $unionid = $this->controller->getParam('unionid');
        $openid = $this->controller->getParam('openid');

        if (empty($unionid) || empty($unionid)) {
            EchoUtility::echoCommonFailed('Invalid ouid or unionid');

            return;
        }
        $nick_name = $this->controller->getParam(('nick_name'));
        $avatar_url = $this->controller->getParam(('avatar_url'));

        $customer_third = HtCustomerThird::model()->getThirdAccount(HtCustomerThird::WEIXIN, $unionid);

        if (!empty($customer_third)) {
            $customer_third['nick_name'] = $nick_name;
            $customer_third['avatar_url'] = $avatar_url;
            $result = $customer_third->update();

            if ($result) {
                EchoUtility::echoCommonMsg(200, '更新账号成功。');
            } else {
                EchoUtility::echoCommonMsg(401, '更新账号失败。');
            }
        } else {
            // TODO add customer_third, then add customer
            $customer = AccountHelper::addCustomerByWX($unionid, $openid, $nick_name, $avatar_url);
            if (!empty($customer)) {
                EchoUtility::echoCommonMsg(200, '创建账号成功。');
            } else {
                EchoUtility::echoCommonMsg(400, '创建账号失败。');
            }
        }
    }

} 