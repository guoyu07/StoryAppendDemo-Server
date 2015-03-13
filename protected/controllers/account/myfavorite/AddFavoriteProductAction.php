<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 9/3/14
 * Time: 3:03 PM
 */
class AddFavoriteProductAction extends CAction
{

    public function run()
    {
        $customer_id = Yii::app()->customer->getCustomerId();
        $product_id = (int)$this->controller->getParam('product_id', 0);
        if ($customer_id == 0 || $product_id == 0) {
            EchoUtility::echoCommonFailed('用户没登录或未传递产品ID。');

            return;
        }

        $is_favorite = HtCustomerFavoriteProduct::model()->isFavorite($product_id);
        if (!$is_favorite) {
            $item = new HtCustomerFavoriteProduct();
            $item['customer_id'] = $customer_id;
            $item['product_id'] = $product_id;
            $item['date_added'] = date('Y-m-d H:i:s');
            $result = $item->insert();
            EchoUtility::echoMsgTF($result, '收藏');
        }else{
            Yii::log('Product_id:'.$product_id.' is favorited!',CLogger::LEVEL_WARNING);
            EchoUtility::echoMsgTF(true, '收藏');
        }

    }

} 