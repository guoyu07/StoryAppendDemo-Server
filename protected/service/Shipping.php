<?php

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/22/14
 * Time: 16:05 PM
 */
class Shipping extends CComponent
{
    public function init()
    {
        require_once('barcode.php');
        require_once('qrcode/full/qrlib.php');
        return true;
    }

    public function shippingOrder($order_id, $preview = false, $with_pdf = false)
    {
        $result = HtInsuranceCode::model()->rationInsuranceCode($order_id);
        if ($result['code'] != 200) {
            return $result;
        }

        $result = HtOrderGiftCoupon::model()->grantGiftCoupon($order_id);
        if ($result['code'] != 200) {
            return $result;
        }

        HtOrder::model()->updateExtractCode($order_id);

        if($preview){
            $result = '';
        }
        //reload order detail with insurance codes and gift coupon;
        $order_data = Yii::app()->order->getOrderDetailForVoucher($order_id);

        foreach ($order_data['order_products'] as $op) {
            if(empty($op['supplier_order']))
                continue;//壳商品或者 Combo 商品

            if ($op['product']['shipping_rule']['need_hitour_voucher']) {
                $data = $this->convertVoucherData($order_data['order'], $op);
                $r = $this->generateVoucher($data, $preview);
                if ($preview) {
                    $result .= $r;
                } else {
                    $result = $result && $r;
                }
            }
            $this->prepareManual($order_data['order'], $op);
        }

        if (empty($result)) {
            $result = array('code' => 400, 'msg' => '发货失败：未生成兑换单');
        } else {
            if ($preview) {
                return $result;
            } else {
                $isok = Yii::app()->notify->notifyCustomer($order_data,$preview,$with_pdf);
                $error_msg = '';
                if (!$isok) {
                    $error_msg = '发送邮件给客户失败。';
                } else {
                    $shipping_count = HtOrderHistory::model()->countByAttributes(['order_id' => $order_id, 'status_id' => HtOrderStatus::ORDER_SHIPPED]);
                    if ($shipping_count == 0) {
                        foreach ($order_data['order_products'] as $op) {
                            if ($this->needNotifySupplier($order_data['order'], $op)) {
                                $isok &= Yii::app()->notify->notifySupplier($order_data['order'], $op);
                            }
                        }
                    }

                    if (!$isok) {
                        $error_msg = '发送邮件给供应商失败。';
                    }
                }

                if ($isok) {
                    $result = array('code' => 200, 'msg' => '发货成功。');
                } else {
                    $result = array('code' => 401, 'msg' => '发货失败: ' . $error_msg);
                }
            }
        }
        Yii::log('Shipping finished, result:code[' . $result['code'] . ']msg[' . $result['msg'] . ']');
        return $result;
    }

    public function convertVoucherData($order, $order_product)
    {
        $data = array();
        //@todo language
        $language_id = $order_product['product']['voucher_rule']['language_id'] & 0x1;

        $data['need_hitour_voucher'] = $order_product['product']['shipping_rule']['need_hitour_voucher'];
        $data['voucher_path'] = $order['voucher_path'];
        $data['voucher_base_url'] = $order['voucher_base_url'];
        $data['supplier_order_id'] = $order_product['supplier_order']['supplier_order_id'];


        $data = array_merge($data, $this->getOrderInfo($order, $order_product));
        $data['confirmation_info'] = $this->getConfirmationInfo($order, $order_product);
        $data['pax_info'] = $this->getPaxInfo($order, $order_product);

        return $data;
    }

