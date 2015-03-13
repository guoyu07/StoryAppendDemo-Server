<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 8/27/14
 * Time: 11:52 AM
 */
class GetFundInfoAction extends CAction
{
    public function run()
    {
        // TODO get fund total and fund usage history
        $customer_id = Yii::app()->customer->getCustomerId();
        if ($customer_id == 0) {
            EchoUtility::echoCommonFailed('请先登录！');

            return;
        }

        $fund_total = Yii::app()->customer->getCustomerInfo()['hitour_fund'];
//        $fund_history = HtCustomerFundHistory::model()->findAllByAttributes(array('customer_id' => $customer_id));

        $items = HtDandelion::model()->with('dandelion_pickup', 'coupon',
                                            'fund_history.customer')->findAllByAttributes(array('owner_id' => $customer_id));

        $dandelions = [];
        if (!empty($items)) {
            foreach ($items as $item) {
                $coupon = $item['coupon'];
                $shared = empty($item['dandelion_pickup']) ? 0 : count($item['dandelion_pickup']);
                $fund_history = $item['fund_history'];

                $discount = HtCoupon::getCouponDiscount($coupon['discount'], $coupon['type']);

                $fund_num = 0;
                foreach ($fund_history as $fund_item) {
                    $amount = $fund_item['amount'];
                    $add_or_sub = $fund_item['add_or_sub'];

                    $fund_num = $add_or_sub == 1 ? $amount : 0 - $amount;
                }

                array_push($dandelions, array(
                    'discount' => $discount,
                    'shared' => $shared,
                    'fund_num' => $fund_num,
                    'fund_history' => $fund_history,
                ));
            }

        }

        EchoUtility::echoCommonMsg(200, '获取分享券数据成功。', array('fund_total' => $fund_total, 'dandelions' => $dandelions));
    }
}