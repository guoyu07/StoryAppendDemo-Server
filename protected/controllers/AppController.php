<?php

require_once "account/AccountHelper.php";

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 1/15/15
 * Time: 11:29 AM
 */
class AppController extends Controller
{
    public function actionLogin()
    {
        $email = $this->getParam('email');
        $password = $this->getParam('password');

        $customer = HtCustomer::model()->getCustomer($email);
        if (empty($customer)) {
            EchoUtility::echoCommonFailed('用户名或密码不正确。');

            return;
        }

        $result = AccountHelper::doLogin($email, $password, false);

        if ($result) {
            $customer = Converter::convertModelToArray(HtCustomer::model()->findByAttributes(['email' => $email]));
//            unset($customer['customer_id']);
            unset($customer['salt']);
            unset($customer['password']);
            EchoUtility::echoCommonMsg(200, 'OK', $customer);
        } else {
            EchoUtility::echoCommonFailed('用户名或密码不正确。');
        }
    }

    public function actionRegister()
    {
        $email = $this->getParam('email');
        $password = $this->getParam('password');
        $confirm_password = $this->getParam('confirm');

        $customer = HtCustomer::model()->addCustomer($email, $password, $confirm_password);
        if ($customer != null && count($customer->getErrors()) == 0) {
            // send email
            $config_name = Setting::instance()->get('config_name');
            $subject = sprintf('%s - 感谢您的注册', $config_name);
            $data = array(
                'firstName'        => $customer['firstname'],
                'email'            => $customer['email'],
                'background_login' => false,
                'BASE_URL'         => Yii::app()->getBaseUrl(true),
                'LOGIN_URL'        => Yii::app()->homeUrl
            );

            Mail::sendToCustomer($email, $subject, Mail::getBody($data, HtNotifyTemplate::REGISTER_OK));

            unset($refined_customer['password']);
            unset($refined_customer['salt']);

            EchoUtility::echoCommonMsg(200, '注册成功！', $customer);
        } else {
            EchoUtility::echoMsgTF(false, '注册',
                                   empty($customer) ? array('error_msg' => '保存数据失败。') : $customer->getErrors());
        }

    }

    public function actionResetPassword()
    {
        // handle input of telephone
        $email = $this->getParam('email');

        $customer = HtCustomer::model()->findByAttributes(array('email' => $email));
        if (empty($customer)) {
            EchoUtility::echoCommonFailed('该邮箱用户不存在。');

            return;
        }

        $password = HtCustomer::model()->resetPassword($customer['customer_id']);
        if (empty($password)) {
            EchoUtility::echoCommonFailed('重置密码失败。');

            return;
        }

        $config_name = Setting::instance()->get('config_name');

        $subject = sprintf('%s - 会员新密码', $config_name);
        $data = array(
            'firstName'    => $customer['firstname'],
            'email'        => $customer['email'],
            'NEW_PASSWORD' => $password,
            'BASE_URL'     => Yii::app()->getBaseUrl(true)
        );

        $result = Mail::sendToCustomer($email, $subject, Mail::getBody($data, HtNotifyTemplate::RESET_PASSWORD));

        if ($result === true) {
            EchoUtility::echoCommonMsg(200, '邮件已发送至您的注册邮箱，请查收并按提示操作。');
        } else {
            EchoUtility::echoCommonMsg(200, '发送重置密码邮件失败。请检查您的邮箱地址，修改后再试。');
        }
    }

    public function actionWechatLogin()
    {
        $unionid = $this->getParam('code');

        $token = $this->getParam('token');
        $nonce = $this->getParam('nonce');

        if (sha1($unionid . 'Hitour' . $nonce) == $token) {

            $customer_third = HtCustomerThird::model()->getThirdAccount(HtCustomerThird::WEIXIN, $unionid);
            if (!empty($customer_third)) {
                $customer = HtCustomer::model()->findByPk($customer_third['customer_id']);
                $result = Converter::convertModelToArray($customer);
                unset($result['password']);
                unset($result['salt']);

                EchoUtility::echoCommonMsg(200, '', $customer);
            } else {
                // TODO add customer

                EchoUtility::echoCommonFailed('账号尚未创建。');

            }
        } else {
            EchoUtility::echoCommonFailed('请求参数有误。');
        }
    }

