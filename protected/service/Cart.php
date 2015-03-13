<?php

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/22/14
 * Time: 16:05 PM
 */
class Cart extends CComponent
{
    CONST KEY = 'cart_v2_15';

    public function init()
    {
        require_once('utility.php');
        return true;
    }

    /**
     * @param $products
     * @return array
     */
    public function addCart($products)
    {
        $result = ['code' => 200, 'msg' => 'ok'];

        Yii::app()->cart->clearCoupon('');

        $session = Yii::app()->session;
        $cart_data = $session->get(self::KEY, array());

        if (isset($products['product_id'])) {
            //为兼容之前的单品调用方式，开发过程中暂时保留
            $products = [$products];
        }

        foreach ($products as $product) {
            if (!isset($product['activity_id'])) {
                $product['activity_id'] = 0;
            }

            if (!isset($product['bundle_product_id'])) {
                $product['bundle_product_id'] = 0;
            }

            if (empty($product['special_code'])) {
                $product['special_code'] = '';
            }

            if (!isset($product['departure_code'])) {
                $product['departure_code'] = '';
            }

            if (!isset($product['departure_time'])) {
                $product['departure_time'] = '';
            }

            if ($product['bundle_product_id'] == 0) {//主商品,先清除
                $cart_data = array();
            }

            if (!isset($product['quantities'])) {
                $product['quantities'] = array();
            }

            if (!isset($product['child_list'])) {
                $product['child_list'] = array();
            }

            if (!$this->validate($product, $result)) {
                Yii::log('Add cart failed. session id=[' . $session->sessionID . ']' . 'cause:' . $result['msg'], CLogger::LEVEL_ERROR, 'hitour.biz.cart');
                $result['code'] = 400;
                return $result;
            } else {
                $cart_data[] = $product;
            }
        }
        $session[self::KEY] = $cart_data;

        return $result;
    }

    private function validate($prod, &$result)
    {
        //check product base
        if (!$prod) {
            $result['msg'] = '商品不能为空!';
            return false;
        }

        $product_id = $prod['product_id'];
        if (!$product_id) {
            $result['msg'] = '商品不存在！';
            return false;
        }

        if ($prod['bundle_product_id'] > 0) {
            if (!$this->contains($product_id['bundle_product_id'])) {
                $result['msg'] = '主商品不存在！';
                return false;
            }
        }

        //如果主商品未上架，不能购买
        $product_model = HtProduct::model()->with('description')->findByPk($product_id);
        $product_name = $product_model['description']['name'];
        if ($product_model['status'] != HtProduct::IN_SALE && 0 == $prod['bundle_product_id'] && Yii::app()->params['PAYMENT_REALLY'] == 1) {
            $result['msg'] = '抱歉，该商品已下架！';
            return false;
        }

        //tour_date
        $date_rule = HtProductDateRule::model()->findByPk($product_id);
        if ($date_rule['need_tour_date']) {
            if (empty($prod['tour_date'])) {
                $result['msg'] = $product_name . $product_model['description']['tour_date_title'] . ' 不能为空！';
                return false;
            } else if ($prod ['tour_date'] < date('Y-m-d')) {//@TODO：需要更严格的控制
                $result['msg'] = $product_name . $product_model['description']['tour_date_title'] . ' 必须在今天之后!';
                return false;
            }
        }

        //special_code
        $need_special = HtProductSpecialCombo::model()->needSpecialCode($product_id);
        if ($need_special && empty($prod['special_code'])) {
            $result['msg'] = $product_name . $product_model['description']['special_title'] . ' 不能为空！';
            return false;
        }

        //departure
        $need_departure = HtProductDeparturePlan::model()->needDeparture($product_id);
        if ($need_departure && empty($prod['departure_code'])) {
            $result['msg'] = $product_name . $product_model['description']['departure_title'] . ' 不能为空！';
            return false;
        }

        //check activity
        if (!empty($prod['activity_id'])) {
            $activity_id = $prod['activity_id'];
            $activity_result = Yii::app()->activity->checkActivity($product_id, $activity_id);
            if ($activity_result['code'] != 200) {
                $this->clearCart();
                $result['msg'] = $activity_result['msg'];
                return false;
            }

            $activity_rule = $activity_result['activity_rule'];
            if ($activity_rule && !empty($activity_rule['coupon'])) {
                $coupon_result = $this->addCoupon($activity_rule['coupon']['code'], $activity_rule['activity_coupon_title']);
                if ($coupon_result['code'] != 200) {
                    $this->clearCart();
                    $result['msg'] = '参加活动失败，请您联系我们！';
                    Yii::log('购买活动产品时，内置优惠券失败！activity_id=' . $activity_id . ',product_id=' . $product_id . ',coupon_code=' . $activity_rule['coupon']['code'],
                        CLogger::LEVEL_ERROR, 'hitour.service.cart');
                    return false;
                }
            }
        }


        return true;
    }

