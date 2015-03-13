<?php

class MobileController extends MController
{
    public $resource_refs;

    //Pages
    public function actionIndex()
    {
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                /* Home Page */
                'getHomeActivity' => $this->createUrl('home/activities'),
                'getHomeRecommend' => $this->createUrl('home/recommend'),
                'getHomeStat' => $this->createUrl('mobile/homeStat'),

                /* City Page */
                'getAllGroups' => $this->createUrl('city/mobileCityInfo', ['city_code' => '']),
                'getOneGroup' => $this->createUrl('city/mobileCityInfo', ['city_code' => 'XXX', 'type' => 'group', 'id' => '']),
                'getOneTag' => $this->createUrl('city/mobileCityInfo', ['city_code' => 'XXX', 'type' => 'tag', 'id' => '']),
//                'getGroupsTree' => $this->createUrl('city/mobileGroupsTree', ['city_code' => '']),
//                'getTagsTree' => $this->createUrl('city/mobileTagsTree', ['city_code' => '']),
//                'getCityTrees' => $this->createUrl('city/mobileCityTrees', ['city_code' => '']),
//                'getTagOrGroupProducts' => $this->createUrl('city/mobileTagOrGroupProducts', ['city_code' => '']),

//                /* Group Page */
                'getHomeGroup' => $this->createUrl('mobile/recommendGroup', array('group_id' => '')),
                'getCityGroup' => $this->createUrl('mobile/productGroup', array('group_id' => '')),

                /* Favorite */
                'getFavoriteProducts' => $this->createUrl('account/getFavoriteProducts'),
                /* Promotion */
                'getPromotionDetail' => $this->createUrl('promotion/promotionDetail', ['promotion_id' => '']),
                /* Article */
                'getArticleDetail' => $this->createUrl('column/articleDetail', ['article_id' => '']),
                'getHotelPlus' => $this->createUrl('promotion/hotelplusdata'),
                /* Login/Register/Reset */
                'getCoupons' => $this->createUrl('account/getCoupons'),
                'loginUser' => $this->createUrl('account/login'),
                'logoutUser' => $this->createUrl('account/logout'),
                'registerUser' => $this->createUrl('account/register'),
                'resetPassword' => $this->createUrl('account/resetPassword'),
                'verifyPhone' => $this->createUrl('mobile/verifyPhone'),
                'registerByPhone' => $this->createUrl('account/registerByPhone'),
                /* Product Page */
                'getProductInfo' => $this->createUrl('product/mobileProductData', ['product_id' => '0000','spc'=>$this->getParam('spc',null)]),
                'checkoutLink' => $this->createUrl('mobile/checkout', ['product_id' => '0000','spc'=>$this->getParam('spc',null)]),
                'cartLink' => $this->createUrl('mobile/cart',
                                               array('product_id' => '0000', 'spc' => $this->getParam('spc', null))),
                'getComments' => $this->createUrl('product/mobileCommentsData'),
                'getBindingProduct' => $this->createUrl('product/bindingProduct'),
                /* Orders Page */
                'getOrders' => $this->createUrl('mobile/orders'),
                /* Activity Page */
                'getSaleData' => $this->createUrl('activity/summerSaleData'),
                'getFridaySaleData' => $this->createUrl('activity/fridaysaledata'),
                'getFridaySaleDetailData' => $this->createUrl('activity/fridaysaledetaildata'),
                'getKidAdultData' => $this->createUrl('activity/kidadultdata'),
                'getDouble11Data' => $this->createUrl('activity/double11data'),
                'getDouble12Data' => $this->createUrl('activity/double12data'),
                'getShoppingData' => $this->createUrl('activity/shoppingdata'),
                'getCcbSaleData' => $this->createUrl('activity/ccbsaledata'),
                'getNewYearSaleData' => $this->createUrl('activity/flashsaledata'),
                'getETravelData' => $this->createUrl('activity/etraveldata'),

                /* Country */
                'countryTabs' => $this->createUrl('country/mobileCountryTabs', ['country_code' => '']),
            )
        );
        $this->resource_refs = 'common';

        $this->render('main', array());
    }

    public function actionLogout()
    {
        Yii::app()->customer->logout();
        EchoUtility::echoMsgTF(true, '', '');
    }

    public function actionCart()
    {
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'product_id' => $this->getParam('product_id'),
                'addCart' => $this->createUrl('checkout/addCart'),
                'cartData' => $this->createUrl('checkout/cartData'),
                'appendCart' => $this->createUrl('checkout/appendCart'),
                'productData' => $this->createUrl('product/productData', array(
                    'spc' => $this->getParam('spc', null),
                    'product_id' => $this->getParam('product_id')
                )),
                'checkoutData' => $this->createUrl('checkout/checkoutData'),
                'checkoutLink' => $this->createUrl('mobile/checkout', array(
                    'spc' => $this->getParam('spc', null),
                    'product_id' => $this->getParam('product_id')
                )),
            )
        );
        $this->resource_refs = 'cart';
        $this->render('cart');
    }

    public function actionCheckout()
    {
        $product_id = (int)$this->getParam('product_id');
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'addCart' => $this->createUrl('product/addCart'),
                'validateCoupon' => $this->createUrl('checkout/validateCoupon'),
                'clearCoupon' => $this->createUrl('checkout/clearCoupon'),
                'customerInfo' => $this->createUrl('account/customerInfo'),
                'addOrder' => $this->createUrl('checkout/addOrder'),
                'checkoutData' => $this->createUrl('checkout/checkoutData',
                                                   array('product_id' => $product_id, 'spc' => $this->getParam('spc'))),
            )
        );
        $this->resource_refs = 'checkout';
        $this->render('checkout', array());
    }

    public function actionResult()
    {
        $this->resource_refs = 'result';
        $trade_info = PayUtility::parseOutTradeNo($this->getParam('out_trade_no'));
        $order_id = $trade_info['order_id'] ? (int)$trade_info['order_id'] : (int)$this->getParam('order_id');

        $this->render('result', array(
            'result' => HtOrderProduct::model()->getShippingDesc($order_id)
        ));
    }

    public function actionFundUser()
    {
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'loginUser' => $this->createUrl('account/loginToGetCoupon'),
                'emailClaimCoupon' => $this->createUrl('account/pickupCoupon'),
                'phoneClaimCoupon' => $this->createUrl('account/phoneToGetCoupon'),
                'verifyPhone' => $this->createUrl('mobile/verifyPhone')
            )
        );

        $this->resource_refs = 'funduser';
        $this->render('funduser');
    }

    public function actionRecommendGroup()
    {
        $recommend_group_id = (int)$this->getParam('group_id');

        $rg = HtHomeRecommend::model()->findByPkWithItemsCityCached($recommend_group_id);
        if (!$rg) {
            EchoUtility::echoCommonFailed('分组不存在', 404);

            return;
        }

        if ($rg['type'] == HtHomeRecommend::TYPE_PRODUCT) {
            foreach ($rg['items'] as $k => &$p) {
                $p = Yii::app()->product->getSimplifiedData($p['product_id']);
                $aty = Yii::app()->activity->getActivityInfo($p['product_id']);
                if (!empty($aty)) {
                    if ($aty['status'] != HtActivity::AS_ONGOING) {
                        unset($rg['products'][$k]);
                        continue;
                    } else {
                        $p['activity_info'] = $aty;
                    }
                }
            }
        }

        EchoUtility::echoJson($rg);
    }

    public function actionProductGroup()
    {
        $product_group_id = (int)$this->getParam('group_id');

        $pg_raw = HtProductGroup::model()->with('products.description',
                                                'products.cover_image')->published()->findByPk($product_group_id);
        $pg = Converter::convertModelToArray($pg_raw);

        if (!$pg) {
            EchoUtility::echoCommonFailed('分组不存在', 404);

            return;
        }

        $city = HtCity::model()->with('country')->findByPk($pg['city_code']);
        $pg['city'] = Converter::convertModelToArray($city);
        $seo = HtSeoSetting::model()->findByGroupCode($product_group_id);
        $pg['seo'] = Converter::convertModelToArray($seo);

        foreach ($pg['products'] as $k => &$p) {
            $p = Yii::app()->product->getSimplifiedData($p['product_id']);
            $aty = Yii::app()->activity->getActivityInfo($p['product_id']);
            if (!empty($aty)) {
                if ($aty['status'] != HtActivity::AS_ONGOING) {
                    unset($pg['products'][$k]);
                    continue;
                } else {
                    $p['activity_info'] = $aty;
                }
            }
        }
        $pg['products'] = array_values($pg['products']);

        EchoUtility::echoJson($pg);
    }

    public function actionHomeStat()
    {
        $sql = 'SELECT count(p.product_id) product_num,count(DISTINCT city.city_code) city_num,count(DISTINCT country.continent_id) continent_num FROM ht_product p LEFT JOIN ht_city city ON p.city_code = city.city_code LEFT JOIN ht_country country ON city.country_code = country.country_code WHERE p.status = "' . HtProduct::IN_SALE . '"';
        $command = Yii::app()->db->createCommand($sql);
        $row = $command->queryRow();

        $seo_setting = HtSeoSetting::model()->findHomeSeoSetting();
        $row['seo'] = $seo_setting;

        EchoUtility::echoJson($row);
    }

    public function actionOrders()
    {
        if (!Yii::app()->customer->isLogged()) {
            EchoUtility::echoCommonFailed('您还未登录!');

            return;
        }

        $customer_id = Yii::app()->customer->customerId;
        $criteria = new CDbCriteria();
        $criteria->addCondition('o.status_id !=' . HtOrderStatus::ORDER_CANCELED);
        $criteria->limit = 30;
        $orders = HtOrder::model()->findAllByAttributes(['customer_id' => $customer_id],
                                                        $criteria); //TODO: limit for test

        $data = array();
        foreach ($orders as $o) {
            $data[] = $this->getOrderDetail($o['order_id']);
        }

        EchoUtility::echoJson($data);
    }

    public function actionProduct()
    {
        $this->redirect($this->createUrl('mobile#/product/' . (int)$_REQUEST['product_id']), true, 301);
    }

    public function actionVerifyPhone()
    {
        $phone_no = (int)$this->getParam('phone_no');
        $verify_code = $this->getParam('verify_code', '');
        $sms_service = new SmsVerify();

        //Simple Verification
        if (strlen($phone_no) == 11 && substr($phone_no, 0, 1) == '1') {
            if ($verify_code) {
                $result = $sms_service->verify($phone_no, $verify_code);
            } else {
                $result = $sms_service->sendVerificationCode($phone_no);
            }

            EchoUtility::echoCommonMsg($result['code'], $result['msg']);
        }
    }

    public function getOrderDetail($order_id)
    {
        $order = HtOrder::model()->with('status')->findByPk($order_id);
        if (!$order) {
            return null;
        }

        $order_data_custom = array(
            'is_combo' => 0,
            'order' => array(
                'total' => $order['sub_total'],
                'order_id' => $order['order_id'],
                'order_date' => substr($order['date_added'], 0, 10),
                'status_id' => $order['status_id'],
                'status_name' => $order['status']['cn_name_customer'],
                'status_shortname' => $order['status_shortname'],
                'payment_url' => $order['payment_url'],
                'cancel_url' => $order['cancel_url'],
                'return_url' => $order['return_url'],
                'send_voucher_url' => $order['send_voucher_url'],
                'download_voucher_url' => $order['download_voucher_url'],
                'voucher_url' => $order['voucher_base_url']
            ),
        );

        //order_product
        $order_products = HtOrderProduct::model()->with(['departure', 'product.description'])->findAllByAttributes(['order_id' => $order_id]);
        if (count($order_products) > 1) {
            $main_order_product = $order_products[0];
            $order_data_custom['product_type'] = $main_order_product['product']['type'];
            $main_product_id = $main_order_product['product_id'];
            $bundle = HtProductBundle::model()->findAllByAttributes(['product_id' => $main_product_id]);
            $bundle_ids = ModelHelper::getList($bundle, 'bundle_id');

            // group product by group type
            $grouped_product = ['group_0' => [], 'group_1' => [], 'group_2' => [], 'group_3' => []];

            foreach ($order_products as $order_product) {
                if ($order_product['product_id'] == $main_product_id) {
                    $product = $this->getOrderProductInfo($order_product, $order['status_id']);
                } else {
                    $product = $this->getOrderProductInfo($order_product, $order['status_id'], $bundle_ids);
                    if (empty($bundle_ids)) {
                        $product['group_type'] = 3;
                    }
                }
                $grouped_product['group_' . $product['group_type']][] = $product;
            }
            $order_data_custom['product'] = $grouped_product;
            $order_data_custom['is_combo'] = $main_order_product['product']['is_combo'];
        } else {
            $order_product = $order_products[0];
            $order_data_custom['order']['total'] = $order_product['total'];
            $order_data_custom['product_type'] = $order_product['product']['type'];

            $order_data_custom['product'] = $this->getOrderProductInfo($order_product, $order['status_id']);
        }

        //insurance_codes
        $order_data_custom['insurance_codes'] = Converter::convertModelToArray(HtInsuranceCode::model()->with('company')->findAllByAttributes(['order_id' => $order_id]));

        return array(
            'data' => $order_data_custom,
            //'raw_data' => $order_data
        );
    }

    public function actionMomoCoupon()
    {
        $this->render('momorule', array());
    }

    private function getOrderProductInfo($order_product, $order_status, $bundle_ids = [])
    {
        $group_type = 0; // 1：N选1；2：必选；3：可选; 0: not in group
        if (!empty($bundle_ids)) {
            $bundle_info = HtProductBundleItem::model()->getBundleInfo($order_product['product_id'], $bundle_ids);
            $group_type = $bundle_info['bundle']['group_type'];
        }

        list($need_lead, $need_every_one, $passenger) = $this->getPassengerInfo($order_product);

        $product = array(
            'product_id' => $order_product['product_id'],
            'name' => $order_product['product']['description']['name'],
            'type' => $order_product['product']['type'],
            'group_type' => $group_type,
            'info' => $this->getProductOptionInfo($order_product),
            'need_lead' => $need_lead,
            'redeem_info' => $this->getRedeemInfo($order_status, $order_product),
            'need_everyone' => $need_every_one,
            'passenger' => $passenger,
        );

        return $product;
    }

    private function getPassengerInfo($order_product)
    {
        $order_id = $order_product['order_id'];
        $order_product_id = $order_product['order_product_id'];
        $product_id = $order_product['product_id'];

        $quantities = HtOrderProductPrice::model()->getQuantities($order_product_id);

        $ticket_types = HtProductTicketRule::model()->getTicketRuleMapForOrder($product_id);

        // passengers & meta &pax rule
        $passengers = HtOrderPassenger::model()->findAllByOrder($order_id, $order_product_id);
        $passenger_rule = HtProductPassengerRule::model()->getPassengerRule($product_id);

        $need_lead = $passenger_rule['pax_rule']['need_lead'] == '1';
        $need_everyone = $passenger_rule['pax_rule']['need_passenger_num'] == '0';

        $passenger = ['summary' => '', 'all' => []];
        //Passenger Summary
        foreach ($quantities as $key => $val) {
            $passenger['summary'] .= $ticket_types[$key]['ticket_type']['cn_name'] . ' x ' . $val . '&nbsp;&nbsp;';
        }

        if ($need_lead && count($passengers) > 0) {
            array_push($passenger['all'], $this->getFields($passengers[0], $passengers[0]['ticket_id'], $ticket_types));
        }
        if ($need_everyone) {
            $start_idx = $need_lead ? 1 : 0;
            for ($i = $start_idx, $len = count($passengers); $i < $len; $i++) {
                array_push($passenger['all'],
                           $this->getFields($passengers[$i], $passengers[$i]['ticket_id'], $ticket_types));
            }
        }

        return [$need_lead, $need_everyone, $passenger];
    }

    private function getFields($one_pax, $ticket_type, $ticket_types_info)
    {
        $tmp = $one_pax['zh_name'];
        // . '&nbsp;&nbsp;' . $one_pax['en_name'];
//        if ($one_pax['is_child']) {
//            $tmp .= '（' . $one_pax['child_age'] . '岁）';
//        }

        if (!($ticket_type == 1 || $ticket_type == 99)) {
            $tmp .= ' (' . $ticket_types_info[$ticket_type]['ticket_type']['description'] . ') ';
        }

        return $tmp;
    }

    private function getProductOptionInfo($order_product)
    {
        $info = [];
        $tour_date_info = $this->getTourDateInfo($order_product);
        if (!empty($tour_date_info)) {
            $info[] = $tour_date_info;
        }

        $special_info = $this->getSpecialInfo($order_product);
        if (!empty($special_info)) {
            $info = array_merge($info, $special_info);
//            $info[] = $special_info;
        }

        $departure_info = $this->getDepartureInfo($order_product);
        if (!empty($departure_info)) {
            $info[] = $departure_info;
        }

        return $info;
    }

    private function getTourDateInfo($order_product)
    {
        //sale rule
        $date_rule = HtProductDateRule::model()->findByPk($order_product['product_id']);
        //Product Options
        if ($date_rule['need_tour_date'] == '1') {
            return array(
                'name' => 'tour_date',
                'label' => $order_product['product']['description']['tour_date_title'],
                'value' => $order_product['tour_date']
            );
        }

        return [];
    }

    private function getSpecialInfo($order_product)
    {
        $result = array();
        $order_product = Converter::convertModelToArray($order_product);
        $special_info = HtProductSpecialCombo::model()->getSpecialDetail($order_product['product_id'], $order_product['special_code']);

        if(!empty($special_info)) {
            foreach($special_info[0]['items'] as $item) {
                $special_group = HtProductSpecialCombo::model()->getSpecialGroupByCode($order_product['product_id'], $item['special_code']);
                $result[] = array(
                    'name' => 'special_code',
                    'label' => $special_group['cn_title'],
                    'value' => $item['cn_name']
                );
            }
        }

        return $result;
    }

    private function getDepartureInfo($order_product)
    {
        if (!empty($order_product['departure']) && strlen($order_product['departure']['departure_point']) > 0) {
            return array(
                'name' => 'departure_point',
                'label' => $order_product['product']['description']['departure_title'],
                'value' => $order_product['departure']['departure_point'] . ' / ' . $order_product['departure_time']
            );
        }

        return [];
    }

    private function getRedeemInfo($order_status, $order_product)
    {
        $redeem_info = '';
        if ($order_status == '3' || $order_status == '24') {
            $redeem_info = '* 此商品的兑换截止日期为' . $order_product['redeem_expire_date'];
        }

        return $redeem_info;
    }
}