    public function actionOrderList()
    {
        $customer_id = $this->getParam('customer_id');

        $criteria = new CDbCriteria();
        $criteria->addNotInCondition('o.status_id', [HtOrderStatus::ORDER_CANCELED, 0]);
        $criteria->limit = 100; //TODO: wenzi

        $orders = HtOrder::model()->with('status', 'order_product.product.cover_image',
                                         'order_product.product_description')->findAllByAttributes(['customer_id' => $customer_id],
                                                                                                   ['order' => 'o.order_id DESC, op.order_product_id ASC'],
                                                                                                   $criteria);
        $orders = Converter::convertModelToArray($orders);

        $data = array();
        foreach ($orders as $o) {
            if (!in_array($o['order_product']['product']['type'], [1, 2, 3, 4, 5])) {
                continue;
            }

            $d['order_id'] = $o['order_id'];
            $d['date_added'] = $o['date_added'];
            $d['tour_date'] = $o['order_product']['tour_date'];
            $d['status_id'] = $o['status_id'];
            $d['status_name'] = $o['status']['cn_name_customer'];
            $d['product_name'] = $o['order_product']['product_description']['name'];
            $d['product_url'] = $o['order_product']['product']['link_url'];
            $d['detail_url'] = $o['detail_url'];
            $d['payment_url'] = $o['payment_url'];
            $d['cancel_url'] = $o['cancel_url'];
            $d['return_url'] = $o['return_url'];
            $d['download_voucher_url'] = $o['download_voucher_url'];
            //'COUPON' product CAN'T download voucher
            if ($o['order_product']['product']['type'] == HtProduct::T_COUPON) {
                $d['download_voucher_url'] = '';
            }

            $d['product_cover'] = $this->getProductCover($o['order_product']['product']['cover_image']);

            if (in_array($o['status_id'], [
                HtOrderStatus::ORDER_BOOKING_FAILED,
                HtOrderStatus::ORDER_PAYMENT_SUCCESS,
                HtOrderStatus::ORDER_PAID_EXPIRED
            ])
            ) {
                $d['allow_return'] = 1;
            } else {
                if ($o['status_id'] == HtOrderStatus::ORDER_SHIPPED && $o['order_product']['return_expire_date'] >= date('Y-m-d')) {
                    $d['allow_return'] = 1;
                } else {
                    $d['allow_return'] = 0;
                }
            }

            $data[] = $d;
        }

        function cmp($a, $b)
        {
            $priority_a = 0;
            if (in_array($a['status_id'],
                         [HtOrderStatus::ORDER_CONFIRMED, HtOrderStatus::ORDER_PAYMENT_FAILED, HtOrderStatus::ORDER_REFUND_SUCCESS,
                             HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION, HtOrderStatus::ORDER_REFUND_PROCESSING, HtOrderStatus::ORDER_RETURN_CONFIRMED])) {
                $priority_a = 1;
            } elseif (in_array($a['status_id'], [HtOrderStatus::ORDER_PAYMENT_SUCCESS, HtOrderStatus::ORDER_SHIPPED,
                HtOrderStatus::ORDER_WAIT_CONFIRMATION, HtOrderStatus::ORDER_STOCK_FAILED,
                HtOrderStatus::ORDER_SHIPPING_FAILED, HtOrderStatus::ORDER_TO_DELIVERY])) {
                $priority_a = 2;
            }

            $priority_b = 0;
            if (in_array($b['status_id'],
                         [HtOrderStatus::ORDER_CONFIRMED, HtOrderStatus::ORDER_PAYMENT_FAILED, HtOrderStatus::ORDER_REFUND_SUCCESS,
                             HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION, HtOrderStatus::ORDER_REFUND_PROCESSING, HtOrderStatus::ORDER_RETURN_CONFIRMED])) {
                $priority_b = 1;
            } elseif (in_array($b['status_id'], [HtOrderStatus::ORDER_PAYMENT_SUCCESS, HtOrderStatus::ORDER_SHIPPED,
                HtOrderStatus::ORDER_WAIT_CONFIRMATION, HtOrderStatus::ORDER_STOCK_FAILED,
                HtOrderStatus::ORDER_SHIPPING_FAILED, HtOrderStatus::ORDER_TO_DELIVERY])) {
                $priority_b = 2;
            }

            if ($priority_a > $priority_b) {
                return -1;
            }
            if ($priority_b > $priority_a) {
                return 1;
            }

            return $a['date_added'] >= $b['date_added'] ? -1 : 1;
        }

        usort($data, "cmp");

        EchoUtility::echoJson($data);
    }

