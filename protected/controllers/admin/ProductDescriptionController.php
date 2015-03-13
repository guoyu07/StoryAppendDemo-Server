<?php

/**
 * Created by hitour.server.
 * User: xingminglister
 * Date: 12/3/14
 * Time: 4:45 PM
 */
class ProductDescriptionController extends AdminController
{
    public function actionGetProductDescription($product_id)
    {
        $pd = HtProductDescription::model()->getFieldValues($product_id, ['summary', 'description', 'benefit',
            'service_include', 'how_it_works', 'package_service_title', 'package_gift_title', 'package_recommend_title',
            'package_service', 'package_gift', 'package_recommend']);

        $result = array('product_id' => $product_id);

        ModelHelper::fillItem($result, $pd, ['cn_summary', 'cn_description', 'cn_benefit',
            'cn_package_service_title', 'cn_package_gift_title', 'cn_package_recommend_title',
            'cn_package_service', 'cn_package_gift', 'cn_package_recommend']);

        $result['cn_service_include'] = html_entity_decode($pd['cn_service_include']);
        $result['cn_how_it_works'] = html_entity_decode($pd['cn_how_it_works']);
        $result['product_detail'] = $this->createUrl('product/detail', ['product_id' => $product_id]);

        EchoUtility::echoCommonMsg(200, '', $result);
    }

    public function actionUpdateProductDescription()
    {
        $data = $this->getPostJsonData();

        $result = HtProductDescription::model()->updateFieldValues($this->getProductID(),
                                                                   ['summary', 'description', 'benefit', 'service_include', 'how_it_works', 'package_service_title', 'package_gift_title', 'package_recommend_title',
                                                                       'package_service', 'package_gift', 'package_recommend'],
                                                                   $data);

        $result = $result ? 1 : 0;

        EchoUtility::echoCommonMsg(200, $result == 1 ? '产品信息更新成功！' : '产品信息更新失败。', $data);
    }

    public function actionUpdateProductDescriptionNew()
    {
        $data = $this->getPostJsonData();
        $pd = HtProductDescription::model()->findByPk(array('product_id' => $this->getProductID(), 'language_id' => 2));
        $pd['description'] = isset($data['cn_description']) ? $data['cn_description'] : '';
        $pd['benefit'] = isset($data['cn_benefit']) ? $data['cn_benefit'] : '';
        $pd['service_include'] = isset($data['cn_service_include']) ? $data['cn_service_include'] : '';
        $pd['summary'] = isset($data['cn_summary']) ? $data['cn_summary'] : '';
        $result = $pd->update();

        EchoUtility::echoMsgTF($result, '更新');
    }

    public function actionProductIntroduction()
    {
        $product_id = $this->getProductID();

        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        if ('get' === $request_method) {
            // get 兑换方法，使用方法，注意事项 from new table
            $data = HtProductIntroduction::model()->findByPk($product_id);
            if (empty($data)) {
                $data = new HtProductIntroduction();
                $data['product_id'] = $product_id;
                $data['redeem_note'] = '';
                $data['buy_note'] = '';
                $data['usage'] = '';
                $data['tips'] = '';

                $data->insert();
            }

            $result = Converter::convertModelToArray($data);

            $result['buy_note'] = html_entity_decode($result['buy_note']);
            $result['redeem_note'] = html_entity_decode($result['redeem_note']);
            $result['tips'] = html_entity_decode($result['tips']);
            $result['usage'] = html_entity_decode($result['usage']);

            EchoUtility::echoCommonMsg(200, 'ok', $result);
        } else {
            if ('post' === $request_method) {
                $this->updateField();
            }
        }
    }

    private function updateField($field = '')
    {
        $product_id = $this->getProductID();

        $data = $this->getPostJsonData();

        $item = HtProductIntroduction::model()->findByPk($product_id);
        if (isset($data['status']) && $data['status'] == 1) {
            // check whether status could be updated
            if(!empty($item) && !empty($item['buy_note']) && strlen($item['buy_note']) > 60) {
            } else {
                EchoUtility::echoCommonFailed('请填写购买须知。');
                return;
            }
        }

        $result = ModelHelper::updateItem($item, $data, empty($field) ? [] : [$field]);

        EchoUtility::echoMsgTF($result == 1, '更新');
    }

    private function getProductID()
    {
        return (int)Yii::app()->request->getParam('product_id');
    }

} 