    public function getOrderInfo($order, $order_product)
    {
        $order_info = array();

        $product_info = $this->getProductInfo($order_product);

        $order_info['order_id'] = $order['order_id'];
        $order_info['contacts_email'] = $order['contacts_email'];


        //@todo combo票的date rule用"子商品"的么？
        $date_rule = $order_product['product']['date_rule'];
        $order_info['user_data'] = [];
        if ($date_rule['need_tour_date']) {
            $order_info['user_data'][] = ['title' => $product_info['tour_date_title'], 'value' => date('Y-m-d', strtotime($order_product['tour_date']))];
        } else {
            if($order_product['redeem_expire_date']!='0000-00-00'){
                $order_info['user_data'][] = ['title' => 'Redeem Before/兑换截止', 'value' => date('Y-m-d', strtotime($order_product['redeem_expire_date']))];
            }
        }

        $special = $order_product['special_info'];
        $need_special = HtProductSpecialCombo::model()->needSpecialCode($product_info['product_id']);
        $special_en_name = '';
        $special_cn_name = '';
        $product_origin_name = '';
        if (!empty($special) && $need_special) {
            foreach($special[0]['items'] as $item){
                $special_value = $item['en_name'];
                if ($special_value != $item['cn_name']) {
                    if(!empty($special_value)){
                        $special_value .= ' ';
                    }
                    $special_value .= $item['cn_name'];
                }
                $order_info['user_data'][] = ['title' => $item['group_title'], 'value' => $special_value];
                $special_en_name .= $item['en_name'] . ',';
                $special_cn_name .= $item['cn_name'] . ',';
                $product_origin_name .= $item['product_origin_name'] . ',';
            }
            $special_en_name = substr($special_en_name,0,-1);
            $special_cn_name = substr($special_cn_name,0,-1);
            $product_origin_name = substr($product_origin_name,0,-1);


            if ($product_origin_name) {
                $product_info['origin_name'] = $product_origin_name;
            } else {
                if ($special_en_name) {
                    $product_info['origin_name'] .= '(' . $special_en_name . ')';
                } else if ($special_cn_name) {
                    $product_info['origin_name'] .= '(' . $special_cn_name . ')';
                }
            }

            $product_info['name'] .= $special_en_name ? '(' . $special_en_name . ')' : '';
            $product_info['sub_name'] .= $special_cn_name ? '(' . $special_cn_name . ')' : '';

            if ($product_info['origin_name'] && $product_info['voucher_rule']['need_origin_name']) {
                $product_info['name'] = $product_info['origin_name'];
            }
        }


        $departure_value = '';
        $departures = $order_product['departures'];
        if (empty($departures) && $product_info['supplier_id'] == HtSupplier::S_GTA) {
            //city_code,departure-code;
            $hotels = GtaSightseeingHotel::model()->findAllByAttributes(['city_code' => $product_info['city_code'], 'hotel_code' => $order_product['departure_code']]);
            foreach ($hotels as $hotel) {
                $departures[] = ['language_id' => $hotel['language_id'], 'departure_point' => $hotel['hotel_name']];
            }
        }
        $need_departure = HtProductDeparturePlan::model()->needDeparture($product_info['product_id']);
        if (!empty($departures) && $need_departure) {
            foreach ($departures as $d) {
                if ($d['language_id'] == 1) {
                    $departure_value = $d['departure_point'];
                } else {
                    if ($departure_value != $d['departure_point']) {
                        $departure_value .= '<br>' . $d['departure_point'];
                    }
                }
            }
        }


        if (($time = date('H:i', strtotime($order_product['departure_time']))) != '00:00') {
            if ($departure_value) $departure_value .= '  ';
            $departure_value .= $time;
        }


        if ($departure_value) {
            $order_info['user_data'][] = ['title' => $product_info['departure_title'], 'value' => $departure_value];
        }
        return ['product_info' => $product_info, 'order_info' => $order_info];
    }

