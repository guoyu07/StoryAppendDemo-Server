<?php

/**
 * Created by hitour.server.
 * User: xingminglister
 * Date: 12/1/14
 * Time: 12:03 PM
 */
class ProductDetailController extends AdminController
{

    public function actionDetailInfo()
    {
        $product_id = $this->getProductID();

        $product = HtProduct::model()->findByPk($product_id);
        if(empty($product)) {
            EchoUtility::echoCommonFailed('Invalid product_id: ' . $product);
            return;
        }

        $type = $product['type'];

        if(HtProduct::T_PASS == $type) {
            // TODO get album info of product
            $album_info = [];
            $more_landinfos = ['hahaha'];

            EchoUtility::echoCommonMsg(200, '', ['type' => $type, 'data' => [
                'album_info' => $album_info,
                'more_landinfos' => $more_landinfos
            ]]);
        } else if (in_array($type, [HtProduct::T_HOTEL_BUNDLE, HtProduct::T_HOTEL, HtProduct::T_COMBO, HtProduct::T_COUPON])) {
            EchoUtility::echoCommonMsg(200, '', ['type' => $type]);
        } else {
            EchoUtility::echoCommonMsg(200, '', ['type' => $type]);
        }
    }

    private function getProductID()
    {
        return (int)Yii::app()->request->getParam('product_id');
    }

} 