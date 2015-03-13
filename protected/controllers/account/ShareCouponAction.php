<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 8/27/14
 * Time: 11:52 AM
 */
require_once "AccountHelper.php";

class ShareCouponAction extends CAction
{
    public function run()
    {
        $did = (int)$this->controller->getParam('did', 0);
        $owner_openid = $this->controller->getParam('owner_openid');
        $share_id = $this->controller->getParam('share_id');
        $picker_openid = $this->controller->getParam('picker_openid');
        if ($did == 0 || empty($owner_openid)) {
            EchoUtility::echoCommonFailed('参数非法。');

            return;
        }

        $dandelion = HtDandelion::model()->findByPk(array('did' => $did));
        if (empty($dandelion)) {
            EchoUtility::echoCommonFailed('did不合法，无法找到分享券信息。');

            return;
        }

        // TODO get user nickname, avatar_url
        $result = array();
        $customer = HtCustomer::model()->getAccountByOpenId($owner_openid);
        if (empty($customer)) {
            EchoUtility::echoCommonFailed('did不合法，无法找到分享券所有者信息。');

            return;
        }

        // TODO validate dandelion
        list($error_code, $msg) = AccountHelper::validateDandelion($dandelion);
        if ($error_code != 200) {
            EchoUtility::echoCommonMsg($error_code, $msg);

            return;
        }

        $result['nickname'] = $customer->customer_third->nick_name;
        $result['avatar_url'] = $customer->customer_third->avatar_url;

        $pickup_detail = array();
        // TODO get pickup list
        $pickup_list = HtDandelionPickup::model()->with('customer_third_weixin')->findAllByAttributes(array('did' => $dandelion['did']));
        foreach ($pickup_list as $pickup) {
            $pickup_detail[] = array('nickname' => $pickup['customer_third_weixin']['nick_name'],
                'avatar_url' => $pickup['customer_third_weixin']['avatar_url'],
                'pick_date' => $pickup['pick_date']);
        }

        $result['pickup_detail'] = $pickup_detail;
        $result['dandelion'] = $dandelion;
        $result['coupon'] = HtCoupon::model()->findByPk($dandelion['coupon_id']);
        $result['picked'] = false;

        $result['sharer_openid'] = '';
        $result['sharer_nickname'] = '';
        $result['sharer_avatar_url'] = '';

        if (!empty($share_id)) {
            $customer_third = HtCustomerThird::model()->getThirdAccount(HtCustomerThird::WEIXIN, '', $share_id);
            if (!empty($customer_third)) {
                $result['sharer_openid'] = $customer_third['ouid'];
                $result['sharer_nickname'] = $customer_third['nick_name'];
                $result['sharer_avatar_url'] = $customer_third['avatar_url'];
            }
        }

        $result['picker_info'] = null;
        $result['picker_nickname'] = '';
        $result['picker_avatar_url'] = '';
        $customer=HtCustomer::model()->getAccountByOpenId($picker_openid);
        if (!empty($customer)) {
            if (AccountHelper::checkPickup($did, $customer['customer_id'])) {
                $result['picked'] = true;
            }
            $result['picker_info'] = $customer;
            if(!empty($customer->customer_third)) {
              $result['picker_nickname'] = $customer->customer_third->nick_name;
              $result['picker_avatar_url'] = $customer->customer_third->avatar_url;
            }
        }

        EchoUtility::echoCommonMsg(200, 'Ok', $result);
    }


}