    /**
     * @param $product_id
     * @return bool whether there is the product with product_id
     */
    private function contains($product_id)
    {
        $session = Yii::app()->session;
        $cart_data = $session->get(self::KEY, array());

        if (!is_array($cart_data)) {
            Yii::log('Cart SHOULD be array');
            return false;
        }

        foreach ($cart_data as $product) {
            if ($product['product_id'] == $product_id) {
                return true;
            }
        }

        return false;
    }

    public function clearCart()
    {
        unset(Yii::app()->session[self::KEY]);
        unset(Yii::app()->session['coupon']);
    }

    public function addCoupon($coupon_code, $coupon_title = '使用优惠券')
    {
        $session = Yii::app()->session;
        //get coupon
        $cart_data = $session->get(self::KEY, array());

        $tmp_data['products'] = $cart_data;
        $result = array();
        Yii::app()->order->checkPrices($tmp_data,$result);

        $main_product = $tmp_data['products'][0];

        $coupon_result = HtCoupon::model()->validateCoupon($coupon_code, $main_product);
        if ($coupon_result['code'] == 200) {
            $coupon = $coupon_result['data'];
            $coupon['title'] = $coupon_title;
            $session['coupon'] = $coupon;
        } else {
            unset($session['coupon']);
            Yii::log('Add coupon failed.' . Yii::app()->session->sessionID . ']' . $coupon_result['msg'], CLogger::LEVEL_ERROR, 'hitour.biz.cart');
        }

        return $coupon_result;
    }

    public function getCartForOrder()
    {
        $cart_data = Yii::app()->session[self::KEY];
        $data = Yii::app()->product->getProductsForAddOrder($cart_data);

        return $data;
    }

    public function clearCoupon($coupon_code)
    {
        $session = Yii::app()->session;
        if (!isset($session['coupon']['code']) || $session['coupon']['code'] != $coupon_code) {
            Yii::log('Clear coupon warning!' . $coupon_code . ' not found.', CLogger::LEVEL_WARNING, 'hitour.biz.cart');
        }

        unset($session['coupon']);
        return ['code' => 200, 'msg' => 'OK'];
    }

    public function getProductForCartData()
    {
        $products = $this->getProductsForCartData();
        return array_shift($products);
    }

    private function getProductsForCartData()
    {
        $products = array();
        foreach (Yii::app()->session[self::KEY] as $p) {
            $products[] = $this->buildProductInfoForCartData($p);
        }

        return $products;
    }