    private function getProductCover($productImage)
    {
        if (!empty($productImage['image_url'])) {
            return $productImage['image_url'];
        } elseif (!empty($productImage)) {
            $landinfo_id = $productImage['landinfo_id'];
            if ($landinfo_id > 0) {
                $landinfo = Landinfo::model()->findByPk($landinfo_id);

                return $landinfo['image_url'];
            }
        }

        return '';
    }

    public function actionProductData()
    {
        $spc = $this->getProductSpc('spc', '');
        $product_id = $this->getProductId();

        $base_data = Yii::app()->product->getBaseData($product_id);
        $land_data = Yii::app()->product->getLandData($product_id);
        $data = array_merge($base_data, $land_data);
        $data['show_prices'] = HtProductPricePlan::model()->getShowPrices($product_id, $spc);

        //Introduction
        $introduction = Yii::app()->product->getProductIntroduction($product_id);
        if (!empty($introduction)) {
            $introduction['please_read']['rules'] = $data['rules'];
            $introduction['redeem_usage']['pickup_landinfos'] = [];
            if (!empty($data['pick_landinfo_groups'][0]['landinfos'])) {
                $introduction['redeem_usage']['pick_landinfo_groups'] = $data['pick_landinfo_groups'];
            }

            $data['introduction'] = $introduction;
        }

        if (!empty($data['description']['service_include'])) {
            $matches = [];

            preg_match_all('/<li[^>]*?>(.*?)<\/li>/s', $data['description']['service_include'], $matches);

            $data['description']['service_include'] = '<p>' . implode('</p><p>', $matches[1]) . '</p>';
        }

        //Activity
        $activity = Yii::app()->activity->getActivityInfo($product_id);
        if ($activity && $data['available'] == 1) {
            $data['available'] = $activity['status'] == HtActivity::AS_ONGOING ? 1 : 0;
            $data['buy_label'] = $activity['buy_label'];
        }
        $data['activity_info'] = $activity;
        $activity_id = isset($activity['activity_id']) ? $activity['activity_id'] : '0';
        $ad = Yii::app()->activity->getAdBanner($activity_id, $data, 0);
        $data['ad_info'] = $ad;

        //Comments
        $data['comments']['state'] = HtProductComment::getStatInfo($product_id);
        $data['comments']['items'] = HtProductComment::getProcessedComments($product_id, 0, 3);

        $data['rule_label'] = array(
            'redeem_desc'   => '兑换时间',
            'return_desc'   => '退换限制',
            'sale_desc'     => '购买时间',
            'shipping_desc' => '发货限制'
        );

        $data['links'] = [
            'comments' => $this->createUrl('appView/productComment', ['product_id' => $product_id]),
            'detail'   => $this->createUrl('appView/productDetail', ['product_id' => $product_id])
        ];

        $data['has_detail'] = HtProductTourPlan::model()->hasTourPlan($product_id);
        $data['product_location'] = Converter::convertModelToArray(HtProductSightseeing::model()->findAllByAttributes(['product_id' => $product_id]));
        $data['sales_volume'] = HtOrder::getSalesVolume($product_id);

        $current_expert = HtProductExpertRef::model()->find('product_id = ' . $product_id);
        if (!empty($current_expert)) {
            $expert = HtTravelExpert::model()->find('id = ' . $current_expert['expert_id']);
            $data['expert_share']['avatar'] = $expert['avatar'];
            $data['expert_share']['name'] = $expert['name'];
            $data['expert_share']['brief'] = $expert['brief'];
        }

        EchoUtility::echoJson($data);
    }

