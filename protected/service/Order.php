<?php

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/22/14
 * Time: 16:30 PM
 */
class Order extends CComponent
{
    private $order = array();

    public function init()
    {
        require_once('utility.php');

        return true;
    }

    public function addOrderWithoutSession($order_data)
    {
        $result = $this->addOrder($order_data, false);
        return $result;
    }

    public function addOrder($order_data, $use_session = true)
    {
        $result = array('code' => 200, 'msg' => 'OK');

        if(!$this->checkOrderData($order_data, $result, $use_session)){
            $result['code'] = 400;
        }
        $order_id = 0;
        if ($result['code'] == 200) {
            try {
                $order_id = $this->insertOrder($order_data, $result);
                $this->insertOrderPax($order_data);
                $this->insertOrderProduct($order_id, $order_data);
                $this->insertCoupon($order_id, $order_data);
            } catch (Exception $e) {
                Yii::log($e, CLogger::LEVEL_ERROR, 'biz.order.addOrder');
                $result['msg'] = '';
                $result['code'] = 400;
            }
        }

        if ($result['code'] != 200 || 0 == $order_id) {
            Yii::log('SessionID=[' . Yii::app()->session->sessionID . '].' . 'Add order failed,' . $result['msg'],
                     CLogger::LEVEL_ERROR, 'hitour.service.order');
            $result['msg'] = '订单提交失败！' . $result['msg'];
        } else {
            $result['data']['order_id'] = $order_id;
            $result['data']['payment_url'] = Yii::app()->createAbsoluteUrl('PayGate/Pay',
                                                                           array('order_id' => $order_id));
            $result['data']['sub_total'] = $order_data['sub_total'];
            $result['data']['total'] = $order_data['payment_total'];
            if (isset($order_data['coupon_total'])) {
                $result['data']['coupon_total'] = $order_data['coupon_total'];
                $result['data']['coupon_title'] = $order_data['coupon_title'];
            }


            if ($result['data']['total'] == 0) {
                $this->savePaymentHistory($order_id, $order_data);

                Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_PAYMENT_SUCCESS);
                $payment_methods = PayUtility::paymentMethods();
                if (isset($payment_methods[$order_data['payment_method']]) && $payment_methods[$order_data['payment_method']]['mobile']) {
                    $result['data']['success_url'] = Yii::app()->createAbsoluteUrl('mobile/result',
                                                                                   array('order_id' => $order_id));
                } else {
                    $result['data']['success_url'] = Yii::app()->createAbsoluteUrl('checkout/success',
                                                                                   array('order_id' => $order_id));
                }
            }
        }

        if ($result['code'] == 200) {
            //TODO:clear cart
            Yii::app()->cart->clearCoupon('');
        }

