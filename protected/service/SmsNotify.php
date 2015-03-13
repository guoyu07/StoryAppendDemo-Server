<?php

/**
 * Class Notify
 */
class SmsNotify
{

    public function init()
    {
        return true;
    }

    public function notifySupplier($order, $order_product, $preview = false)
    {
        return true;
    }


    public function notifyOP($order, $order_product, $preview = false)
    {
        return true;
    }

    /**
     * @param $order_data
     * @param bool $preview
     * @return bool|string
     */
    public function notifyCustomer($order_data, $preview = false)
    {
        if (empty(Yii::app()->params['SEND_SMS'])) {
            return true;
        }

        $order_id = $order_data['order']['order_id'];
        $status_id = $order_data['order']['status_id'];
        $to = $order_data['order']['contacts_telephone'];

        if (!$this->validateTelephone($to)) {
            Yii::log('Invalid telephone number:' . $to . ' of order:' . $order_id, CLogger::LEVEL_INFO);
            return true;
        }

        $body = $this->generateSmsContent($order_data);
        if ($preview) {
            return $body;
        }

        if (empty($body)) {
            Yii::log('Order [' . $order_id . '] dont need  send sms,status_id:' . $status_id, CLogger::LEVEL_INFO);
            return true;
        }

        //发送短信
        $sms = new Sms();
        $result = $sms->send($to, $body);
        if ($result['code'] != 200) {
            Yii::log('Send sms failed to ' . $to . ',order_id=' . $order_id . ',' . $result['msg']);
        } else {
            Yii::log('Send sms success to ' . $to . ',order_id=' . $order_id . ',' . $result['msg']);
        }
        return ($result['code'] == 200);
    }

    private function validateTelephone($mobile_phone)
    {
        $result = preg_match("/^1[3458][0-9]{9}/", $mobile_phone);
        return $result;
    }

    private function generateSmsContent($order_data)
    {
        $content = '';
        $order_id = $order_data['order']['order_id'];
        $order_total = $order_data['order']['total'];
        $email = $order_data['order']['contacts_email'];
        $product = $order_data['order_products'][0]['product'];

        $content = '';
        if (in_array($order_data['order']['status_id'], [HtOrderStatus::ORDER_PAYMENT_SUCCESS])) {
            if ($product['type'] == HtProduct::T_COUPON) {
                $content = sprintf('您的订单#%d支付成功(￥%d)，抵用券随后将邮件给您。客服电话:4000101900', $order_id, $order_total);
            } else {
                $content = sprintf('您的订单#%d支付成功(￥%d)，兑换单将在%s。客服电话:4000101900', $order_id, $order_total, $product['date_rule']['shipping_desc']);
            }
        } else if (in_array($order_data['order']['status_id'], [HtOrderStatus::ORDER_TO_DELIVERY, HtOrderStatus::ORDER_SHIPPED, HtOrderStatus::ORDER_SHIPPING_FAILED])) {
            if ($product['type'] == HtProduct::T_COUPON) {
                $content = sprintf('您的订单#%d已发货到%s，请妥善保存邮件中的抵用券。客服电话:4000101900', $order_id, $email);
            } else {
                $content = sprintf('您的订单#%d已发货到%s，请务必下载并打印兑换单并携带。客服电话:4000101900', $order_id, $email);
            }
        } else if ($order_data['order']['status_id'] == HtOrderStatus::ORDER_REFUND_SUCCESS) {
            $content = sprintf('您的订单#%d已退款(￥%d)。客服电话:4000101900', $order_id, $order_total);
        }

        return $content;
    }
}