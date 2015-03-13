<?php

/**
 * Class Notify
 */
class WeixinNotify
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
        $order_id = $order_data['order']['order_id'];
        $status_id = $order_data['order']['status_id'];
        $customer_id = $order_data['order']['customer_id'];

        $customer = HtCustomer::model()->findByPk($customer_id);

        if (empty($customer['wx_openid'])) {
            Yii::log('Weixin OpenId Empty,order_id=' . $order_id . ',customer_id=' . $customer_id);
            return true;
        }

        $weixin_open_id = $customer['wx_openid'];

        if ($preview) {
            return json_encode($order_data, 271);
        } else {
            //发送短信
            $weixin = new Weixin();
            $weixin_data = $this->generateWeixinContent($order_data);
            $result = $weixin->sendTemplateMsg($weixin_open_id, $weixin_data);
            if ($result['code'] != 200) {
                Yii::log('Send weixin failed to ' . $weixin_open_id . ',order_id=' . $order_id . ',status_id:' . $status_id . ',' . $result['msg']);
            } else {
                Yii::log('Send weixin success to ' . $weixin_open_id . ',order_id=' . $order_id . ',status_id:' . $status_id . ',' . $result['msg']);
            }
            return ($result['code'] == 200);
        }

    }


    private function generateWeixinContent($order_data)
    {
        $data = array();
        $order_id = $order_data['order']['order_id'];
        $order_total = $order_data['order']['total'];
        $email = $order_data['order']['contacts_email'];
        $status_id = $order_data['order']['status_id'];

        $data['order_id'] = $order_id;
        $data['status_id'] = $status_id;
        $product = $order_data['order_products'][0]['product'];
        $data['product_name'] = $product['description']['name'];
        $data['pay_total'] = '￥' . $order_total;

        if (in_array($status_id, [HtOrderStatus::ORDER_PAYMENT_SUCCESS])) {
            $data['status_id'] = HtOrderStatus::ORDER_PAYMENT_SUCCESS;

            $preface = sprintf('您的订单 #%d 支付成功', $order_id);
            $afterword = '订单编号：' . $order_id . "\n";
            $afterword .= '订单邮箱：' . $email . "\n\n";
            if($product['type']==HtProduct::T_COUPON){
                $afterword .= sprintf("抵用券将在随后通过邮件发送，请注意查收！");
            }else{
                $afterword .= sprintf("兑换单将在%s，请注意查收！", $order_data['product']['date_rule']['shipping_desc']);
            }
        } else if (in_array($status_id, [HtOrderStatus::ORDER_TO_DELIVERY, HtOrderStatus::ORDER_SHIPPED, HtOrderStatus::ORDER_SHIPPING_FAILED])) {
            $data['status_id'] = HtOrderStatus::ORDER_SHIPPED;
            $preface = sprintf('您的订单 #%d 已发货，请注意查收！', $order_id);
            $afterword = '订单邮箱：' . $email . "\n\n";

            if($product['type']==HtProduct::T_COUPON){
                $afterword .= sprintf("抵用券已经发送到您的邮箱，请妥善保存！");
            }else{
                $afterword .= sprintf('出行前请务必下载并打印兑换单，并随身携带。');
            }
        } else if ($status_id == HtOrderStatus::ORDER_REFUND_SUCCESS) {
            $afterword = sprintf('您的订单#%d已退款(￥%d)。客服电话:4000101900', $order_id, $order_total);
            $preface = '';
        } else {
            $afterword = '';
            $preface = '';
        }

        $afterword .= "\n客服电话：400-010-1900";
        $data['preface'] = $preface;
        $data['afterword'] = $afterword;

        return $data;
    }

}