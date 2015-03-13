<?php
/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 8/29/14
 * Time: 11:27 AM
 */

require_once "AccountHelper.php";

class AlreadyPickedAction extends CAction
{

    public function run()
    {
        $did = $this->controller->getParam('did', 0);
        $wx_openid = $this->controller->getParam('openid');
        $wx_unionid = $this->controller->getParam('unionid');

        if ($did == 0 || empty($wx_unionid)) {
            EchoUtility::echoCommonFailed('请求数据不合法。');

            return;
        }

        list($has_customer_third, $has_customer, $customer, $customer_third) = AccountHelper::getCustomer($wx_unionid);
        if ($has_customer) {
            if (AccountHelper::checkPickup($did, $customer['customer_id'])) {
                EchoUtility::echoCommonFailed('用户已经领取过分享券。');

                return;
            }
        }

        EchoUtility::echoCommonMsg(200, '用户尚未领取分享券。');
    }

} 