    private function getProductInfo($order_product)
    {
        $product = $order_product['product'];
        $voucher_rule = $product['voucher_rule'];
        $product_info['product_id'] = $product['product_id'];
        $product_info['supplier_id'] = $product['supplier_id'];
        $product_info['city_code'] = $product['city_code'];
        $product_info['voucher_rule'] = $voucher_rule;
        foreach ($product['descriptions'] as $pd) {
            if ($pd['language_id'] == 1) {
                $product_info['origin_name'] = $pd['origin_name'];
                $product_info['name'] = $pd['name'];
                $product_info['please_note'] = $pd['please_note'];
                $product_info['summary'] = $pd['summary'];
                $product_info['tour_date_title'] = $pd['tour_date_title'];
                $product_info['special_title'] = $pd['special_title'];
                $product_info['departure_title'] = $pd['departure_title'];
            } else {
                if (empty($product_info['origin_name']) && $pd['origin_name']) {
                    $product_info['origin_name'] = $pd['origin_name'];
                }
                $product_info['sub_name'] = $pd['name'];
                $product_info['tour_date_title'] .= '/' . $pd['tour_date_title'];
                $product_info['special_title'] .= '/' . $pd['special_title'];
                $product_info['departure_title'] .= '/' . $pd['departure_title'];
            }
        }


        if (empty($product_info['name'])) $product_info['name'] = $product_info['sub_name'];

        return $product_info;
    }

    private function getConfirmationInfo($order, $order_product)
    {
        $confirmation_info = array();
        $product = $order_product['product'];
        $supplier_order = $order_product['supplier_order'];
        $voucher_rule = $order_product['product']['voucher_rule'];
        $shipping_rule = $order_product['product']['shipping_rule'];

        if (isset($supplier_order['additional_info']) && $supplier_order['additional_info']) {
            $confirmation_info['additional_info'] = $supplier_order['additional_info'];
        }

        if (isset($supplier_order['additional_info']) && $supplier_order['payable_by']) {
            $confirmation_info['payable_by'] = 'FOR SUPPLIER USE ONLY:' . $supplier_order['payable_by'];
        } else if ($voucher_rule['need_pay_cert']) {
            if ($voucher_rule['pay_cert']) {
                $confirmation_info['payable_by'] = $voucher_rule['pay_cert'];
            } else {
                $confirmation_info['payable_by'] = $product['supplier']['payable_by'];
            }
        }


        $confirmation_info['need_signature'] = $voucher_rule['need_signature'];


        if ($shipping_rule['booking_type'] == HtProductShippingRule::BT_GTA) {
            if ($supplier_order['confirmation_ref']) {
                $cf['title'] = 'GTA Voucher No.';
                $cf['value'] = $supplier_order['confirmation_ref'];
                $confirmation_info['codes'][] = $cf;
            }
            //api ref
            if ($supplier_order['supplier_booking_ref']) {
                $api_ref['title'] = 'API Reference';
                $api_ref['value'] = '041/' . $supplier_order['supplier_booking_ref'];
                $confirmation_info['codes'][] = $api_ref;
            }
            //Supplied By
            if ($supplier_order['tour_supplier_code']) {
                $sf['title'] = 'Supplied By';
                $sf['value'] = $supplier_order['tour_supplier'] . '/' . $supplier_order['tour_supplier_code'];
                $confirmation_info['codes'][] = $sf;
            }


            if ($supplier_order['payable_by']) {
                $confirmation_info['payable_by'] = 'FOR SUPPLIER USE ONLY:' . $supplier_order['payable_by'];
            }
            $confirmation_info['need_signature'] = 0;


        } else {
            if ($shipping_rule['need_supplier_booking_ref']) {
                $confirmation_info['codes'][] = ['title' => 'Booking Ref.', 'value' => $supplier_order['supplier_booking_ref']];
            } else if ($shipping_rule['need_hitour_booking_ref']) {
                $confirmation_info['codes'][] = ['title' => 'Voucher No.', 'value' => $supplier_order['hitour_booking_ref']];
            }

            if ($shipping_rule['confirmation_type'] == HtProductShippingRule::CT_ONE) {
                $confirmation_info['codes'][] = ['title' => 'Confirmation Code', 'value' => $supplier_order['confirmation_ref']];
            } else if ($shipping_rule['confirmation_type'] == HtProductShippingRule::CT_EVERYONE) {
                $title = 'Confirmation Code';
                $value = array();

                foreach (explode(',', $supplier_order['confirmation_ref']) as $c_code) {
                    $v['code'] = $c_code;

                    if ($shipping_rule['confirmation_display_type'] == HtProductShippingRule::DT_BARCODE) {
                        $v['barcode_url'] = $this->genBarCode($order, $c_code);
                    }
                    $value[] = $v;
                }

                $confirmation_info['codes'][] = ['title' => $title, 'value' => $value];
            }
        }


        $confirmation_info['logo'] = $product['supplier']['image_url'] ? $product['supplier']['image_url'] : (Yii::app()->request->hostinfo . Yii::app()->params['WEB_PREFIX'] . Yii::app()->params['THEME_BASE_URL'] . '/images/voucher/supplier_logo_hitour.png');
        $confirmation_info['verify_qrcode_url'] = $this->genVerifyCode($order, $supplier_order['hitour_booking_ref']);


        foreach ($product['local_support'] as $aot) {
            $confirmation_info['local_support'][] = ['title' => $aot['language_name'], 'value' => $aot['office_location'] . '&nbsp;&nbsp;' . $aot['international'] . '&nbsp;&nbsp;' . $aot['office_hours'] . '&nbsp;&nbsp;' . $aot['phone']];
        }


        return $confirmation_info;
    }