    private function buildProductInfoForCartData($cart_prod)
    {
        $data['raw_data'] = $cart_prod;

        $product_id = $cart_prod['product_id'];
        $prod = Converter::convertModelToArray(HtProduct::model()->with('description')->findByPk($product_id));
        $data['product']['name'] = $prod['description']['name'];
        $product_image = HtProductImage::model()->findByAttributes(['product_id' => $product_id, 'as_cover' => 1]);
        $data['product']['cover_image_url'] = $product_image['image_url'];
        $data['product']['tour_date_title'] = $prod['description']['tour_date_title'];
//        $data['product']['special_title'] = $prod['description']['special_title'];
        $data['product']['departure_title'] = $prod['description']['departure_title'];


        //special code
        $special_product_id = 0;
        if (!empty($cart_prod['special_code'])) {
//            $special_code_model = Converter::convertModelToArray(HtProductSpecialCode::model()->findByPk(['product_id' => $product_id, 'special_code' => $cart_prod['special_code']]));
//            $data['raw_data']['special_name'] = $special_code_model['cn_name'];
            $special_info = HtProductSpecialCombo::model()->getSpecialDetail($product_id,$cart_prod['special_code']);
            $data['product']['special_info'] = $special_info;
            $special_product_id = $special_info[0]['items'][0]['mapping_product_id'];
        }

        //Todo
        $tour_date = !empty($cart_prod['tour_date']) ? $cart_prod['tour_date'] : date('Y-m-d');
        $sale_date = date('Y-m-d');
        $price_plan = HtProductPricePlan::model()->getPricePlan($product_id, $tour_date, $sale_date);
        $price_plan = $price_plan[0];
        $special_code = $cart_prod['special_code'];
        $sub_total = 0;
        $orig_total = 0;
        $need_tier_pricing = $price_plan['need_tier_pricing'];
        foreach ($data['raw_data']['quantities'] as $ticket_id => $qty) {
            $price = 99999;
            $orig_price = 99999;
            foreach ($price_plan['items'] as $pi) {
                if ($pi['ticket_id'] == $ticket_id && $pi['special_code'] == $special_code && (!$need_tier_pricing && $pi['quantity'] == 1 || $need_tier_pricing && $pi['quantity'] == $qty)) {
                    $price = $pi['price'];
                    $orig_price = $pi['orig_price'];
                }
            }
            $sub_total += $price * $qty;
            $orig_total += $orig_price * $qty;
        }
        $data['product']['sub_total'] = $sub_total;
        $data['product']['orig_total'] = $orig_total;

        if ($prod['type'] == HtProduct::T_HOTEL_BUNDLE) {
            $data['bundles'] = $this->getBundlesForCartData($cart_prod, $special_product_id);
        }

        return $data;

//        $product_id = $cart_prod['product_id'];
//        $prod = Converter::convertModelToArray(HtProduct::model()->with('description', 'cover_image')->findByPk($product_id));
//        $data['description'] = $prod;
//        $data['cover_image'] = $prod['cover_image'];
//        unset($prod['description']);
//        unset($prod['cover_image']);
//        $data['base_info'] = $prod;
//
//        //Todo
//        $tour_date = !empty($cart_prod['tour_date']) ? $cart_prod['tour_date'] : date('Y-m-d');
//        $sale_date = date('Y-m-d');
//        $price_plan = HtProductPricePlan::model()->getPricePlan($product_id, $tour_date, $sale_date);
//        $price_plan = $price_plan[0];
//        $special_code = $cart_prod['special_code'];
//        $sub_total = 0;
//        $need_tier_pricing = $price_plan['need_tier_pricing'];
//        foreach ($data['raw_data']['quantities'] as $ticket_id => $qty) {
//            $price = 99999;
//            foreach ($price_plan['items'] as $pi) {
//                if ($pi['ticket_id'] == $ticket_id && $pi['special_code'] == $special_code && (!$need_tier_pricing && $pi['quantity'] == 1 || $need_tier_pricing && $pi['quantity'] == $qty)) {
//                    $price = $pi['price'];
//                }
//            }
//            $sub_total += $price * $qty;
//        }
//        $data['sub_total'] = $sub_total;
//
//        //special code
//        $special_product_id = 0;
//        if (!empty($cart_prod['special_code'])) {
//            $special_code_model = Converter::convertModelToArray(HtProductSpecialCode::model()->findByPk(['product_id' => $product_id, 'special_code' => $cart_prod['special_code']]));
//            $data['special'] = $special_code_model;
//            $special_product_id = $special_code_model['mapping_product_id'];
//        }
//
//        if ($prod['type'] == HtProduct::T_HOTEL_BUNDLE) {
//            $data['bundles'] = $this->getBundlesForCartData($cart_prod, $special_product_id);
//        }
//
//        return $data;
    }

