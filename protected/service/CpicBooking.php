<?php
/**
 * @project hitour.server
 * @file CpicBooking.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 14-8-21 下午6:20
 **/

class CpicBooking {

    private function getTermDate($product_id, $tour_date, $special_code)
    {
        $term_date = '';
//        $cpic_special = HtProductSpecialCode::model()->findByAttributes(
//            ['product_id'=>$product_id, 'special_code'=>$special_code]
//        );
        $cpic_special = HtProductSpecialCombo::getSpecialDetail($product_id,$special_code);

        if (!empty($cpic_special)) {
            $special_en = $cpic_special[0]['items'][0]['en_name'];
            $term_date = strtotime($tour_date . '+' . $special_en);
        }
        return empty($term_date) ? '' : date('Y-m-d', $term_date);
    }

    public function addBooking($order_data, $order_product_data)
    {
        $ret = array();

        $order_id           = $order_product_data['order_id'];
        $product_id         = $order_product_data['product']['product_id'];
        $supplier_order_id  = $order_product_data['supplier_order']['supplier_order_id'];
        $booking_ref        = $order_product_data['supplier_order']['hitour_booking_ref'];
        $city_code          = $order_product_data['product']['city_code'];
        $item_id            = $order_product_data['product']['supplier_product_id'];
        $special_code       = $order_product_data['special_code'];
        $tour_date          = $order_product_data['tour_date'];
        $departure_point    = $this->getDeparturePoint($order_product_data);
        $passengers         = $order_product_data['passengers'];

        Yii::app()->cpic->setTransType(CPICService::$TYPE_INSURE);
        Yii::app()->cpic->setVoucherPath($order_data['voucher_path']);
        Yii::log('CPIC booking start: order_id['.$order_id.']booking_ref['.$booking_ref.']', CLogger::LEVEL_INFO);
        $confirmation_ref = '';
        if ($passengers && count($passengers) > 0) {
            $results = array();
            $term_date = $this->getTermDate($product_id, $tour_date, $special_code);
            if (empty($term_date)) {
                $result['code'] = 501;
                $result['msg'] = 'Special code date transfer to terminate date failed.';
            }else{
                foreach($passengers as &$passenger) {
                    $status = HtInsuranceOrder::PENDING;
                    $pol_number = '';
                    $cpic_order_id = $booking_ref . '_' . $passenger['passenger_id'] . '_' . (date('YmdHis'));
                    $result = Yii::app()->cpic->addBooking(
                        $order_id, $cpic_order_id, $city_code, $item_id, $tour_date, $term_date, $passenger, $departure_point
                    );
                    if (empty($result) || !isset($result['status'])) {
                        $ret['code'] = 300;
                        $ret['msg'] = 'Sent CPIC booking, but no response';
                    }else if ($result['status'] == 'Fail') {
                        $ret['code'] = 301;
                        $ret['msg'] = $result['status_desc'];
                    }else if ($result['status'] > 1) {
                        $ret['code'] = 500;
                        $ret['msg'] = $result['status_desc'];
                    }else if ($result['status'] == 1) {
                        $pol_number = isset($result['polnumber']) ? $result['polnumber'] : '';
                        $status = HtInsuranceOrder::CONFIRMED;
                        $ret['code'] = 200;
                        $ret['msg'] = $result['status_desc'];
                    }
                    $cpic_order = $this->saveCpicOrder($order_id,$passenger['passenger_id'], $cpic_order_id, $pol_number, $status);
                    array_push($results, $ret);
                    if (!empty($pol_number)) {
                        $confirmation_ref .= $pol_number.'('.$passenger['zh_name'].'),';
                    }
                }
                $result = Converter::mergeResult($results);
            }
        }else{
            $result['code'] = 400;
            $result['msg'] = 'Passenger is empty';
        }
        $result['item_id'] = $item_id;
        $result['confirmation_ref'] = trim($confirmation_ref, ',');
        if ($result['code'] == 200) {
            $result['current_status'] = HtSupplierOrder::CONFIRMED;
        }else{
            $result['current_status'] = HtSupplierOrder::PENDING;
        }
        $this->updateSupplierOrder($supplier_order_id, $result);

        Yii::log('CPIC booking end. Result: code['.$ret['code'].']msg['.$ret['msg'].']', CLogger::LEVEL_INFO);
        return $result;
    }

