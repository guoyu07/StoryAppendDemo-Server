<?php

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/15/14
 * Time: 10:59 AM
 */
class ProductController extends Controller
{
    public $staticData;
    public $resource_refs;

    public function actionIndex()
    {
        $product_id = (int)$this->getParam('product_id');
        $spc = $this->getParam('spc');

        $key = 'ProductController_header_info_' . $product_id;
        $header_info = $this->getCached($key);
        if (empty($header_info)) {
            $product_info = HtProduct::model()->with('city')->findByPk($product_id);
            if (empty($product_info)) {
                $this->redirect($this->createUrl('site/error'));
            }
            $country_info = HtCountry::model()->findByPk($product_info->city['country_code']);
            $header_info = array(
                'product_type' => $product_info['type'],
                'country'      => array(
                    'cn_name'      => $country_info['cn_name'],
                    'country_code' => $country_info['country_code'],
                    'link_url'     => ''
                ),
                'city'         => array(
                    'cn_name'   => $product_info->city['cn_name'],
                    'city_code' => $product_info->city['city_code'],
                    'link_url'  => ''
                )
            );

            $this->setCache($key, $header_info, 1 * 60 * 60);
        }

        $data = $this->initData();
        $this->current_page = 'product';
        $this->header_info = $header_info;

        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'productData'     => $this->createUrl('product/productData',
                                                      array('product_id' => $product_id, 'spc' => $spc)),
                'productScenes'   => $this->createUrl('product/landData', array('product_id' => $product_id)),
                'getBizData'      => $this->createUrl('product/saleData', array('product_id' => $product_id)),
                'addCart'         => $this->createUrl('checkout/addCart'),
                'commentStatData' => $this->createUrl('product/commentStatData', array('product_id' => $product_id)),
                'commentsData'    => $this->createUrl('product/commentsData', array('product_id' => $product_id)),
                'bindingProduct'  => $this->createUrl('product/bindingProduct')
            )
        );
        $this->staticData = array(//            str_replace(array("/","?","=","&"),'_',$this->request_urls['productData'])=>$this->datamation($this->actionProductData(true)),
        );

        $seo_setting = HtSeoSetting::model()->findByProductId($product_id);
        $this->initDataBySEOSetting($seo_setting);

        if ($header_info['product_type'] == HtProduct::T_HOTEL_BUNDLE) {
            $this->resource_refs = 'product_package.res';
            $this->render('package', $data);
        } else {
            $this->render('new', $data);
        }
    }

    public function actionProductData($rawData = false)
    {
        $product_id = (int)$this->getParam('product_id');
        $spc = $this->getParam('spc');
        $product = $this->getProductData($product_id, $spc);
        $product['spc'] = $spc;
        if ($rawData) {
            return $product;
        } else {
            EchoUtility::echoJson($product);
        }

    }

    private function getProductData($product_id, $spc = '')
    {
        global $sale;
        $product = Yii::app()->product->getBaseData($product_id);
        $land = Yii::app()->product->getLandData($product_id);
        $sale = Yii::app()->product->getSaleData($product_id, $spc);
        if ($sale['sale_rule']['sale_in_package']) {
            foreach ($sale['ticket_types'] as $tt) {
                if ($tt['ticket_type'] == HtTicketType::TYPE_PACKAGE) {
                    $sale['ticket_types'] = [$tt];
                    break;
                }
            }
        }


        /*function sort_by_price($sp1, $sp2)
        {
            global $sale;
            if (empty($sale['price_plan'][0]['price_map'])) {
                return 0;
            }
            $price_map = $sale['price_plan'][0]['price_map'];
            $p1 = empty($price_map[$sp1['special_code']]) ? '' : $price_map[$sp1['special_code']];
            $p2 = empty($price_map[$sp2['special_code']]) ? '' : $price_map[$sp2['special_code']];

            $v1 = 0;
            if (is_array($p1)) {
                foreach ($p1 as $ty => $pi) {
                    foreach ($pi as $quantity => $price) {
                        $v1 = $price['price'];
                        break;
                    }
                    break;
                }
            }

            $v2 = 0;
            if (is_array($p2)) {
                foreach ($p2 as $ty => $pi) {
                    foreach ($pi as $quantity => $price) {
                        $v2 = $price['price'];
                        break;
                    }
                    break;
                }
            }

            if ($v1 == $v2) {
                return strcmp($sp1['cn_name'], $sp2['cn_name']);
            }

            return ($v1 < $v2) ? -1 : 1;
        }

        usort($sale['special_codes'], 'sort_by_price');*/

        $product = array_merge($product, $land, $sale);

        $introduction = Yii::app()->product->getProductIntroduction($product_id);
        if (!empty($introduction)) {
            $introduction['service_include'] = Yii::app()->product->formatServiceInclude($product['description']['service_include']);
            $introduction['please_read']['rules'] = $product['rules'];
            $introduction['redeem_usage']['pickup_landinfos'] = [];
            if (!empty($product['pick_landinfo_groups'][0]['landinfos'])) {
                $introduction['redeem_usage']['pick_landinfo_groups'] = $product['pick_landinfo_groups'];
            } else {
                $introduction['redeem_usage']['pick_landinfo_groups'] = [];
            }

            $product['introduction'] = $introduction;
        }

        $stock_info = HtProductSaleStock::model()->getProductSaleStock($product_id, date('Y-m-d'));
        if (!empty($stock_info) && isset($stock_info['current_stock_num'])) {
            if ($stock_info['current_stock_num'] <= 0) {
                $product['available'] = 0;
                $product['buy_label'] = '已售罄';
            }
        }

        $activity = Yii::app()->activity->getActivityInfo($product_id);
        if ($activity && $product['available'] == 1) {
            $product['available'] = $activity['status'] == HtActivity::AS_ONGOING ? 1 : 0;
            $product['buy_label'] = $activity['buy_label'];
        }
        $product['activity_info'] = $activity;

        $activity_id = isset($activity['activity_id']) ? $activity['activity_id'] : '0';
        $ad = Yii::app()->activity->getAdBanner($activity_id, $product, 0);
        $product['ad_info'] = $ad;

        $product['is_favorite'] = HtCustomerFavoriteProduct::model()->isFavorite($product_id);

        $product['comment_stat'] = $this->getCommentsState($product_id);
        if ($product['type'] == HtProduct::T_HOTEL_BUNDLE) {
            $product['bundles'] = Yii::app()->product->getBundleProducts($product_id);
            $product['bundle'] = Yii::app()->product->getBundles($product_id);
        }

        Yii::app()->product->fillMultiDayInfo($product);

        $this->handleSaleDate($product);

        return $product;
    }

    private function handleSaleDate(&$product_data){
        if(in_array($product_data['supplier_id'],[13])){
            if(date('H')>=14 && date('H')<=23){
                $product_data['date_rule']['start'] = date('Y-m-d',strtotime('+1Day',strtotime($product_data['date_rule']['start'])));
            }
        }
    }

    private function getCommentsState($product_id)
    {
        return HtProductComment::getStatInfo($product_id);
    }

    public function actionMobileProductData()
    {
        $product_id = (int)$this->getParam('product_id');
        $spc = $this->getParam('spc', '');
        $base_data = Yii::app()->product->getBaseData($product_id);
        $land_data = Yii::app()->product->getLandData($product_id);

        $base_data['show_prices'] = HtProductPricePlan::model()->getShowPrices($product_id, $spc);
        $base_data['next_product'] = Yii::app()->product->getNextProduct($product_id);
        if ($base_data['next_product']) {
            $base_data['next_product']['images'] = array(
                'cover' => $base_data['next_product']['cover_image']
            );
        }

        $product = array_merge($base_data, $land_data);
        $introduction = Yii::app()->product->getProductIntroduction($product_id);
        if (!empty($introduction)) {
            $introduction['please_read']['rules'] = $product['rules'];
            $introduction['redeem_usage']['pickup_landinfos'] = [];
            if (!empty($product['pick_landinfo_groups'][0]['landinfos'])) {
                $introduction['redeem_usage']['pick_landinfo_groups'] = $product['pick_landinfo_groups'];
            } else {
                $introduction['redeem_usage']['pick_landinfo_groups'] = [];
            }

            $product['introduction'] = $introduction;
        }

        $product['date_rule'] = Yii::app()->product->getDateRule($product_id);
        $product['special_info'] = Yii::app()->product->getSpecialCodes($product_id);
        $product['price_plan'] = HtProductPricePlan::model()->getPricePlanWithMap($product_id);

        $product['seo'] = HtSeoSetting::model()->findByProductId($product_id);
        $stock_info = HtProductSaleStock::model()->getProductSaleStock($product_id, date('Y-m-d'));
        if (!empty($stock_info) && isset($stock_info['current_stock_num'])) {
            if ($stock_info['current_stock_num'] <= 0) {
                $product['available'] = 0;
                $product['buy_label'] = '已售罄';
            }
        }

        $activity = Yii::app()->activity->getActivityInfo($product_id);
        if ($activity && $product['available'] == 1) {
            $product['available'] = $activity['status'] == HtActivity::AS_ONGOING ? 1 : 0;
            $product['buy_label'] = $activity['buy_label'];
        }
        $product['activity_info'] = $activity;


        $activity_id = isset($activity['activity_id']) ? $activity['activity_id'] : '0';
        $ad = Yii::app()->activity->getAdBanner($activity_id, $product, 1);
        $product['ad_info'] = $ad;
        $product['is_favorite'] = HtCustomerFavoriteProduct::model()->isFavorite($product_id);

        $product['comments']['state'] = $this->getCommentsState($product_id);
        $product['comments']['items'] = HtProductComment::model()->getProcessedComments($product_id, 0, 1);

        if ($product['type'] == HtProduct::T_HOTEL_BUNDLE) {
            $product['bundle'] = Yii::app()->product->getBundles($product_id);
        }

        Yii::app()->product->fillMultiDayInfo($product);
//        if ($product['type'] == HtProduct::T_MULTI_DAY) {
//            $recommendation = HtProductTripIntroduction::model()->getTripIntroductionByProductId($product['product_id']);
//            $trip_highlight = HtTripHighlight::model()->getProductTripHighlights($product['product_id']);
//            $product['multi_day_general']['recommendation'] = $recommendation;
//            $product['multi_day_general']['trip_highlight'] = $trip_highlight;
//            foreach ($product['multi_day_general']['trip_highlight']['highlight_refs'] as &$ref) {
//                $ref['local_highlight'] = str_replace(array("\n", "\r\n", "\r"), "\n", $ref['local_highlight']);
//                $arr = explode("\n" , $ref['local_highlight']);
//                $ref['local_highlight'] = $arr;
//            }
//        }

        $this->handleSaleDate($product);
        EchoUtility::echoJson($product);
    }

    public function actionMobileCheckoutData()
    {
        $product_id = (int)$this->getParam('product_id');
        $product = Yii::app()->product->getBaseData($product_id);
        $sale = Yii::app()->product->getSaleData($product_id);
        if ($sale['sale_rule']['sale_in_package']) {
            foreach ($sale['ticket_types'] as $tt) {
                if ($tt['ticket_type'] == HtTicketType::TYPE_PACKAGE) {
                    $sale['ticket_types'] = [$tt];
                    break;
                }
            }
        }

        $product = array_merge($product, $sale);
        $product['spc'] = $this->getParam('spc');
        $activity = Yii::app()->activity->getActivityInfo($product_id);
        $product['activity_info'] = $activity;
        EchoUtility::echoJson($product);
    }

    public function actionCommentStatData()
    {
        $product_id = (int)$this->getParam('product_id');
        $statInfo = $this->getCommentsState($product_id);
        EchoUtility::echoCommonMsg(200, '', $statInfo);
    }

    public function actionCommentsData()
    {
        $product_id = (int)$this->getParam('product_id');
        $page_index = (int)$this->getParam('page', 0);
        $data = HtProductComment::model()->getProcessedComments($product_id, $page_index);
        EchoUtility::echoCommonMsg(200, '', $data);
    }

    public function actionMobileCommentsData()
    {
        $product_id = (int)$this->getParam('product_id');
        $page_index = (int)$this->getParam('page_index', 0);
        $counts = (int)$this->getParam('counts', 5);
        $data = HtProductComment::model()->getProcessedComments($product_id, $page_index, $counts);
        EchoUtility::echoCommonMsg(200, '', $data);
    }

    public function actionBindingProduct()
    {
        $parent_id = $this->getParam('parent_id', 0);
        $binding_product_id = $this->getParam('product_id');
        $product = Yii::app()->product->getBindingProductDetail($binding_product_id, $parent_id);
        EchoUtility::echoJson($product);
    }
}
