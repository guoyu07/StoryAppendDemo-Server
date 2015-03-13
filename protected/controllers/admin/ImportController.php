<?php

/**
 * @project hitour.server
 * @file ImportController.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 14-7-24 下午6:42
 **/
class ImportController extends AdminController
{
    public $data = array();
    public $layout = '//layouts/common';

    public function actionIndex()
    {
        $this->pageTitle = 'GTA商品导入';

        $request_urls = array(
            'fetchProducts' => $this->createUrl('product/getProducts'),
            'fetchImportedProducts' => $this->createUrl('import/getImportList'),
            'addImportProduct' => $this->createUrl('import/addImportProduct'),
            'updateImport' => $this->createUrl('import/update'),
            'cancelImport' => $this->createUrl('import/cancel')
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );
        $this->render('index');
    }

    public function actionGetImportList() {
        $items = $this->getImportItems();
        $data = array(
            'total_count' => 0,
            'data' => array_values($items)
        );

        EchoUtility::echoMsgTF(true, '成功获取导入数据', $data);
    }
    public function actionAddImportProduct() {
        $validate_result = $this->validate( $this->getPostJsonData() );

        if( $validate_result == 401 ) {
            EchoUtility::echoCommonFailed( '您要添加的商品已经存在' );
        } else if( $validate_result == 200 ) {
            $result = $this->addImport();
            $msg = $result ? '添加商品成功!' : '添加商品失败!';

            EchoUtility::echoMsg( (int)$result, '', $msg );
        } else {
            EchoUtility::echoCommonFailed( $validate_result );
        }
    }

    public function itemCmp($a, $b)
    {
        return ($a['product_id'] > $b['product_id']) ? -1 : +1;
    }

    private function getImportItems()
    {
        $items = array();
        $import_list = GtaAutoImport::model()->findAll();
        if ($import_list) {
            foreach ($import_list as $item) {
                if ($item['auto_id'] == 1) {
                    continue;
                }
                $sql = 'SELECT p.product_id, c.cn_name, pd.name FROM ht_product p ';
                $sql .= 'LEFT JOIN ht_product_description pd ON p.product_id=pd.product_id ';
                $sql .= 'LEFT JOIN ht_city c ON p.city_code=c.city_code ';
                $sql .= 'WHERE p.city_code="' . $item['city_code'] . '" ';
                $sql .= 'AND p.supplier_product_id="' . $item['item_id'] . '" ';
                $sql .= 'AND pd.language_id=2';
                $row = Yii::app()->db->createCommand($sql)->queryRow();
                $items[] = array(
                    'auto_id' => $item['auto_id'],
                    'product_id' => isset($row['product_id']) ? $row['product_id'] : 9999999,
                    'city_code' => $item['city_code'],
                    'item_code' => $item['item_id'],
                    'city_name' => isset($row['cn_name']) ? $row['cn_name'] : '',
                    'product_name' => isset($row['name']) ? $row['name'] : '',
                    'update_time' => $item['update_time'],
                    'status' => $item['status'],
                );
            }
            uasort($items, array('ImportController', 'itemCmp'));
            foreach ($items as $ikey => $item) {
                if ($item['product_id'] == 9999999) {
                    $items[$ikey]['product_id'] = '';
                }
            }
        }
        return $items;
    }

    private function validate( $data )
    {
        $city_code = $data['city_code'];
        $item_id = $data['item_id'];

        if (empty($city_code) || empty($item_id)) {
            return '城市ID或GTA商品ITEM ID为空!';
        } else if (!HtCity::model()->findByPk($city_code)) {
            return '您输入的城市ID不存在!';
        } else {
            $row = GtaAutoImport::model()->findByAttributes(array('city_code' => $city_code, 'item_id' => $item_id));
            if (empty($row)) {
                $this->data['city_code'] = $city_code;
                $this->data['item_id'] = $item_id;

                return 200;
            } else {
                return 401;
            }
        }
    }

    private function addImport()
    {
        if (empty($this->data['city_code']) || empty($this->data['item_id'])) {
            return false;
        }
        $gtaimport = new GtaAutoImport();
        $gtaimport['city_code'] = $this->data['city_code'];
        $gtaimport['item_id'] = $this->data['item_id'];
        $isok = $gtaimport->insert();

        return $isok;
    }

    public function actionUpdate()
    {
        $post_data = $this->getPostJsonData();
        $auto_id = $post_data['auto_id'] ? (int)$post_data['auto_id'] : '';

        $gtaimport = GtaAutoImport::model()->findByPk($auto_id);
        if ($gtaimport) {
            $gtaimport['status'] = 0;
            $gtaimport->update();
            EchoUtility::echoMsgTF( true, '更新' );
        } else {
            EchoUtility::echoMsgTF( false, '更新' );
        }
    }

    public function actionCancel()
    {
        $post_data = $this->getPostJsonData();
        $auto_id = $post_data['auto_id'] ? (int)$post_data['auto_id'] : '';

        $gtaimport = GtaAutoImport::model()->findByPk($auto_id);
        if ($gtaimport) {
            $gtaimport->delete();
            EchoUtility::echoMsgTF( true, '删除' );
        } else {
            EchoUtility::echoMsgTF( false, '删除' );
        }
    }

} 