<?php

/**
 * @project hitour.server
 * @file Returning.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 14-5-22 下午4:05
 **/
class Returning extends CComponent
{
    public function init()
    {
        return true;
    }

    public function returnRequest($order_id, $force = 0)
    {
        $result = array('code' => 200, 'msg' => '');
        //1. 检查订单是否仍然可退
        $order = Yii::app()->order->getOrderDetailForVoucher($order_id);
        if (empty($order) || !Yii::app()->order->allowReturn($order['order'], $order['order_products'][0], $force)) {
            Yii::log('Return request order[' . $order_id . '] failed, Reason:order not exist or not allowed return.', CLogger::LEVEL_WARNING);
            $result = array('code' => 401, 'msg' => 'order not exist or not allowed return');
            return $result;
        }

        //2. 如果订单在不需要执行退货请求的状态，则直接返回成功，意为不需要退货
        if (!Yii::app()->stateMachine->needReturn($order['order']['status_id'])) {
            Yii::log('Return request no need to execute because state is payment success.', CLogger::LEVEL_INFO);
            $result = array('code' => 200, 'msg' => '当前状态不需要执行退货');
            return $result;
        }

        //3. 如果订单已经在“等待退货确认”状态，则应返回并提示调用模块继续执行退货确认流程
        if ($order['order']['status_id'] == HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION) {
            Yii::log('Return request no need to execute because already in wait return confirmation.', CLogger::LEVEL_INFO);
            $result = array('code' => 301, 'msg' => '已在退货待确认状态');
            return $result;
        }

        //4. 切换订单状态至退订申请
        $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_RETURN_REQUEST);