    public function returnRequest($order_data, $order_product_data)
    {
        $ret = array();

        $order_id           = $order_product_data['order_id'];
        $supplier_order_id  = $order_product_data['supplier_order']['supplier_order_id'];
        $booking_ref        = $order_product_data['supplier_order']['hitour_booking_ref'];
        $confirmation_ref   = $order_product_data['supplier_order']['confirmation_ref'];
        $passengers         = $order_product_data['passengers'];

        Yii::app()->cpic->setTransType(CPICService::$TYPE_CANCEL);
        Yii::app()->cpic->setVoucherPath($order_data['voucher_path']);
        Yii::log('CPIC cancel start: order_id['.$order_id.']booking_ref['.$booking_ref.']', CLogger::LEVEL_INFO);
        if (!empty($passengers) && count($passengers) > 0) {
            $results = array();
            foreach($passengers as $passenger) {
                $passenger_id = $passenger['passenger_id'];
                $cpic_order = $this->getInsuranceOrder($order_id, $passenger_id);
                if (empty($cpic_order)) {
                    continue;
                }
                $result = Yii::app()->cpic->cancelBooking(
                    $order_id, $cpic_order['cpic_order_id'], $cpic_order['pol_number']
                );
                if (empty($result) || !isset($result['status'])) {
                    $ret['code'] = 300;
                    $ret['msg'] = 'Sent CPIC booking, but no response';
                }else if ($result['status'] == 'Fail') {
                    $ret['code'] = 301;
                    $ret['msg'] = $result['status_desc'];
                }else if ($result['status'] > 1) {
                    $ret['code'] = 500;
                    $ret['msg'] = $result['status_desc'];
                }else if ($result['status'] == 1) {
                    $ret['code'] = 200;
                    $ret['msg'] = $result['status_desc'];
                }
                $status = $result['status'] == 1 ? HtInsuranceOrder::CANCELED : 0;
                $cpic_order = $this->saveCpicOrder($order_id,$passenger_id, $cpic_order['cpic_order_id'], $cpic_order['pol_number'], $status);
                array_push($results, $ret);
            }
            $result = Converter::mergeResult($results);
        }
        if (empty($result) || !isset($result['code'])) {
            $ret['code'] = 300;
            $ret['msg'] = 'Sent CPIC cancel booking, but no response.';
        }else if ($result['code'] == 200) {
            $ret['code'] = 200;
            $ret['msg'] = 'OK';
            $this->updateSupplierOrder($supplier_order_id, HtSupplierOrder::CANCELED);
        }else{
            $ret['code'] = 500;
            $ret['msg'] = 'code['.$result['code'].']:'.$result['msg'];
        }
        Yii::log('CPIC cancel end. Result: code['.$ret['code'].']msg['.$ret['msg'].']', CLogger::LEVEL_INFO);
        return $ret;
    }

    public function returnConfirm($order_data, $order_product_data)
    {
        $ret = array();

        $order_id           = $order_product_data['order_id'];
        $supplier_order_id  = $order_product_data['supplier_order']['supplier_order_id'];
        $booking_ref        = $order_product_data['supplier_order']['hitour_booking_ref'];

        Yii::log('CPIC cancel confirm start: order_id['.$order_id.']booking_ref['.$booking_ref.']', CLogger::LEVEL_INFO);
        $result = $this->checkBooking($order_data, $order_product_data);
        if (empty($result) || !isset($result['status'])) {
            $ret['code'] = 400;
            $ret['msg'] = 'Check CPIC cancel booking, but no response.';
        }else if (trim($result['status']) == 'X') {
            $ret['code'] = 200;
            $ret['msg'] = 'OK';
            $this->updateSupplierOrder($supplier_order_id, HtSupplierOrder::CANCELED);
        }else{
            $ret['code'] = 500;
            $ret['msg'] = 'status['.$result['status'].']:'.$result['status_desc'];
        }
        Yii::log('CPIC cancel check end. Result: code['.$ret['code'].']msg['.$ret['msg'].']', CLogger::LEVEL_INFO);
        return $ret;
    }

    public function modifyBooking($order)
    {
        $result = array('code' => 500, 'msg' => '此功能未实现');
        return $result;
    }

