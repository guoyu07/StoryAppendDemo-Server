<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 7/20/14
 * Time: 10:41 PM
 */
class ActivityController extends Controller
{
    public $resource_refs;

    public function actionIndex()
    {
        echo 'hahaha';
    }

    public function actionHappyMuseum()
    {
        $this->initData();

        $this->setPageTitle('搭建快乐博物馆');
        $this->setKeywords('玩途_搭建快乐博物馆');
        $this->setDescription('玩途_搭建快乐博物馆');

        $this->render("museum");
    }
    public function actionDisney360()
    {
        $this->layout = '//layouts/360';
        $this->resource_refs = 'promotion.res';
        $data = $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'getPromotionDetail' => $this->createUrl('promotion/promotionDetail',
                    array('promotion_id' => 19))
            )
        );

        $this->setPageTitle('全球迪士尼_迪士尼门票_特惠预订_360旅游_玩途自由行');
        $this->setKeywords('香港迪士尼,东京迪士尼,加州迪士尼,巴黎迪士尼,奥兰多迪士尼');
        $this->setDescription('玩途提供全球迪士尼门票特价预订，搜罗全球迪士尼特价门票尽在玩途自由行');


        $this->render('../promotion/main', $data);
    }

    public function actionDisney()
    {
        $this->initData();
        $this->render('disney');
    }

    public function actionSummerSale()
    {
        $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array('getSummerSaleData' => $this->createUrl('activity/summerSaleData'))
        );
        $this->resource_refs = 'summersale.res';

        $this->setPageTitle('香港_新加坡_伦敦_洛杉矶_夏威夷_悉尼_景点门票_特价预订_玩途自由行');
        $this->setKeywords('香港,新加坡,伦敦,洛杉矶,夏威夷,悉尼,景点门票');
        $this->setDescription('玩途银联联合促销：提供香港，新加坡，伦敦，洛杉矶，夏威夷，悉尼的景点门票及当地游特价预订,每单立减150元');

        $this->render("summersale");
    }

    public function actionSummerSale360()
    {
        $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array('getSummerSaleData' => $this->createUrl('activity/summerSaleData'))
        );
        $this->render("360/summersale360");
    }

    public function actionSummerSaleData()
    {
        $data = Yii::app()->activity->getActivityData(100); //summer sale activity id = 100;
        EchoUtility::echoJson($data);
    }

    public function actionKidAdult()
    {
        $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array('getKidAdultData' => $this->createUrl('activity/kidAdultData'))
        );

        $this->setPageTitle('玩途双11立减特惠_全球乐园门票_玩途自由行');
        $this->setKeywords('玩途双11立减，全球乐园门票');
        $this->setDescription('玩途双11，儿童价抢成人票，狂欢全球迪士尼、环球影城等6大乐园');

        $this->render("kidadult");
    }

    public function action1111()
    {
        $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array('getKidAdultData' => $this->createUrl('activity/double11Data'))
        );

        $this->setPageTitle('玩途双11立减特惠_全球乐园门票_玩途自由行');
        $this->setKeywords('玩途双11立减，全球乐园门票');
        $this->setDescription('玩途双11，下单立减50，畅玩迪士尼、海洋公园等全球32座顶级乐园!');

        $this->render("kidadult");
    }

    public function actionShopping()
    {
        $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array('getKidAdultData' => $this->createUrl('activity/shoppingData'))
        );

        $this->setPageTitle('玩途跨年扫货_玩转全球10大购物圣地_每单立减50_玩途自由行');
        $this->setKeywords('玩途购物优惠，全球购物');
        $this->setDescription('玩途跨年扫货，玩转全球10大购物圣地，银联支付，每单立减50!');

        $this->render("kidadult");
    }

    public function actionFridaySale()
    {
        $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array('getFridaySaleData' => $this->createUrl('activity/fridaySaleData'))
        );

        $this->setPageTitle('玩途五折特卖_境外游门票_全场限时五折预订_玩途自由行');
        $this->setKeywords('玩途周五特卖, 景点门票5折,自由行产品5折');
        $this->setDescription('玩途周五特卖,每周五晚5点起，爆款商品5折，还有更多海外自由行产品6折预订，轻松海外游，尽在玩途自由行！');

        $this->render("fridaysale");
    }