    private function genBarCode($order_model, $confirm_code)
    {
        $barcode_file = $order_model['voucher_path'] . DIRECTORY_SEPARATOR . 'barcode_' . $confirm_code . '.png';
        generate_barcode($confirm_code, $barcode_file);
        return $order_model['voucher_base_url'] . '/barcode_' . $confirm_code . '.png';
    }

    private function genVerifyCode($order_model, $booking_reference)
    {
        $redeem_verify_url = Yii::app()->createAbsoluteUrl('redeem/verify', ['order_id' => $order_model['order_id'], 'booking_ref' => $booking_reference]);
        QRcode::png($redeem_verify_url, $order_model['voucher_path'] . '/redeem_qrcode.png', QR_ECLEVEL_M, 4);
        return $order_model['voucher_base_url'] . '/redeem_qrcode.png';
    }

    private function getPaxInfo($order, $order_product)
    {
        $pax_info = array();
        $passengers = $order_product['passengers'];

        $voucher_rule = $order_product['product']['voucher_rule'];
        $metas = $order_product['product']['pax_meta'];
        $pax_rule = $order_product['product']['pax_rule'];

        //total stat.
        foreach ($order_product['quantities'] as $ticket_id => $qn) {
            $ty = $order_product['ticket_types'][$ticket_id]['ticket_type'];
            $pax_info['total'][] = ['title' => $ty['en_name'] . '/' . $ty['cn_name'], 'value' => $qn];
        }


        $lead_pax_id = 0;
        $lead_ticket_id = '';
        //lead
        if ($pax_rule['need_lead'] && !empty($passengers[0])) {
            $lead_info = $this->parsePaxInfo($metas, $passengers[0], $voucher_rule['lead_ids'], 1);
            $pax_info['lead'] = $lead_info;
            $lead_pax_id = $passengers[0]['passenger_id'];
            $lead_ticket_id = $passengers[0]['ticket_id'];
        }

        //detail
        if ($pax_rule['need_passenger_num'] == 0) {
            if ($pax_rule['need_lead']) { //领队+所有人
                $everyone_set = $passengers; //array_slice($passengers,1);
            } else { //所有人
                $everyone_set = $passengers;
            }
            $detail['has_everyone'] = (count($everyone_set) > $pax_rule['need_lead']);

            $ids = array();
            foreach ($voucher_rule['id_map'] as $vi) {
                $ids = $vi;
                break;
            }
            $need_table = !($ids == [1, 2] || $ids == [1, 2, 5]);
            $detail['need_table'] = (int)$need_table;
            if ($need_table) {
                $detail['table']['title'] = array();
                $detail['table']['value'] = array();
                foreach ($ids as $id) {
                    if (empty($id)) continue;
                    if ($id == 2 || $id == 5) continue;
                    $meta = $metas[$id];
                    $detail['table']['title'][] = $meta['en_label'] . '/' . $meta['label'];
                }

                foreach ($everyone_set as $p) {
                    if ($p['passenger_id'] == $lead_pax_id)
                        continue;
                    if (empty($voucher_rule['id_map'][$p['ticket_id']]))
                        continue;
                    $ids = $voucher_rule['id_map'][$p['ticket_id']];
                    if (empty($ids))
                        continue;
                    $detail['table']['value'][] = $this->parsePaxInfo($metas, $p, $ids, 0);
                }
            } else {
                foreach ($everyone_set as $p) {
                    if (!isset($detail_value[$p['ticket_id']])) {
                        $detail_value[$p['ticket_id']] = array();
                    }
                    if ($p['passenger_id'] == $lead_pax_id)
                        continue;
                    if (empty($voucher_rule['id_map'][$p['ticket_id']])) {
                        if (empty($voucher_rule['id_map'][HtTicketType::TYPE_UNIFIED])) {
                            continue;
                        } else {
                            $ids = $voucher_rule['id_map'][HtTicketType::TYPE_UNIFIED];
                        }

                    } else {
                        $ids = $voucher_rule['id_map'][$p['ticket_id']];
                    }
                    if (empty($ids)) {
                        continue;
                    }

                    $detail_value[$p['ticket_id']][] = $this->parsePaxInfo($metas, $p, $ids, 0);
                }


                foreach ($order_product['quantities'] as $ticket_id => $qn) {
                    if ($ticket_id == $lead_ticket_id)
                        $qn = $qn - 1;
                    if ($qn > 0) {
                        $ty = $order_product['ticket_types'][$ticket_id]['ticket_type'];
                        $detail['flat'][] = ['title' => $ty['en_name'] . '/' . $ty['cn_name'], 'value' => $detail_value[$ticket_id]];
                    }
                }


            }
            $pax_info['detail'] = $detail;
        }

        return $pax_info;
    }