    public function actionOrderDetail()
    {
        $order_id = $this->getParam('order_id');
        $customer_id = $this->getParam('customer_id');

        //order
        $order = HtOrder::model()->with('status')->findByPk($order_id, 'customer_id=:cid', [':cid' => $customer_id]);
        if (!$order) {
            EchoUtility::echoCommonFailed('订单不存在!订单号:' . $order_id);

            return;
        }

        $order_data_custom = array(
            'order' => array(
                'order_id'         => $order['order_id'],
                'order_date'       => date('Y-m-d', strtotime($order['date_added'])),
                'status_id'        => $order['status_id'],
                'status_name'      => $order['status']['cn_name_customer'],
                'status_shortname' => $order['status_shortname'],
                'payment_url'      => $order['payment_url'],
                'cancel_url'       => $order['cancel_url'],
                'return_url'       => $order['return_url'],
                //'download_voucher_url' => '',
                //'send_voucher_url'     => $order['send_voucher_url'],
                'voucher_base_url' => $order['voucher_base_url'],
            ),
        );

        //order_product
        $order_products = HtOrderProduct::model()->with(['special', 'departure', 'product.description'])->findAllByAttributes(['order_id' => $order_id]);

        if (count($order_products) > 1) {
            $main_order_product = $order_products[0];
            $main_product_id = $main_order_product['product_id'];
            $bundle = HtProductBundle::model()->findAllByAttributes(['product_id' => $main_product_id]);
            if (!empty($bundle)) {
                $bundle_ids = ModelHelper::getList($bundle, 'bundle_id');

                $grouped_product = ['group_0' => [], 'group_1' => [], 'group_2' => [], 'group_3' => []];
                foreach ($order_products as $order_product) {
                    if ($order_product['product_id'] == $main_product_id) {
                        $product = $this->getOrderProductInfo($order, $order_product);
                    } else {
                        $product = $this->getOrderProductInfo($order, $order_product, $bundle_ids);
                        if (empty($bundle_ids)) {
                            $product['group_type'] = 3;
                        }
                    }

                    $grouped_product['group_' . $product['group_type']][] = $product;
                }
                // hacked to solve that the total of product in group_1 was recorded in product of group_0
                if (count($grouped_product['group_1']) > 0 && count($grouped_product['group_0']) > 0) {
                    $grouped_product['group_1'][0]['total'] = $grouped_product['group_0'][0]['total'];
                }

                $order_data_custom['product'] = $grouped_product;
            } else {
                $order_data_custom['product'] = $this->getOrderProductInfo($order, $main_order_product);
                foreach ($order_products as $order_product) {
                    if ($order_product['product_id'] == $main_product_id) {
                        continue;
                    }

                    $order_data_custom['sub_products'][] = $this->getOrderProductInfo($order, $order_product);
                }
            }
        } else {
            $main_order_product = $order_products[0];

            $order_data_custom['product'] = $this->getOrderProductInfo($order, $main_order_product);
        }

        $order_data_custom['order']['download_voucher_url'] = $main_order_product['product']['type'] == HtProduct::T_COUPON ? '' : $order['download_voucher_url'];
        $order_data_custom['product_type'] = $main_order_product['product']['type'];
        $order_data_custom['is_combo'] = $main_order_product['product']['is_combo'];

        //insurance_codes
        $order_data_custom['insurance_codes'] = Converter::convertModelToArray(HtInsuranceCode::model()->with('company')->findAllByAttributes(['order_id' => $order_id]));

        //gift coupon
        $order_data['gift_coupon'] = Converter::convertModelToArray(HtOrderGiftCoupon::model()->with('coupon')->findAllByAttributes(['order_id' => $order_id]));

        EchoUtility::echoJson($order_data_custom);
    }