//    public function actionNewYearSale()
//    {
//        $this->initData();
//        $this->request_urls = array_merge(
//            $this->request_urls,
//            array('getFlashSaleData' => $this->createUrl('activity/flashSaleData'))
//        );
//
//        $this->setPageTitle('玩途新春5折来袭，扫码领券5折享不停');
//        $this->setKeywords('玩途新年特卖，景点门票5折，自由行产品5折');
//        $this->setDescription('玩途新年特卖，爆款商品5折，还有更多海外自由行产品6折预订，轻松海外游，尽在玩途自由行！');
//
//        $this->render("newyearsale");
//    }

    public function actionCcbSale()
    {
        $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array('getFridaySaleData' => $this->createUrl('activity/ccbSaleData'))
        );

        $this->setPageTitle('玩途1元秒杀_韩国游门票_韩国一日游_韩国酒店_玩途自由行');
        $this->setKeywords('玩途1元秒杀秀票, 酒店立减50元,游玩产品立减20元');
        $this->setDescription('玩途看秀畅购游韩国，1元抢精彩秀票，还有更多旅行产品最高立减50元，轻松游韩国，尽在玩途自由行！');

        $this->render("ccbsale");
    }


    public function action1212()
    {
        $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array('getDouble12Data' => $this->createUrl('activity/double12Data'))
        );

        $this->setPageTitle('玩途五折特卖_境外游门票_全场限时五折预订_玩途自由行');
        $this->setKeywords('玩途周五特卖, 景点门票5折,自由行产品5折');
        $this->setDescription('玩途周五特卖,每周五晚5点起，爆款商品5折，还有更多海外自由行产品6折预订，轻松海外游，尽在玩途自由行！');

        $this->render("double12");
    }

    public function actionFridaySale360()
    {
        $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array('getFridaySaleData' => $this->createUrl('activity/fridaySaleData'))
        );
        $this->render("360/fridaysale360");
    }

    public function actionFridaySaleDetail()
    {
        $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array('getFridaySaleDetailData' => $this->createUrl('activity/fridaySaleDetailData'))
        );
        $this->render("fridaysaledetail");
    }

    public function actionFridaySaleData()
    {
        $data['flash_sale_data'] = $this->getFlashSaleData(173);
        $data['friday_sale_data'] = $this->getFridaySaleData(174);
        EchoUtility::echoJson($data);
    }

    public function actionFlashSaleData()
    {
        $data['flash_sale_data'] = $this->getFlashSaleData(173);
        EchoUtility::echoJson($data);
    }

    public function actionETravelData()
    {
        $data['flash_sale_data'] = $this->getFlashSaleData(175);
        EchoUtility::echoJson($data);
    }

    public function actionFridaySaleDetailData()
    {
        $product_ids = array([3535, 3192, 3511], [3658, 3704, 3507], [3534, 3826, 3853], [3532, 3512, 3513]);
        //默认第一组是主推商品
        $sub_products = array();

        foreach($product_ids as $group) {
            $sub_group = array();
            foreach($group as $product_id) {
                $product = $this->getProductData($product_id);
                $sub_group[] = $product;
            }
            $sub_products['products'] = $sub_group;
            $data['groups'][] = $sub_products;
        }

        EchoUtility::echoJson($data);
    }

    public function getFlashSaleData($activity_id)
    {
        $raw_data = HtActivity::model()->getByPkWithActivityProductAndActivityRule($activity_id);

        $data['activity_id'] = $raw_data['activity_id'];
        $data['title'] = $raw_data['title'];
        $data['name'] = $raw_data['name'];
        $data['start_date'] = $raw_data['start_date'];
        $data['end_date'] = $raw_data['end_date'];

        $now = Yii::app()->activity->getNow();
        if($raw_data['status'] == HtActivity::AS_IN_SALE) {
            if($now < $raw_data['start_date']) {
                $data['status'] = HtActivity::AS_PENDING;
                $data['countdown'] = strtotime($raw_data['start_date']) - strtotime(Yii::app()->activity->getNow());
            } else if($now > $raw_data['end_date']) {
                $data['status'] = HtActivity::AS_OUTDATED;
                $data['countdown'] = strtotime('2014-10-01 23:59:59') - strtotime(Yii::app()->activity->getNow());
                if($data['countdown'] <= 0) {
                    $data['countdown'] = 0;
                }
            } else {
                $data['status'] = HtActivity::AS_ONGOING;
                $data['countdown'] = strtotime($raw_data['end_date']) - strtotime(Yii::app()->activity->getNow());
            }
        } else {
            $data['status'] = HtActivity::AS_OUTDATED;
            $data['countdown'] = 0;
        }

        $products = array();
        foreach($raw_data['activity_product'] as $ap) {
            $pids = array_filter(json_decode($ap['product_ids']));
            foreach($pids as $pid) {
                $rp = $this->getProductData($pid, '', true, true, $activity_id);
                if($pid == 3619 ){
                    $rp['name'] = '新西兰南岛13天深度自驾游——精选12晚4星酒店+13天全险SUV租用。4人同行，劲爆五折价！';
                    $rp['show_price'] = ['price'=>29900,'orig_price'=>60000,'title'=>'每套'];
                }
                $products[] = $rp;
            }
        }
        $data['products'] = $products;

        return $data;
    }

    /**
     * @param $pid
     * @param $rp
     * @return mixed
     */
    public function getProductData($pid, $sale_date = '', $with_city = false,$with_description = false, $activity_id = 0)
    {
        $rp = array();
        if($with_description){
            $raw_product = HtProduct::model()->with(['description' => ['select' => 'name,summary,description,benefit'], 'cover_image'])->findByPk($pid);
        }else{
            $raw_product = HtProduct::model()->with(['description' => ['select' => 'name,summary,benefit'], 'cover_image'])->findByPk($pid);
        }
        $product = Converter::convertModelToArray($raw_product);

        $rp['product_id'] = $product['product_id'];
        $rp['city_code'] = $product['city_code'];
        if($with_city) {
            $city = Converter::convertModelToArray(HtCity::model()->findByPk($rp['city_code']));
            $rp['city_name'] = $city['cn_name'];
            $rp['city_link'] = $city['link_url'];
        }

        $rp['name'] = $product['description']['name'];
        $rp['summary'] = $product['description']['summary'];
        if($with_description){
            $rp['description'] = $product['description']['description'];
        }
        $rp['benefit'] = $product['description']['benefit'];
        $rp['link_url'] = $product['link_url'];
        $rp['cover_image_url'] = $product['cover_image']['image_url'];
        $show_prices = HtProductPricePlan::model()->getShowPrices($pid, '', $sale_date);
        $rp['show_price'] = $show_prices;
        $rp['stock_info'] = HtProductSaleStock::model()->getProductSaleStock($rp['product_id'], date('Y-m-d'), $activity_id);

        return $rp;
    }

    public function getFridaySaleData($activity_id)
    {
        $raw_data = HtActivity::model()->getByPkWithActivityProductAndActivityRule($activity_id);

        $data['activity_id'] = $raw_data['activity_id'];
        $data['title'] = $raw_data['title'];
        $data['name'] = $raw_data['name'];
        $data['start_date'] = $raw_data['start_date'];
        $data['end_date'] = $raw_data['end_date'];

        $now = Yii::app()->activity->getNow();
        if($raw_data['status'] == HtActivity::AS_IN_SALE) {
            if($now < $raw_data['start_date']) {
                $data['status'] = HtActivity::AS_PENDING;
                $data['countdown'] = strtotime($raw_data['start_date']) - time();
            } else if($now > $raw_data['end_date']) {
                $data['status'] = HtActivity::AS_OUTDATED;
                $data['countdown'] = 0;
            } else {
                $data['status'] = HtActivity::AS_ONGOING;
                $data['countdown'] = strtotime($raw_data['end_date']) - time();
            }
        } else {
            $data['status'] = HtActivity::AS_OUTDATED;
            $data['countdown'] = 0;
        }

        $groups = array();
        foreach($raw_data['activity_product'] as $ap) {
            $pids = array_filter(json_decode($ap['product_ids']));
            foreach($pids as $pid) {
                $rp = $this->getProductData($pid, date('Y-m-d', strtotime($data['start_date'])));
                $this->reorgGroups($groups, $rp, 0);
            }
        }
        $data['groups'] = array_values($groups);

        return $data;
    }

    private function reorgGroups(&$groups, $product, $type = 0)
    {
        $location = $this->getFridaySaleLocation($product, $type);

        if(!isset($groups[$location['code']]['products'])) {
            $groups[$location['code']]['location'] = $location;
            $groups[$location['code']]['products'] = array();
        }
        $groups[$location['code']]['products'][] = $product;
    }

    private function getFridaySaleLocation($product, $type = 0)
    {
        $location = array();
        $city_code = $product['city_code'];

        $city_raw = HtCity::model()->with('country')->findByPk($city_code);
        $city_raw = Converter::convertModelToArray($city_raw);

        $raw = $city_raw;
        $location['code'] = $city_code;
        if($type == 1) {
            $raw = $city_raw['country'];
            $location['code'] = $raw['country_code'];
        }
        $location['cn_name'] = $raw['cn_name'];
        $location['en_name'] = $raw['en_name'];
        $location['link_url'] = $raw['link_url'];

        if(in_array($product['product_id'], [3753, 3805, 3814])) {
            $location['code'] = 'BCSD';
        } else if(in_array($product['product_id'], [1177, 3857, 3186])) {
            $location['code'] = 'OZJH';
        }

        $location['pc_nav_image_url'] = 'themes/public/images/activities/fridaysale/nav_' . $location['code'] . '.png';
        $location['mobile_nav_image_url'] = 'themes/mobile/images/activities/fridaysale/nav_' . $location['code'] . '.png';
        $location['mobile_header_image_url'] = 'themes/mobile/images/activities/fridaysale/header_' . $location['code'] . '.png';

        return $location;
    }

    public function actionCcbSaleData()
    {
        $now = Yii::app()->activity->getNow();
        $data['phase_id'] = date('n', strtotime($now));
        $data['flash_sale_data'] = $this->getCcbFlashSaleData($data['phase_id']);
        $data['discount_sale_data'] = $this->getCcbDiscountSaleData($data['phase_id']);

        EchoUtility::echoJson($data);
    }

