<?php
/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 8/29/14
 * Time: 11:30 AM
 */

require_once "AccountHelper.php";

class PickupCouponAction extends CAction
{

    public function run()
    {
        $did = (int)$this->controller->getParam('did', 0);
        $customer_id = (int)$this->controller->getParam('customer_id', 0);
        $openid = $this->controller->getParam('openid', 0);
        $is_ajax = $this->controller->getParam('is_ajax', true);

        if ($customer_id == 0) {
            $customer_id = Yii::app()->customer->getCustomerId();
        }
        if ($did == 0 || $customer_id == 0) {
            EchoUtility::echoCommonFailed('请求数据不合法。');

            return;
        }

        $dandelion = HtDandelion::model()->findByPk($did);
        if (empty($dandelion)) {
            EchoUtility::echoCommonFailed('无效的did。');

            return;
        }
        $coupon = HtCoupon::model()->findByPk($dandelion['coupon_id']);

        // pickup one coupon
        $item = new HtDandelionPickup();
        $item['pick_type'] = HtDandelionPickup::PT_BY_PICKUP;
        $item['did'] = $did;
        $item['from_order_id'] = 0;
        $item['coupon_id'] = $dandelion['coupon_id'];
        $item['customer_id'] = $customer_id;
        $item['pick_date'] = date('Y-m-d H:i:s');
        $item['used_order_id'] = 0;
        $item['used_date'] = '0000-00-00';
        $result = $item->insert();

        if ($is_ajax !== 'false') {
            EchoUtility::echoMsgTF($result, '领取');
        } else {
            if ($result) {
                // TODO auto login
                $customer = HtCustomer::model()->findByPk($customer_id);
                $customer_third = HtCustomerThird::model()->getThirdAccount(HtCustomerThird::WEIXIN, $openid, $customer['customer_id']);
                if(empty($customer) || empty($customer_third) || $customer_third['customer_id'] != $customer['customer_id']) {
                    EchoUtility::echoCommonFailed('请求数据不合法。');
                } else {
                    AccountHelper::doLogin($customer['email'], '', false, true);

                    $this->controller->redirect($this->controller->createUrl('mobile/funduser',
                                                                         array('save' => (int)$coupon['discount'], 'is_success' => true)));
                }
            } else {
                echo "领取失败。";
            }
        }
    }
} 