    private function getOrderProductInfo($order, $order_product, $bundle_ids = [])
    {
        $group_type = 0; // 1：N选1；2：必选；3：可选; 0: not in group
        if (!empty($bundle_ids)) {
            $bundle_info = HtProductBundleItem::model()->getBundleInfo($order_product['product_id'], $bundle_ids);
            $group_type = $bundle_info['bundle']['group_type'];
        }

        $product_id = $order_product['product_id'];
        //Product Options
        $product = array(
            'product_id' => $product_id,
            'name'       => $order_product['product']['description']['name'],
            'is_combo'   => $order_product['product']['is_combo'],
            'is_main'    => ($order_product['bundle_product_id'] == 0 ? 1 : 0),
            'total'      => $order_product['total'],
            'group_type' => $group_type,
            'info'       => array(),
            'date'       => array()
        );

        if (!empty($order_product['special_code'])) {
            $product['special_info'] = HtProductSpecialCombo::getSpecialDetail($product_id,
                                                                               $order_product['special_code']);
        }
        if (!empty($order_product['departure']) && strlen($order_product['departure']['departure_point']) > 0) {
            //$product['info']['departure'] = [$order_product['product']['description']['departure_title'] => $order_product['departure']['departure_point'] . ' / ' . $order_product['departure_time']];
            $product['info'][$order_product['product']['description']['departure_title']] = $order_product['departure']['departure_point'] . ' / ' . $order_product['departure_time'];
        }

        $date_rule = HtProductDateRule::model()->findByPk($product_id);
        if ($date_rule['need_tour_date'] == '1') {
            $product['date'][$order_product['product']['description']['tour_date_title']] = $order_product['tour_date'];
        } else {
            if ($order['status_id'] == '3' || $order['status_id'] == '24') {
                $product['date']['兑换截止日期'] = $order_product['redeem_expire_date'];
            }
        }

        $product_introduction = HtProductIntroduction::model()->findByPk($product_id);
        if (!empty($product_introduction) && $product_introduction['status'] == 1) {
            $product['usage'] = Converter::parseMdHtml($product_introduction['usage']);
        } else {
            $product['usage'] = Converter::parseMdHtml($order_product['product']['description']['how_it_works']);
        }
        $product_return_rule = HtProductReturnRule::model()->findByPk($product_id);
        $product['return_info'] = $product_return_rule->getRuleDesc();

        $product['passenger'] = $this->getOrderPassenger($order['order_id'], $order_product['order_product_id'],
                                                         $product_id);
        $pick_ticket_info = Yii::app()->product->getPickticketLandinfoData($product_id);
        $product = array_merge($product, $pick_ticket_info);

        $product['local_supports'] = HtSupplierLocalSupport::model()->getProductLocalSupport($product_id);
        $product['shipping_rule'] = HtProductShippingRule::model()->findByPk($product_id);
        $product['supplier_order'] = HtSupplierOrder::model()->findByPk($order_product['supplier_order_id']) ;

        return $product;
    }

