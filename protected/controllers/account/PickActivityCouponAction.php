<?php

/**
 * Created by hitour.server.
 * User: xingminglister
 * Date: 9/17/14
 * Time: 3:29 PM
 */
class PickActivityCouponAction extends CAction
{

    public function run()
    {
        $wx_openid = $this->controller->getParam('openid');
        $activity_id = $this->controller->getParam('activity_id');
        $slogan = $this->controller->getParam('slogan');

        if (empty($wx_openid) || (empty($activity_id) && empty($slogan))) {
            EchoUtility::echoCommonFailed('Not enough parameters.');

            return;
        }

        if (empty($activity_id)) {
            $activity_id = HtWxCouponSetting::model()->getActivityID($slogan);
        }
        if ($activity_id > 0) {
            $picked = HtWxCouponHistory::model()->picked($activity_id, $wx_openid);
            $pick_info = HtWxCouponHistory::model()->pick($activity_id, $wx_openid);
            if (!empty($pick_info)) {
                $data = array();
                $data['picked'] = $picked;
                $data['code'] = $pick_info['coupon']['code'];
                $data['discount'] = $pick_info['coupon']['discount'];
                $data['type'] = $pick_info['coupon']['type'];

                EchoUtility::echoCommonMsg(200, '获取优惠券成功。', $data);
            } else {
                EchoUtility::echoCommonFailed('获取优惠券失败。');
            }
        } else {
            EchoUtility::echoCommonFailed('Failed to get activity info.');
        }
    }
} 