<?php
/**
 * Created by PhpStorm.
 * User: app
 * Date: 14-5-24
 * Time: 下午1:05
 */
/**
 * @project hitour.server
 * @file GtaBooking.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 14-5-24 下午1:05
**/

class GtaBooking {

    public function addBooking($order_data,$order_product_data)
    {
        $ret = array();

        $order_id           = $order_product_data['order_id'];
        $supplier_order_id  = $order_product_data['supplier_order']['supplier_order_id'];
        $booking_ref        = $order_product_data['supplier_order']['hitour_booking_ref'];
        $city_code          = $order_product_data['product']['city_code'];
        $item_id            = $order_product_data['product']['supplier_product_id'];
        $product_id         = $order_product_data['product']['product_id'];
        $special_code       = $order_product_data['special_code'];
        $tour_date          = $order_product_data['tour_date'];
        $departure_point    = $order_product_data['departure_code'];
        $language           = $order_product_data['language'];
        $language_list_code = $order_product_data['language_list_code'];
        $departure_time     = $this->convertDepartureTime($order_product_data['departure_time']);
        $passengers         = $this->convertPassenger($order_product_data['passengers']);
        $currency           = $this->getCurrency($product_id, $tour_date);

        Yii::app()->gta->setLanguage('zh');
        Yii::app()->gta->setCurrency($currency);
        Yii::app()->gta->setVoucherPath($order_data['voucher_path']);

        Yii::log('GTA booking start: order_id['.$order_id.']product_id['.$product_id.']booking_ref['.$booking_ref.']', CLogger::LEVEL_INFO);
        $result = Yii::app()->gta->addBookingSightseeing(
            $order_id, $booking_ref, $city_code, $item_id, $special_code, $tour_date,
            $passengers, $departure_point, $departure_time, $language, $language_list_code
        );
        if (empty($result) || !isset($result['status'])) {
            $ret['code'] = 300;
            $ret['msg'] = 'Sent GTA booking, but no response';
        }else if ($result['status'] == 'CP') {
            $ret['code'] = 301;
            $ret['msg'] = $result['status_desc'];
            $result['current_status'] = HtSupplierOrder::PENDING;
            $this->updateSupplierOrder($supplier_order_id, $result);
        }else if ($result['status'] == 'F') {
            $ret['code'] = 400;
            $ret['msg'] = $result['status_desc'];
        }else if ($result['status'] == 'X') {
            $ret['code'] = 500;
            $ret['msg'] = 'The order already be canceled.';
        }else if ($result['status'] == 'C') {
            $ret['code'] = 200;
            $ret['msg'] = '';
            $result['current_status'] = HtSupplierOrder::CONFIRMED;
            $this->updateSupplierOrder($supplier_order_id, $result);
            $this->saveChargeCondition($order_id, $booking_ref);
        }
        Yii::log('GTA booking end. Result: code['.$ret['code'].']msg['.$ret['msg'].']', CLogger::LEVEL_INFO);
        return $ret;
    }

    public function returnRequest($order_data, $order_product_data)
    {
        $ret = array();

        $order_id           = $order_product_data['order_id'];
        $supplier_order_id  = $order_product_data['supplier_order']['supplier_order_id'];
        $booking_ref        = $order_product_data['supplier_order']['hitour_booking_ref'];
        $tour_date          = $order_product_data['tour_date'];
        $currency           = $this->getCurrency($order_product_data['product_id'], $tour_date);

        Yii::app()->gta->setLanguage('zh');
        Yii::app()->gta->setCurrency($currency);
        Yii::app()->gta->setVoucherPath($order_data['voucher_path']);

        Yii::log('GTA cancel start: order_id['.$order_id.']booking_ref['.$booking_ref.']', CLogger::LEVEL_INFO);
        $result = Yii::app()->gta->cancelBookingSightseeing(
            $order_id, $booking_ref
        );
        if (empty($result) || !isset($result['status'])) {
            $ret['code'] = 300;
            $ret['msg'] = 'Sent GTA cancel booking, but no response.';
        }else if (trim($result['status']) == 'X') {
            $ret['code'] = 200;
            $ret['msg'] = 'OK';
            $this->updateSupplierOrder($supplier_order_id, HtSupplierOrder::CANCELED);
        }else{
            $ret['code'] = 500;
            $ret['msg'] = 'status['.$result['status'].']:'.$result['status_desc'];
        }
        Yii::log('GTA cancel end. Result: code['.$ret['code'].']msg['.$ret['msg'].']', CLogger::LEVEL_INFO);
        return $ret;
    }