    public function checkBooking($order, $order_product_data)
    {
        $order_id    = $order_product_data['order_id'];
        $passengers  = $order_product_data['passengers'];

        Yii::app()->cpic->setTransType(CPICService::$TYPE_SEARCH);
        Yii::app()->cpic->setVoucherPath($order['order']['voucher_path']);
        if (!empty($passengers) && count($passengers) > 0) {
            $results = array();
            foreach($passengers as $passenger) {
                $passenger_id = $passenger['passenger_id'];
                $cpic_order = $this->getInsuranceOrder($order_id, $passenger_id);
                if (empty($cpic_order)) {
                    continue;
                }
                $cpic_order_id = $cpic_order['cpic_order_id'];
                $cpic_vars = explode('_', $cpic_order_id);
                if (count($cpic_vars) != 3) {
                    Yii::log('Fatal error: cpic_order_id['.$cpic_order_id.'] is invalid.', CLogger::LEVEL_ERROR);
                    continue;
                }
                $result = Yii::app()->cpic->searchBooking(
                    $order_id, $cpic_order_id, $cpic_order['pol_number'],
                    date('Y-m-d', strtotime($cpic_vars[2])),
                    date('H:i:s', strtotime($cpic_vars[2]))
                );
                if (empty($result) || !isset($result['status'])) {
                    $ret['code'] = 300;
                    $ret['msg'] = 'Search CPIC booking, but no response';
                }else if ($result['status'] == 'Fail') {
                    $ret['code'] = 301;
                    $ret['msg'] = $result['status_desc'];
                }else if ($result['status'] > 1) {
                    $ret['code'] = 500;
                    $ret['msg'] = $result['status_desc'];
                }else if ($result['status'] == 1) {
                    $ret['code'] = 200;
                    $ret['msg'] = $result['status_desc'];
                }
                $status = $result['status'] == 1 ? HtInsuranceOrder::CANCELED : 0;
                $cpic_order = $this->saveCpicOrder($order_id,$passenger_id, $cpic_order['cpic_order_id'], $cpic_order['pol_number'], $status);
                array_push($results, $ret);
            }
            $result = Converter::mergeResult($results);
        }
        return $result;
    }

    public function updateSupplierOrder($supplier_order_id, $data)
    {
        if (is_array($data)) {
            $isok = HtSupplierOrder::model()->updateByPk($supplier_order_id, array(
                'supplier_booking_ref'=> '',
                'supplier_product_id' => $data['item_id'],
                'confirmation_ref'    => $data['confirmation_ref'],
                'additional_info'     => '',
                'payable_by'          => '',
                'tour_supplier'       => '',
                'tour_supplier_code'  => '',
                'current_status'      => $data['current_status']
            ));
        }else{
            $isok = HtSupplierOrder::model()->updateByPk($supplier_order_id, array(
                'current_status'      => $data
            ));
        }
        return $isok !== false;
    }

    private function saveCpicOrder($order_id, $order_passenger_id, $cpic_order_id, $pol_number = '', $status = 1)
    {
        $order = HtInsuranceOrder::model()->findByAttributes([
            'order_id' => $order_id, 'order_passenger_id'=>$order_passenger_id
        ]);
        if (empty($order)) {
            $order = new HtInsuranceOrder();
            $order['order_id'] = $order_id;
            $order['order_passenger_id'] = $order_passenger_id;
            $order['cpic_order_id'] = $cpic_order_id;
            $order['pol_number'] = $pol_number;
            $order['status'] = $status;
            $order->insert();
        }else if (!empty($pol_number)) {
            $order = HtInsuranceOrder::model()->findByAttributes([
                'order_id' => $order_id, 'order_passenger_id'=>$order_passenger_id
            ]);
            $order['status'] = $status;
            $order['date_modified'] = date('Y-m-d H:i:s');
            $order->save();
        }
        return $order;
    }

    private function getDeparturePoint($order_product)
    {
        $departure_point = '';
        $departures = $order_product['departures'];
        $departure_code = $order_product['departure_code'];
        foreach($departures as $language_id => $departure) {
            if ($language_id == 2 && $departure['departure_code'] == $departure_code) {
                $departure_point = $departure['departure_point'];
            }
        }
        return $departure_point;
    }

    private function getInsuranceOrder($order_id, $passenger_id)
    {
        $cpic_order = HtInsuranceOrder::model()->findByAttributes([
            'order_id' => $order_id, 'order_passenger_id'=>$passenger_id
        ]);
        if (empty($cpic_order)) {
            Yii::log('Not found cpic order for order['.$order_id.']order_passenger['.$passenger_id.']', CLogger::LEVEL_ERROR);
        }
        return $cpic_order;
    }
} 