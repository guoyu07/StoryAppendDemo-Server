<?php

class ProductBundleController extends AdminController
{

    public function actionIsBundledProduct()
    {
        $product_id = $this->getParam('product_id');
        $product = HtProduct::model()->findByPk($product_id);
        $result = 0;
        if (!empty($product) && $product['type'] == HtProduct::T_HOTEL_BUNDLE) {
            $result = 1;
        }

        EchoUtility::echoCommonMsg(200, '', array('is_bundle_product' => $result));
    }

    public function actionGetBundleList()
    {
        $product_id = $this->getProductId();
        $result = HtProductBundle::model()->with('items')->findAll("product_id = " . $product_id);
        $result = Converter::convertModelToArray($result);
        if (!empty($result)) {
            foreach ($result as &$bundle) {
                foreach ($bundle['items'] as &$item) {
                    $item_with_pd = HtProductBundleItem::model()->with('product.description')->findByPk(array('bundle_id' => $item['bundle_id'], 'binding_product_id' => $item['binding_product_id']));
                    $item = Converter::convertModelToArray($item_with_pd);
                }
            }
        }

//        $final_data = [];
//        $prev = '';
//        foreach ($result as $bundle) {
//            if ($bundle['top_group_title'] != $prev) {
//                array_push($final_data, array('bundle_id' => $bundle['bundle_id'],
//                    'product_id' => $bundle['product_id'],
//                    'top_group_title' => $bundle['top_group_title'],
//                    'top_group_alias' => $bundle['top_group_alias'],
//                    'groups' => array(),
//                ));
//                $prev = $bundle['top_group_title'];
//            }
//
//            foreach ($final_data as &$data) {
//                if($data['bundle_id'] == $bundle['bundle_id']) {
//                    array_push($data['groups'], array(
//                        'group_id' => $bundle['group_id'],
//                        'group_title' => $bundle['group_title'],
//                        'group_type' => $bundle['group_type'],
//                        'items' => $bundle['items'],
//                    ));
//                    break;
//                }
//            }
//        }


        EchoUtility::echoCommonMsg(200, '', $result);
    }

    public function actionProductInfo()
    {
        $product_id = $this->getParam('product_id');
        $binding_product_desc = HtProductDescription::model()->find('product_id = ' . $product_id . ' and language_id = 2');
        if (!empty($binding_product_desc)) {
            $result_data['product_id'] = $product_id;
            $result_data['name'] = $binding_product_desc['name'];
            $result_data['summary'] = $binding_product_desc['summary'];
            EchoUtility::echoCommonMsg(200, '绑定商品信息查找', $result_data);
        } else {
            EchoUtility::echoCommonFailed('未找到id为 ' . $product_id . ' 的商品。');
        }
    }

    public function actionSaveBundle()
    {
        $data = $this->getPostJsonData();
        $bundle_id = $data['bundle_id'];

        if (($bundle_id == 0)) {
            $bundle = new HtProductBundle;
            ModelHelper::fillItem($bundle, $data, ['product_id', 'top_group_title', 'top_group_alias', 'group_id',
                'group_title', 'group_type']);
            $result = $bundle->insert();
            if ($result == false) {
                EchoUtility::echoCommonFailed('添加新分组失败。');

                return;
            }
            $bundle_id = $bundle['bundle_id'];
        } else {
            $bundle = HtProductBundle::model()->findByPk($bundle_id);
            ModelHelper::updateItem($bundle, $data, ['top_group_title', 'top_group_alias', 'group_id',
                'group_title', 'group_type']);
        }

        if (!empty($bundle)) {
            $bundle = Converter::convertModelToArray($bundle);

            $products = $data['items'];

            $items = [];
            foreach ($products as $product) {
                $item = HtProductBundleItem::model()->findByPk(array('bundle_id' => $bundle_id, 'binding_product_id' => $product['binding_product_id']));
                if (empty($item)) {
                    $item = new HtProductBundleItem();
                    $item['bundle_id'] = $bundle_id;
                    ModelHelper::fillItem($item, $product,
                                          ['binding_product_id', 'discount_type', 'discount_amount', 'count_type', 'count', 'display_order']);
                    $result = $item->insert();
                    if ($result == false) {
                        break;
                    }
                } else {
                    $result = ModelHelper::updateItem($item, $product,
                                                      ['discount_type', 'discount_amount', 'discount_amount', 'count_type', 'count', 'display_order']);
                    if ($result != 1) {
                        break;
                    }
                }
                array_push($items, Converter::convertModelToArray($item));
            }
        }
        HtProductPassengerRule::model()->updateHotelPassengerRule($data['product_id']);

        EchoUtility::echoMsgTF($result, '保存', array('bundle' => $bundle, 'items' => $items));
    }