//    private function reorgGroups(&$groups, $product, $type = 0)
//    {
//        $location = $this->getFridaySaleLocation($product, $type);
//        if (!isset($groups[$location['code']]['products'])) {
//            $groups[$location['code']]['location'] = $location;
//            $groups[$location['code']]['products'] = array();
//        }
//        $groups[$location['code']]['products'][] = $product;
//    }

//    private function reorgGroups(&$groups,&$product){
//        $data = array(
//            '2513' => ['5', '400'],
//            '1886' => ['5', '800'],
//            '2682' => ['5', '800'],
//            '2681' => ['4', '400'],
//            '1889' => ['4', '800'],
//            '1887' => ['4', '800'],
//            '1891' => ['3', '400'],
//            '1893' => ['3', '400'],
//            '2586' => ['3', '800'],
//        );
//        $conf = $data[$product['product_id']];
//        $location = array('code'=>$conf[0].'STAR');
//        $location['cn_name'] = $conf[0] ==5?'超级体验':($conf[0]==4?'高级体验':'中级体验');
//        $location['link_url'] = $this->createUrl('country/index',['en_name'=>'New_Zealand']);
//        $location['pc_nav_image_url'] = 'themes/public/images/activities/fridaysale/nav_'.$location['code'].'.png';
//        $location['mobile_nav_image_url'] = 'themes/mobile/images/activities/fridaysale/nav_'.$location['code'].'.png';
//        $location['mobile_header_image_url'] = 'themes/mobile/images/activities/fridaysale/header_'.$location['code'].'.png';
//
//        if(!isset($groups[$location['code']]['products'])){
//            $groups[$location['code']]['location'] = $location;
//            $groups[$location['code']]['products'] = array();
//        }
//        $product['benefit']=sprintf('活动期间返%d优惠券',$conf[1]);
//        $city_code = $product['city_code'];
//        $city_raw = HtCity::model()->findByPk($city_code);
//        $product['city_name'] = $city_raw['cn_name'];
//
//        $groups[$location['code']]['products'][]= $product;
//    }

    public function getCcbFlashSaleData($phase_id)
    {
        $raw_data = HtActivity::model()->getByPkWithActivityProductAndActivityRule(141 + $phase_id - 1);

        $data['activity_id'] = $raw_data['activity_id'];
        $data['title'] = $raw_data['title'];
        $data['name'] = $raw_data['name'];

        $ap = HtActivityProduct::model()->findByAttributes(['activity_id' => $data['activity_id'], 'phase_id' => 1]);
        $data['start_date'] = $ap['start_date'];
        $data['end_date'] = $ap['end_date'];

        $now = Yii::app()->activity->getNow();
        if($raw_data['status'] == HtActivity::AS_IN_SALE) {
            if($now < $data['start_date']) {
                $data['status'] = HtActivity::AS_PENDING;
                $data['countdown'] = strtotime($data['start_date']) - strtotime(Yii::app()->activity->getNow());
            } else if($now > $data['end_date']) {
                $data['status'] = HtActivity::AS_OUTDATED;
                $data['countdown'] = strtotime($data['end_date']) - strtotime(Yii::app()->activity->getNow());
                if($data['countdown'] <= 0) {
                    $data['countdown'] = 0;
                }
            } else {
                $data['status'] = HtActivity::AS_ONGOING;
                $data['countdown'] = strtotime($data['end_date']) - strtotime(Yii::app()->activity->getNow());
            }
        } else {
            $data['status'] = HtActivity::AS_OUTDATED;
            $data['countdown'] = 0;
        }

        $products = array();
        foreach ($raw_data['activity_product'] as $ap) {
            $pids = array_filter(json_decode($ap['product_ids']));
            foreach ($pids as $pid) {
                $products[] = $this->getProductData($pid, '', false, true);
            }
        }
        $data['products'] = $products;

        return $data;
    }

