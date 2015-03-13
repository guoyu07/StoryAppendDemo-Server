<?php

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/22/14
 * Time: 16:05 PM
 */
class Booking extends CComponent
{
    public function init()
    {
        return true;
    }

    /**
     * @param $order_id
     * @param int $product_id 如果指定了 Product_id，是指只 booking 订单中的某个子商品
     * @return array
     */
    public function bookingOrder($order_id, $product_id = 0)
    {
        $result = ['code'=>200,'msg'=>'OK'];
        $order_data = Yii::app()->order->getOrderDetailForVoucher($order_id);

        if (empty($order_data) || !Yii::app()->stateMachine->allowBooking($order_data['order']['status_id'])) {
            $result = array('code' => '401', 'msg' => 'Order not exist or Invalid order status[' . $order_data['order']['status_id'] . ']');
            Yii::log('Booking order[' . $order_id . '] failed. Reason:['.$result['msg'].']', CLogger::LEVEL_ERROR);
            return $result;
        }

        $results = array();
        foreach ($order_data['order_products'] as $op) {
            if($op['product']['type']==HtProduct::T_HOTEL_BUNDLE || $op['product']['is_combo']==1){
                continue;//壳商品忽略
            }
            $op['supplier_order'] = Yii::app()->order->linkSupplierOrder($op);
            if ($op['supplier_order']['current_status'] == HtSupplierOrder::CONFIRMED) {
                $results[$op['order_product_id']] = ['code' => 200, 'msg' => 'OK'];
            } else {
                if (empty($product_id) || $op['product_id'] == $product_id) {
                    $results[$op['order_product_id']] = Yii::app()->order->executeOrderAction($order_data['order'], $op, 'addBooking');
                }
            }
        }
        if (!empty($results)) {
            $result = Converter::mergeResult($results);
        }
        return $result;
    }

//    public function returnRequest($order_id, $product_id = 0)
//    {
//        $order_data = Yii::app()->order->getOrderDetailForVoucher($order_id);
//
//        if (empty($order_data)) {
//            $result = array('code' => '300', 'msg' => 'Not found order');
//        } else if (!in_array($order_data['order']['status_id'], [HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION])) {
//            $result = array('code' => '405', 'msg' => 'Invalid order status[' . $order_data['order']['status_id'] . ']');
//        } else {
//            $result = array('code' => '200', 'msg' => 'OK');
//        }
//
//        if ($result['code'] > 200) {
//            Yii::log('Booking order[' . $order_id . '] failed. code[' . $result['code'] . ']msg[' . $result['msg'] . ']', CLogger::LEVEL_ERROR);
//            return $result;
//        } else if ($order_data['order']['status_id'] == HtOrderStatus::ORDER_STOCK_FAILED) {
//            return $result;
//        }
//
//        $results = array();
//        foreach ($order_data['order_products'] as $op) {
//            if($op['product_type']==HtProduct::T_HOTEL_BUNDLE || $op['product']['is_combo']==1){
//                continue;//壳商品忽略
//            }
//            $op['supplier_order'] = $this->linkSupplierOrder($op);
//            if ($op['supplier_order']['current_status'] == HtSupplierOrder::CANCELED) {
//                $results[$op['order_product_id']] = ['code' => 200, 'msg' => 'OK'];
//            } else {
//                if (empty($product_id) || $op['product_id'] == $product_id) {
//                    $results[$op['order_product_id']] = $this->doBooking($order_data['order'], $op, 'returnRequest');
//                }
//            }
//        }
//        if (!empty($results)) {
//            $result = Converter::mergeResult($results);
//        }
//
//        return $result;
//
//        $result = array();
//        $order_data = Yii::app()->order->getOrderDetailForVoucher($order_id);
//        if (empty($order_data)) {
//            $result = array('code' => '300', 'msg' => 'Not found order');
//        } else if (!in_array($order_data['order']['status_id'], [HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION])) {
//            $result = array('code' => '405', 'msg' => 'Invalid order status[' . $order_data['order']['status_id'] . ']');
//        } else {
//            $result = array('code' => '200', 'msg' => 'OK');
//        }
//        if ($result['code'] > 200) {
//            Yii::log('Booking order[' . $order_id . '] failed. code[' . $result['code'] . ']msg[' . $result['msg'] . ']', CLogger::LEVEL_ERROR);
//            return $result;
//        } else if ($order_data['order']['status_id'] == HtOrderStatus::ORDER_STOCK_FAILED) {
//            return $result;
//        }
//
//        if (count($order_data['order_product_subs'])) {
//            $result_sub = array();
//            foreach ($order_data['order_product_subs'] as $sub) {
//                $supplier_order = HtSupplierOrder::model()->findByPk($sub['supplier_order_id']);
//                if (in_array($supplier_order['current_status'], [HtSupplierOrder::CANCELED])) {
//                    $result_sub[$sub['sub_product_id']] = ['code' => 200, 'msg' => 'OK'];
//                } else {
//                    if (empty($product_id) || $sub['sub_product_id'] == $product_id) {
//                        $order_data['product'] = $sub['product'];
//                        $order_data_copy = $this->refineOrderData($order_data);
//                        $result_sub[$sub['sub_product_id']] = $this->doBooking($order_data_copy, $supplier_order, 'returnRequest');
//                    }
//                }
//            }
//            $result = Converter::mergeResult($result_sub);
//        } else {
//            $supplier_order = $order_data['order_product']['supplier_order'];
//            if (in_array($supplier_order['current_status'], [HtSupplierOrder::CANCELED])) {
//                $result['code'] = 200;
//                $result['msg'] = 'OK';
//            } else {
//                $result = $this->doBooking($order_data, $supplier_order, 'returnRequest');
//            }
//        }
//
//        return $result;
//    }

//    private function refineOrderData($order_data)
//    {
//        $copy = $order_data;
//        $product_id = $copy['product']['product_id'];
//        $special_code = $copy['order_product']['special_code'];
//        $departure_code = $copy['order_product']['departure_code'];
//        $departure_time = $copy['order_product']['departure_time'];
//
//        if (!empty($special_code) && !HtProductSpecialCode::model()->needSpecialCode($product_id)) {
//            $copy['order_product']['special_code'] = '';
//            unset($copy['order_product']['special']);
//        }
//
//        if ((!empty($departure_code) || !empty($departure_time)) && !HtProductDeparturePlan::model()->needDeparture($product_id)) {
//            $copy['order_product']['departure_code'] = '';
//            $copy['order_product']['departure_time'] = '00:00:00';
//            unset($copy['order_product']['departures']);
//        }
//
//        return $copy;
//    }

//    public function returnConfirm($order_id, $product_id = 0)
//    {
//        $order_data = Yii::app()->order->getOrderDetailForVoucher($order_id);
//
//        if (empty($order_data)) {
//            $result = array('code' => '300', 'msg' => 'Not found order');
//        } else if (!in_array($order_data['order']['status_id'], [HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION, HtOrderStatus::ORDER_RETURN_CONFIRMED])) {
//            $result = array('code' => '405', 'msg' => 'Invalid order status[' . $order_data['order']['status_id'] . ']');
//        } else {
//            $result = array('code' => '200', 'msg' => 'OK');
//        }
//        if ($result['code'] > 200) {
//            Yii::log('Return confirm order[' . $order_id . '] failed. code[' . $result['code'] . ']msg[' . $result['msg'] . ']', CLogger::LEVEL_ERROR);
//            return $result;
//        }
//
//        $results = array();
//        foreach ($order_data['order_products'] as $op) {
//            if($op['product_type']==HtProduct::T_HOTEL_BUNDLE || $op['product']['is_combo']==1){
//                continue;//壳商品忽略
//            }
//            $op['supplier_order'] = $this->linkSupplierOrder($op);
//            if ($op['supplier_order']['current_status'] == HtSupplierOrder::CANCELED) {
//                $results[$op['order_product_id']] = ['code' => 200, 'msg' => 'OK'];
//            } else {
//                if (empty($product_id) || $op['product_id'] == $product_id) {
//                    $results[$op['order_product_id']] = $this->doBooking($order_data['order'], $op, 'returnConfirm');
//                }
//            }
//        }
//        if (!empty($results)) {
//            $result = Converter::mergeResult($results);
//        }
//
//        return $result;
//
//        $result = array();
//        $order_data = Yii::app()->order->getOrderDetailForVoucher($order_id);
//        if (empty($order_data)) {
//            $result = array('code' => '300', 'msg' => 'Not found order');
//        } else if (!in_array($order_data['order']['status_id'], [HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION, HtOrderStatus::ORDER_RETURN_CONFIRMED])) {
//            $result = array('code' => '405', 'msg' => 'Invalid order status[' . $order_data['order']['status_id'] . ']');
//        } else {
//            $result = array('code' => '200', 'msg' => 'OK');
//        }
//        if ($result['code'] > 200) {
//            Yii::log('Return confirm order[' . $order_id . '] failed. code[' . $result['code'] . ']msg[' . $result['msg'] . ']', CLogger::LEVEL_ERROR);
//            return $result;
//        }
//
//        if (count($order_data['order_product_subs'])) {
//            $result_sub = array();
//            foreach ($order_data['order_product_subs'] as $sub) {
//                $supplier_order = HtSupplierOrder::model()->findByPk($sub['supplier_order_id']);
//                if (in_array($supplier_order['current_status'], [HtSupplierOrder::CANCELED])) {
//                    $result_sub[$sub['sub_product_id']] = ['code' => 200, 'msg' => 'OK'];
//                } else {
//                    if (empty($product_id) || $sub['sub_product_id'] == $product_id) {
//                        $order_data['product'] = $sub['product'];
//                        $order_data_copy = $this->refineOrderData($order_data);
//                        $result_sub[$sub['sub_product_id']] = $this->doBooking($order_data_copy, $supplier_order, 'returnConfirm');
//                    }
//                }
//            }
//            $result['msg'] = Converter::mergeResult($result_sub);
//        } else {
//            $supplier_order = HtSupplierOrder::model()->findByPk($order_data['order_product']['supplier_order_id']);
//            if (in_array($supplier_order['current_status'], [HtSupplierOrder::CANCELED])) {
//                $result['code'] = 200;
//                $result['msg'] = 'OK';
//            } else {
//                $result = $this->doBooking($order_data, $supplier_order, 'returnConfirm');
//            }
//        }
//
//        return $result;
//    }
}