    public function actionDeleteBundle()
    {
        $bundle_id = $this->getBundleId();
        HtProductBundle::model()->deleteAllByAttributes(array('bundle_id' => $bundle_id));
        HtProductBundleItem::model()->deleteAllByAttributes(array('bundle_id' => $bundle_id));
        HtProductPassengerRule::model()->updateHotelPassengerRule($this->getProductId());
        EchoUtility::echoMsgTF(true, '删除');
    }

    public function actionDeleteBundleProduct()
    {
        $bundle_id = $this->getBundleId();
        $binding_product_id = $this->getBindingProductId();
        HtProductBundleItem::model()->deleteAllByAttributes(array('bundle_id' => $bundle_id, 'binding_product_id' => $binding_product_id));
        HtProductPassengerRule::model()->updateHotelPassengerRule($this->getProductId());
        EchoUtility::echoMsgTF(true, '删除');
    }

    public function actionBundleItemChangeOrder()
    {
        $data = $this->getPostJsonData();
        foreach ($data as $item) {
            HtProductBundleItem::model()->updateByPk(array('bundle_id' => $item['bundle_id'],
                                                         'binding_product_id' => $item['binding_product_id']),
                                                     array('display_order' => $item['display_order']));
        }

        EchoUtility::echoCommonMsg(200, '更新成功！');
    }

    public function actionGetBundleHotelSpecial()
    {
        $product_id = $this->getProductId();
        $query = "SELECT pbi.binding_product_id
        FROM `ht_product_bundle` AS pb
        JOIN `ht_product_bundle_item` AS pbi
        ON pb.bundle_id = pbi.bundle_id
        JOIN `ht_product` AS p
        ON pbi.binding_product_id = p.product_id
        WHERE p.type = 7
        AND pb.product_id = " . $product_id;
        $command = Yii::app()->db->createCommand($query);
        $hotel_list = $command->queryAll();
        $hotel_desc = array();
        $hotel_special = array();

        foreach ($hotel_list as $hotel) {
            $pid = $hotel['binding_product_id'];
            $description = Converter::convertModelToArray(HtProduct::model()->with('descriptions')->findByPk($pid));
            $special_codes = HtProductSpecialCombo::getSpecialDetail($pid);
            $special_codes_wkeys = array();
            foreach($special_codes as $code) {
                $special_codes_wkeys[$code['special_id']] = $code['items'][0];
            }
            $hotel_desc[$pid] = array(
                'product_id' => $pid,
                'product_name' => $description['descriptions'][1]['name'],
                'product_en_name' => $description['descriptions'][0]['name']
            );
            $hotel_special[$pid] = array(
                'need_special_code' => HtProductSpecialCombo::model()->needSpecialCode($pid),
                'special_codes' => $special_codes_wkeys
            );
        }

        EchoUtility::echoJson(array(
                                  'description' => $hotel_desc,
                                  'special_code' => $hotel_special
                              ));
    }

    private function getBundleId()
    {
        return (int)$this->getParam('bundle_id');
    }

    private function getProductId()
    {
        return (int)$this->getParam('product_id');
    }

    private function getBindingProductId()
    {
        return (int)$this->getParam('binding_product_id');
    }
}