    private function parsePaxInfo($metas, $pax_data, $ids, $with_title)
    {
        $info_with_title = array();
        $info = array();


        foreach ($ids as $id) {
            if (empty($id)) continue;
            $meta = $metas[$id];
            if ($id == 1) {
                $title = 'Name/姓名';
                $value = $pax_data['en_name'] . '/' . $pax_data['zh_name'];


                if (in_array(5, $ids) && isset($metas[5])) {
                    $age = $pax_data[$metas[5]['storage_field']];
                    if ($age)
                        $value .= '(Age: ' . $age . ' )';
                }
                $info_with_title[] = ['title' => $title, 'value' => $value];
                $info[] = $value;
                continue;
            }
            if ($id == 2 || $id == 5) continue;


            $title = $meta['en_label'] . '/' . $meta['label'];
            $store_field = $meta['storage_field'];
            if ($store_field == 'gender') {
                $value = $pax_data[$store_field] ? 'M/男' : 'F/女';
            } else {
                $value = $pax_data[$store_field];
            }


            $info_with_title[] = ['title' => $title, 'value' => $value];
            $info[] = $value;
        }


        return $with_title ? $info_with_title : $info;
    }


    //gen barcodes

    public function generateVoucher($voucher_data, $preview = false)
    {
        $product_id = $voucher_data['product_info']['product_id'];
        $product_name = $voucher_data['product_info']['sub_name'];
        $product_name = FileUtility::replaceInvalidChars($product_name);
        $voucher_dir = $voucher_data['voucher_path'];

        if ($voucher_data['need_hitour_voucher']) {
            $content = $this->renderVoucher($voucher_data);
            if ($preview) {
                return $content;
            }

            //generate voucher
            $voucher_html = $voucher_dir . 'voucher_' . $product_id . '.html';
            file_put_contents($voucher_html, $content);
            $voucher_pdf = $voucher_dir . 'voucher_' . $product_id . '.pdf';
            system(Yii::app()->params['DIR_PDF_SCRIPT'] . "html2pdf -B 0 -T 0 $voucher_html $voucher_pdf");
            $customer_voucher = $voucher_dir . '兑换单_' . $product_name . '.pdf';
            rename($voucher_pdf, $customer_voucher);

            //update supplier order voucher ref
            HtSupplierOrder::model()->updateByPk($voucher_data['supplier_order_id'], ['voucher_ref' => json_encode(['兑换单_' . $product_name . '.pdf'])]);
        }


        return true;
    }