    private function getBundlesForCartData($cart_prod, $special_product_id = 0)
    {
        $product_id = $cart_prod['product_id'];
        $bundles = Converter::convertModelToArray(HtProductBundle::model()->with('items')->findAllByAttributes(['product_id' => $product_id]));
        if (!empty($bundles)) {
            foreach ($bundles as &$b) {
                $products = array();
                foreach ($b['items'] as $bi) {
                    if ($b['group_type'] == HtProductBundle::GT_SELECTION && !empty($special_product_id)) {
                        if ($bi['binding_product_id'] != $special_product_id) {
                            continue;
                        }
                    }

                    $p = HtProduct::model()->with('description', 'cover_image')->findByPk($bi['binding_product_id']);
                    $p = Converter::convertModelToArray($p);
                    $p['description']['service_include'] = Converter::parseMdHtml($p['description']['service_include']);
//                    $p['special_codes'] = Converter::convertModelToArray(HtProductSpecialCode::model()->findAllByAttributes(['product_id' => $bi['binding_product_id'], 'status' => 1]));
                    $p['special_info'] = Yii::app()->product->getSpecialCodes($bi['binding_product_id']);

                    $p['date_rule'] = Yii::app()->product->getDateRule($bi['binding_product_id']);
                    $p['sale_rule'] = Yii::app()->product->getSaleRule($bi['binding_product_id']);
                    $p['ticket_types'] = Yii::app()->product->getTicketTypes($bi['binding_product_id']);
                    $p['departure_rule'] = Yii::app()->product->getDepartureRule($bi['binding_product_id']);
                    $p['price_plan'] = HtProductPricePlan::model()->getPricePlanWithMap($bi['binding_product_id']);
                    $p['bundle_product_id'] = $product_id;
                    if ($b['group_type'] == HtProductBundle::GT_OPTIONAL) {
                        $p['show_prices'] = HtProductPricePlan::model()->getShowPrices($bi['binding_product_id']);
                    }
                    $products[] = $p;
                }
                $b['products'] = $products;
            }
        }

        return $bundles;
    }

    public function getProductForCheckoutData()
    {
        $products = $this->getProductsForCheckoutData();
        return array_shift($products);
    }

    private function getProductsForCheckoutData()
    {
        $products = array();
        if (!empty(Yii::app()->session[self::KEY])) {
            foreach (Yii::app()->session[self::KEY] as $p) {
                $products[] = $this->buildProductInfoForCheckoutData($p);
            }
        }
        return $products;
    }

    private function buildProductInfoForCheckoutData($cart_prod)
    {
        $data['raw_data'] = $cart_prod;
        if(!empty($data['raw_data']['special_code'])){
//            $data['raw_data']['special'] = Converter::convertModelToArray(HtProductSpecialCode::model()->findByAttributes(['product_id'=>$data['raw_data']['product_id'],'special_code'=>$data['raw_data']['special_code']]));
            $data['raw_data']['special'] = HtProductSpecialCombo::getSpecialDetail($data['raw_data']['product_id'],$data['raw_data']['special_code']);
        }

        $product_id = $cart_prod['product_id'];
        $prod = Converter::convertModelToArray(HtProduct::model()->with('description')->findByPk($product_id));
        $data['product']['name'] = $prod['description']['name'];
        $data['product']['tour_date_title'] = $prod['description']['tour_date_title'];
//        $data['product']['special_title'] = $prod['description']['special_title'];
        $data['product']['departure_title'] = $prod['description']['departure_title'];
//        $data['product']['ticket_types'] = Yii::app()->product->getTicketTypes($product_id);

        //ticket types
        $ticket_types = Yii::app()->product->getTicketTypes($product_id);
        $sale_rule = Yii::app()->product->getSaleRule($product_id);

        if (!empty($sale_rule['sale_in_package']) && $pr = $sale_rule['package_rules']) {
            $ticket_type_package = array();
            foreach ($ticket_types as $tt) {
                if ($tt['ticket_id'] == HtTicketType::TYPE_PACKAGE) {
                    $ticket_type_package = $tt;
                    break;
                }
            }
            $ticket_type_package['package_rule'] = $pr;
            $ticket_types = [HtTicketType::TYPE_PACKAGE => $ticket_type_package];
        }
        unset($sale_rule['package_rules']);

        $data['product']['ticket_types'] = $ticket_types;
        $data['product']['sale_rule'] = $sale_rule;

        $passenger_rule = HtProductPassengerRule::model()->getPassengerRule($product_id);
        $data['product']['pax_rule'] = $passenger_rule['pax_rule'];
        $data['product']['pax_meta'] = $passenger_rule['pax_meta'];
        $data['product']['rule_desc'] = Yii::app()->product->getRuleDesc($product_id);
        $data['product']['sub_total'] = array();
        $data['product']['orig_sub_total'] = array();


        //special code
        $special_product_id = 0;
        if (!empty($cart_prod['special_code'])) {
//            $special_model = Converter::convertModelToArray(HtProductSpecialCode::model()->findByPk(['product_id' => $product_id, 'special_code' => $cart_prod['special_code']]));
//            $data['raw_data']['special_name'] = $special_model['cn_name'];
            $special_info = HtProductSpecialCombo::getSpecialDetail($product_id,$cart_prod['special_code']);

            $special_product_id = $special_info[0]['items'][0]['mapping_product_id'];
        }

        //departure point
        if (!empty($cart_prod['departure_code'])) {
            $departure_model = Converter::convertModelToArray(HtProductDeparture::model()->findByPk(['product_id' => $product_id, 'departure_code' => $cart_prod['departure_code'], 'language_id' => 2]));
            $data['raw_data']['departure_point'] = $departure_model['departure_point'];
        }

        //Todo
        $tour_date = !empty($cart_prod['tour_date']) ? $cart_prod['tour_date'] : date('Y-m-d');
        $sale_date = date('Y-m-d');
        $price_plan = HtProductPricePlan::model()->getPricePlan($product_id, $tour_date, $sale_date);
        $price_plan = $price_plan[0];
        $special_code = $cart_prod['special_code'];
        $sub_total = 0;
        $orig_sub_total = 0;
        $need_tier_pricing = $price_plan['need_tier_pricing'];
        foreach ($data['raw_data']['quantities'] as $ticket_id => $qty) {
            $price = 99999;
            $orig_price = 99999;
            foreach ($price_plan['items'] as $pi) {
                if ($pi['ticket_id'] == $ticket_id && $pi['special_code'] == $special_code && (!$need_tier_pricing && $pi['quantity'] == 1 || $need_tier_pricing && $pi['quantity'] == $qty)) {
                    $price = $pi['price'];
                    $orig_price = $pi['orig_price'];
                }
            }
            $sub_total += $price * $qty;
            $orig_sub_total += $orig_price * $qty;
        }
        $data['product']['sub_total'] = $sub_total;
        $data['product']['orig_sub_total'] = $orig_sub_total;

        if ($prod['type'] == HtProduct::T_HOTEL_BUNDLE) {
            $data['bundles'] = $this->getBundlesForCheckoutData($cart_prod,$special_product_id);
        }

        return $data;
    }