    private function getOrderPassenger($order_id, $order_product_id, $product_id)
    {
        $passenger = array('summary' => '', 'lead' => [], 'everyone' => [], 'all' => []);

        $quantities = HtOrderProductPrice::model()->calcRealQuantities($order_product_id, $product_id);
        $ticket_types = HtProductTicketRule::model()->getTicketRuleMapForOrder($product_id);

        //passengers & meta &pax rule
        $passengers = HtOrderPassenger::model()->findAllByOrder($order_id, $order_product_id);

        $passenger_rule = HtProductPassengerRule::model()->getPassengerRule($product_id);

        // refactor code to extract method for repeated code
        //Passenger Summary
        foreach ($quantities as $key => $val) {
            $passenger['summary'] .= $ticket_types[$key]['ticket_type']['cn_name'] . ' x ' . $val . '  ';
        }
        //Lead Passenger
        if ($passenger_rule['pax_rule']['need_lead'] == '1') {
            $passenger['has_lead'] = true;
            $lead_fields = $passenger_rule['pax_rule']['lead_ids'];
            if (isset($passengers[0])) {
                $lead_info = $passengers[0];
                $lead_result = $this->fillFieldLabel($passenger_rule, $lead_fields, $lead_info);
                $passenger['lead'] = $lead_result;
            }
        }
        //Everyone Else
        if ($passenger_rule['pax_rule']['need_passenger_num'] == '0') {
            $order_data_custom['passenger']['has_everyone'] = true;
            $everyone_set = $passenger_rule['pax_rule']['need_lead'] == '1' ? array_slice($passengers,
                                                                                          1) : $passengers;
            $everyone_result = array();
            foreach ($everyone_set as $key => $person) {
                $person_fields = $passenger_rule['pax_rule']['id_map'][$person['ticket_id']];
                $person_result = $this->fillFieldLabel($passenger_rule, $person_fields, $person);
                $everyone_result[$key] = $person_result;
            }
            $passenger['everyone'] = $everyone_result;
        }

        for ($i = 0, $len = count($passengers); $i < $len; $i++) {
            array_push($passenger['all'],
                       $this->getFields($passengers[$i], $passengers[$i]['ticket_id'], $ticket_types));
        }

        return $passenger;
    }

    private function getFields($one_pax, $ticket_type, $ticket_types_info)
    {
        $tmp = $one_pax['zh_name'];

        if (!($ticket_type == 1 || $ticket_type == 99)) {
            $tmp .= ' (' . $ticket_types_info[$ticket_type]['ticket_type']['description'] . ') ';
        }

        return $tmp;
    }

    private function fillFieldLabel($passenger_rule, $person_fields, $lead_info)
    {
        $lead_result = [];
        foreach ($person_fields as $field) {
            if ($passenger_rule['pax_meta'][$field]['input_type'] == 'enum') {
                $field_tmp = json_decode($passenger_rule['pax_meta'][$field]['range']);
                foreach ($field_tmp as $sub_field) {
                    if ($sub_field->value == $lead_info[$passenger_rule['pax_meta'][$field]['storage_field']]) {
                        $lead_result[$passenger_rule['pax_meta'][$field]['label']] = $sub_field->title;
                    }
                }
            } else {
                $lead_result[$passenger_rule['pax_meta'][$field]['label']] = empty($lead_info[$passenger_rule['pax_meta'][$field]['storage_field']]) ? '' : $lead_info[$passenger_rule['pax_meta'][$field]['storage_field']];
            }
        }

        return $lead_result;
    }

    public function actionVoucherBrief()
    {
        $order_id = $this->getParam('order_id');
        $customer_id = $this->getParam('customer_id');

        $order = HtOrder::model()->findByAttributes(['order_id' => $order_id, 'customer_id' => $customer_id]);
        if (empty($order)) {
            EchoUtility::echoCommonFailed('Invalid order_id/customer_id');

            return;
        }

        $order_products = HtOrderProduct::model()->with('supplier_order')->findAllByAttributes(['order_id' => $order_id]);
        $supplier_orders = [];
        $shipping_rules = [];
        foreach ($order_products as $order_product) {
            $shipping_rules[] = HtProductShippingRule::model()->findByPk($order_product['product_id']);
            $supplier_orders[] = $order_product['supplier_order'];
        }

        EchoUtility::echoCommonMsg(200, 'Ok',
                                   ['shipping_rules' => $shipping_rules, 'supplier_orders' => $supplier_orders]);
    }

