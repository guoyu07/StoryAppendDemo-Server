<?php

/**
 * Created by PhpStorm.
 * User: app
 * Date: 14-5-22
 * Time: 上午9:59
 */
class StateMachine extends CComponent
{

    public function init()
    {
        return true;
    }

    public function switchStatus($order_id, $to_status, $comment = '')
    {
        if (!$this->couldSwitchTo($order_id, $to_status)) {
            return false;
        }
        $ret = false;

        //3. 根据检查结果进行状态操作
        $result = HtOrder::model()->updateByPk($order_id,
            array('status_id' => $to_status, 'date_modified' => (date('Y-m-d H:i:s')))
        );
        if (false === $result) {
            Yii::log(__CLASS__ . ': switch status,ID[' . $order_id . '],but update failed.', CLogger::LEVEL_ERROR);

            return $ret;
        } else {
            $order_history = new HtOrderHistory();
            $order_history['order_id'] = (int)$order_id;
            $order_history['status_id'] = (int)$to_status;
            $order_history['comment'] = $comment;
            $order_history['date_added'] = date('Y-m-d H:i:s');;
            $result = $order_history->insert();
            if (!$result) {
                Yii::log(__CLASS__ . ': switch status,ID[' . $order_id . '], but failed to insert order_history.', CLogger::LEVEL_ERROR);
            }
            $order_product = HtOrderProduct::model()->findByAttributes(array('order_id' => $order_id));
            if (empty($order_product)) {
                Yii::log(__CLASS__ . ': switch status,ID[' . $order_id . '], but not found order_product.', CLogger::LEVEL_ERROR);
            }else{
                $order_product_history = new HtOrderProductHistory();
                $order_product_history['order_id'] = (int)$order_id;
                $order_product_history['order_product_id'] = (int)$order_product->order_product_id;
                $order_product_history['status_id'] = (int)$to_status;
                $order_product_history['comment'] = $comment;
                $result = $order_product_history->insert();
                if (!$result) {
                    Yii::log(__CLASS__ . ': switch status,ID[' . $order_id . '], but failed to insert order_product_history.', CLogger::LEVEL_ERROR);
                }
            }
            $ret = true;
        }

        //4. 执行现状态需要触发的动作
        if (in_array($to_status, [HtOrderStatus::ORDER_PAYMENT_SUCCESS,])) {
            // other actions after user pay success
            Yii::app()->order->afterUserPayed($order_id);

            //update redeem/return expire date
            Yii::app()->order->updateRedeemReturnExpireDate($order_id);

            $order_data = Yii::app()->order->getOrderDetailForVoucher($order_id);
            $isok = Yii::app()->notify->notifyCustomer($order_data);
            if (!$isok) {
                $isok = Yii::app()->notify->notifyOP($order_data, $order_data['order_products'][0], false, 1);
            }

            $first_product = $order_data['order_products'][0]['product'];
            Yii::log('Ready to switch to wait confirmation:order['.$order_id.']product['.$first_product['product_id'].']type['.$first_product['type'].']');
            if ($first_product['type'] != HtProduct::T_HOTEL_BUNDLE) {
                $isok = Yii::app()->stateMachine->switchStatus($order_id,HtOrderStatus::ORDER_WAIT_CONFIRMATION);
                if (!$isok) {
                    Yii::log('Switch status for booking failed. order_id['.$order_id.']',CLogger::LEVEL_ERROR);
                }
            }else{
                Yii::app()->notify->notifyOP($order_data, $order_data['order_products'][0]);
            }
        }
        if ($to_status == HtOrderStatus::ORDER_WAIT_CONFIRMATION) {
            $order_data = Yii::app()->order->getOrderDetailForVoucher($order_id);
            $first_product = $order_data['order_products'][0]['product'];

            $supplier_id = empty($first_product['supplier_id']) ? 0 : $first_product['supplier_id'];
            if (!empty($order_data['order']['total']) || ($order_data['order']['total'] == 0.0 && $supplier_id != 11 )) {
                Yii::log('Booking request will be send for order[' . $order_id . ']...', CLogger::LEVEL_INFO);
                $result = Yii::app()->booking->bookingOrder($order_id);
                $comment = '返回码[' . $result['code'] . ']说明[' . $result['msg'] . ']';
                if ($result['code'] == 200) { //Booking success means ready to delivery
                    $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_TO_DELIVERY, $comment);
                    Yii::log('Booking successfully for order[' . $order_id . '], Result:code[' . $result['code'] . ']msg[' . $result['msg'] . ']', CLogger::LEVEL_INFO);
                } else if ($result['code'] >= 400) {
                    $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_STOCK_FAILED, $comment);
                    Yii::log('Booking failed for order[' . $order_id . '], Result:code[' . $result['code'] . ']msg[' . $result['msg'] . ']', CLogger::LEVEL_ERROR);
                } else {
                    Yii::log('Booking not finished for order[' . $order_id . '], Result:code[' . $result['code'] . ']msg[' . $result['msg'] . ']', CLogger::LEVEL_INFO);
                }
            }
        }

        Yii::log(__CLASS__ . ': switch status,ID[' . $order_id . ']to[' . $to_status . '] finished. Result[' . $ret . ']', CLogger::LEVEL_INFO);
        //5. 返回成功或失败
        return $ret;
    }

    public function couldSwitchTo($order_id, $to_status)
    {
        //1. 提取当前状态
        $order = HtOrder::model()->findByPk($order_id);
        if (empty($order)) {
            Yii::log(__CLASS__ . ': want switch status but not found order.order_id[' . $order_id . ']',
                CLogger::LEVEL_ERROR);
            return false;
        }
        $now_status = $order->status_id;
        if (empty($now_status)) {
            Yii::log(__CLASS__ . ': want switch status but order status is invalid.order_id[' . $order_id . ']',
                CLogger::LEVEL_ERROR);
            return false;
        } else if ($now_status == $to_status) {
            return true;
        }

        //2. 状态机切换检查
        $allow_switch = false;
        switch ($to_status) {
            case HtOrderStatus::ORDER_CONFIRMED:
                $allow_switch = in_array($now_status, []);
                break;
            case HtOrderStatus::ORDER_PAYMENT_SUCCESS:
                $allow_switch = in_array($now_status,
                    [HtOrderStatus::ORDER_CONFIRMED, HtOrderStatus::ORDER_PAYMENT_FAILED, HtOrderStatus::ORDER_CANCELED]);
                break;
            case HtOrderStatus::ORDER_PAYMENT_FAILED:
                $allow_switch = in_array($now_status,
                    [HtOrderStatus::ORDER_CONFIRMED, HtOrderStatus::ORDER_PAYMENT_FAILED]);
                break;
            case HtOrderStatus::ORDER_CANCELED:
                $allow_switch = in_array($now_status,
                    [HtOrderStatus::ORDER_CONFIRMED, HtOrderStatus::ORDER_PAYMENT_FAILED]);
                break;
            case HtOrderStatus::ORDER_NOTPAY_EXPIRED:
                $allow_switch = in_array($now_status, [HtOrderStatus::ORDER_CONFIRMED]);
                break;
            case HtOrderStatus::ORDER_PAID_EXPIRED:
                $allow_switch = in_array($now_status, [HtOrderStatus::ORDER_NOTPAY_EXPIRED]);
                break;
            case HtOrderStatus::ORDER_WAIT_CONFIRMATION:
                $allow_switch = in_array($now_status,
                    [HtOrderStatus::ORDER_PAYMENT_SUCCESS, HtOrderStatus::ORDER_STOCK_FAILED, HtOrderStatus::ORDER_BOOKING_FAILED]);
                break;
            case HtOrderStatus::ORDER_TO_DELIVERY:
                $allow_switch = in_array($now_status, [HtOrderStatus::ORDER_WAIT_CONFIRMATION, HtOrderStatus::ORDER_STOCK_FAILED]);
                break;
            case HtOrderStatus::ORDER_STOCK_FAILED:
                $allow_switch = in_array($now_status, [HtOrderStatus::ORDER_WAIT_CONFIRMATION]);
                break;
            case HtOrderStatus::ORDER_BOOKING_FAILED:
                $allow_switch = in_array($now_status,
                    [HtOrderStatus::ORDER_WAIT_CONFIRMATION, HtOrderStatus::ORDER_STOCK_FAILED]);
                break;
            case HtOrderStatus::ORDER_SHIPPED:
                $allow_switch = in_array($now_status, [HtOrderStatus::ORDER_TO_DELIVERY, HtOrderStatus::ORDER_SHIPPING_FAILED]);
                break;
            case HtOrderStatus::ORDER_SHIPPING_FAILED:
                $allow_switch = in_array($now_status, [HtOrderStatus::ORDER_TO_DELIVERY]);
                break;
            case HtOrderStatus::ORDER_OUTOF_REFUND:
                $allow_switch = in_array($now_status, [HtOrderStatus::ORDER_SHIPPED]);
                break;
            case HtOrderStatus::ORDER_RETURN_REQUEST:
                $allow_switch = in_array($now_status, [HtOrderStatus::ORDER_PAYMENT_SUCCESS, HtOrderStatus::ORDER_PAID_EXPIRED, HtOrderStatus::ORDER_WAIT_CONFIRMATION, HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION, HtOrderStatus::ORDER_TO_DELIVERY, HtOrderStatus::ORDER_STOCK_FAILED, HtOrderStatus::ORDER_SHIPPED, HtOrderStatus::ORDER_BOOKING_FAILED, HtOrderStatus::ORDER_SHIPPING_FAILED]);
                break;
            case HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION:
                $allow_switch = in_array($now_status, [HtOrderStatus::ORDER_RETURN_REQUEST]);
                break;
            case HtOrderStatus::ORDER_RETURN_CONFIRMED:
                $allow_switch = in_array($now_status, [HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION,HtOrderStatus::ORDER_RETURN_FAILED]);
                break;
            case HtOrderStatus::ORDER_RETURN_REJECTED:
                $allow_switch = in_array($now_status, [HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION]);
                break;
            case HtOrderStatus::ORDER_RETURN_FAILED:
                $allow_switch = in_array($now_status, [HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION]);
                break;
            case HtOrderStatus::ORDER_REFUND_PROCESSING:
                $allow_switch = in_array($now_status, [HtOrderStatus::ORDER_PAYMENT_SUCCESS,HtOrderStatus::ORDER_RETURN_CONFIRMED,HtOrderStatus::ORDER_PAID_EXPIRED]);
                break;
            case HtOrderStatus::ORDER_REFUND_SUCCESS:
                $allow_switch = in_array($now_status, [HtOrderStatus::ORDER_RETURN_CONFIRMED,HtOrderStatus::ORDER_REFUND_PROCESSING,HtOrderStatus::ORDER_REFUND_FAILED]);
                break;
            case HtOrderStatus::ORDER_REFUND_FAILED:
                $allow_switch = in_array($now_status, [HtOrderStatus::ORDER_RETURN_CONFIRMED,HtOrderStatus::ORDER_REFUND_PROCESSING]);
                break;
            default:
                break;
        }
        if (!$allow_switch) {
            Yii::log(__CLASS__ . ': switch status,ID[' . $order_id . '],but status[' . $now_status . ']to[' . $to_status . '] is forbidden.',
                CLogger::LEVEL_ERROR);
            return false;
        }

        return true;
    }

    public function allowBooking($status)
    {
        return in_array($status, [HtOrderStatus::ORDER_WAIT_CONFIRMATION, HtOrderStatus::ORDER_BOOKING_FAILED, HtOrderStatus::ORDER_STOCK_FAILED]);
    }

    public function allowReturn($status)
    {
        return in_array($status, [HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION]);
    }

    public function needReturn($status)
    {
        return in_array($status, [HtOrderStatus::ORDER_RETURN_FAILED, HtOrderStatus::ORDER_RETURN_REQUEST, HtOrderStatus::ORDER_SHIPPED, HtOrderStatus::ORDER_SHIPPING_FAILED, HtOrderStatus::ORDER_STOCK_FAILED, HtOrderStatus::ORDER_TO_DELIVERY, HtOrderStatus::ORDER_WAIT_CONFIRMATION, HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION]);
    }

    public function allowReturnConfirm($status)
    {
        return in_array($status, [HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION,HtOrderStatus::ORDER_RETURN_CONFIRMED,HtOrderStatus::ORDER_PAYMENT_SUCCESS]);
    }

    public function allowRefund($status)
    {
        return in_array($status, [HtOrderStatus::ORDER_RETURN_CONFIRMED,HtOrderStatus::ORDER_SHIPPED,HtOrderStatus::ORDER_PAYMENT_SUCCESS,HtOrderStatus::ORDER_WAIT_CONFIRMATION,HtOrderStatus::ORDER_PAID_EXPIRED]);
    }
}