//    /**
//     * @param $pid
//     * @param $rp
//     * @return mixed
//     */
//    public function getProductData($activity_id, $pid, $sale_date = '')
//    {
//        $rp = array();
//        $raw_product = HtProduct::model()->with(['description' => ['select' => 'name,summary,benefit'], 'cover_image'])->findByPk($pid);
//        $product = Converter::convertModelToArray($raw_product);
//
//        $rp['product_id'] = $product['product_id'];
//        $rp['city_code'] = $product['city_code'];
//        $rp['name'] = $product['description']['name'];
//        $rp['summary'] = $product['description']['summary'];
//        $rp['benefit'] = $product['description']['benefit'];
//        $rp['link_url'] = $product['link_url'];
//        $rp['cover_image_url'] = $product['cover_image']['image_url'];
//        $show_prices = HtProductPricePlan::model()->getShowPrices($pid, '', $sale_date);
//        $rp['show_price'] = $show_prices;
//        $rp['stock_info'] = HtProductSaleStock::model()->getProductSaleStock($rp['product_id'], date('Y-m-d'), $activity_id);
//
//        return $rp;
//    }

    //0-获取城市信息   1-获取国家信息

    public function getCcbDiscountSaleData($phase_id)
    {
        $raw_data = HtActivity::model()->getByPkWithActivityProductAndActivityRule(147 + $phase_id - 1);
        $raw_data2 = HtActivity::model()->getByPkWithActivityProductAndActivityRule(153 + $phase_id - 1);

        $data['activity_id'] = $raw_data['activity_id'];
        $data['title'] = $raw_data['title'];
        $data['name'] = $raw_data['name'];
//        $data['start_date'] = $raw_data['start_date'];
//        $data['end_date'] = $raw_data['end_date'];

        $ap = HtActivityProduct::model()->findByAttributes(['activity_id' => $data['activity_id'], 'phase_id' => 1]);
        $data['start_date'] = $ap['start_date'];
        $data['end_date'] = $ap['end_date'];

        $now = Yii::app()->activity->getNow();
        if($raw_data['status'] == HtActivity::AS_IN_SALE) {
            if($now < $data['start_date']) {
                $data['status'] = HtActivity::AS_PENDING;
                $data['countdown'] = strtotime($raw_data['start_date']) - time();
            } else if($now > $data['end_date']) {
                $data['status'] = HtActivity::AS_OUTDATED;
                $data['countdown'] = 0;
            } else {
                $data['status'] = HtActivity::AS_ONGOING;
                $data['countdown'] = strtotime($raw_data['end_date']) - time();
            }
        } else {
            $data['status'] = HtActivity::AS_OUTDATED;
            $data['countdown'] = 0;
        }

        $groups = array();
        foreach ($raw_data['activity_product'] as $ap) {
            $data['start_date'] = $ap['start_date'];
            $data['end_date'] = $ap['end_date'];
            $pids = array_filter(json_decode($ap['product_ids']));
            foreach ($pids as $pid) {
                $rp = $this->getProductData($pid, date('Y-m-d', strtotime($data['start_date'])));
                $this->reorgGroups($groups, $rp, 1);
            }
        }
        foreach ($raw_data2['activity_product'] as $ap) {
            $pids = array_filter(json_decode($ap['product_ids']));
            foreach ($pids as $pid) {
                $rp = $this->getProductData($pid, date('Y-m-d', strtotime($data['start_date'])));
                $this->reorgGroups($groups, $rp, 1);
            }
        }
        $data['groups'] = array_values($groups);

        return $data;
    }

    public function actionKidAdultData()
    {
        $raw_data = HtActivity::model()->getByPkWithActivityProductAndActivityRule(118);

        $data['activity_id'] = $raw_data['activity_id'];
        $data['title'] = $raw_data['title'];
        $data['name'] = $raw_data['name'];
        $data['start_date'] = $raw_data['start_date'];
        $data['end_date'] = $raw_data['end_date'];

        $now = Yii::app()->activity->getNow();
        if($raw_data['status'] == HtActivity::AS_IN_SALE) {
            if($now < $raw_data['start_date']) {
                $data['status'] = HtActivity::AS_PENDING;
                $data['countdown'] = strtotime($raw_data['start_date']) - time();
            } else if($now > $raw_data['end_date']) {
                $data['status'] = HtActivity::AS_OUTDATED;
                $data['countdown'] = 0;
            } else {
                $data['status'] = HtActivity::AS_ONGOING;
                $data['countdown'] = strtotime($raw_data['end_date']) - time();
            }
        } else {
            $data['status'] = HtActivity::AS_OUTDATED;
            $data['countdown'] = 0;
        }

        $group_data = $this->kidadult_rawdata();

        $group_id = $this->getParam('group_id', 0);
        $group_data['groups'];
        foreach($group_data['groups'] as &$ap) {
            if(!empty($group_id) && $group_id <> $ap['group_id']) {
                continue;
            }
            $products = array();
            foreach($ap['product_ids'] as $pid) {
                $rp = $this->getProductData($pid, date('Y-m-d', strtotime($data['end_date'])), true);
                $rp['qr_code_url'] = Yii::app()->product->getQrCodeLink($pid);
                $products[] = $rp;
            }
            $ap['products'] = $products;
            $data['groups'][] = $ap;
        }


        EchoUtility::echoJson($data);
    }

    private function kidadult_rawdata()
    {
        $data['groups'] = array(
            array(
                'group_id' => 1,
                'name' => '迪士尼乐园',
                'en_name' => 'DisneyLand',
                'title_image' => "http://hitour.qiniudn.com/cbfc0bfd21e608899fbebefc18dc06a5.png",
                'product_ids' => [2992, 2993, 2990, 2991],
            ),
            array(
                'group_id' => 2,
                'name' => '环球影城',
                'en_name' => 'Universal Studio',
                'title_image' => "http://hitour.qiniudn.com/9f44551ac15e4d7969df7f85ff9b0332.png",
                'product_ids' => [2994, 2995],
            ),
            array(
                'group_id' => 3,
                'name' => '海洋馆',
                'en_name' => 'Sea World',
                'title_image' => "http://hitour.qiniudn.com/0dba46680310eb3a90214f538ec5f2cb.png",
                'product_ids' => [2996, 2997, 2998, 2999, 3000, 3001, 3002, 3003, 3004, 3005, 3006, 3007, 3008, 3009],
            ),
            array(
                'group_id' => 4,
                'name' => '科技馆',
                'en_name' => 'Science and Technology Museum',
                'title_image' => "http://hitour.qiniudn.com/1be529ad4c35e444357cd8f12d7b203e.png",
                'product_ids' => [3010, 3017, 3011, 3012],
            ),
            array(
                'group_id' => 5,
                'name' => '动物园',
                'en_name' => 'Zoo',
                'title_image' => "http://hitour.qiniudn.com/7be3a8d6152fa673504a6bf109b6292e.png",
                'product_ids' => [3013, 3014],
            ),
            array(
                'group_id' => 6,
                'name' => '乐高主题公园',
                'en_name' => 'LegoLand',
                'title_image' => "http://hitour.qiniudn.com/f1969f21c28c5e2da45b10266a17e038.png",
                'product_ids' => [3015, 3016],
            ),
        );
        return $data;
    }

    public function actionDouble12Data()
    {
        $group_data = $this->double12_rawdata();
        $data = $this->getActivityData(130, $group_data);
        EchoUtility::echoJson($data);
    }

    private function double12_rawdata()
    {
        $data['groups'] = array(
            array(
                'group_id' => 1,
                'name' => '新加坡',
                'en_name' => 'Singapore',
                'tab_bg_color' => '#f7b811',
                'tab_decorator_color' => '#da8d0b',
                'link_url' => "http://www.hitour.cc/Singapore/Singapore",
                'mobile_link_url' => "http://www.hitour.cc/Singapore/Singapore",
//                'product_ids' => [3286, 3287, 3288],
                'product_ids' => [3161, 3287, 3288],
            ),
            array(
                'group_id' => 2,
                'name' => '韩国',
                'en_name' => 'South Korea',
                'tab_bg_color' => '#d56034',
                'tab_decorator_color' => '#a4471e',
                'link_url' => "http://www.hitour.cc/South_Korea",
                'mobile_link_url' => "http://www.hitour.cc/South_Korea/Seoul",
//                'product_ids' => [3292, 3293, 3294],
                'product_ids' => [3227, 3293, 3294],
            ),
            array(
                'group_id' => 3,
                'name' => '普吉岛',
                'en_name' => 'Phuket',
                'tab_bg_color' => '#d3004f',
                'tab_decorator_color' => '#780431',
                'link_url' => "http://www.hitour.cc/Thailand/Phuket",
                'mobile_link_url' => "http://www.hitour.cc/Thailand/Phuket",
//                'product_ids' => [3303, 3301, 3302],
                'product_ids' => [3172, 3301, 3302],
            ),
            array(
                'group_id' => 4,
                'name' => '台湾',
                'en_name' => 'TaiWan',
                'tab_bg_color' => '#77c04d',
                'tab_decorator_color' => '#5c8e39',
                'link_url' => "http://www.hitour.cc/Taiwan",
                'mobile_link_url' => "http://www.hitour.cc/Taiwan/Taipei",
                'product_ids' => [3289, 3290, 3291],
            ),

            array(
                'group_id' => 5,
                'name' => '香港',
                'en_name' => 'HongKong',
                'tab_bg_color' => '#219ebe',
                'tab_decorator_color' => '#177996',
                'link_url' => "http://www.hitour.cc/HongKong/Hong_Kong",
                'mobile_link_url' => "http://www.hitour.cc/HongKong/Hong_Kong",
                'product_ids' => [3321, 3322, 3297],
            ),
            array(
                'group_id' => 6,
                'name' => '日本',
                'en_name' => 'Japan',
                'tab_bg_color' => '#7c74c1',
                'tab_decorator_color' => '#594aa7',
                'link_url' => "http://www.hitour.cc/Japan",
                'mobile_link_url' => "http://www.hitour.cc/Japan/Tokyo",
                'product_ids' => [3298, 3299, 3300],
            ),

        );
        return $data;
    }

    private function getActivityData($activity_id, $group_data)
    {
        $raw_data = HtActivity::model()->getByPkWithActivityProductAndActivityRule($activity_id);

        $data['activity_id'] = $raw_data['activity_id'];
        $data['title'] = $raw_data['title'];
        $data['name'] = $raw_data['name'];
        $data['start_date'] = $raw_data['start_date'];
        $data['end_date'] = $raw_data['end_date'];

        $now = Yii::app()->activity->getNow();
        if($raw_data['status'] == HtActivity::AS_IN_SALE) {
            if($now < $raw_data['start_date']) {
                $data['status'] = HtActivity::AS_PENDING;
                $data['countdown'] = strtotime($raw_data['start_date']) - strtotime($now);
            } else if($now > $raw_data['end_date']) {
                $data['status'] = HtActivity::AS_OUTDATED;
                $data['countdown'] = 0;
            } else {
                $data['status'] = HtActivity::AS_ONGOING;
                $data['countdown'] = strtotime($raw_data['end_date']) - strtotime($now);
            }
        } else {
            $data['status'] = HtActivity::AS_OUTDATED;
            $data['countdown'] = 0;
        }

        $group_id = $this->getParam('group_id', 0);
        $group_data['groups'];
        foreach($group_data['groups'] as &$ap) {
            if(!empty($group_id) && $group_id <> $ap['group_id']) {
                continue;
            }
            $products = array();
            foreach($ap['product_ids'] as $pid) {
                $rp = $this->getProductData($pid, date('Y-m-d', strtotime($data['end_date'])), true);
                $rp['qr_code_url'] = Yii::app()->product->getQrCodeLink($pid);
                $products[] = $rp;
            }
            $ap['products'] = $products;
            if($activity_id == Activity::SHOPPING) {
                $ap['group_link_url'] = empty($products[0]['city_link']) ? '' : $products[0]['city_link'];
            }
            $data['groups'][] = $ap;
        }

        return $data;
    }

    public function actionDouble11Data()
    {
        $group_data = $this->double11_rawdata();
        $data = $this->getActivityData(119, $group_data);
        EchoUtility::echoJson($data);
    }

    private function double11_rawdata()
    {
        $data['groups'] = array(
            array(
                'group_id' => 1,
                'name' => '迪士尼乐园',
                'en_name' => 'DisneyLand',
                'nav_image' => "http://hitour.qiniudn.com/cbfc0bfd21e608899fbebefc18dc06a5.png",
                'product_ids' => [1306, 1307, 1333, 909, 1105, 1441, 1336],
            ),
            array(
                'group_id' => 2,
                'name' => '环球影城',
                'en_name' => 'Universal Studio',
                'title_image' => "http://hitour.qiniudn.com/9f44551ac15e4d7969df7f85ff9b0332.png",
                'product_ids' => [884, 885, 915, 1083],
            ),
            array(
                'group_id' => 6,
                'name' => '乐高主题公园',
                'en_name' => 'LegoLand',
                'title_image' => "http://hitour.qiniudn.com/f1969f21c28c5e2da45b10266a17e038.png",
                'product_ids' => [1091, 1106, 956, 900],
            ),
            array(
                'group_id' => 3,
                'name' => '海洋馆',
                'en_name' => 'Sea World',
                'title_image' => "http://hitour.qiniudn.com/0dba46680310eb3a90214f538ec5f2cb.png",
                'product_ids' => [1058, 1470, 1450, 3079, 1371, 1259, 1440, 979, 971, 972, 954, 1060, 907, 1004, 994, 985, 1042, 1043, 1045, 1516, 902, 1008],
            ),
            array(
                'group_id' => 5,
                'name' => '动物园',
                'en_name' => 'Zoo',
                'title_image' => "http://hitour.qiniudn.com/7be3a8d6152fa673504a6bf109b6292e.png",
                'product_ids' => [1086, 1090, 1270, 1019, 1116],
            ),
            array(
                'group_id' => 7,
                'name' => '日韩风',
                'en_name' => 'J&K Series',
                'title_image' => "http://hitour.qiniudn.com/1be529ad4c35e444357cd8f12d7b203e.png",
                'product_ids' => [1563, 1479, 1480, 1466, 1463],
            ),
            array(
                'group_id' => 4,
                'name' => '科技馆',
                'en_name' => 'Science and Technology Museum',
                'title_image' => "http://hitour.qiniudn.com/1be529ad4c35e444357cd8f12d7b203e.png",
                'product_ids' => [992, 942, 694, 736],
            ),

        );
        return $data;
    }

    public function actionShoppingData()
    {
        $group_data = $this->shopping_rawdata();
        $data = $this->getActivityData(126, $group_data);
        EchoUtility::echoJson($data);
    }

    private function shopping_rawdata()
    {
//$a = ["1551", "1484", "1346", "2835", "2819", "690", "1614", "1171", "2438", "1615", "1173", "2317", "1092", "2905", "2768", "2532", "1333", "1083", "2540", "734", "1556", "2560", "2037", "1325", "1134", "2675", "1136", "2334", "2804", "2417"];
        $data['groups'] = array(
            array(
                'group_id' => 1,
                'name' => '巴黎',
                'en_name' => 'Paris',
                'title_image' => "http://hitour.qiniudn.com/d72e26bf54a98f5c9fe23c6fd461c0c3.jpg",
                'product_ids' => [1551, 1484, 1346, 2476],
            ),
            array(
                'group_id' => 2,
                'name' => '伦敦',
                'en_name' => 'London',
                'title_image' => "http://hitour.qiniudn.com/1a0d70d434e839dc2ab8fe131cf20cf0.png",
                'product_ids' => [1860, 2815, 690, 2392],
            ),
            array(
                'group_id' => 3,
                'name' => '米兰',
                'en_name' => 'Milan',
                'title_image' => "http://hitour.qiniudn.com/44005303924ad3b3be5c2b6178c9f77d.png",
                'product_ids' => [1614, 1171, 2438, 2449],
            ),
            array(
                'group_id' => 4,
                'name' => '佛罗伦萨',
                'en_name' => 'Florence',
                'title_image' => "http://hitour.qiniudn.com/213e61ebe6ab32b036dd31d5f0b63717.png",
                'product_ids' => [1615, 1173, 2317, 2231],
            ),
            array(
                'group_id' => 5,
                'name' => '纽约',
                'en_name' => 'New York',
                'title_image' => "http://hitour.qiniudn.com/167a44863ab8a7f3446f2f1e81e3bd75.jpg",
                'product_ids' => [1092, 2905, 2768, 1100],
            ),
            array(
                'group_id' => 6,
                'name' => '洛杉矶',
                'en_name' => 'Los Angeles',
                'title_image' => "http://hitour.qiniudn.com/a042f5eb7b11b2420c769ee288f3ccce.jpg",
                'product_ids' => [2532, 1333, 1083, 1109],
            ),
            array(
                'group_id' => 7,
                'name' => '芝加哥',
                'en_name' => 'Chicago',
                'title_image' => "http://hitour.qiniudn.com/cfce0b8bc95f5e4599245606c25b057b.jpg",
                'product_ids' => [2540, 734, 1556, 735],
            ),
            array(
                'group_id' => 8,
                'name' => '新加坡',
                'en_name' => 'Singapore',
                'title_image' => "http://hitour.qiniudn.com/8aac9d1c6e05ab53e1cd537c19938875.jpg",
                'product_ids' => [2560, 3333, 1325],
            ),
            array(
                'group_id' => 9,
                'name' => '首尔',
                'en_name' => 'Seoul',
                'title_image' => "http://hitour.qiniudn.com/75c8f254639b70cf357de1cd0b4ef8b8.png",
                'product_ids' => [1134, 2675, 2571, 3320],
            ),
            array(
                'group_id' => 10,
                'name' => '东京',
                'en_name' => 'Tokyo',
                'title_image' => "http://hitour.qiniudn.com/7a5d6029fdd12eca3ec9be00245da6d2.jpg",
                'product_ids' => [2334, 2804, 2417, 1441],
            ),

        );
        return $data;
    }

    public function actionSubscribe()
    {
        $result = ['code' => 200, 'msg' => 'OK'];

        $email = trim($this->getParam('email'));
        $mail = Mail::getInstanceForCustomer();
        if($mail->mailValidator($email)) {
            $subscribe = HtSubscribeEmail::model()->findByAttributes(['email' => $email]);
            if(!$subscribe) {
                $subscribe = new HtSubscribeEmail();
                $subscribe->email = $email;
                $subscribe->insert();

                $this->actionSendEdmCoupon($email);
            }
        } else {
            $result['code'] = 400;
            $result['msg'] = '邮件地址不正确，请检查后重新提交！';
        }

        echo json_encode($result, 271);
    }

    public function actionSendEdmCoupon($email, $preview = 0)
    {
        $template_coupon = Converter::convertModelToArray(HtCoupon::model()->findByPk(117961));
        if(empty($template_coupon)) {
            return;
        }

        //insert htcoupon
        $coupon = new HtCoupon();
        $coupon['name'] = '订阅EDM送' . (int)$template_coupon['discount'] . '优惠券_' . $email . '_' . date('Y-m-d H:i:s');
        $coupon['code'] = strtoupper(substr(md5($coupon['name'] . $template_coupon['code']), 0, 10));
        $coupon['discount'] = $template_coupon['discount'];
        $coupon['description'] = $template_coupon['description'];
        $coupon['type'] = $template_coupon['type'];
        $coupon['use_type'] = $template_coupon['use_type'];
        $coupon['product_min'] = $template_coupon['product_min'];
        $coupon['product_max'] = $template_coupon['product_max'];
        $coupon['total'] = $template_coupon['total'];
        $coupon['logged'] = $template_coupon['logged'];
        $coupon['shipping'] = $template_coupon['shipping'];
        $coupon['date_start'] = date('Y-m-d');
        $coupon['date_end'] = date('Y-m-d', strtotime('3Month-1day'));

        $coupon['uses_total'] = 1;
        $coupon['uses_customer'] = 1;
        $coupon['customer_id'] = 0;
        $coupon['status'] = 1;
        $coupon['date_added'] = date('Y-m-d H:i:s');

        $coupon['rel_coupon_id'] = $template_coupon['coupon_id'];

        if(!$coupon->insert()) {
            return false;
        }

        //insert coupon use limit
        $sql = 'INSERT INTO ht_coupon_use_limit(coupon_id, id,limit_type,valid_type) SELECT ' . (int)$coupon['coupon_id'] . ',id,limit_type,valid_type FROM ht_coupon_use_limit WHERE coupon_id =' . (int)$template_coupon['coupon_id'];
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->execute();

        //
        $subject = '玩途自由行感谢您的订阅';
        $firstName = $email;
        if($pos = strpos($email, '@')) {
            $firstName = substr($email, 0, $pos);
        }
        $template = dirname(Yii::app()->basePath) . Yii::app()->params['THEME_BASE_URL'] . '/views/email/edm_subscribe.php';
        $data = ['firstName' => $firstName, 'coupon' => $coupon];
        $body = Mail::templateRender($template, $data);

        if(!empty($body)) {
            if($preview) {
                echo $body;
                return;
            }
            $mail = Mail::getInstanceForCustomer();
            $result = $mail->send($email, $subject, $body);
        }
    }
