<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 8/28/14
 * Time: 6:25 PM
 */
class GenerateShareCouponAction extends CAction
{
    public function run()
    {
        $openid = $this->controller->getParam('openid');

        // TODO add customer third, customer
        if (empty($openid)) {
            EchoUtility::echoCommonFailed('参数有误。');

            return;
        }
        $customer_id = 0;
        $customer = HtCustomer::model()->find("wx_openid='".$openid."'");
        if (empty($customer)) {
            EchoUtility::echoCommonFailed('无法查到该微信账号对应的Hitour账号。');

            return;
        } else {
            $customer_id = $customer->customer_id;
        }

        // TODO generate share coupon first
        list($result, $coupon_id, $coupon_code) = HtCouponBase::model()->generateCouponByBase(20);
        if ($result == false) {
            EchoUtility::echoCommonFailed('生成分享券用优惠券失败。');

            return;
        }

        // TODO generate dandelion
        $item = new HtDandelion();
        $item['coupon_id'] = $coupon_id;
        $item['owner_id'] = $customer_id;
        $item['use_limit'] = 2;
        $item['return_or_not'] = 1;
        $item['max_return_count'] = 100;
        $item['return_amount'] = 50;
        $item['fund_expire_date'] = date("Y-m-d",strtotime('+1 year'));
        $item['share_max_time'] = 0;
        $item['share_date_limit'] = date("Y-m-d",strtotime('+3 month'));
        $item['insert_time'] = date('Y-m-d H:i:s');

        $result = $item->insert();

        EchoUtility::echoMsgTF($result, '生成分享券', array('dandelion' => $item));
    }
}