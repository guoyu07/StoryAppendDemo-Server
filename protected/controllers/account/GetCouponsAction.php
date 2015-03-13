<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 8/27/14
 * Time: 11:52 AM
 */
class GetCouponsAction extends CAction
{

    public function run()
    {
        // get coupons of current user
        $customer_id = Yii::app()->customer->getCustomerId();

        if ($customer_id == 0) {
            EchoUtility::echoCommonFailed('请先登录！');

            return;
        }

        $coupons = HtCoupon::model()->with('history',
                                           'use_limit')->findAllByAttributes(array('customer_id' => $customer_id, 'status' => 1));

        $code_list = [];
        $result = [];
        foreach ($coupons as $coupon) {
            $valid_type = 0;
            $limit_type = 1;
            if(is_array($coupon['use_limit']) && count($coupon['use_limit']) > 0){
                $valid_type = $coupon['use_limit'][0]['valid_type'];
                $limit_type = $coupon['use_limit'][0]['limit_type'];
            }
            $history = $coupon['history'];
            $used_times = 0;
            $my_history = null;
            foreach ($history as $h) {
                if ($h['customer_id'] == $customer_id) {
                    $used_times++;
                    $my_history = $h;
                }
            }

            $ids = ModelHelper::getList($coupon['use_limit'], 'id');
            $limit_data = $this->getLimitInfo($ids,$valid_type);

            $could_use = (count($limit_data) > 0) ? $limit_type : 1;

            array_push($code_list, $coupon['code']);
            array_push($result, array(
                'code' => $coupon['code'],
                'description' => $coupon['description'],
                'discount' => HtCoupon::getCouponDiscount($coupon['discount'], $coupon['type']),
                'used_times' => $used_times,
                'date_start' => $coupon['date_start'],
                'date_end' => $coupon['date_end'],
                'expired' => strtotime($coupon['date_end']) < time() ? 0 : 1,
                'history' => $my_history,
                'could_use' => $could_use,
                'valid_type' =>  $valid_type,
                'limit_ids' => $limit_data
            ));
        }


        // TODO find coupons that pickup from dandelion coupons
        $pickups = HtDandelionPickup::model()->with('coupon',
                                                    'use_limit')->findAllByAttributes(array('customer_id' => $customer_id));

        foreach ($pickups as $pickup) {
            $coupon = $pickup['coupon'];
            $valid_type = 0;
            $limit_type = 1;
            if(is_array($pickup['use_limit']) && count($pickup['use_limit']) > 0){
                $valid_type = $pickup['use_limit'][0]['valid_type'];
                $limit_type = $pickup['use_limit'][0]['limit_type'];
            }

            if (in_array($coupon['code'], $code_list)) {
                continue;
            }

            $history = HtCouponHistory::model()->findAllByAttributes(array('coupon_id' => $coupon['coupon_id']));
            $used_times = 0;
            $my_history = null;
            foreach ($history as $h) {
                if ($h['customer_id'] == $customer_id) {
                    $used_times++;
                    $my_history = $h;
                }
            }

            $ids = ModelHelper::getList($pickup['use_limit'], 'id');
            $limit_data = $this->getLimitInfo($ids,$valid_type);

            $could_use = (count($limit_data) > 0) ? $limit_type : 1;

            array_push($result, array(
                'code' => $coupon['code'],
                'description' => $coupon['description'],
                'discount' => HtCoupon::getCouponDiscount($coupon['discount'], $coupon['type']),
                'used_times' => $used_times,
                'date_start' => $coupon['date_start'],
                'date_end' => $coupon['date_end'],
                'expired' => strtotime($coupon['date_end']) < time() ? 0 : 1,
                'history' => $my_history,
                'could_use' => $could_use,
                'valid_type' =>  $valid_type,
                'limit_ids' => $limit_data
            ));
        }

        EchoUtility::echoCommonMsg(200, '获取优惠券信息成功！', $result);

    }

    private function getLimitInfo($ids,$valid_type)
    {
        $count = count($ids);
        $data = array();
        if ($count > 0) {
            if($valid_type == 1){
                // TODO collect product data
                $c = new CDbCriteria();
                $c->addInCondition('p.product_id', $ids);
                $products = HtProduct::model()->with('description')->findAll($c);
                foreach ($products as $product) {
                    $data[] = array(
                        'product_id' => $product['product_id'],
                        'name' => $product['description']['name'],
                        'url' => $this->controller->createUrl('product/index', array('product_id' => $product['product_id'])),
                    );
                }
            }else if($valid_type == 2){
                // TODO collect city data
                $c = new CDbCriteria();
                $c->with = 'country';
                $c->addInCondition('city.city_code', $ids);
                $cities = HtCity::model()->findAll($c);
                foreach ($cities as $city) {
                    $data[] = array(
                        'id' => $city['city_code'],
                        'name' => $city['cn_name'],
                        'url' => str_replace(array("+"),'_',$this->controller->createUrl('city/index', array('city_name' => $city['en_name'], 'country_name' => $city['country']['en_name']))),
                    );
                }
            }else if($valid_type == 3){
            // TODO collect country data
                $c = new CDbCriteria();
                $c->addInCondition('cnt.country_code', $ids);
                $countries = HtCountry::model()->findAll($c);
                foreach ($countries as $country) {
                    $data[] = array(
                        'id' => $country['country_code'],
                        'name' => $country['cn_name'],
                        'url' => str_replace(array("+"),'_',$this->controller->createUrl('country/index', array('en_name' => $country['en_name']))),
                    );
                }
            }
        }

        return $data;
    }
}