<?php

class AppViewController extends AController
{
    function actionProduct()
    {
        $spc = $this->getProductSpc('spc', '');
        $product_id = $this->getProductId();

        $base_data = Yii::app()->product->getBaseData($product_id);
        $land_data = Yii::app()->product->getLandData($product_id);
        $data = array_merge($base_data, $land_data);
        $data['show_prices'] = HtProductPricePlan::model()->getShowPrices($product_id, $spc);

        //Introduction
        $introduction = Yii::app()->product->getProductIntroduction($product_id);
        if(!empty($introduction)) {
            $introduction['please_read']['rules'] = $data['rules'];
            $introduction['redeem_usage']['pickup_landinfos'] = [];
            if(!empty($data['pick_landinfo_groups'][0]['landinfos'])) {
                $introduction['redeem_usage']['pick_landinfo_groups'] = $data['pick_landinfo_groups'];
            }

            $data['introduction'] = $introduction;
        }

        if(!empty($data['description']['service_include'])) {
            $matches = [];

            preg_match_all('/<li[^>]*?>(.*?)<\/li>/s', $data['description']['service_include'], $matches);

            $data['description']['service_include'] = '<p>' . implode('</p><p>', $matches[1]) . '</p>';
        }

        //Activity
        $activity = Yii::app()->activity->getActivityInfo($product_id);
        if($activity && $data['available'] == 1) {
            $data['available'] = $activity['status'] == HtActivity::AS_ONGOING ? 1 : 0;
            $data['buy_label'] = $activity['buy_label'];
        }
        $data['activity_info'] = $activity;

        //Comments
        $data['comments']['state'] = HtProductComment::getStatInfo($product_id);
        $data['comments']['items'] = HtProductComment::getProcessedComments($product_id, 0, 3);

        $data['rule_label'] = array(
            'redeem_desc' => '兑换时间',
            'return_desc' => '退换限制',
            'sale_desc' => '购买时间',
            'shipping_desc' => '发货限制'
        );

        $data['links'] = [
            'comments' => $this->createUrl('appView/productComment', ['product_id' => $product_id]),
            'detail' => $this->createUrl('appView/productDetail', ['product_id' => $product_id])
        ];

        $data['has_detail'] = HtProductTourPlan::model()->hasTourPlan($product_id);
        $data['product_location'] = json_encode(Converter::convertModelToArray(HtProductSightseeing::model()->findAllByAttributes(['product_id' => $product_id])));
        $data['sales_volume'] = HtOrder::getSalesVolume($product_id);

        $this->render('product/product', $data);
    }

    function actionEchoProduct()
    {
        $spc = $this->getProductSpc('spc', '');
        $product_id = $this->getProductId();

        $base_data = Yii::app()->product->getBaseData($product_id);
        $land_data = Yii::app()->product->getLandData($product_id);
        $data = array_merge($base_data, $land_data);
        $data['show_prices'] = HtProductPricePlan::model()->getShowPrices($product_id, $spc);

        //Introduction
        $introduction = Yii::app()->product->getProductIntroduction($product_id);
        if(!empty($introduction)) {
            $introduction['please_read']['rules'] = $data['rules'];
            $introduction['redeem_usage']['pickup_landinfos'] = [];
            if(!empty($data['pick_landinfo_groups'][0]['landinfos'])) {
                $introduction['redeem_usage']['pick_landinfo_groups'] = $data['pick_landinfo_groups'];
            }

            $data['introduction'] = $introduction;
        }

        //Activity
        $activity = Yii::app()->activity->getActivityInfo($product_id);
        if($activity && $data['available'] == 1) {
            $data['available'] = $activity['status'] == HtActivity::AS_ONGOING ? 1 : 0;
            $data['buy_label'] = $activity['buy_label'];
        }
        $data['activity_info'] = $activity;

        //Comments
        $data['comments']['state'] = HtProductComment::getStatInfo($product_id);
        $data['comments']['items'] = HtProductComment::getProcessedComments($product_id, 0, 3);

        EchoUtility::echoJson($data);
    }

    function actionProductDetail()
    {
        $product_id = $this->getProductId();
        $product = Converter::convertModelToArray(HtProduct::model()->findByPk($product_id));

        $tour_plan = HtProductTourPlan::model()->getProductTourPlan($product_id);
        $tour_plan_type = '';
        if(count($tour_plan) > 0) {
            $tour_plan_type = $tour_plan[0]['plan_type'] == '2' ? 'simple' : 'days';
            $tour_plan = $tour_plan[0];

            if($tour_plan_type == 'simple') {
                $tour_plan['groups'] = array_slice($tour_plan['groups'], 0, 1);
            }
        }

        $data = ['type' => $product['type'], 'tour_plan' => $tour_plan, 'tour_plan_type' => $tour_plan_type];

        if(empty($data['tour_plan']['title'])) {
            $data['tour_plan']['title'] = HtProductTourPlan::model()->getProductTourPlanTitle($product_id);
        }

        $this->render('product/detail', $data);
    }

    function actionProductComment()
    {
        $product_id = $this->getProductId();
        $data = ['comments' => HtProductComment::getProcessedComments($product_id, 0, 100)];

        $this->render('product/comment', $data);
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