        return $result;
    }

    private function checkOrderData(&$order_data, &$result, $use_sessoin = true)
    {
        $cart_data = array();
        if ($use_sessoin) {
            $cart_data = Yii::app()->cart->getCartForOrder();
        }else{
            $cart_data = Yii::app()->product->getProductsForAddOrder(array($order_data));
        }

        $products = isset ($order_data['products']) ? $order_data['products'] : array();
        $order_data['products'] = array_merge($cart_data, $products);

        if (!$this->checkProducts($order_data, $result)) {
            return false;
        }

        if (!$this->checkPassengers($order_data, $result)) {
            return false;
        }

        if (!$this->checkPrices($order_data, $result)) {
            return false;
        }

        if (!$this->checkSale($order_data, $result)) {
            return false;
        }

        return true;
    }

    private function checkProducts(&$order_data, &$result)
    {
        foreach ($order_data['products'] as &$p) {
            $product_id = $p['product_id'];
            $prod = HtProduct::model()->with('description')->findByPk($product_id);
            $product_name = $prod['description']['name'];
            $p['name'] = $product_name;
            if (empty($p['bundle_product_id'])) $p['bundle_product_id'] = 0;

            //tour_date
            if (empty($p['tour_date'])) $p['tour_date'] = '';
            $date_rule = HtProductDateRule::model()->findByPk($product_id);
            if ($date_rule['need_tour_date']) {
                if (empty($p['tour_date'])) {
                    $result['msg'] = $product_name . $prod['description']['tour_date_title'] . ' 不能为空！';

                    return false;
                } else if ($p ['tour_date'] < date('Y-m-d')) {
                    $result['msg'] = $product_name . $prod['description']['tour_date_title'] . ' 必须在今天之后!';

                    return false;
                }
            }

            //special_code
//            if (empty($p['special_code'])) $p['special_code'] = '';
//            $need_special = HtProductSpecialCode::model()->needSpecialCode($product_id);
//            if ($need_special && empty($p['special_code'])) {
//                $result['msg'] = $product_name . $prod['description']['special_title'] . ' 不能为空！';
//
//                return false;
//            }

            //departure
            if (empty($p['departure_code'])) $p['departure_code'] = '';
            if (empty($p['departure_time'])) $p['departure_time'] = '';
            $need_departure = HtProductDeparturePlan::model()->needDeparture($product_id);
            if ($need_departure && empty($p['departure_code'])) {
                $result['msg'] = $product_name . $prod['description']['departure_title'] . ' 不能为空！';

                return false;
            }

            //bundle info
            $bundle_info = array();
            if (!empty($p['bundle_product_id'])) {
                $bundle_info = ['binding_product_id' => $p['product_id'], 'group_type' => HtProductBundle::GT_SELECTION];
                if (!isset($bundles[$p['bundle_product_id']])) {
                    $bundles[$p['bundle_product_id']] = Converter::convertModelToArray(HtProductBundle::model()->with('items')->findAllByAttributes(['product_id' => $p['bundle_product_id']]));
                }
                foreach ($bundles[$p['bundle_product_id']] as $group) {
                    foreach ($group['items'] as $item) {
                        if ($item['binding_product_id'] == $p['product_id']) {
                            $bundle_info = $item;
                            $bundle_info['group_type'] = $group['group_type'];
                        }
                    }
                }
            }
            $p['bundle_info'] = $bundle_info;
        }

        $order_data['activity_id'] = empty($order_data['products'][0]['activity_id']) ? 0 : $order_data['products'][0]['activity_id'];
        return true;
    }

    private function checkPassengers(&$order_data, &$result)
    {
        $products = $order_data['products'];
        $passengers = isset ($order_data['passengers']) ? $order_data['passengers'] : array();

        if (empty($order_data['passengers'])) {
            $result['msg'] = '没有填写出行人信息！';

            return false;
        }else{
            foreach($passengers as &$p){
                if (!isset($p['age']) && isset($p['birth_date'])) {
                    $p['age'] = getAge($p['birth_date']);
                }
            }
        }

        //主商品
        $main_product = array_shift($products);
        //所有 Passengers 默认都是主商品的 Passengers（单品时主商品就是自身）
        $main_product_passengers = array();
        foreach ($passengers as $i => $pax) {
            if (!isset($main_product_passengers[$pax['ticket_id']])) {
                $main_product_passengers[$pax['ticket_id']] = array();
            }
            $main_product_passengers[$pax['ticket_id']][] = $i;
        }
        $main_product['passengers'] = $main_product_passengers;

        //“主”酒店
        $main_product_special_mapping = 0;
        $p_model = HtProduct::model()->findByPk($main_product['product_id']);
        if ($p_model['type'] == HtProduct::T_HOTEL_BUNDLE) { //酒店套餐补充酒店商品
            if (!empty($main_product['special_code'])) {
                //酒店套餐商品应该有 special code，指向某个具体的酒店
//                $special_model = HtProductSpecialCode::model()->findByPk(['product_id' => $main_product['product_id'], 'special_code' => $main_product['special_code']]);
                $special_info = HtProductSpecialCombo::getSpecialDetail($main_product['product_id'],$main_product['special_code']);
                $main_product_special_mapping = $special_info[0]['items'][0]['mapping_product_id'];
            }
        }

        //如果是套餐商品，后续
        foreach ($products as &$p) {
            $bundle_product_id = $p['bundle_product_id'];
            if ($bundle_product_id > 0 && $bundle_product_id != $main_product['product_id']) {
                $result['msg'] = '主套餐商品未选购！';

                return false;
            }

            //对于独立商品和可选商品，必须有 quantities 和 passenger
            if (empty($bundle_product_id) || $p['bundle_info']['group_type'] == HtProductBundle::GT_OPTIONAL) {
                if (empty($p['quantities']) || empty($p['passengers'])) {
                    $result['msg'] = $p['name'] . ' 未选择出行人！';

                    return false;
                }
            } else if ($p['bundle_info']['group_type'] == HtProductBundle::GT_SELECTION) { //N 选一商品，和主商品保持一致
                if ($p['product_id'] != $main_product_special_mapping) {
                    Yii::log('N选1商品和主商品的Special不一致，主商品Special对应的id:' . $main_product_special_mapping . ',' . $p['product_id'],
                             CLogger::LEVEL_WARNING, 'biz.order.addOrder');
                }
                $p['passengers'] = $main_product['passengers'];
            } else {//对于必选商品，需要根据“套餐规则”来“搭配”出行人
                $main_product_qty = array_sum($main_product['quantities']);

                $qty = $this->getQuantity($main_product_qty, $main_product['pax_num'], $p['bundle_info']['count_type'], $p['bundle_info']['count']);

                $sub_passengers = array();
                $sub_quantities = array();

                $ticket_rules = Converter::convertModelToArray(HtProductTicketRule::model()->findAllByAttributes(['product_id' => $p['product_id']]));
                for ($i = 0; $i < $qty; $i++) {
                    $pax = $passengers[$i];
                    $age = isset($pax['age']) ? $pax['age'] : 0;
                    if (empty($age)) {
                        if (isset($pax['birth_date'])) {
                            $age = getAge($pax['birth_date']);
                        }
                    }

                    foreach ($ticket_rules as $tr) {
                        $is_eligible = false;
                        if (0 == $age || empty($tr['age_range'])) {
                            $is_eligible = true;
                        } else {
                            $ranges = explode('-', $tr['age_range']);
                            if ($age >= $ranges[0] && $age <= $ranges[1]) {
                                $is_eligible = true;
                            }
                        }


                        if ($is_eligible) {
                            if (!isset($sub_quantities[$tr['ticket_id']])) {
                                $sub_quantities[$tr['ticket_id']] = 1;
                            } else {
                                $sub_quantities[$tr['ticket_id']] += 1;
                            }

                            if (!isset($sub_passengers[$tr['ticket_id']])) {
                                $sub_passengers[$tr['ticket_id']] = array();
                            }
                            $sub_passengers[$tr['ticket_id']][] = $i;
                            break;
                        }
                    }
                }

                $p['passengers'] = $sub_passengers;
                $p['quantities'] = $sub_quantities;
            }
        }

        //主商品重新加入到数组最开始
        array_unshift($products, $main_product);
        $order_data['products'] = $products;

        return true;
    }

    private function getQuantity($main_product_quantity, $pax_num, $count_type, $count)
    {
        switch ($count_type) {
            case 1:
                $qty = $main_product_quantity;
                break;
            case 2:
                $qty = 1;
                break;
            case 3:
                $qty = max($main_product_quantity, $pax_num);
                break;
            default:
                $qty = 1;
                break;
        }
        $qty *= $count;

        return $qty;
    }

    public function checkPrices(&$order_data, &$result)
    {
        $order_sub_total = 0;
        $order_cost_total = 0;
        foreach ($order_data['products'] as &$p) {
            //prices
            $sub_total = 0;
            $cost_total = 0;
            $prices = array();
            if (empty($p['bundle_info']['group_type']) || $p['bundle_info']['group_type'] == HtProductBundle::GT_OPTIONAL) {
                $p['bundle_product_id'] = 0;
                $tour_date = !empty($p['tour_date']) ? $p['tour_date'] : date('Y-m-d');
                $sale_date = date('Y-m-d');
                $price_plan = HtProductPricePlan::model()->getPricePlan($p['product_id'], $tour_date, $sale_date);
                $price_plan = $price_plan[0];
                $special_code = $p['special_code'];

                $need_tier_pricing = $price_plan['need_tier_pricing'];

                foreach ($p['quantities'] as $ticket_id => $qty) {
                    $price['ticket_id'] = $ticket_id;
                    $price['qty'] = $qty;
                    $price['price'] = 99999;
                    $price['cost_price'] = 0;

                    $price_item = array();
                    $qty_tmp = 1;
                    //Todo:查找价格的
                    foreach ($price_plan['items'] as $pi) {
                        if ($pi['ticket_id'] == $ticket_id && $pi['special_code'] == $special_code) {
                            if (!$need_tier_pricing || $need_tier_pricing && $price['qty'] == $pi['quantity']) {
                                $price_item = $pi;
                                break;
                            } else {
                                if ($pi['quantity'] > $qty_tmp && $pi['quantity'] < $price['qty']) {
                                    $price_item = $pi;
                                    $qty_tmp = $pi['quantity'];
                                }
                            }
                        }
                    }

                    if (!empty($price_item)) {
                        $price['cost_price'] = $price_item['cost_price'];
                        if (empty($p['bundle_info'])) {
                            $price['price'] = $price_item['price'];
                        } else {
                            if ($p['bundle_info']['discount_type'] == 'F') {
                                $price['price'] = $price_item['price'] - $p['bundle_info']['discount_amount'];
                            } else if ($p['bundle_info']['discount_type'] == 'P') {
                                $price['price'] = (int)($price_item['price'] * (100 - $p['bundle_info']['discount_amount']) / 100);
                            } else {
                                $price['price'] = $price_item['price'];
                            }
                        }

                        if ($price['price'] < 0) $price_item['price'] = 0;
                    }

                    $sub_total += $price['price'] * $qty;
                    $cost_total += $price['cost_price'] * $qty;
                    $prices[] = $price;
                }
            } else {
                foreach ($p['quantities'] as $ticket_id => $qty) {
                    $price['ticket_id'] = $ticket_id;
                    $price['qty'] = $qty;
                    $price['price'] = 0;
                    $price['cost_price'] = 0;
                    $prices[] = $price;
                }
            }

            $p['sub_total'] = $sub_total;
            $order_sub_total += $sub_total;
            $p['cost_total'] = $cost_total;
            $order_cost_total += $cost_total;
            $p['prices'] = $prices;
        }

        $order_data['sub_total'] = $order_sub_total;
        $order_data['payment_total'] = $order_sub_total;
        $order_data['cost_total'] = $order_cost_total;

        return true;
    }

    private function checkSale(&$order_data, &$result)
    {
        $main_product = $order_data['products'][0];
        $activity_id = $order_data['activity_id'];

        $payment_methods = Yii::app()->activity->getPaymentMethods($activity_id);
        if (!(isset($order_data['payment_method']) && isset($payment_methods[$order_data['payment_method']]))) {
            $result['code'] = 400;
            $result['msg'] = '支付方式不正确！';

            return false;
        }

        $address = isset($order_data['address']) ? $order_data['address'] : array();
        $contacts_telephone = isset($address['telephone']) ? $address['telephone'] : '';
        $contacts_email = isset($address['email']) ? $address['email'] : '';

        $blacklist_phone = ['18910533692', '13366072950','13386261967','13524545782', '13816006153','15330012882', '18810543085', '13331983591', '13601741022', '15321892313','18910533692','13601741022','15321715697','13366072950','13331983591','15321892313','15313210379','15330012882','15311417437','13386261967','13701723512','15300788783','13261901159','13816006135','18810543753','13524545782','15800565175','18810379047','15910592700','18910559197','13917400176','13341189906','13391621365','18001223507','18211071716','2158028769','15010884535','13391381059','18911187613','15699911769','18810342976','18911201290','13021277165','15313851532','18911089671','13621945179','15810603281'];
        if(empty($contacts_telephone)){
            $result['code'] = 400;
            $result['msg'] = '联系电话不能为空!';
            return $result;
        }else if(in_array(trim($contacts_telephone),$blacklist_phone)){
            $result['code'] = 400;
            $result['msg'] = '联系电话有错误，请确保是您本人的联系电话!';
            return $result;
        }

        $blacklist_email = ['421114531@qq.com','yfxxxuminjie@qq.com','13601741022@qq.com','1772245526@qq.com','421114531@qq.com','18910533692@qq.com','baicaixmj@foxmail.com','yfxxxuminjie@qq.com','yfxxxuminjie@qq.com','mengmeizibibi@qq.com','mengmeizibibi@foxmail.com','15321892313@qq.com','byxuminjie@qq.com','byxuminjie@foxmail.com','244667237@139.com','gonghongbinmama@163.com','lovelytomato@yeah.net','xuminjievenus@gmail.com','nanhuinongjiale@qq.com','2065855814@qq.com','lovenanaco@163.com','15313210379@126.com','byxuminjie@126.com','15321715697@163.com','lovenanaco@126.com','venillacake@sina.com','15330012882@qq.com','fengjiang92@163.com','zzshuimitao@126.com','huafu506@126.com','qitaishui@163.com','mafenwu@163.com','lovezack@163.com','andyloveandy@yeah.net','blesszzy@163.com','dostertjan@163.com','youxiangds001@163.com','jessicazhao@21cn.com','luohong05@163.com','yangjuan2048@163.com'];
        if(empty($contacts_email)){
            $result['code'] = 400;
            $result['msg'] = '邮箱不能为空!';
            return $result;
        }else if(in_array(trim($contacts_email),$blacklist_email)){
            $result['code'] = 400;
            $result['msg'] = '联系邮箱错误，请确保是您本人的联系邮箱!';
            return $result;
        }

        $coupon = Yii::app()->cart->getCoupon();
        if (isset($coupon['code'])) {
            $coupon_result = HtCoupon::model()->validateCoupon($coupon['code'], $main_product);
            if ($coupon_result['code'] != 200) {
                $result = $coupon_result;

                return false;
            } else {
                $coupon_info = $coupon_result['data'];
                $order_data['coupon_info'] = $coupon_info;
                $sub_total = $order_data['sub_total'];
                $coupon_total = 0.0;
                if ($coupon_info) {
                    if ($coupon_info['type'] == HtCoupon::T_FUND) {
                        $coupon_total = (int)(min($sub_total, $coupon_info['discount']));
                    } else if ($coupon_info['type'] == HtCoupon::T_PERCENT) {
                        $coupon_total = round($sub_total / 100 * floatval($coupon_info['discount']));
                    }
                }

                $order_data['coupon_total'] = $coupon_total > $sub_total ? $sub_total : $coupon_total;
                $order_data['coupon_title'] = empty($coupon['title']) ? '' : $coupon['title'];
                $order_data['payment_total'] = $sub_total - $order_data['coupon_total'];
            }
        }

        //check activity limit
        if ($activity_id > 0) {
            $product_id = $main_product['product_id'];
            $activity_result = Yii::app()->activity->checkActivity($product_id, $activity_id, $contacts_telephone,
                                                                   $contacts_email,$_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_USER_AGENT']);
            if ($activity_result['code'] != 200) {
                $result = $activity_result;

                return false;
            }


            //calc activity discount
            $pay_discounts = Converter::convertModelToArray(HtActivityDiscount::model()->findAllByAttributes(['activity_id' => $activity_id]));
            if (!empty($pay_discounts)) {
                foreach ($pay_discounts as $discount) {
                    if (empty($discount['payment_method']) || $discount['payment_method'] == $order_data['payment_method']) {
                        if ($discount['discount_type'] == 'F') {
                            $order_data['payment_total'] = $order_data['payment_total'] - $discount['discount_amount'];
                        } else if ($discount['discount_type'] == 'P') {
                            $order_data['payment_total'] = (int)$order_data['payment_total'] * (100 - $discount['discount_amount']) / 100;
                        }
                        break;
                    }
                }
            }
        }

        if ($order_data['payment_total'] < 0) {
            $order_data['payment_total'] = 0;
        }

        //check stock
        $this->validateStock($order_data,$result,$activity_id);
        if($result['code']!=200){
            return false;
        }

        $result['code'] = 200;
        $result['msg'] = 'OK';

        return true;
    }

    /**
     * @param $data
     * @return int
     * @throws CDbException
     *
     */
    private function insertOrder($data)
    {
        $order = new HtOrder();

        $order->customer_id = $data['customer_id'];
        $order->activity_id = isset($data['activity_id']) ? $data['activity_id'] : 0;
        $order->status_id = HtOrderStatus::ORDER_CONFIRMED;
        $order->total = 0;

        $address = isset($data['address']) ? $data['address'] : array();
        $order->contacts_name = isset($address['firstname']) ? $address['firstname'] : '';
        $order->contacts_address = isset($address['contacts_address']) ? $address['contacts_address'] : '';
        $order->contacts_telephone = isset($address['telephone']) ? $address['telephone'] : '';
        $order->contacts_email = isset($address['email']) ? $address['email'] : '';
        $order->contacts_passport = isset($address['contacts_passport']) ? $address['contacts_passport'] : '';
        $order->payment_method = $data['payment_method'];
        $order->ip = $_SERVER['REMOTE_ADDR'];
        $order->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $order->accept_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        $order->payment_time_limit = $this->getPaymentTimeLimit($data);
        $order->sub_total = $data['sub_total'];
        $order->cost_total = $data['cost_total'];
        $order->total = isset($data['payment_total']) ? $data['payment_total'] : $data['sub_total'];
        $order->date_added = date('Y-m-d H:i:s');
        $order->date_modified = date('Y-m-d H:i:s');

        if ($order->insert()) {
            $order_his = new HtOrderHistory();
            $order_his->order_id = $order->order_id;
            $order_his->status_id = $order->status_id;
            $order_his->notify = 0;
            $order_his->comment = '';
            $order_his->date_added = date('Y-m-d H:i:s');
            if (!$order_his->insert()) {
                throw new HtException('Insert HtOrderHistory Failed!');
            }

            $order_trace = new HtOrderTrace();
            $order_trace->order_id = $order->order_id;
            $order_trace->first_uri = Yii::app()->session->get('first_uri', '');
            $order_trace->url_referer = Yii::app()->session->get('url_referer', '');
            $order_trace->ip = $_SERVER['REMOTE_ADDR'];
            $order_trace->user_agent = $_SERVER['HTTP_USER_AGENT'];
            $order_trace->accept_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
            $order_trace->channel = Yii::app()->cps->getUnionFromCookie();
            $order_trace->cookies = json_encode($_COOKIE);
            if (!$order_trace->insert()) {
                throw new HtException('Insert HtOrderTrace Failed!');
            }
        } else {
            throw new HtException('Insert HtOrder Failed!');
        }

        return $order->order_id;
    }

    private function getPaymentTimeLimit($data)
    {
        $deadline = '48hours';

        $activity_id = isset($data['activity_id']) ? $data['activity_id'] : 0;
        if ($activity_id) {
            $activity_rule = HtActivityRule::model()->findOneByPk($activity_id);

            if (!empty($activity_rule['payment_reservation'])) {
                $deadline = $activity_rule['payment_reservation'];
            }
        }
        $time_limit = date('Y-m-d H:i:s', strtotime($deadline));

        $tour_date = isset($data['tour_date']) ? $data['tour_date'] : '';
        if ($tour_date) {
            $date_rule = HtProductDateRule::model()->findByPk($data['products'][0]['product_id']);
            $time_limit = min($time_limit, date('Y-m-d H:i:s',
                                                strtotime($tour_date . '-' . $date_rule['buy_in_advance'] . '+1Day-1Second')));
        }

        return $time_limit;
    }

    /**
     * 只是将 Pax 基本信息插入到 ht_passenger表，还未和order_product关联
     * @param $data
     * @throws CDbException
     */
    private function insertOrderPax(&$data)
    {
        $pax_metas = HtPassengerMetaData::model()->findAll();
        foreach ($data['passengers'] as &$p) {
            $merged_fields = array();
            $pax = new HtPassenger();
            foreach ($pax_metas as $pm) {
                $storage_field = $pm['storage_field'];
                if ($pm['storage_merge']) {
                    if (isset($p[$storage_field])) {
                        $merged_fields[$storage_field] = isset($p[$storage_field]) ? $p[$storage_field] : '';
                    }
                } else {
                    $pax[$storage_field] = isset($p[$storage_field]) ? $p[$storage_field] : '';
                }
            }
            $pax['merged_fields'] = $merged_fields ? json_encode($merged_fields) : '';
            $pax->insert();

            //记录
            $p['db_passenger_id'] = $pax->passenger_id;
            Yii::log('Pax zh_name:' . $p['zh_name']);
        }
    }

    private function insertOrderProduct($order_id, $order_data)
    {
        $products = $order_data['products'];

//        $order_product_id = 0;
//        $order_total = 0;
//        $order_cost_total = 0;

        foreach ($products as $prod) {
            //order_product
            $op = new HtOrderProduct();
            $op->order_id = $order_id;
            $product_id = $prod['product_id'];

            $op->product_id = $product_id;
            $op->name = $prod['name'];
            $op->bundle_product_id = $prod['bundle_product_id'];
            $op->special_code = isset($prod['special_code']) ? $prod['special_code'] : '';
            $op->departure_code = isset($prod['departure_code']) ? $prod['departure_code'] : '';
            $op->departure_time = isset($prod['departure_time']) ? $prod['departure_time'] : '';
            $op->tour_date = isset($prod['tour_date']) ? $prod['tour_date'] : '';
            $pn = 0;
            foreach($prod['passengers'] as $t) {
                $pn+= count($t);
            }
            $op->pax_num = $pn;
            $op->total = isset($prod['sub_total']) ? $prod['sub_total'] : 0;
            $op->cost_total = isset($prod['cost_total']) ? $prod['cost_total'] : 0;
            if ($op->tour_date) {
                $tour_operation = HtProductTourOperation::model()->findByTourDate($product_id, $op->tour_date);
                if ($tour_operation) {
                    $op->language = $tour_operation->language_code;
                    $op->language_list_code = $tour_operation->language_list_code;
                }
            }
            $op->date_added = date('Y-m-d H:i:s');
            $op->date_modified = date('Y-m-d H:i:s');
            $op->insert();
            if (empty($order_product_id)) {
                $order_product_id = $op->order_product_id;
            }
//            $order_total += $op->total;
//            $order_cost_total += $op->cost_total;

            $op_id = $op->order_product_id;

            //order_product_history
            $op_his = new HtOrderProductHistory();
            $op_his->order_id = $op->order_id;
            $op_his->order_product_id = $op_id;
            $op_his->status_id = HtOrderStatus::ORDER_CONFIRMED;
            $op_his->comment = '创建订单';
            $op_his->date_added = date('Y-m-d H:i:s');
            $op_his->insert();

            //order_product_price
            //只有“主”商品或者选购商品才计算价格，包含在内的不需要单独计算

            foreach ($prod['prices'] as $price) {
                $opp = new HtOrderProductPrice();
                $opp->order_product_id = $op_id;
                $opp->ticket_id = $price['ticket_id'];
                $opp->quantity = $price['qty'];
                $opp->price = $price['price'];
                $opp->cost_price = $price['cost_price'];
                $opp->insert();
            }

            //order_passenger
            foreach ($prod['passengers'] as $ticket_id => $pax_ids) {
                foreach ($pax_ids as $pax_id) {
                    $opax = new HtOrderPassenger();
                    $opax->order_id = $order_id;
                    $opax->order_product_id = $op_id;
                    $opax->ticket_id = $ticket_id;
                    $opax->passenger_id = $order_data['passengers'][$pax_id]['db_passenger_id'];
                    $opax->insert();
                }
            }

        }
    }

    public function insertCoupon($order_id, $order_data)
    {

        //coupon
        if (isset($order_data['coupon_info']['coupon_id']) && isset($order_data['coupon_total'])) {
            HtCouponHistory::model()->addCouponHistory($order_data['coupon_info']['coupon_id'], $order_id,
                                                       $order_data['customer_id'],
                                                       -$order_data['coupon_total']);
        }
    }

//    /**
//     * 新增一个订单
//     * @param $data '订单确认时所需要的所有数据(如Product ID,Special Code,Tour Date,Departure Code,
//     * Departure ID,Pax Info,Contact Info,Payment Code,Coupon Code);
//     * @return array d
//     */
//    public function  addOrder_old($data)
//    {
//        $result = $this->validate($data);
//        if ($result['code'] == 200) {
//            try {
//                $order_id = $this->insertOrder($data, $result);
//                $this->insertOrderPax($data);
//                $order_product_id = $this->insertOrderProduct($order_id, $data, $result);
//                $this->insertCoupon($order_id, $data, $result);
//            } catch (Exception $e) {
//                Yii::log($e, CLogger::LEVEL_ERROR, 'hitour.service.order');
//                $result['code'] == 400;
//            }
//        }
//
//        if ($result['code'] != 200) {
//            Yii::log('SessionID=[' . Yii::app()->session->sessionID . '].' . 'Add order failed,' . $result['msg'],
//                CLogger::LEVEL_ERROR, 'hitour.service.order');
//            $result['msg'] = '订单提交失败！' . $result['msg'];
//        } else {
//            $result['data']['order_id'] = $order_id;
//            $result['data']['payment_url'] = Yii::app()->createAbsoluteUrl('PayGate/Pay',
//                array('order_id' => $order_id));
//            if ($result['data']['total'] == 0) {
//                $this->savePaymentHistory($order_id, $order_product_id, $data);
//                $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_PAYMENT_SUCCESS);
//                $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_WAIT_CONFIRMATION);
//                $payment_methods = PayUtility::paymentMethods();
//                if (isset($payment_methods[$data['payment_method']]) && $payment_methods[$data['payment_method']]['mobile']) {
//                    $result['data']['success_url'] = Yii::app()->createAbsoluteUrl('mobile/result',
//                        array('order_id' => $order_id));
//                } else {
//                    $result['data']['success_url'] = Yii::app()->createAbsoluteUrl('checkout/success',
//                        array('order_id' => $order_id));
//                }
//            }
//        }
//
//        if ($result['code'] == 200) {
//            Yii::app()->cart->clearCoupon('');
//        }
//
//        return $result;
//    }
//
//    /**
//     * @param $data
//     * @return array
//     */
//    private function validate(&$data)
//    {
//        $result = array();
//
//        if (!$data) {
//            $result['code'] = 400;
//            $result['msg'] = '订单数据不完整！';
//
//            return $result;
//        }
//
//        $payment_methods = PayUtility::paymentMethods();
//        if (!(isset($data['payment_method']) && isset($payment_methods[$data['payment_method']]))) {
//            $result['code'] = 400;
//            $result['msg'] = '支付方式不正确！';
//
//            return $result;
//        }
//
//        if (isset($data['coupon']['code'])) {
//            $coupon_result = HtCoupon::model()->validateCoupon($data['coupon']['code'], array());
//            if ($coupon_result['code'] != 200) {
//                return $coupon_result;
//            } else {
//                $data['coupon_info'] = $coupon_result['data'];
//            }
//        }
//
//        $product = HtProduct::model()->findByPk($data['product']['product_id']);
//        if ($product['status'] != HtProduct::IN_SALE) {
//            $result['msg'] = '抱歉，该商品已下架！';
//
//            return false;
//        }
//
//        //check tour date;
//        $date_rule = Yii::app()->product->getDateRule($data['product']['product_id']);
//        $tour_date = isset($data['tour_date']) ? $data['tour_date'] : '';
//        if ($date_rule['need_tour_date']) {
//            if (!$tour_date) {
//                $result['code'] = 400;
//                $result['msg'] = '订单没有填写出行日期!';
//
//                return $result;
//            } else if ($data ['tour_date'] < date('Y-m-d')) {
//                $result['code'] = 400;
//                $result['msg'] = '出行日期必须在今天之后!';
//
//                return false;
//            }
//
//        }
//
//        //check special
//        $special_code = isset($data['special']['special_code']) ? $data['special']['special_code'] : '';
//        $special_codes = Yii::app()->product->getSpecialCodes($data['product']['product_id']);
//        if ($special_codes) {
//            if (!$special_code) {
//                $result['code'] = 400;
//                $result['msg'] = '订单没有选择套餐!';
//
//                return $result;
//            } else {
//                $found = false;
//                foreach ($special_codes as $si) {
//                    if ($si['special_code'] == $special_code) {
//                        $found = true;
//                        break;
//                    }
//                }
//
//                if (!$found) {
//                    $result['code'] = 400;
//                    $result['msg'] = '订单套餐选择错误!';
//
//                    return $result;
//                }
//            }
//        }
//
//        //check departure
//
//
//        //check pax
//        if (count($data['passengers']) <= 0) {
//            $result['code'] = 400;
//            $result['msg'] = '订单没有填写出行人信息!';
//
//            return $result;
//        }
//
//        //check activity
//        $activity_id = isset($data['activity_id']) ? $data['activity_id'] : 0;
//        if ($activity_id > 0) {
//            $address = isset($data['address']) ? $data['address'] : array();
//            $contacts_telephone = isset($address['telephone']) ? $address['telephone'] : '';
//            $contacts_email = isset($address['email']) ? $address['email'] : '';
//            $product_id = $data['product']['product_id'];
//            $activity_result = Yii::app()->activity->checkActivity($product_id, $activity_id, $contacts_telephone, $contacts_email);
//            if ($activity_result['code'] != 200) {
//                return $activity_result;
//            }
//        }
//
//        //check stock
//        $stock_result = $this->validateStock($data);
//
//        $result['code'] = 200;
//        $result['msg'] = 'OK';
//
//        return $result;
//    }

    private function savePaymentHistory($order_id, $product_id)
    {
        $payment_history = HtPaymentHistory::model()->findByAttributes(
            ['order_id' => $order_id, 'pay_or_refund' => 1]
        );
        if ($payment_history && count($payment_history) > 0) {
            Yii::log('Payment history already exist. order_id[' . $order_id . ']', CLogger::LEVEL_WARNING);

            return false;
        } else {
            $product_info = HtProduct::model()->findByPk($product_id);
            $payment_history = new HtPaymentHistory();
            $payment_history['pay_or_refund'] = 1;
            $payment_history['payment_really'] = Yii::app()->params['PAYMENT_REALLY'];
            $payment_history['payment_type'] = 1;
            $payment_history['supplier_id'] = $product_info['supplier_id'];
            $payment_history['order_id'] = $order_id;
            $payment_history['product_id'] = $product_info['product_id'];
            $payment_history['trade_id'] = '';
            $payment_history['notify_id'] = '';
            $payment_history['trade_total'] = 0;
            $payment_history['buyer_id'] = '';
            $payment_history['buyer_email'] = '';
            $payment_history['trade_time'] = date('Y-m-d H:i:s');
            $payment_history['raw_data'] = '';
            $isok = $payment_history->insert();
            if (!$isok) {
                $error = $payment_history->getErrors();
                Yii::log('Save payment_history failed.error[' . (json_encode($error)) . ']', CLogger::LEVEL_ERROR);
            }

            return $isok;
        }
    }

    /**
     * @param $order_product
     * @return array
     * @throws CDbException
     */
    public function linkSupplierOrder($order_product)
    {
        $op_id = $order_product['order_product_id'];
        $product_id = $order_product['product_id'];
        $hitour_booking_ref = strtoupper(substr(md5($op_id . '_' . $product_id . '_' . time() . '_' . rand(1, 100)), 0,
                                                8));

        $supplier_order_id = $order_product['supplier_order_id'];
        if (empty($supplier_order_id)) {
            $so = new HtSupplierOrder();
            $so['hitour_booking_ref'] = $hitour_booking_ref;
            $so['current_status'] = HtSupplierOrder::PENDING;
            $isok = $so->insert();
            if ($isok) {
                HtOrderProduct::model()->updateByPk($op_id, ['supplier_order_id' => $so->supplier_order_id]);
            } else {
                Yii::log('Insert supplier order failed. order[' . $order_product['order_id'] . ']',
                         CLogger::LEVEL_ERROR);
            }
        } else {
            $so = HtSupplierOrder::model()->findByPk($supplier_order_id);
            if ($so['current_status'] != HtSupplierOrder::CONFIRMED) {
                $so['hitour_booking_ref'] = $hitour_booking_ref;
                $so['current_status'] = HtSupplierOrder::PENDING;
                $so->save();
            }
        }

        return Converter::convertModelToArray($so);
    }

    public function getOrderDetailForVoucher($order_id)
    {
        $order_data = array();

        //order
        $order = Converter::convertModelToArray(HtOrder::model()->findByPk($order_id));
        $order_data['order'] = $order;
        $order_id = $order['order_id'];

        //order_products
        $order_products = Converter::convertModelToArray(HtOrderProduct::model()->with(['departures', 'supplier_order', 'prices'])->findAllByAttributes(['order_id' => $order_id]));

        $order_all_passengers = HtOrderPassenger::model()->findAllByOrder($order_id);
        foreach ($order_products as &$op) {
            $order_product_id = $op['order_product_id'];
            $product_id = $op['product_id'];
            $op['special_info'] = HtProductSpecialCombo::getSpecialDetail($op['product_id'],$op['special_code']);

            //re-Org departures
            if ($op['departures']) {
                $dps = array();
                foreach ($op['departures'] as $dp) {
                    $dps[$dp['language_id']] = $dp;
                }
                $op['departures'] = $dps;
            }

            $op['product'] = $this->getProductDataForVoucher($product_id);

            $op['ticket_types'] = HtProductTicketRule::model()->getTicketRuleMapForOrder($op['product_id']);
            $op['quantities'] = HtOrderProductPrice::model()->calcRealQuantities($order_product_id, $product_id);

            $op_passengers = array();
            foreach ($order_all_passengers as $p) {
                if ($p['order_product_id'] == $order_product_id) {
                    $op_passengers[] = $p;
                }
            }
            $op['passengers'] = $op_passengers;

            $product_introduction = HtProductIntroduction::model()->findByPk($product_id);
            if (!empty($product_introduction) && $product_introduction['status'] == 1) {
                $op['usage'] = Converter::parseMdHtml($product_introduction['usage']);
            } else {
                $op['usage'] = Converter::parseMdHtml($order_product['product']['description']['how_it_works']);
            }
        }
        $order_data['order_products'] = $order_products;

        //insurance_codes
        $order_data['insurance_codes'] = Converter::convertModelToArray(HtInsuranceCode::model()->with('company')->findAllByAttributes(['order_id' => $order_id]));

        //gift coupon
        $gifts = Converter::convertModelToArray(HtOrderGiftCoupon::model()->with('coupon')->findAllByAttributes(['order_id' => $order_id]));

        foreach ($gifts as &$coupon) {
            $coupon['coupon']['valid_type'] = 0;
            $coupon['coupon']['limit_type'] = 1;

            list($use_limit, $limit_ids) = HtCouponUseLimit::model()->getLimitInfo($coupon['coupon_id']);
            if ($use_limit) {
                $coupon['coupon']['valid_type'] = $use_limit[0]['valid_type'];
                $coupon['coupon']['limit_type'] = $use_limit[0]['limit_type'];
                $coupon['coupon']['limit_ids'] = $limit_ids;
            }
        }

        $coupons = array();
        foreach ($gifts as $g) {
            $coupons[] = $g['coupon'];
        }
        $order_data['gift_coupons'] = $coupons;


        return $order_data;
    }

    private function getProductDataForVoucher($product_id)
    {
        $product = HtProduct::model()->with('descriptions', 'introduction', 'date_rule', 'return_rule', 'sale_rule', 'supplier',
                                            'shipping_rule')->findByPk($product_id);
        $product = Converter::convertModelToArray($product);
        $product_voucher_rule = HtProductVoucherRule::model()->getVoucherRule($product_id);
        if (is_array($product) && is_array($product_voucher_rule)) {
            $product = array_merge($product, $product_voucher_rule);
        } else {
            Yii::log('Not found voucher rule for product[' . $product_id . ']', CLogger::LEVEL_WARNING);
        }
        $product_passenger_rule = HtProductPassengerRule::model()->getPassengerRule($product_id);
        if (is_array($product) && is_array($product_passenger_rule)) {
            $product = array_merge($product, $product_passenger_rule);
        } else {
            Yii::log('Not found voucher rule for product[' . $product_id . ']', CLogger::LEVEL_WARNING);
        }

        $des = array();
        if (!empty($product['descriptions']) && count($product['descriptions']) > 0) {
            foreach ($product['descriptions'] as $pd) {
                $des[$pd['language_id']] = $pd;
            }
        } else {
            Yii::log('Not descriptions for product[' . $product_id . ']', CLogger::LEVEL_WARNING);
        }
        $product['descriptions'] = $des;
        $product['description'] = $des[2];
        $product['local_support'] = HtSupplierLocalSupport::model()->getProductLocalSupport($product_id);

        return $product;
    }

    public function getBaseInfoWithoutProductDetail($order_id)
    {
        //订单基本信息
        $order = HtOrder::model()->with('status')->findByPk($order_id);
        if (empty($order)) {
            return [];
        }
        $order = Converter::convertModelToArray($order);

        $baseInfo['order_id'] = $order['order_id'];
        $baseInfo['status_id'] = $order['status_id'];
        $baseInfo['status_name'] = $order['status']['cn_name'];
        $baseInfo['return_url'] = $order['return_url'];
        $baseInfo['total'] = $order['total'];

        $order_product = HtOrderProduct::model()->findByAttributes(['order_id' => $order_id]);

        $baseInfo['product_name'] = $order_product['name'];
        $baseInfo['tour_date'] = $order_product['tour_date'];

        return $baseInfo;
    }

    public function getBaseInfo($order_id)
    {
        //订单基本信息
        $order = HtOrder::model()->with('status')->findByPk($order_id);
        if (empty($order)) {
            return [];
        }
        $order = Converter::convertModelToArray($order);

        $insurance_code = HtInsuranceCode::model()->findAll('order_id = ' . $order_id);
        $insurance_code_arr = ModelHelper::getList($insurance_code, 'redeem_code');

        $baseInfo['order_id'] = $order['order_id'];
        $baseInfo['status_id'] = $order['status_id'];
        $baseInfo['status_name'] = $order['status']['cn_name'];
        $baseInfo['return_url'] = $order['return_url'];
        $baseInfo['insurance_code'] = $insurance_code_arr;
        $baseInfo['order'] = $order;
        $baseInfo['gift_coupon'] = HtOrderGiftCoupon::model()->getCouponsByOrderId($order_id);

        $order_products = HtOrderProduct::model()->with(
            'product.supplier',
            'product.shipping_rule',
            'product.date_rule',
            'product.return_rule',
            'product.ticket_rule',
//            'special',
            'product_descriptions',
            'departures')->findAllByAttributes(['order_id' => $order_id]);
        if (count($order_products) == 1) {
            $order_product = $order_products[0];
            $baseInfo['product'] = $this->getOrderProductDetail($order_product);
            $baseInfo['product_type'] = $order_product['product']['type'];
            $baseInfo['product_count'] = 1;

        } else {
            $main_order_product = $order_products[0];
            $baseInfo['product_type'] = $main_order_product['product']['type'];
            $baseInfo['product_count'] = count($order_products);

            // TODO handle order have more than one order product
            $main_order_product = $order_products[0];
            $main_product_id = $main_order_product['product_id'];
            $bundle = HtProductBundle::model()->findAllByAttributes(['product_id' => $main_product_id]);
            $bundle_ids = ModelHelper::getList($bundle, 'bundle_id');

            $grouped_product = ['group_0' => [], 'group_1' => [], 'group_2' => [], 'group_3' => []];
            foreach ($order_products as $order_product) {
                if ($order_product['product_id'] == $main_product_id) {
                    $product = $this->getOrderProductDetail($order_product);
                } else {
                    $product = $this->getOrderProductDetail($order_product, $bundle_ids);
                }

                $grouped_product['group_' . $product['group_type']][] = $product;
            }

            // hacked to solve that the total of product in group_1 was recorded in product of group_0
            if (count($grouped_product['group_1']) > 0 && count($grouped_product['group_0']) > 0) {
                $grouped_product['group_1'][0]['total'] = $grouped_product['group_0'][0]['total'];
            }

            $baseInfo['products'] = $grouped_product;
        }

        return $baseInfo;
    }

    private function getOrderProductDetail($order_product, $bundle_ids = [])
    {
        $group_type = 0; // 1：N选1；2：必选；3：可选; 0: not in group
        if (!empty($bundle_ids)) {
            $bundle_info = HtProductBundleItem::model()->getBundleInfo($order_product['product_id'], $bundle_ids);
            $group_type = $bundle_info['bundle']['group_type'];
        } else if ($order_product['bundle_product_id'] > 0) {
            $group_type = 1;
        }

        $product = [];
        $product = array_merge($product, $order_product->attributes);
        $product['group_type'] = $group_type;
        $product['product'] = $order_product['product']->attributes;
        $product['product']['ticket_rule'] = $order_product['product']['ticket_rule'];
        $product['product']['return_rule'] = $order_product['product']['return_rule'];

        $product['need_tour_date'] = $order_product['product']['date_rule']['need_tour_date'];
        $product['supplier_name_en'] = $order_product['product']['supplier']['name'];
        $product['supplier_name_zh'] = $order_product['product']['supplier']['cn_name'];
        $product['need_special'] = 0;

//        if (is_array($order_product['special']) && count($order_product['special']) > 0) {
//            $product['need_special'] = 1;
//            $product['special_name_en'] = $order_product['special']['en_name'];
//            $product['special_name_zh'] = $order_product['special']['cn_name'];
//        }
        if(HtProductSpecialCombo::model()->needSpecialCode($order_product['product_id'])){
            $product['need_special'] = 1;
            $special_info = HtProductSpecialCombo::getSpecialDetail($order_product['product_id'],$order_product['special_code']);
            $product['special_info'] = $special_info;
        }

        $special_group = HtProductSpecialCombo::model()->getSpecialGroupByCode($order_product['product_id'], $order_product['special_code']);
        $product['group_cn_title'] = $special_group['cn_title'];
        $product['group_en_title'] = $special_group['en_title'];
        $titles = $this->getTitlesByLanguage($order_product['product_descriptions']);
        $product = array_merge($product, $titles);

        list($need_departure, $points) = $this->getDepartureInfo($order_product['departures']);
        $product['need_departure'] = $need_departure;
        if ($need_departure) {
            $product = array_merge($product, $points);
        }

        $product['departure_time'] = $order_product['departure_time'];

        return $product;
    }

    private function getTitlesByLanguage($product_descriptions)
    {
        $titles = [];
        foreach ($product_descriptions as $pd) {
            if ($pd['language_id'] == 1) {
                $titles['tour_date_title_en'] = $pd['tour_date_title'];
                $titles['special_title_en'] = $pd['special_title'];
                $titles['departure_title_en'] = $pd['departure_title'];
            } else {
                $titles['tour_date_title_zh'] = $pd['tour_date_title'];
                $titles['special_title_zh'] = $pd['special_title'];
                $titles['departure_title_zh'] = $pd['departure_title'];
                $titles['product_name'] = $pd['name'];
            }
        }

        return $titles;
    }

    private function getDepartureInfo($departures)
    {
        $need_departure = 0;
        $result = [];
        if (is_array($departures) && count($departures) > 0) {
            $need_departure = 1;

            foreach ($departures as $dp) {
                if ($dp['language_id'] == 1) {
                    $result['departure_point_en'] = $dp['departure_point'];
                } else {
                    $result['departure_point_zh'] = $dp['departure_point'];
                }
            }
        }

        return [$need_departure, $result];
    }

    public function getPassengerInfo($order_id)
    {
        $passengerInfo = array();

        $data = HtOrder::model()->with('order_product.product')->findByPk($order_id);
        $order = Converter::convertModelToArray($data);
        if (!$order) {
            return $passengerInfo;
        }

//        $data = HtOrderPassengerBak::model()->findAll('order_id = ' . $order_id);
//        $passengerInfo['pax_data'] = Converter::convertModelToArray($data);
        $passengerInfo['pax_data'] = HtOrderPassenger::model()->findAllByOrder($order_id);
        $data = HtTicketType::model()->findAll();
        foreach (Converter::convertModelToArray($data) as $ticket) {
            $passengerInfo['pax_ticket'][$ticket['ticket_id']]['cn_name'] = $ticket['cn_name'];
            $passengerInfo['pax_ticket'][$ticket['ticket_id']]['en_name'] = $ticket['en_name'];
        }
        $passengerInfo = array_merge($passengerInfo,
                                     HtProductPassengerRule::model()->getPassengerRule($order['order_product']['product_id']));
        $passengerInfo['pax_quantities'] = HtOrderProductPrice::model()->calcRealQuantities($order['order_product']['order_product_id'],
                                                                                            $order['order_product']['product_id']);

        if ($order['order_product']['product']['type'] == 8) { // if product type is hotel package we will remove the same passenger info with different product.
            $passenger_ids = [];
            $diff_passenger = array();
            foreach ($passengerInfo['pax_data'] as $p) {
                if (false == in_array($p['passenger_id'], $passenger_ids)) {
                    array_push($passenger_ids, $p['passenger_id']);
                    array_push($diff_passenger, $p);
                }
            }
            $passengerInfo['pax_data'] = $diff_passenger;
        }

        return $passengerInfo;
    }

    public function getContactsInfo($order_id)
    {
        $contactsInfo = array();
        $data = HtOrder::model()->with('payment_history')->findByPk($order_id);
        $order = Converter::convertModelToArray($data);
        if (!$order) {
            return $contactsInfo;
        }

        $customer = HtCustomer::model()->findByPk($data["customer_id"]);
        if (!empty($customer)) {
            $customer_type = [];
            $customer_third = HtCustomerThird::model()->findAll("customer_id=" . $data["customer_id"]);
            if (!empty($customer_third[0])) {
                if ($customer_third[0]["otype"] == HtCustomerThird::QQ) {
                    array_push($customer_type, "QQ");
                } else if ($customer_third[0]["otype"] == HtCustomerThird::WEIBO) {
                    array_push($customer_type, "Weibo");
                } else if ($customer_third[0]["otype"] == HtCustomerThird::WEIXIN) {
                    array_push($customer_type, "Weixin");
                }
            }

            if (strpos($customer["email"], "@") !== false) {
                array_push($customer_type, "Email");
                $contactsInfo["customer_account"] = $customer["email"];
            }
            if ($customer['bind_phone'] == 1) {
                array_push($customer_type, "Phone");
                $contactsInfo["customer_account"] = $customer["telephone"];
            }
        }

        $contactsInfo['contacts_name'] = $order['contacts_name'];
        //手机号码归属地查询
        $zone = getTelephoneZone($order['contacts_telephone']);

        $contactsInfo['contacts_telephone'] = $order['contacts_telephone'] . $zone;
        $contactsInfo['contacts_email'] = $order['contacts_email'];
        $contactsInfo['contacts_pay_account'] = $order['payment_history']['buyer_email'];
        $contactsInfo["customer_type"] = $customer_type;

        return $contactsInfo;
    }

    public function getStatusHistory($order_id)
    {
        $statusHistory = array();
        $data = HtOrder::model()->with('order_history.order_status', 'status')->findByPk($order_id);
        $order = Converter::convertModelToArray($data);
        if (!$order) {
            return $statusHistory;
        }

        foreach ($order['order_history'] as $history) {
            $item = array();
            $item['status_name'] = $history['order_status']['cn_name'];
            $item['date_added'] = $history['date_added'];
            $item['comment'] = $history['comment'];
            $statusHistory[] = $item;
        }

        return $statusHistory;
    }

    public function getActivityInfo($order_info)
    {
        if (empty($order_info['order']['activity_id'])) {
            return array();
        }
        $aty = HtActivity::model()->findByPk($order_info['order']['activity_id']);
        if (empty($aty)) {
            return array();
        }

        return Converter::convertModelToArray($aty);
    }

    public function getProductsOfOrder($order_id)
    {
        $data = HtOrderProduct::model()->with('product')->findAllByAttributes(['order_id' => $order_id]);
        $order_products = Converter::convertModelToArray($data);
        if (empty($order_products)) {
            return [];
        } else {
            return $order_products;
        }
    }

    public function executeOrderAction($order_data, $order_product_data, $method)
    {
        $booking_type = $order_product_data['product']['shipping_rule']['booking_type'];
        $class = ucfirst(strtolower($booking_type)) . 'Booking';
        if (!empty($class) && class_exists($class) && method_exists($class, $method)) {
            $result = (new $class())->{$method}($order_data, $order_product_data);
        } else {
            $result = array('code' => 500, 'msg' => 'Class[' . $class . '] not found.');
            Yii::log('Booking but Class[' . $class . '] or Method[' . $method . '] not found.', CLogger::LEVEL_ERROR);
        }

        return $result;
    }

    public function allowReturn($order_data, $order_product_data, $force = 0)
    {
        $allow_return = true;
        $return_type = $order_product_data['product']['return_rule']['return_type'];
        $current_date = date('Y-m-d');
        $expire_date = $order_product_data['return_expire_date'];
        if (!$force) {
            if ($return_type == HtProductReturnRule::DONT_RETURN || $current_date > $expire_date) {
                Yii::log('Return forbidden or last day arived.order[' . $order_data['order_id'] . ']',
                         CLogger::LEVEL_WARNING);
                $allow_return = false;
            }
        }

        return $allow_return;
    }

    public function checkOrderReturn($order_id)
    {
        $can_return = 1;
        if (empty($this->order)) {
            $this->order = HtOrder::model()->findByPk($order_id);
        }
        $order = $this->order;
        if (!$order) {
            return 0;
        }
        if (in_array($order['status_id'], [
            HtOrderStatus::ORDER_CANCELED,
            HtOrderStatus::ORDER_NOTPAY_EXPIRED,
            HtOrderStatus::ORDER_OUTOF_REFUND,
            HtOrderStatus::ORDER_PAYMENT_FAILED,
            HtOrderStatus::ORDER_REFUND_SUCCESS,
            HtOrderStatus::ORDER_RETURN_FAILED,
            HtOrderStatus::ORDER_CONFIRMED
        ])
        ) {
            $can_return = 0;
        }

        $refund_result = HtPaymentHistory::model()->findAll('order_id = ' . $order_id . ' AND pay_or_refund = 0');
        $refund_info = Converter::convertModelToArray($refund_result);
        if (is_array($refund_info) && count($refund_info) > 0) {
            $can_return = 2;
        }

        return $can_return;
    }

    public function checkOrderShipping($order_id)
    {
        if (empty($this->order)) {
            $this->order = HtOrder::model()->findByPk($order_id);
        }
        $order = $this->order;
        if ($order['status_id'] == HtOrderStatus::ORDER_SHIPPED) {
            $canshipping = 0;
        } else {
            $canshipping = $this->checkOrderStocked($order_id);
            $canshipping = ($canshipping && in_array($order['status_id'],
                                                     [HtOrderStatus::ORDER_TO_DELIVERY, HtOrderStatus::ORDER_SHIPPING_FAILED])) ? 1 : 0;
        }

        return $canshipping;
    }

    public function checkOrderStocked($order_id)
    {
        $stocked = true;
        $this->order = HtOrder::model()->with('order_product.product')->findByPk($order_id);
        $order = $this->order;

        if (!$order) {
            Yii::log('Order[' . $order_id . '] not found.', CLogger::LEVEL_WARNING);

            return 0;
        }

        $shippingInfo = $this->getShippingInfo($order_id);
        foreach ($shippingInfo as $product_id => $info) {
            if ($info['shipping_rule']['need_supplier_booking_ref'] && empty($info['supplier_order']['supplier_booking_ref'])) {
                Yii::log('Order[' . $order_id . '] need supplier booking reference, but no, so stock failed.',
                         CLogger::LEVEL_INFO);
                $stocked = false;
            }
            if ($info['shipping_rule']['need_hitour_booking_ref'] && empty($info['supplier_order']['hitour_booking_ref'])) {
                Yii::log('Order[' . $order_id . '] need hitour booking reference, but no, so stock failed.',
                         CLogger::LEVEL_INFO);
                $stocked = false;
            }
            $confirmation_num = 0;
            if (!empty($info['supplier_order']['confirmation_ref'])) {
                foreach($info['supplier_order']['confirmation_ref'] as $confirmation_code) {
                    if (!empty($confirmation_code) && strlen($confirmation_code) > 1) {
                        $confirmation_num++;
                    }
                }
            }
            $voucher_num = count($info['supplier_order']['voucher_ref']);
            switch ($info['shipping_rule']['supplier_feedback_type']) {
                case HtProductShippingRule::FT_CODE:
                    if ($info['shipping_rule']['confirmation_type'] == HtProductShippingRule::CT_ONE) {
                        if ($confirmation_num != HtProductShippingRule::CT_ONE) {
                            Yii::log('Order[' . $order_id . '] supplier_feedback_type[1] and confirmation_type[1], but confirmation num not 1, so stock failed.',
                                     CLogger::LEVEL_INFO);
                            $stocked = false;
                        }
                    } else if ($info['shipping_rule']['confirmation_type'] == HtProductShippingRule::CT_EVERYONE) {
                        if ($confirmation_num != $info['ticket_num']) {
                            Yii::log('Order[' . $order_id . '] supplier_feedback_type[1] and confirmation_type[2], but confirmation num [' . $info['ticket_num'] . '], so stock failed.',
                                     CLogger::LEVEL_INFO);
                            $stocked = false;
                        }
                    } else if ($info['shipping_rule']['confirmation_type'] != HtProductShippingRule::CT_NONE) {
                        Yii::log('Order[' . $order_id . '] supplier_feedback_type[1], but confirmation_type not in [1,2], so stock failed.',
                                 CLogger::LEVEL_INFO);
                        $stocked = false;
                    }
                    break;
                case HtProductShippingRule::FT_PDF:
                    if (empty($info['supplier_order']['voucher_ref']) || !is_array($info['supplier_order']['voucher_ref'])) {
                        Yii::log('Order[' . $order_id . '] supplier_feedback_type[2] voucher_ref is empty, so stock failed.',
                                 CLogger::LEVEL_INFO);
                        $stocked = false;
                    } else if ($info['shipping_rule']['confirmation_type'] == HtProductShippingRule::CT_ONE) {
                        $voucher_read_path = $order['voucher_path'] . $info['supplier_order']['voucher_ref'][0]['voucher_name'];
                        if ($voucher_num != 1 || !file_exists($voucher_read_path)) {
                            Yii::log('Order[' . $order_id . '] supplier_feedback_type[2] confirmation_type[1] voucher_num[' . $voucher_num . '] voucher_path[' . $voucher_read_path . '], so stock failed.',
                                     CLogger::LEVEL_INFO);
                            $stocked = false;
                        }
                    } else if ($info['shipping_rule']['confirmation_type'] == HtProductShippingRule::CT_EVERYONE) {
                        if ($voucher_num != $info['ticket_num']) {
                            Yii::log('Order[' . $order_id . '] supplier_feedback_type[2] voucher_num[' . $voucher_num . '] passenger_num[' . $info['ticket_num'] . '], so stock failed.',
                                     CLogger::LEVEL_INFO);
                            $stocked = false;
                        } else {
                            foreach ($info['supplier_order']['voucher_ref'] as $voucher) {
                                if (!file_exists($order['voucher_path'] . $voucher['voucher_name'])) {
                                    Yii::log('Order[' . $order_id . '] supplier_feedback_type[2] confirmation_type[2] voucher_num[' . $voucher_num . '] voucher_path[' . ($order['voucher_path'] . $voucher['voucher_name']) . '], so stock failed.',
                                             CLogger::LEVEL_INFO);
                                    $stocked = false;
                                }
                            }
                        }
                    } else if ($info['shipping_rule']['confirmation_type'] == HtProductShippingRule::CT_NOLIMIT) {
                        $voucher_read_path = $order['voucher_path'] . $info['supplier_order']['voucher_ref'][0]['voucher_name'];
                        if ($voucher_num == 0 || !file_exists($voucher_read_path)) {
                            Yii::log('Order[' . $order_id . '] supplier_feedback_type[2] confirmation_type[3] voucher_num[' . $voucher_num . '] voucher_path[' . $voucher_read_path . '], so stock failed.',
                                     CLogger::LEVEL_INFO);
                            $stocked = false;
                        }
                    } else {
                        Yii::log('Order[' . $order_id . '] supplier_feedback_type[2], but confirmation_type not in [1,2,3], so stock failed.',
                                 CLogger::LEVEL_INFO);
                        $stocked = false;
                    }
                    break;
                case HtProductShippingRule::FT_OK:
                    if ($info['shipping_rule']['booking_type'] == 'GTA' && $confirmation_num != 1) {
                        Yii::log('Order[' . $order_id . '] gta supplier_feedback_type[3] and confirmation_type[2], but confirmation number not 1, so stock failed.',
                            CLogger::LEVEL_INFO);
                        $stocked = false;
                    }else if ($info['shipping_rule']['booking_type'] == 'CPIC' && $confirmation_num != $info['ticket_num']) {
                        Yii::log('Order[' . $order_id . '] cpic supplier_feedback_type[3] and confirmation_type[2], but confirmation num [' . $info['ticket_num'] . '], so stock failed.',
                            CLogger::LEVEL_INFO);
                        $stocked = false;
                    }else if ($info['shipping_rule']['confirmation_type'] == HtProductShippingRule::CT_NONE && $info['shipping_rule']['display_additional_info'] && empty($info['supplier_order']['additional_info'])) {
                        Yii::log('Order[' . $order_id . '] supplier_feedback_type[3] and confirmation_type[0] and display_additional_info, but not found additional_info, so stock failed.',
                            CLogger::LEVEL_INFO);
                        $stocked = false;
                    }
                    break;
                default:
                    Yii::log('Order[' . $order_id . '] supplier_feedback_type[' . $info['shipping_rule']['supplier_feedback_type'] . '], so stock failed.',
                             CLogger::LEVEL_INFO);
                    $stocked = false;
                    break;
            }

            if (!$stocked) {
                break;
            }
        }

        return $stocked ? 1 : 0;
    }

    public function getShippingInfo($order_id)
    {
        $shippingInfo = array();
        $data = HtOrder::model()->with('order_products.product.supplier',
                                       'order_products.special',
                                       'order_products.product_descriptions',
                                       'order_products.product.date_rule',
                                       'order_products.departures')->findByPk($order_id);
        $order = Converter::convertModelToArray($data);
        if (!$order) {
            return $shippingInfo;
        }

        foreach ($order['order_products'] as $order_product) {
            $quantities = HtOrderProductPrice::model()->calcRealQuantities($order_product['order_product_id'],
                                                                           $order_product['product_id']);
            $ticket_num = array_sum($quantities);
            if ($order_product['product']['is_combo'] != 1 && $order_product['product']['type'] != HtProduct::T_HOTEL_BUNDLE) {
                $shippingInfo[$order_product['product_id']] = $this->getShippingDetail($order_product['product_id'],
                                                                                       $order_product['supplier_order_id'],
                                                                                       $order);
                $shippingInfo[$order_product['product_id']]['ticket_num'] = $ticket_num;
                $shippingInfo[$order_product['product_id']]['baseInfo'] = array();
                //挂接类型
                $shippingInfo[$order_product['product_id']]['baseInfo']['bundle_type_name'] = '';
                $bundle_info = HtProductBundleItem::model()->with('bundle')->find('binding_product_id = ' . $order_product['product_id']);
                if ($bundle_info && $order['order_products'][0]['product']['type'] == HtProduct::T_HOTEL_BUNDLE) {
                    $shippingInfo[$order_product['product_id']]['baseInfo']['bundle_type_name'] = $bundle_info['bundle']['top_group_title'];
                }
                $bundle_info = Converter::convertModelToArray($bundle_info);
                $shippingInfo[$order_product['product_id']]['baseInfo']['bundle_type_name'] = $bundle_info['bundle']['top_group_title'];

                $shippingInfo[$order_product['product_id']]['baseInfo']['product_id'] = $order_product['product_id'];

                $titles = $this->getTitlesByLanguage($order_product['product_descriptions']);
                $shippingInfo[$order_product['product_id']]['baseInfo'] = array_merge($shippingInfo[$order_product['product_id']]['baseInfo'],
                                                                                      $titles);

                $shippingInfo[$order_product['product_id']]['baseInfo']['need_tour_date'] = $order_product['product']['date_rule']['need_tour_date'];
                if ($shippingInfo[$order_product['product_id']]['baseInfo']['need_tour_date'] == 1) {
                    $shippingInfo[$order_product['product_id']]['baseInfo']['tour_date'] = $order_product['tour_date'];
                }

                $order_passengers = HtOrderPassenger::model()->with('passenger')->findAll('order_product_id = ' . $order_product['order_product_id']);
                $order_passengers = Converter::convertModelToArray($order_passengers);
                foreach ($order_passengers as $p) {
                    $shippingInfo[$order_product['product_id']]['baseInfo']['passengers'][$p['passenger_id']] = $p['passenger']['zh_name'];
                }

                $shippingInfo[$order_product['product_id']]['baseInfo']['supplier_name_en'] = $order_product['product']['supplier']['name'];
                $shippingInfo[$order_product['product_id']]['baseInfo']['supplier_name_zh'] = $order_product['product']['supplier']['cn_name'];

//                if (is_array($order_product['special']) && count($order_product['special']) > 0) {
//                    $shippingInfo[$order_product['product_id']]['baseInfo']['need_special'] = 1;
//                    $shippingInfo[$order_product['product_id']]['baseInfo']['special_code'] = $order_product['special_code'];
//                    $shippingInfo[$order_product['product_id']]['baseInfo']['special_name_en'] = $order_product['special']['en_name'];
//                    $shippingInfo[$order_product['product_id']]['baseInfo']['special_name_zh'] = $order_product['special']['cn_name'];
//                    $shippingInfo[$order_product['product_id']]['baseInfo']['special_status'] = $order_product['special']['status'];
//                } else {
//                    $shippingInfo[$order_product['product_id']]['baseInfo']['need_special'] = 0;
//                }
                if(HtProductSpecialCombo::model()->needSpecialCode($order_product['product_id'])){
                    $special_info = HtProductSpecialCombo::getSpecialDetail($order_product['product_id'],$order_product['special_code']);
                    $special_info = $special_info[0];
                    foreach($special_info['items'] as &$info) {
                        $special_group = HtProductSpecialCombo::model()->getSpecialGroupByCode($order_product['product_id'], $info['special_code']);
                        $info['group_cn_title'] = $special_group['cn_title'];
                        $info['group_en_title'] = $special_group['en_title'];
                    }

                    $shippingInfo[$order_product['product_id']]['baseInfo']['need_special'] = 1;
                    $shippingInfo[$order_product['product_id']]['baseInfo']['special_info'] = $special_info;
                } else {
                    $shippingInfo[$order_product['product_id']]['baseInfo']['need_special'] = 0;
                }

                list($need_departure, $points) = $this->getDepartureInfo($order_product['departures']);
                $shippingInfo[$order_product['product_id']]['baseInfo']['need_departure'] = $need_departure;
                if ($need_departure) {
                    $shippingInfo[$order_product['product_id']]['baseInfo']['departure_code'] = $order_product['departure_code'];
                    $shippingInfo[$order_product['product_id']]['baseInfo']['departure_time'] = $order_product['departure_time'];

                    $shippingInfo[$order_product['product_id']]['baseInfo'] = array_merge($shippingInfo[$order_product['product_id']]['baseInfo'],
                                                                                          $points);

                }

                $shippingInfo[$order_product['product_id']]['baseInfo']['type'] = $order_product['product']['type'];

                //价格计划
                $shippingInfo[$order_product['product_id']]['pricePlanInfo'] = $this->getPricePlanInfo($shippingInfo[$order_product['product_id']]['baseInfo']);
            }
        }

//        $quantities = HtOrderProductPrice::model()->calcRealQuantities($order['order_product']['order_product_id'],
//            $order['order_product']['product_id']);
//        $ticket_num = array_sum($quantities);
//        if ($order['order_product']['product']['is_combo'] == 1) {
//            $item = HtOrderProductSub::model()->with('product.description')->findAll('order_product_id = ' . $order['order_product']['order_product_id']);
//            $subOrderProducts = Converter::convertModelToArray($item);
//            foreach ($subOrderProducts as $sub) {
//                $shippingInfo[$sub['sub_product_id']] = $this->getShippingDetail($sub['sub_product_id'],
//                    $sub['supplier_order_id'], $order);
//                $shippingInfo[$sub['sub_product_id']]['ticket_num'] = $ticket_num;
//            }
//        } else {
//            $shippingInfo[$order['order_product']['product_id']] = $this->getShippingDetail($order['order_product']['product_id'],
//                $order['order_product']['supplier_order_id'],
//                $order);
//            $shippingInfo[$order['order_product']['product_id']]['ticket_num'] = $ticket_num;
//        }

        return $shippingInfo;
    }

    private function getShippingDetail($product_id, $supplier_order_id, $order)
    {
        $shippingDetail = array();
        $shippingDetail['shipping_rule'] = HtProductShippingRule::model()->findByPk($product_id)->attributes;
//        $shippingDetail['supplier_order'] = HtSupplierOrder::model()->findByPk($supplier_order_id);
        $product_data = HtProduct::model()->with('description')->findByPk($product_id);
        $product = Converter::convertModelToArray($product_data);
        $shippingDetail['shipping_rule']['product_name'] = $product['description']['name'];


        $supplierOrder = HtSupplierOrder::model()->findByPk($supplier_order_id);
        if (empty($supplierOrder)) {
            $supplierOrder = new HtSupplierOrder();
            $supplierOrder['confirmation_ref'] = [];
            $supplierOrder['voucher_ref'] = [];
            $shippingDetail['supplier_order'] = $supplierOrder;

            return $shippingDetail;
        }

        //确认码
        $confirmation_ref = str_replace(';', ',', trim($supplierOrder['confirmation_ref'], " \t,"));
        $supplierOrder['confirmation_ref'] = explode(',', $confirmation_ref);
        //voucher
        $voucher_ref = json_decode($supplierOrder['voucher_ref']);
        $voucher_list = array();
        if (count($voucher_ref) > 0) {
            foreach ($voucher_ref as $k => $v) {
                if (!empty($v)) {
                    $voucher_list[$k]['voucher_name'] = $v;
                    $voucher_list[$k]['voucher_url'] = $order['voucher_base_url'] . $v;
                }
            }
        }
        $supplierOrder['voucher_ref'] = $voucher_list;
        $shippingDetail['supplier_order'] = $supplierOrder;

        return $shippingDetail;
    }

    public function getPricePlanInfo($order_info)
    {
        $pricePlanInfo = array();

        // TODO
        //tour operation.
        $tour_operations = HtProductTourOperation::model()->findAll('product_id = ' . $order_info['product_id']);
        $tour_operations = Converter::convertModelToArray($tour_operations);
        if ($order_info['need_tour_date']) {
            foreach ($tour_operations as $operation) {
                if ($order_info['tour_date'] >= $operation['from_date'] && $order_info['tour_date'] <= $operation['to_date']) {
                    $pricePlanInfo['tour_operation'] = $operation;
                }
            }
        }

        //price plan.
        $pricePlans = HtProductPricePlan::model()->with('items')->findAll('product_id = ' . $order_info['product_id']);
        $pricePlans = Converter::convertModelToArray($pricePlans);
        if ($order_info['need_tour_date']) {
            foreach ($pricePlans as $plan) {
                if ($order_info['tour_date'] >= $plan['from_date'] && $order_info['tour_date'] <= $plan['to_date'] || $plan['valid_region'] == 0) {
                    $pricePlanInfo['price_plan'] = $plan;
                }
            }
        }

        $tour_date = $order_info['need_tour_date'] ? $order_info['tour_date'] : '0000-00-00';
        //departures.
        $pricePlanInfo['departure_list'] = Yii::app()->product->getDepartureListByDate($order_info['product_id'],
                                                                                       $tour_date);

        return $pricePlanInfo;
    }

    public function checkOrderReShipping($order_id)
    {
        $can_reshipping = true;
        if (empty($this->order)) {
            $this->order = HtOrder::model()->findByPk($order_id);
        }
        $order = $this->order;
        if (!$order) {
            return 0;
        }
        if ($order['status_id'] != HtOrderStatus::ORDER_SHIPPED) {
            $can_reshipping = false;
        }

        return $can_reshipping ? 1 : 0;
    }

    public function checkOrderBooking($order_id)
    {
        $this->order = HtOrder::model()->with('order_product.product')->findByPk($order_id);
        $order = $this->order;
        if ($order['order_product']['product']['type'] == HtProduct::T_HOTEL_BUNDLE) {
            if ($order['status_id'] == HtOrderStatus::ORDER_WAIT_CONFIRMATION) {
                $canbooking = 0;
            } else {
                //套餐商品校验
                $canbooking = 1;
                $canbooking = ($canbooking && in_array($order['status_id'],
                                                       [HtOrderStatus::ORDER_PAYMENT_SUCCESS, HtOrderStatus::ORDER_BOOKING_FAILED])) ? 1 : 0;
            }
        } else {
            $canbooking = 0;
        }

        return $canbooking;
    }

    public function checkOrderReBooking($order_id)
    {
        $need_rebooking = false;
        if (empty($this->order)) {
            $this->order = HtOrder::model()->findByPk($order_id);
        }
        $order = $this->order;
        if (!$order) {
            return 0;
        }
        $shippingInfo = $this->getShippingInfo($order_id);
        foreach ($shippingInfo as $product_id => $info) {
            switch ($info['shipping_rule']['booking_type']) {
                case HtProductShippingRule::BT_STOCK:
                    if ($order['status_id'] == HtOrderStatus::ORDER_STOCK_FAILED) {
                        $need_rebooking = true;
                    }
                    break;
                case HtProductShippingRule::BT_HITOUR:
                    break;
                case HtProductShippingRule::BT_B2B:
                    break;
                case HtProductShippingRule::BT_EMAIL:
                    break;
                case HtProductShippingRule::BT_GTA:
                    break;
                case HtProductShippingRule::BT_CPIC:
                    break;
                default:
                    break;
            }
        }

        return $need_rebooking ? 1 : 0;
    }

    public function updateRedeemReturnExpireDate($order_id)
    {
        $order = Converter::convertModelToArray(HtOrder::model()->findByPk($order_id));
        $order_products = Converter::convertModelToArray(HtOrderProduct::model()->findAllByAttributes(['order_id' => $order_id]));

        if (empty($order) || empty($order_products)) {
            Yii::log('Order Not Found.order_id=' . $order_id, CLogger::LEVEL_ERROR, 'hitour.biz.order');

            return;
        }

        $main_op = $order_products[0];

        foreach ($order_products as $op) {
            $order_product_id = $op['order_product_id'];
            $product_id = $op['product_id'];

            //update redeem expire date
            $tour_date = $op['tour_date'];
            $redeem_expire_date = $op['redeem_expire_date'];

            $redeem_rule = HtProductRedeemRule::model()->findByPk($product_id);
            if ($redeem_expire_date == '0000-00-00') {
                if ($redeem_rule['redeem_type'] == HtProductRedeemRule::TOUR_DATE_ONLY) {
                    $redeem_expire_date = $tour_date;
                } else if ($redeem_rule['redeem_type'] == HtProductRedeemRule::ISSUED_DYNAMIC) {
                    $redeem_expire_date = date('Y-m-d', strtotime('+' . $redeem_rule['duration'] . '-1day'));
                } else if ($redeem_rule['redeem_type'] == HtProductRedeemRule::ABSOLUTE_EXPIRED_DATE) {
                    $redeem_expire_date = $redeem_rule['expire_date'];
                } else if ($redeem_rule['redeem_type'] == HtProductRedeemRule::TOUR_DATE_DURATION) {
                    $redeem_expire_date = date('Y-m-d', strtotime($redeem_rule['duration'], strtotime($tour_date)));
                } else {
                    $redeem_expire_date = $tour_date;
                }
            }
            HtOrderProduct::model()->updateByPk($order_product_id, ['redeem_expire_date' => $redeem_expire_date]);

            //order return expire date
            if ($main_op['order_product_id'] == $order_product_id) {
                $return_rule = HtProductReturnRule::model()->findByPk($main_op['product_id']);
                if ($return_rule['return_type'] == HtProductReturnRule::DONT_RETURN) {
                    $return_expire_date = '';
                } else if ($return_rule['return_type'] == HtProductReturnRule::BEFORE_REDEEM) {
                    $return_expire_date = date('Y-m-d',
                                               strtotime('-' . $return_rule['offset'], strtotime($redeem_expire_date)));
                } else if ($return_rule['return_type'] == HtProductReturnRule::BEFORE_TOUR_DATE) {
                    $return_expire_date = date('Y-m-d',
                                               strtotime('-' . $return_rule['offset'], strtotime($tour_date)));
                } else {
                    $return_expire_date = '';
                }

                HtOrderProduct::model()->updateByPk($order_product_id, ['return_expire_date' => $return_expire_date]);
            }
        }
    }

    public function afterUserPayed($order_id)
    {
        // TODO actions after user pay success

        // TODO check whether user used coupon
        $coupon_history = HtCouponHistory::model()->findByAttributes(array('order_id' => $order_id));
        if (!empty($coupon_history)) { // 本次订单用户使用优惠券了
            // TODO check whether coupon is get by other's share
            $coupon_id = $coupon_history['coupon_id'];

            $dandelion = HtDandelion::model()->findByAttributes(array('coupon_id' => $coupon_id));
            if (!empty($dandelion)) {
                Yii::log('用户通过朋友分享获得的优惠券，处理给分享者返现。', CLogger::LEVEL_INFO);
                $owner_id = $dandelion['owner_id'];
                // TODO return some fund to sharer if needed
                $return_or_not = $dandelion['return_or_not'];
                if ($return_or_not == 1) {
                    $max_return_count = $dandelion['max_return_count'];
                    // TODO check whether has already returned fund to that sharer
                    $fund_history_list = HtCustomerFundHistory::model()->findAllByAttributes(array('did' => $dandelion['did'], 'add_or_sub' => 1));
                    if ($max_return_count > count($fund_history_list)) {
                        $amount = $dandelion['return_amount'];
                        $fund_history = new HtCustomerFundHistory();
                        $fund_history['did'] = $dandelion['did'];
                        $fund_history['order_id'] = $order_id; //表示本次返利是从哪个订单贡献的
                        $fund_history['customer_id'] = $owner_id;
                        $fund_history['add_or_sub'] = 1;
                        $fund_history['amount'] = $dandelion['return_amount'];
                        $fund_history['add_date'] = date('Y-m-d H:i:s');
                        $fund_history['sub_date'] = '0000-00-00';
                        $fund_history['expire_date'] = $dandelion['fund_expire_date'];

                        $result = $fund_history->insert();
                        if ($result) {
                            $customer = HtCustomer::model()->findByPk($owner_id);
                            if (empty($customer)) {
                                Yii::log('获取分享者用户信息失败。customer_id: ' . $owner_id, CLogger::LEVEL_INFO,
                                         'hitour.service.order');

                                return;
                            }
                            $hitour_fund = $customer['hitour_fund'];
                            $customer['hitour_fund'] = $hitour_fund + $amount; //TODO:需要考虑基金过期的问题,基金总余额是不是需要每次动态计算？

                            $result = $customer->update();
                            if ($result) {
                                Yii::log('更新客户玩途旅行基金成功。当前基金：' . $customer['hitour_fund'], CLogger::LEVEL_INFO,
                                         'hitour.service.order');
                            } else {
                                Yii::log('更新客户玩途旅行基金失败。customer_id: ' . $customer['customer_id'], CLogger::LEVEL_ERROR,
                                         'hitour.service.order');
                            }
                        } else {
                            Yii::log('添加记录到 ht_customer_fund_history失败。order_id: ' . $order_id, CLogger::LEVEL_ERROR,
                                     'hitour.service.order');
                        }
                    }
                }

            }
        }
    }

    // Get passenger info for order edit, this method return whole info with all hidden info.
    public function getPassengerTotalInfo($order_id)
    {
        $passengerInfo = array();

        $data = HtOrder::model()->with('order_product.product')->findByPk($order_id);
        $order = Converter::convertModelToArray($data);
        if (!$order) {
            return $passengerInfo;
        }

//        $data = HtOrderPassengerBak::model()->findAll('order_id = ' . $order_id);
//        $passengerInfo['pax_data'] = Converter::convertModelToArray($data);
        $passengerInfo['pax_data'] = HtOrderPassenger::model()->findAllByOrder($order_id);
        //remove the same passenger info with different product.
        $current_passenger_id = 0;
        $diff_passenger = array();
        foreach ($passengerInfo['pax_data'] as $p) {
            if ($p['passenger_id'] != $current_passenger_id) {
                $current_passenger_id = $p['passenger_id'];
                array_push($diff_passenger, $p);
            }
        }
        $passengerInfo['pax_data'] = $diff_passenger;

        $data = HtTicketType::model()->findAll();
        foreach (Converter::convertModelToArray($data) as $ticket) {
            $passengerInfo['pax_ticket'][$ticket['ticket_id']]['cn_name'] = $ticket['cn_name'];
            $passengerInfo['pax_ticket'][$ticket['ticket_id']]['en_name'] = $ticket['en_name'];
        }

        if ($order['order_product']['product']['type'] == HtProduct::T_HOTEL_BUNDLE) {
            $passengerInfo = array_merge($passengerInfo,
                                         HtProductPassengerRule::model()->getPassengerTotalRule($order_id));


            $passengerInfo['pax_quantities']['1'] = count($passengerInfo['pax_data']);
        } else {
            $passengerInfo = array_merge($passengerInfo,
                                         HtProductPassengerRule::model()->getPassengerRule($order['order_product']['product_id']));
            $passengerInfo['pax_quantities'] = HtOrderProductPrice::model()->calcRealQuantities($order['order_product']['order_product_id'],
                                                                                                $order['order_product']['product_id']);
        }

        return $passengerInfo;
    }

    private function validateStock($order_data,&$result,$activity_id)
    {
        $result = array('code' => 200, 'msg' => 'OK');
        //validate stock
        $main_product = $order_data['products'][0];
        $product_id = $main_product['product_id'];
        $tour_date = isset($main_product['tour_date']) ? $main_product['tour_date'] : '';
        $purchase_quantity = array_sum($main_product['quantities']);

        //validate stock status
        $stock_info = HtProductSaleStock::model()->checkSaleStock($product_id, $tour_date, $purchase_quantity, $result, $activity_id);
        if ($stock_info) {
            $in_stock = HtProductSaleStock::model()->reduceStock($product_id, $tour_date, $purchase_quantity, $activity_id, $result);
            if (!$in_stock) {
                $result['code'] = 305;
                $result['msg'] = '抱歉，库存不足！';
            }
        }

        return $result;
    }

}