//    补发
//    public function actionReissueEdm(){
//        for($i = 454;$i<468;$i++) {
//            $se = HtSubscribeEmail::model()->findByPk($i);
//            if(!empty($se['email'])){
//                $this->actionSendEdmCoupon($se['email']);
//            }
//            sleep(1);
//        }
//
//    }

//    private function double11_rawdata()
//    {
//        $data['groups'] = array(
//            array(
//                'group_id' => 1,
//                'name' => '迪士尼',
//                'en_name' => 'DisneyLand',
//                'nav_image' => "http://hitour.qiniudn.com/cbfc0bfd21e608899fbebefc18dc06a5.png",
//                'product_ids' => [1305, 1306, 1307, 1333, 909, 1105, 1441, 1336],
//            ),
//            array(
//                'group_id' => 2,
//                'name' => '环球影城',
//                'en_name' => 'Universal Studio',
//                'title_image' => "http://hitour.qiniudn.com/9f44551ac15e4d7969df7f85ff9b0332.png",
//                'product_ids' => [884, 885, 915, 1083],
//            ),
//            array(
//                'group_id' => 3,
//                'name' => '乐高主题公园',
//                'en_name' => 'LegoLand',
//                'title_image' => "http://hitour.qiniudn.com/f1969f21c28c5e2da45b10266a17e038.png",
//                'product_ids' => [1091, 1106, 956, 900],
//            ),
//            array(
//                'group_id' => 4,
//                'name' => '海洋馆',
//                'en_name' => 'Sea World',
//                'title_image' => "http://hitour.qiniudn.com/0dba46680310eb3a90214f538ec5f2cb.png",
//                'product_ids' => [1058, 1087, 1470, 1450, 1258, 1371, 1259, 1440, 979, 971, 972, 954, 1060, 907, 1004, 994, 985, 1042, 1043, 1045, 1516, 902, 1008],
//            ),
//            array(
//                'group_id' => 5,
//                'name' => '动物园',
//                'en_name' => 'Zoo',
//                'title_image' => "http://hitour.qiniudn.com/7be3a8d6152fa673504a6bf109b6292e.png",
//                'product_ids' => [1075, 1086, 1090, 1270, 1019, 1116],
//            ),
//            array(
//                'group_id' => 6,
//                'name' => '日韩风',
//                'en_name' => 'J&K Series',
//                'title_image' => "http://hitour.qiniudn.com/1be529ad4c35e444357cd8f12d7b203e.png",
//                'product_ids' => [1563, 1479, 1480, 1466, 1463],
//            ),
//            array(
//                'group_id' => 7,
//                'name' => '科技馆',
//                'en_name' => 'Science and Technology Museum',
//                'title_image' => "http://hitour.qiniudn.com/1be529ad4c35e444357cd8f12d7b203e.png",
//                'product_ids' => [992, 942, 694, 736],
//            ),
//
//        );
//        return $data;
//    }
}

//"1305","1306 ","1307","1333","909","1105","1441","1336","884","885","915","1083","1091","1106","956","900","1058","1087","1470","1450","1258","1371","1259","1440","979","971","972","954","1060","907","1004","994","985","1042","1043","1045","1516","902","1008","1075","1086","1090","1270","1019","1116","1563","1479","1480","1466","1463","992","942","694","736"