    private function getBundlesForCheckoutData($cart_prod,$special_product_id)
    {
        $products = array();
        $bundle_parent_id = $cart_prod['product_id'];
        $bundles = Converter::convertModelToArray(HtProductBundle::model()->with('items')->findAllByAttributes(['product_id' => $bundle_parent_id]));
        foreach ($bundles as &$b) {
            foreach ($b['items'] as $bi) {
                $p = array();
//                if ($b['group_type'] == HtProductBundle::GT_SELECTION) {
//                    continue;
//                }
                /*if ($b['group_type'] == HtProductBundle::GT_SELECTION && !empty($special_product_id)) {
                    if ($bi['binding_product_id'] != $special_product_id) {
                        continue;//多选1商品不是当前选中的情况
                    }
                }
                else if ($b['group_type'] == HtProductBundle::GT_OPTIONAL &&!$this->containsAppend($bi['binding_product_id'])) {
//                    if (!$this->containsAppend($bi['binding_product_id'])) {
                        continue;//可选商品没有被选中的情况
//                    }
                }else {*/
                    $product_id = $bi['binding_product_id'];
                    $prod = Converter::convertModelToArray(HtProduct::model()->with('description')->findByPk($product_id));
                    $p['product_id'] = $product_id;
                    $p['name'] = $prod['description']['name'];
                    if (!empty($cart_prod['append'])) {
                        foreach ($cart_prod['append'] as $append) {
                            if ($append['product_id'] == $product_id) {
                                if (isset($append['special_code'])) {
                                    $p['special_code'] = $append['special_code'];
                                }
                            }
                        }
                    }

                    $p['tour_date_title'] = $prod['description']['tour_date_title'];
//                    $p['special_title'] = $prod['description']['special_title'];
                    $p['departure_title'] = $prod['description']['departure_title'];
                    $p['ticket_types'] = Yii::app()->product->getTicketTypes($product_id);
                    $p['date_rule'] = Yii::app()->product->getDateRule($product_id);
                    $p['sale_rule'] = Converter::convertModelToArray(HtProductSaleRule::model()->findByPk($product_id));
                    $p['pax_rule'] = $this->getPaxRuleForBundle($product_id);

                    $p['special_info'] = Yii::app()->product->getSpecialCodes($product_id);
                    $p['departure_rule'] = Yii::app()->product->getDepartureRule($product_id);
                    $p['price_plan'] = HtProductPricePlan::model()->getPricePlanWithMap($bi['binding_product_id']);
                    $p['cover_image'] = Yii::app()->product->getImages($product_id);
                    $p['cover_image'] = $p['cover_image']['cover'];

                    $p['bundle_info'] = $bi;
                    $p['bundle_info']['group_type'] = $b['group_type'];
                    if ($b['group_type'] != HtProductBundle::GT_OPTIONAL) {
                        $p['bundle_product_id'] = $bundle_parent_id;
                    } else {
                        $p['bundle_product_id'] = 0;
                    }
                    $p['bundle_product_id'] = $bundle_parent_id;

                    $products[] = $p;
                //}
            }
        }

        return $products;
    }