    public function actionVoucherDetail()
    {
        $order_id = $this->getParam('order_id');
        $customer_id = $this->getParam('customer_id');

        if (empty($order_id) || empty($customer_id)) {
            EchoUtility::echoCommonFailed('Invalid parameter');

            return;
        }

        $voucher_data = Yii::app()->order->getOrderDetailForVoucher($order_id);

        EchoUtility::echoCommonMsg(200, 'Ok', $voucher_data);
    }

    public function actionCities()
    {
        $cities = [];
        $city_codes = ['SIN', 'HKT'];
        foreach ($city_codes as $city_code) {
            $cities[] = Converter::convertModelToArray(HtCity::model()->with('city_image')->findByAttributes(['city_code' => $city_code]));
        }
        foreach ($cities as $key => $city) {
            $cities[$key] = array();
            $cities[$key]['city_code'] = $city['city_code'];
            $cities[$key]['city_name'] = $city['cn_name'];
            $cities[$key]['en_name'] = $city['en_name'];
            $cities[$key]['cover_url'] = $city['city_image']['app_image_url'];
            $cities[$key]['strip_url'] = $city['city_image']['app_strip_image_url'];
        }

        EchoUtility::echoCommonMsg(200, 'OK', $cities);
    }

    public function actionProductGroups()
    {
        $city_code = $this->getParam('city_code');

        $groups = Converter::convertModelToArray(HtProductGroup::model()->with('product_group_ref')->findAllByAttributes(['city_code' => $city_code, 'type' => 8, 'status' => 2]));
        foreach ($groups as &$pg) {
            foreach ($pg['product_group_ref'] as $pgr) {
                $product = Yii::app()->product->getProductSummaryForApp($pgr['product_id']);
                if (!empty($product)) {
                    $pg['products'][] = $product;
                }
            }
            unset($pg['product_group_ref']);
        }

        EchoUtility::echoCommonMsg(200, 'OK', $groups);
    }

    public function actionMyFavoriteProducts()
    {
        $customer_id = $this->getParam('customer_id');
        $city_code = $this->getParam('city_code');

        $criteria = new CDbCriteria();
        if (!empty($city_code)) {
            $criteria->addCondition("p.city_code = '$city_code'");
        }
        $criteria->order = 'favorite_product.date_added DESC';

        $favorites = Converter::convertModelToArray(HtCustomerFavoriteProduct::model()->with('product')->findAllByAttributes(['customer_id' => $customer_id],
                                                                                                                             $criteria));
        $products = [];
        foreach ($favorites as $f) {
            $product = Yii::app()->product->getProductSummaryForApp($f['product_id']);
            if (!empty($product)) {
                array_push($products, $product);
            }
        }

        EchoUtility::echoCommonMsg(200, 'OK', $products);
    }

    public function actionAccountInfo()
    {
        EchoUtility::echoCommonMsg(200, 'OK', []);
    }

    public function actionAddOrder()
    {
        $input = $this->getActionParams();
        Yii::log(print_r($input, 1), CLogger::LEVEL_INFO, 'app.addOrder');
        //$input['payment_method'] = 'alipay_wap';
        $result = Yii::app()->order->addOrderWithoutSession($input);
        echo json_encode($result, 271);
//        EchoUtility::echoCommonMsg(200, 'OK', $result);
    }

    public function actionProductDetail()
    {
        EchoUtility::echoCommonMsg(200, 'OK', []);
    }

    public function actionUpdateAccountInfo()
    {
        EchoUtility::echoCommonMsg(200, 'OK', []);
    }