    //generate redeem verify qrcode

    public function renderVoucher($data)
    {
        $content = FileUtility::render(Yii::app()->basePath . '/../themes/public/views/account/voucher.php', $data);

        return $content;
    }

    public function prepareManual($order, $order_product)
    {
        if($order_product['product']['type'] == HtProduct::T_HOTEL){
            return;//酒店商品暂时不发送使用说明
        }
        $product_info = $this->getProductInfo($order_product);
        $product_id = $product_info['product_id'];
        $product_name = $product_info['sub_name'];
        $voucher_dir = $order['voucher_path'];;
        $product_manual = Yii::app()->product->getProductManual($product_id);
        foreach ($product_manual as $pvi) {
            $dest = str_replace($product_id, $product_name . '_玩途使用说明', basename($pvi));
            $dest = FileUtility::replaceInvalidChars($dest);
            copy($pvi, $voucher_dir . $dest);
        }
    }

    private function needNotifySupplier($order, $order_product)
    {
        $shipping_rule = $order_product['product']['shipping_rule'];
        $to = $shipping_rule['supplier_email'];
        $order_status_id = $order['status_id'];
        if (empty($to)) {
            Yii::log('Supplier Email Empty.Order_id = ' . $order_product['order_id'] . ',status_id=' . $order_status_id, CLogger::LEVEL_WARNING, 'biz.shipping.notify');
            return false; //没有供应商 email 地址，说明不需要通知供应商
        }

        if ($shipping_rule['booking_type'] == HtProductShippingRule::BT_HITOUR) {
            return true;
        }

        return false;
    }

    public function needBackendShipping($order_id)
    {
        $order_data = Yii::app()->order->getOrderDetailForVoucher($order_id);
        if (empty($order_data) || empty($order_data['order_products'][0])) {
            Yii::log('Not found order['.$order_id.'] for check needBackendShipping.', CLogger::LEVEL_WARNING);
            return false;
        }
        if ($order_data['order_products'][0]['product']['type'] == HtProduct::T_HOTEL_BUNDLE) {
            return true;
        }
        return false;
    }

    public function backendShipping($order_id, $comment)
    {
        if (empty($order_id) || empty($comment)) {
            Yii::log('Want to backend shipping but order_id['.$order_id.'] or comment['.$comment.'] is empty.', CLogger::LEVEL_INFO);
            return false;
        }
        $result = HtOrderAuto::model()->findByAttributes(
            array('order_id'=>$order_id, 'action'=>'shipping', 'status'=>0)
        );
        if (empty($result)) {
            $order_auto = new HtOrderAuto();
            $order_auto['order_id'] = $order_id;
            $order_auto['action'] = 'shipping';
            $order_auto['status'] = 0;
            $order_auto['comment'] = $comment;
            $isok = $order_auto->insert();
            if (!$isok) {
                Yii::log('Insert backend shipping action to order auto failed.order_id['.$order_id.']', CLogger::LEVEL_WARNING);
                return false;
            }
        }
        return true;
    }

//    private function sendVoucherByEmail($order_data)
//    {
//        $mailSetting = HtEmailSetting::model()->findByAttributes(array('setting_name' => 'customer'))->getAttributes();
//        $mail = new Mail($mailSetting);
//        $to = $order_data['order']['contacts_email'];
//        $subject = '订单已完成，订单号:' . $order_data['order']['order_id'];
//        $body = '请打印附件兑换单，并在出行时携带！';
//        $attachment = array();
//
//        $voucher_folder = $order_data['order']['voucher_path'];
//        if (is_dir($voucher_folder)) {
//            if ($dh = opendir($voucher_folder)) {
//                while (($file = readdir($dh)) !== false) {
//                    if (strpos($file, '.pdf')) {
//                        $attachment[] = $voucher_folder . $file;
//                    }
//                }
//            }
//        }
//        $mail->send($to, $subject, $body, $attachment); //测试邮箱
//    }
}