    public function returnConfirm($order_data, $order_product_data)
    {
        $ret = array();

        $order_id           = $order_product_data['order_id'];
        $supplier_order_id  = $order_product_data['supplier_order']['supplier_order_id'];
        $booking_ref        = $order_product_data['supplier_order']['hitour_booking_ref'];

        Yii::log('GTA cancel confirm start: order_id['.$order_id.']booking_ref['.$booking_ref.']', CLogger::LEVEL_INFO);
        $result = $this->checkBooking($order_data, $order_product_data);
        if (empty($result) || !isset($result['status'])) {
            $ret['code'] = 400;
            $ret['msg'] = 'Check GTA cancel booking, but no response.';
        }else if (trim($result['status']) == 'X' || trim($result['status']) == 'None') {
            $ret['code'] = 200;
            $ret['msg'] = 'OK';
            $this->updateSupplierOrder($supplier_order_id, HtSupplierOrder::CANCELED);
        }else{
            $ret['code'] = 500;
            $gta_status_msg = isset($result['status_desc']) ? $result['status_desc'] : '';
            $ret['msg'] = 'status['.$result['status'].']:'.$gta_status_msg;
        }
        Yii::log('GTA cancel check end. Result: code['.$ret['code'].']msg['.$ret['msg'].']', CLogger::LEVEL_INFO);
        return $ret;
    }

    public function modifyBooking($order)
    {
        $result = array('code' => 500, 'msg' => '此功能未实现');
        return $result;
    }

    public function checkBooking($order_data, $order_product_data)
    {
        $order_id    = $order_product_data['order_id'];
        $booking_ref = $order_product_data['supplier_order']['hitour_booking_ref'];

        Yii::app()->gta->setLanguage('zh');
        Yii::app()->gta->setCurrency('');
        Yii::app()->gta->setVoucherPath($order_data['voucher_path']);
        $result = Yii::app()->gta->searchBookingSightseeing($order_id, $booking_ref);

        return $result;
    }

    public function updateSupplierOrder($supplier_order_id, $data)
    {
        if (is_array($data)) {
            $isok = HtSupplierOrder::model()->updateByPk($supplier_order_id, array(
                'supplier_booking_ref'=> $data['api_reference'],
                'supplier_product_id' => $data['item_id'],
                'confirmation_ref'    => $data['confirmation_ref'],
                'additional_info'     => '',
                'payable_by'          => $data['payable_by'],
                'tour_supplier'       => $data['supplier_title'],
                'tour_supplier_code'  => $data['supplier_code'],
                'current_status'      => $data['current_status']
            ));
        }else{
            $isok = HtSupplierOrder::model()->updateByPk($supplier_order_id, array(
                'current_status'      => $data
            ));
        }
        return $isok !== false;
    }

    private function saveChargeCondition($order_id, $booking_ref)
    {
        $result = Yii::app()->gta->getChargeConditions(false, false, false, 1, array(), '', '', '', $booking_ref, $order_id);

        if ($result['status'] != 'OK' || empty($result['data'])) {
            return array();
        }
        foreach ($result['data'] as $ikey => $item) {
            $condition = new GtaChargecondition();
            $condition['booking_reference'] = $booking_ref;
            $condition['type'] = $item['type'];
            $condition['maximum_possible_charges_shown'] = (int)$item['maximum_possible_charges_shown'];
            $condition['charge'] = (int)$item['charge'];
            $condition['allowable'] = $item['allowable'];
            $condition['from_date'] = $item['from_date'];
            $condition['to_date'] = $item['to_date'];
            $condition['currency'] = $item['currency'];
            if (isset($item['effective_from_date'])) {
                $condition['effective_from_date'] = $item['effective_from_date'];
            }
            if (isset($item['effective_to_date'])) {
                $condition['effective_to_date'] = $item['effective_to_date'];
            }

            $condition['amount'] = $item['amount'];
            $condition['msg'] = isset($item['msg']) ? $item['msg'] : '';
            $condition->save();
        }
        return $result;
    }

    private function convertDepartureTime($departure_time)
    {
        $items = explode(':', $departure_time);
        if (count($items) >= 2) {
            return sprintf('%02d.%02d', $items[0] , $items[1]);
        }else{
            return $departure_time;
        }
    }

    private function convertPassenger($passengers)
    {
        $gta_passengers = array();
        foreach ($passengers as $p) {
            $pax = array();
            $pax['en_name'] = $p['en_name'];

            if (in_array($p['ticket_id'], [HtTicketType::TYPE_CHILD,HtTicketType::TYPE_YOUTH,HtTicketType::TYPE_INFANT]) || $p['is_child']) {
                $pax['is_child'] = 1;
                $pax['age'] = $p['age'];
            } else {
                $pax['is_child'] = 0;
            }
            $gta_passengers[] = $pax;
        }
        return $gta_passengers;
    }

    private function getCurrency($product_id, $tour_date)
    {
        $currency = '';
        $sql = 'SELECT currency FROM ht_product_price_plan ';
        $sql .= 'WHERE product_id="'.$product_id.'" AND ';
        $sql .= '"'.$tour_date.'>=from_date" AND "'.$tour_date.'"<=to_date';
        $plan = Yii::app()->db->createCommand($sql)->queryRow();
        if ($plan) {
            $currency = $plan['currency'];
        }
        return $currency;
    }
}