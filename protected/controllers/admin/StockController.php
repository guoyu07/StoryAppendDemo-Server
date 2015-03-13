<?php

class StockController extends AdminController
{

    public $layout = '//layouts/common';

    public function actionIndex()
    {
        $this->pageTitle = '产品库存列表';

        $request_urls = array(
            //Insurance
            'fetchAvailableInsurance' => $this->createUrl('productStock/getRemainInsuranceCounts'),
            'uploadInsuranceStock' => $this->createUrl('productStock/uploadInsurance'),

            //Stock Info
            'viewStockHistoryUrl' => $this->createUrl('stock/history', array('product_id' => '')),
            'fetchTicketTypes' => $this->createUrl('productPrice/ticketTypes'),
            'fetchProductStock' => $this->createUrl('stock/stockList'),
            'fetchDuplicatedStock' => $this->createUrl('productStock/fileDuplicated', array('batch_id' => '')),
            'fetchInspectStock' => $this->createUrl('productStock/getUnconfirmed', array('batch_id' => '')),
            'confirmStock' => $this->createUrl('productStock/confirm', array('batch_id' => '')),
            'deleteStock' => $this->createUrl('productStock/delete', array('batch_id' => '')),

            //Upload
            'uploadProductStock' => $this->createUrl('productStock/uploadFile'),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('index');
    }

    public function actionHistory()
    {
        $this->pageTitle = '库存历史';

        $product_id = (int)Yii::app()->request->getParam('product_id');

        $request_urls = array(
            'fetchStockHistory' => $this->createUrl('productStock/stockHistory', array(
                'product_id' => $product_id)),
            'fetchDuplicatedStock' => $this->createUrl('productStock/fileDuplicated', array('batch_id' => '')),
            'fetchInspectStock' => $this->createUrl('productStock/getUnconfirmed', array('batch_id' => '')),
            'confirmStock' => $this->createUrl('productStock/confirm', array('batch_id' => '')),
            'deleteStock' => $this->createUrl('productStock/delete', array('batch_id' => '')),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('history');
    }

    public function actionStockList()
    {
        $data = HtProductShippingRule::model()->with('product.supplier')->findAll("booking_type = '" . HtProductShippingRule::BT_STOCK . "'");
        $stockProducts = Converter::convertModelToArray($data);

        $product_ids = array();
        $product_info = array();
        foreach ($stockProducts as $item) {
            $m_name = $item['product']['supplier']['name'] . '/' . $item['product']['supplier']['cn_name'];

            $p_id = (int)$item['product']['product_id'];
            $product = Converter::convertModelToArray(HtProduct::model()->with('city',
                                                                               'description')->findByPk($p_id));

            if (!($product['status'] == 3 && $product['is_combo'] == 0)) {
                continue;
            }

            if (!in_array($product['product_id'], $product_ids)) {
                $product_ids[] = $product['product_id'];
                $i['supplier_name'] = $m_name;
                $i['product_id'] = $product['product_id'];
                $i['product_name'] = $product['description']['name'];
                $i['city_name'] = $product['city']['en_name'] . '/' . $product['city']['cn_name'];
                $product_info[] = $i;
            }
        }
        foreach ($product_info as &$product) {
            $ticketRule = Converter::convertModelToArray(HtProductTicketRule::model()->with('ticket_type')->findAll('product_id = ' . $product['product_id']));
            $left_ticket = '';
            $product['ticket_ids'] = '';
            foreach ($ticketRule as $ticket) {
                $Criteria = new CDbCriteria();
                $Criteria->addCondition("product_id = " . $product['product_id']);
                $Criteria->addCondition("order_id = 0");
                $Criteria->addCondition("ticket_id = " . $ticket['ticket_id']);
                $Criteria->addCondition("status = 1");
                $count = HtProductStockPdf::model()->count($Criteria);
                $left_ticket .= $ticket['ticket_type']['cn_name'] . $count . ',';
                $product['ticket_ids'] .= ',' . $ticket['ticket_id'];
            }
            $product['left_ticket'] = substr($left_ticket, 0, -1);
            $product['ticket_ids'] = substr($product['ticket_ids'], 1);
            $product['batch_id'] = -1;
            $product['status'] = -1;
            $Criteria = new CDbCriteria();
            $Criteria->select = 'batch_id,status';
            $Criteria->addCondition("product_id = " . $product['product_id']);
            $Criteria->order = "upload_time DESC";
            $Criteria->limit = 1;
            $status_info = Converter::convertModelToArray(HtProductStockPdfHistory::model()->find($Criteria));
            if ($status_info) {
                $product['batch_id'] = $status_info['batch_id'];
                $product['status'] = $status_info['status'];
            }
        }

        EchoUtility::echoMsgTF(true, '获取库存列表', array(
            'total_count' => 0,
            'data' => $product_info
        ));
    }
}