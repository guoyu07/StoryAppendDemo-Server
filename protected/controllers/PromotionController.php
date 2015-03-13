<?php

class PromotionController extends Controller
{
    public $resource_refs = 'promotion.res';

    public function actionIndex()
    {
        $data = $this->initData();

        $promotion_id = $this->getPromotionID();
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'getPromotionDetail' => $this->createUrl('promotion/promotionDetail',
                    array('promotion_id' => $promotion_id))
            )
        );

        $seo_setting = HtSeoSetting::model()->findByPromotionId($promotion_id);
        $this->initDataBySEOSetting($seo_setting);

        $this->render('main', $data);
    }

    private function getPromotionID()
    {
        return (int)$this->getParam('promotion_id');
    }

    public function actionPromotionDetail()
    {
        $promotion_id = $this->getPromotionID();
        $result = HtPromotion::model()->fetchPromotionDetail($promotion_id);

        EchoUtility::echoJson($result);
    }

    public function actionHotelplus()
    {
        $this->resource_refs = 'hotel_plus_aggregation.res';
        $this->initData();
        $this->setPageTitle('酒店预订_海外酒店推荐_热门境外酒店查询_半价境外景点门票_玩途自由行');
        $this->setKeywords('海外酒店,境外酒店,全球酒店预订,自由行酒店推荐,新加坡酒店,泰国酒店,韩国酒店,普吉岛酒店');
        $this->setDescription('海外酒店预订,玩途为您提供境外数百城市的酒店预订,酒店价格查询,优质的境外酒店推荐、以及海外目的地景点门票半价预订。玩途自由行为您的境外目的地自由行全程服务。');

        $this->render('hotel_plus', Array('data' => $this->actionHotelplusData(true)));
    }

//    public function actionHotelplusData($flag = false)
//    {
//        $product_ids = [3161, 3192,3535, 3167, 3132, 3245, 3345, 3362, 3403];
//
//        $data = array();
//
//        foreach($product_ids as $pid) {
//            $product = Converter::convertModelToArray(HtProduct::model()->with('city', 'description', 'cover_image')->findByPk($pid));
//            if(!empty($product)) {
//                if($product['type'] != HtProduct::T_HOTEL_BUNDLE || $product['status'] != HtProduct::IN_SALE) {
//                    continue;
//                }
//
//                $product_data = [];
//                $product_data['city'] = $product['city'];
//                if(in_array($product['city_code'],['SIN','SEL','HKT'])){
//                    $product_data['city']['link_url'] = $product['city']['link_url'] . '/hotel_plus';
//                }else{
//                    $product_data['city']['link_url'] = $product['city']['link_url'] . '#Hotel Packages';
//                }
//                $product_data['link_url'] = $product['link_url'];
//                $product_data['description'] = [
//                    'name' => $product['description']['name'],
//                    'cover_image' => $product['cover_image']['image_url'],
//                    'summary' => explode("\n", $product['description']['summary']),
//                ];
//                $product_data['show_prices'] = HtProductPricePlan::model()->getShowPrices($pid);
//                $product_data['star_level'] = 3;
//
//                $hotels = [];
//                $bundle = Converter::convertModelToArray(HtProductBundle::model()->with('items')->findByAttributes(['product_id' => $pid, 'group_type' => HtProductBundle::GT_SELECTION]));
//                if(!empty($bundle['items'])) {
//                    foreach($bundle['items'] as $item) {
//                        $hotels[] = $item['binding_product_id'];
//                    }
//                }
//
//                $criteria = new CDbCriteria();
//                $criteria->addInCondition('product_id', $hotels);
//                $criteria->addCondition(['order' => 'star_level']);
//
//                $hotel = HtProductHotel::model()->find($criteria);
//                if(!empty($hotel)) {
//                    $product_data['star_level'] = $hotel['star_level'];
//                }
//
//                $data[] = $product_data;
//
//            }
//        }
//
//        if(!$flag) {
//            EchoUtility::echoJson($data);
//        } else return $data;
//    }

    public function actionHotelplusData($flag = false)
    {
        $promotion_id = '33';
        $data = HtPromotion::model()->fetchHotelplusDetail($promotion_id);
        if(!$flag) {
            EchoUtility::echoJson($data);
        } else return $data;
    }
}