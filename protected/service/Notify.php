<?php

/**
 * Class Notify
 */
class Notify
{
    public function init()
    {
        return true;
    }

    public function notifySupplier($order, $order_product, $preview = false)
    {
        $order_model = Converter::convertModelToArray(HtOrder::model()->findByPk($order['order_id']));
        $order['status_id'] = $order_model['status_id'];
        $result = Yii::app()->email_notify->notifySupplier($order, $order_product, $preview);
        return $result;
    }

    public function notifyOP($order, $order_product, $preview = false, $priority_type = 0)
    {
        $order_model = Converter::convertModelToArray(HtOrder::model()->findByPk($order['order_id']));
        $order['status_id'] = $order_model['status_id'];
        $result = Yii::app()->email_notify->notifyOp($order, $order_product, $preview, $priority_type);
        return $result;
    }

    /**
     * @param $order_data
     * @param bool $preview
     * @return bool|string
     */
    public function notifyCustomer($order_data, $preview = false, $with_pdf = false)
    {
        $this->appendEmailAd($order_data);
        $order_data['order_product'] = $order_data['order_products'][0];
        $order_data['product'] = $order_data['order_products'][0]['product'];
        $status_id = $order_data['order']['status_id'];
        $result = Yii::app()->email_notify->notifyCustomer($order_data, $preview, $with_pdf);
        if ($result || $status_id == HtOrderStatus::ORDER_PAYMENT_SUCCESS) {
            Yii::app()->sms_notify->notifyCustomer($order_data, $preview, $with_pdf);
            Yii::app()->weixin_notify->notifyCustomer($order_data, $preview, $with_pdf);
        }
        return $result;
    }

    private function appendEmailAd(&$order_data)
    {
        $city_code = $order_data['order_products'][0]['product']['city_code'];
        $order_data['email_ad'] = Yii::app()->activity->getEmailAd($city_code);
    }
}