    public function actionProductSaleData()
    {
        $product_id = $this->getParam('product_id');
        $data = Yii::app()->product->getSaleData($product_id);
        if ($data['special_info'] == null) {
            $data['special_info'] = [];
        }
        $passenger_rule = HtProductPassengerRule::model()->getPassengerRule($product_id);
        $data['pax_rule'] = $passenger_rule['pax_rule'];
        $data['pax_meta'] = $passenger_rule['pax_meta'];
        $data['rule_desc'] = Yii::app()->product->getRuleDesc($product_id);

        foreach ($data['ticket_types'] as $k => &$ty) {
            if (empty($ty['cn_name'])) {
                $ty['cn_name'] = $ty['ticket_type']['cn_name'];
            }
            if (empty($ty['en_name'])) {
                $ty['en_name'] = $ty['ticket_type']['en_name'];
            }
            if (empty($ty['description'])) {
                $ty['description'] = $ty['ticket_type']['description'];
            }
            unset($ty['ticket_type']);
        }

        $product_desc = HtProductDescription::model()->findByAttributes(['product_id' => $product_id, 'language_id' => 2],
                                                                        ['select' => 'tour_date_title,special_title,departure_title']);
//        $data['product_desc'] = Converter::convertModelToArray($product_desc);
        $data['product_desc'] = ['tour_date_title' => $product_desc['tour_date_title'], 'special_title' => $product_desc['special_title'], 'departure_title' => $product_desc['departure_title']];


//        $data['date_rule'] = Yii::app()->product->getDateRule($product_id);
//        $data['special_codes'] = Yii::app()->product->getSpecialCodes($product_id);
//        $data['departure_rule'] = Yii::app()->product->getDepartureRule($product_id);
//        $pax_rule = HtProductPassengerRule::model()->getPassengerRule($product_id);
//        $data['pax_rule'] = $pax_rule['pax_rule'];
//        $data['pax_meta'] = $pax_rule['pax_meta'];
//        $product['price_plan'] = HtProductPricePlan::model()->getPricePlanWithMap($product_id);

        EchoUtility::echoCommonMsg(200, 'OK', $data);
    }

    public function actionVerificationCodeBySMS($phone_no, $verify_code = '')
    {
        $account = [];
        if (strlen($phone_no) == 11 && substr($phone_no, 0, 1) == '1') {
            $sms_service = new SmsVerify();
            if ($verify_code) {
                $result = $sms_service->verify($phone_no, $verify_code);
                if ($result['code'] == 200) {
                    $account = $this->registerByPhone($phone_no);
                }
            } else {
                $result = $sms_service->sendVerificationCode($phone_no);
            }

            EchoUtility::echoCommonMsg($result['code'], $result['msg'], $account);
        } else {
            EchoUtility::echoCommonFailed('手机号码有误。请检查修改后重试。');
        }
    }

    private function registerByPhone($phone_no)
    {
        $phone_no = $this->getParam('phone_no');

        $result = AccountHelper::addPhoneCustomer($phone_no);

        if ($result['customer'] != null && count($result['customer']->getErrors()) == 0) {
            $refined_customer = $result['customer'];

            unset($refined_customer['password']);
            unset($refined_customer['salt']);

            return $refined_customer;
        } else {
            return [];
        }
    }

    public function actionCancelOrder()
    {
        $order_id = $this->getParam('order_id');
        $result = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_CANCELED);
        if ($result) {
            HtCouponHistory::model()->deleteAllByAttributes(['order_id' => $order_id]);
        }
        EchoUtility::echoCommonMsg($result ? 200 : 400, $result ? '订单已取消。' : '订单取消失败。');
    }

    public function actionReturnOrder()
    {
        $result = array('code' => 200, 'msg' => '退订申请已提交,我们会尽快处理!');
        $order_id = $this->getParam('order_id');

        if (empty($order_id)) {
            $result = array('code' => 400, 'msg' => '退订申请已发送成功,我们将及时联系您处理相关事宜！');
        } else {
            $result = Yii::app()->returning->returnRequest($order_id, 1);
            if ($result['code'] == 200) {
                $result = Yii::app()->returning->returnConfirm($order_id);
                if ($result['code'] == 200) {
                    $result = Yii::app()->returning->refundOrder($order_id);
                }
            }
        }
        EchoUtility::echoCommonMsg($result['code'], '退订申请已发送成功，我们将及时联系您处理相关事宜！');
    }

    private function getProductId()
    {
        return Yii::app()->request->getParam('product_id');
    }

    private function getProductSpc()
    {
        return Yii::app()->request->getParam('spc');
    }

}