    private function containsAppend($append_product_id)
    {
        $session = Yii::app()->session;
        $cart_data = $session->get(self::KEY, array());

        if (!is_array($cart_data)) {
            Yii::log('Cart SHOULD be array');
            return false;
        }

        foreach ($cart_data as $product) {
            if (empty($product['append'])) {
                continue;
            }
            foreach ($product['append'] as $ap) {
                if ($ap['product_id'] == $append_product_id) {
                    return true;
                }
            }
        }

        return false;
    }

    public function appendCart($products)
    {
        $result = ['code' => 200, 'msg' => 'ok'];
        $cart_data = Yii::app()->session[self::KEY];
        if (empty($cart_data)) {
            $result = ['code' => 401, 'msg' => '没有选择主套餐商品，不能配套选购！'];

        } else {
            $cart_data[0]['append'] = $products;
            Yii::app()->session[self::KEY] = $cart_data;
        }
        return $result;
    }

    private function reOrgProductInfo($cart_product, $with_cost = '')
    {
        $data = array();

        $product_id = $cart_product['product_id'];
        $bundle_product_id = isset($cart_product['bundle_product_id']) ? $cart_product['bundle_product_id'] : 0;
        $special_code = isset($cart_product['special_code']) ? $cart_product['special_code'] : '';
        $tour_date = isset($cart_product['tour_date']) ? $cart_product['tour_date'] : '0000-00-00';
        $dc = isset($cart_product['departure_code']) ? $cart_product['departure_code'] : '';
        $dt = isset($cart_product['departure_time']) ? $cart_product['departure_time'] : '';

        //product
        $product = HtProduct::model()->with('description', 'city.country', 'cover_image')->findByPk($product_id);
        $data['product'] = Converter::convertModelToArray($product);
        $data['product']['image'] = Yii::app()->product->getImages($product_id);
        $data['bundle_product_id'] = $bundle_product_id;

        //rules
        $data['rules'] = Yii::app()->product->getRuleDesc($product_id);

        //tour date
        $data['tour_date'] = $tour_date;

        //special code
        if ($special_code) {
            $special_code_model = HtProductSpecialCode::model()->findByPk(['product_id' => $product_id, 'special_code' => $special_code]);
            $data['special'] = Converter::convertModelToArray($special_code_model);
        }

        //departure code & time
        if ($dc || $dt) {
            $cond = 'valid_region = 0 OR :tour_date BETWEEN from_date AND to_date';
            $params = [':tour_date' => $data['tour_date'] ? $data['tour_date'] : date('Y-m-d')];
            $dep_plan = HtProductDeparturePlan::model()->with('departure')->findByAttributes(['product_id' => $product_id, 'departure_code' => $dc, 'time' => $dt], $cond, $params);
            $data['departure'] = Converter::convertModelToArray($dep_plan);
        }

        //ticket types
        $ticket_types = Yii::app()->product->getTicketTypes($product_id);
        $sale_rule = Yii::app()->product->getSaleRule($product_id);

        if (!empty($sale_rule['sale_in_package']) && $pr = $sale_rule['package_rules']) {
            $ticket_type_package = array();
            foreach ($ticket_types as $tt) {
                if ($tt['ticket_id'] == HtTicketType::TYPE_PACKAGE) {
                    $ticket_type_package = $tt;
                    break;
                }
            }
            $ticket_type_package['package_rule'] = $pr;
            $ticket_types = [HtTicketType::TYPE_PACKAGE => $ticket_type_package];
        }
        unset($sale_rule['package_rules']);

        $data['ticket_types'] = $ticket_types;
        $data['sale_rule'] = $sale_rule;

        //quantities
        $quantities = array();
        $costs = array();
        $product_total = 0;
        $sub_total = 0;
        $tour_date = $data['tour_date'] ? $data['tour_date'] : date('Y-m-d');
        $price_plan = HtProductPricePlan::model()->getPricePlanWithMap($product_id, $tour_date);
        $price_map = array();
        foreach ($price_plan as $pp) {
            if ($pp['valid_region'] == 0 || ($pp['from_date'] <= $tour_date && $pp['to_date'] >= $tour_date)) {
                $price_map = $pp['price_map'];
                break;
            }
        }
        if (empty($price_map)) {
            $price_map = $price_plan[0]['price_map'];
        }


        $sk = $special_code;
        $org_quantities = $cart_product['quantities'];
        foreach ($org_quantities as $ticket_id => $qn) {
            if (isset($price_map[$sk][$ticket_id][$qn])) {
                $price = $price_map[$sk][$ticket_id][$qn]['price'];
                $cost_price = $price_map[$sk][$ticket_id][$qn]['cost_price'];
            } else if (isset($price_map[$sk][$ticket_id]['1'])) {
                $price = $price_map[$sk][$ticket_id]['1']['price'];
                $cost_price = $price_map[$sk][$ticket_id]['1']['cost_price'];
            } else {
                $price = 9999;
                $cost_price = 9999;
                Yii::log('Price Error:[product_id:' . $product_id . ',date:' . $tour_date . ',special_code' . $sk . ',ticket_id:' . $ticket_id . ',quantity:' . $qn . ']', CLogger::LEVEL_ERROR, 'hitour.service.cart');
                return false;
            }
            //price
            $quantities[] = ['ticket_id' => $ticket_id, 'quantity' => $qn, 'price' => $price];
            $costs[] = ['ticket_id' => $ticket_id, 'quantity' => $qn, 'price' => $price, 'cost_price' => $cost_price];
            $product_total += $qn;
            $sub_total += $qn * $price;
        }
        $data['quantities'] = $quantities;
        if ($with_cost) {
            $data['costs'] = $costs;
        }
        $data['product_total'] = $product_total;
        $data['sub_total'] = $sub_total;

        //pax rule
        $data = array_merge($data, HtProductPassengerRule::model()->getPassengerRule($product_id));

        $activity_id = isset($product['activity_id']) ? $product['activity_id'] : 0;
        $data['payment_methods'] = Yii::app()->activity->getPaymentMethods($activity_id);

        return $data;
    }

