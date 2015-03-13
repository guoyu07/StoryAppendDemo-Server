<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 9/3/14
 * Time: 3:03 PM
 */
function mysort($item1, $item2)
{
    if (empty($item2)) return -1;
    if ($item1['online']) {
        return -1;
    } else if ($item2['online']) {
        return 1;
    } else {
        return 0;
    }
}

class GetFavoriteProductsAction extends CAction
{

    public function run()
    {
        $customer_id = Yii::app()->customer->getCustomerId();
        if ($customer_id == 0) {
            EchoUtility::echoCommonFailed('用户没登录。');

            return;
        }

        $items = HtCustomerFavoriteProduct::model()->findAllByAttributes(array('customer_id' => $customer_id));
        $product_ids = ModelHelper::getList($items, 'product_id');

        $c = new CDbCriteria();
        $c->addInCondition('p.product_id', $product_ids);
        $products = HtProduct::model()->with('description', 'city', 'cover_image')->findAll($c);

        $product_list = array();
        foreach ($products as $product) {
            $product_list[$product['product_id']] = $product;
        }

        // TODO group items by city
        $result = array();
        $result['all'] = array(
            'title' => '全部',
            'products' => array(),
        );

        foreach ($items as $item) {
            $product = $product_list[$item['product_id']];
            if (empty($product)) {
                continue;
            }

            $city = $product['city'];
            $city_code = $city['city_code'];
            if (!array_key_exists($city_code, $result)) {
                $result[$city_code] = array(
                    'title' => $city['cn_name'],
                    'products' => array(),
                );
            }

            $show_prices = HtProductPricePlan::model()->getShowPrices($product['product_id']);

            $data = array(
                'product_id' => $product['product_id'],
                'cover_image' => $product['cover_image']['image_url'],
                'name' => $product['description']['name'],
                'online' => $product['status'] == 3,
                'benefit' => $product['description']['benefit'],
                'price' => $show_prices['price'],
                'orig_price' => $show_prices['orig_price'],
                'link' => $this->controller->createUrl('product/index', array('product_id' => $product['product_id'])),
            );

            $result[$city_code]['products'][] = $data;
            $result['all']['products'][] = $data;
        }

        // TODO sort data to move products not online to bottom
        foreach ($result as $key => &$value) {
            $products = $value['products'];

            usort($products, "mysort");

            $value['products'] = $products;
        }

        EchoUtility::echoMsgTF(true, '', array_values($result));
    }

} 