        //5. 如果退订申请成功,非GTA订单给OP发送通知
        if ($isok) {
            //所有订单自动进入退货待确认
            $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION);
            $result = $this->executeForOrder($order, 'returnRequest');
            if ($result['code'] == 200) {
                Yii::log('Return request is ok for order[' . $order_id . ']. Then need to refund...', CLogger::LEVEL_INFO);
                $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_RETURN_CONFIRMED);
            } else if ($result['code'] >= 400) {
                Yii::log('Return request failed for order[' . $order_id . '].', CLogger::LEVEL_ERROR);
                $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_RETURN_FAILED);
            } else {
                Yii::log('Returning request be commited for order[' . $order_id . ']', CLogger::LEVEL_INFO);
            }
        } else {
            Yii::log('Returning order[' . $order_id . '], but switch status[' . $order['order']['status_id'] . '] failed.', CLogger::LEVEL_ERROR);
            $result = array('code' => 400, 'msg' => '更改订单状态失败');
        }

        return $result;
    }

    public function returnConfirm($order_id)
    {
        $result = array('code' => 200, 'msg' => '');
        //1. 检查订单是否仍然可退
        $order = Yii::app()->order->getOrderDetailForVoucher($order_id);
        if (empty($order) || !Yii::app()->stateMachine->allowReturnConfirm($order['order']['status_id'])) {
            Yii::log('Return confirm failed for order[' . $order_id . '], Reason:order not exist or not be allowed', CLogger::LEVEL_WARNING);
            $result = array('code' => 401, 'msg' => 'order not exist or not be allowed');
            return $result;
        }
        //2. 如果订单在“支付成功”状态，则直接返回成功，意为不需要退货
        if ($order['order']['status_id'] == HtOrderStatus::ORDER_PAYMENT_SUCCESS) {
            Yii::log('Return request no need to execute because state is payment success.', CLogger::LEVEL_INFO);
            $result = array('code' => 200, 'msg' => '在支付成功状态不需要退货确认');
            return $result;
        }

        $result = $this->executeForOrder($order, 'returnConfirm');
        if ($result['code'] == 200) {
            Yii::log('Return is confirmed for order[' . $order_id . ']. Then need to refund...', CLogger::LEVEL_INFO);
            $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_RETURN_CONFIRMED);
        } else {
            Yii::log('Return failed for order[' . $order_id . '].', CLogger::LEVEL_ERROR);
            $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_RETURN_FAILED);
        }
        return $result;
    }

    private function executeForOrder($order_data, $action)
    {
        $results = array();
        foreach ($order_data['order_products'] as $op) {
            if($op['product']['type']==HtProduct::T_HOTEL_BUNDLE || $op['product']['is_combo']==1){
                continue;//壳商品忽略
            }
            $op['supplier_order'] = Yii::app()->order->linkSupplierOrder($op);
            if ($op['supplier_order']['current_status'] == HtSupplierOrder::CANCELED) {
                $results[$op['order_product_id']] = ['code' => 200, 'msg' => 'OK'];
            } else {
                if (empty($product_id) || $op['product_id'] == $product_id) {
                    $results[$op['order_product_id']] = Yii::app()->order->executeOrderAction($order_data['order'], $op, $action);
                }
            }
        }
        if (!empty($results)) {
            $result = Converter::mergeResult($results);
        }
        return $result;
    }

    public function refuseReturn($order_id)
    {
        $result = array('code' => 200, 'msg' => '');
        $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_RETURN_REJECTED);
        if (!$isok) {
            Yii::log('Modify order['.$order_id.'] status to ['.HtOrderStatus::ORDER_RETURN_REJECTED.'] failed.', CLogger::LEVEL_ERROR);
            $result = array('code' => 400, 'msg' => '更改订单状态失败');
        }
        return $result;
    }

    public function refundOrder($order_id, $data = array())
    {
        $result = array('code' => 200, 'msg' => '退款成功');

        Yii::log('Ready to refund for order['.$order_id.'].', CLogger::LEVEL_INFO);
        $order = HtOrder::model()->findByPk($order_id);
        if (empty($order) || !Yii::app()->stateMachine->allowRefund($order['status_id'])) {
            $result = array('code' => 404, 'msg' => '订单不存在或当前状态不允许退');
        }elseif ($order['total'] == 0){
            $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_REFUND_PROCESSING);
            if ($isok) {
                $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_REFUND_SUCCESS);
            }
        }else{
            $data = array_merge($data, array('order_id'=>$order_id));
            $url = Yii::app()->createAbsoluteUrl('payGate/refund', $data);
            $return = HTTPRequest::asyncRequest($url, 10);
        }
        //如果该订单已经对账成功，则重新将对账状态置为有问题
        @HtOrderInvoiceStatus::model()->updateRefundOrderStatus($order_id);
        return $result;
    }

    public function saveRefund($order_id, $data)
    {
        $result = array('code' => 200, 'msg' => '');

        if (empty($data['refund_amount']) || empty($data['comment'])) {
            $result = array('code' => 300, 'msg' => '参数不完整');
        }else{
            $order = HtOrder::model()->findByPk($order_id);
            $payment_history = new HtPaymentHistory();
            $payment_history['pay_or_refund'] = HtPaymentHistory::REFUND;
            $payment_history['payment_really'] = Yii::app()->params['PAYMENT_REALLY'];
            $payment_history['payment_type'] = $order['payment_method'];
            $payment_history['supplier_id'] = 0;
            $payment_history['order_id'] = $order_id;
            $payment_history['product_id'] = 0;
            $payment_history['trade_id'] = '';
            $payment_history['notify_id'] = '';
            $payment_history['trade_total'] = isset($data['refund_amount']) ? $data['refund_amount'] : 0;
            $payment_history['comment'] = isset($data['comment']) ? $data['comment'] : '';
            $payment_history['refund_reason'] = 16;
            $payment_history['buyer_id'] = '';
            $payment_history['buyer_email'] = '';
            $payment_history['trade_time'] = date('Y-m-d H:i:s', time());
            $payment_history['raw_data'] = '';
            $isok = $payment_history->insert();
            if (!$isok) {
                $error = $payment_history->getErrors();
                Yii::log('Save payment_history failed.error['.(json_encode($error)).']', CLogger::LEVEL_ERROR);
                $result = array('code' => 400, 'msg' => json_encode($error));
            }
            //如果该订单已经对账成功，则重新将对账状态置为有问题
            @HtOrderInvoiceStatus::model()->updateRefundOrderStatus($order_id);
        }
        return $result;
    }

}