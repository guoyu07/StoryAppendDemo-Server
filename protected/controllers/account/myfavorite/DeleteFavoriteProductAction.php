<?php
/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 9/3/14
 * Time: 3:03 PM
 */

class DeleteFavoriteProductAction extends CAction{

    public function run() {
        $customer_id = Yii::app()->customer->getCustomerId();
        $product_id = (int)$this->controller->getParam('product_id', 0);
        if($customer_id == 0 || $product_id == 0) {
            EchoUtility::echoCommonFailed('用户没登录或未传递产品ID。');
            return;
        }

        $result = HtCustomerFavoriteProduct::model()->deleteByPk(array('customer_id' => $customer_id, 'product_id' => $product_id));

        EchoUtility::echoMsgTF($result, '取消收藏');
    }

} 