    private function getCouponTotal(&$data)
    {
        $coupon_info = $this->getCoupon();
        $sub_total = $data['sub_total'];
        $coupon_total = 0.0;
        if ($coupon_info) {
            $data['coupon'] = $this->refineCouponInfo($coupon_info);
            if ($coupon_info['type'] == HtCoupon::T_FUND) {
                $coupon_total = (int)(min($sub_total, $coupon_info['discount']));
            } else if ($coupon_info['type'] == HtCoupon::T_PERCENT) {
                $coupon_total = round($sub_total / 100 * floatval($coupon_info['discount']));
            }
        }

        $data['coupon_total'] = $coupon_total > $sub_total ? $sub_total : $coupon_total;
        $data['coupon_title'] = empty($coupon_info['title']) ? '' : $coupon_info['title'];
        $data['total'] = $sub_total - $data['coupon_total'];
    }

    public function getCoupon()
    {
        $session = Yii::app()->session;
        $coupon = $session->get('coupon', array());
        return $coupon;
    }

    private function refineCouponInfo($coupon_info)
    {
        return array_filter_by_keys($coupon_info, ['code']);
    }

    private function getPaxRuleForBundle($product_id)
    {
        $result = ['lead_fields'=>[],'other_fields'=>[]];
        $rule = Converter::convertModelToArray(HtProductPassengerRule::model()->with('items')->findByPk($product_id));

        if(!empty($rule)){
            $result['lead_fields'] = array_filter(explode(',',$rule['lead_fields']));
            foreach ($rule['items'] as $item) {
                $result['other_fields'] = array_merge($result['other_fields'],array_filter(explode(',',$item['fields'])));
            }
            $result['other_fields'] = array_values(array_unique($result['other_fields']));
        }

        return $result;
    }
}