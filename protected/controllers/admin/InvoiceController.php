<?php

/**
 * Created by PhpStorm.
 * User: zhifan
 * Date: 14-8-5
 * Time: 15:03
 */
class InvoiceController extends AdminController
{
    public $layout = '//layouts/common';

    public function actionIndex()
    {
        $this->pageTitle = '对账';
        $request_urls = array(
            'getSupplierInvoiceList' => $this->createUrl('invoice/getSupplierInvoiceList'),
            'addSupplierInvoice' => $this->createUrl('invoice/addSupplierInvoice'),
            'uploadFile' => $this->createUrl('invoice/uploadFile'),
            'deleteFile' => $this->createUrl('invoice/deleteFile'),
            'edit' => $this->createUrl('invoice/edit', array('invoice_id' => ''))
        );
        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );
        $this->render('index');
    }

    public function actionEdit()
    {
        $this->pageTitle = '对账';
        $invoice_id = (int)Yii::app()->request->getParam('invoice_id');
        $request_urls = array(
            'getSupplierInvoiceList' => $this->createUrl('invoice/getSupplierInvoiceList'),
            'getSupplierInvoiceDetail' => $this->createUrl('invoice/getSupplierInvoiceDetail',
                                                           array('invoice_id' => $invoice_id)),
            'addSupplierInvoice' => $this->createUrl('invoice/addSupplierInvoice'),
            'uploadFile' => $this->createUrl('invoice/uploadFile', array('invoice_id' => $invoice_id)),
            'deleteFile' => $this->createUrl('invoice/deleteFile'),
            'updateInvoice' => $this->createUrl('invoice/updateInvoice'),
            'getInvoiceOperationList' => $this->createUrl('invoice/getInvoiceOperationList', array('order_id' => '')),
            'updateInvoiceStatus' => $this->createUrl('invoice/updateInvoiceStatus',
                                                      array('invoice_id' => $invoice_id)),
            'searchInvoiceOrder' => $this->createUrl('invoice/searchInvoiceOrder'),
            'getInvoiceOrderList' => $this->createUrl('invoice/getInvoiceOrderList',
                                                      array('invoice_id' => $invoice_id)),
        );
        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );
        $this->render('edit');
    }

    //获取供应商对账单历史
    public function actionGetSupplierInvoiceList()
    {
        $data = $this->getPostJsonData();
        $supplier_id = !empty($data['query_filter']) ? (int)$data['query_filter']['supplier_id'] : '';

        $order_invoice = '';
        $c = new CDbCriteria();
        if ($supplier_id) {
            $c->addCondition(" supplier_id = " . $supplier_id);
        }
        $c->order = " invoice_date DESC";
        $order_invoice = HtOrderInvoice::model()->findAll($c);
        $order_invoice = Converter::convertModelToArray($order_invoice);
        if(is_array($order_invoice) && count($order_invoice) > 0) {
            foreach($order_invoice as &$invoice) {
                //对账成功、失败的订单
                $success = HtOrderInvoice::model()->getTotalsByInvoiceStatus($invoice['invoice_id'], 1);
                $fail = HtOrderInvoice::model()->getTotalsByInvoiceStatus($invoice['invoice_id'], 2);
                $invoice['success'] = $success;
                $invoice['fail'] = $fail;
            }
        }
        EchoUtility::echoMsgTF(true, '', array('data' => $order_invoice));
    }

    //获取供应商对账单详情
    public function actionGetSupplierInvoiceDetail()
    {
        $invoice_id = (int)Yii::app()->request->getParam('invoice_id');
        $order_invoice = HtOrderInvoice::model()->find('invoice_id = ' . $invoice_id);
        $order_invoice = Converter::convertModelToArray($order_invoice);
        //对账成功、失败的订单
        $success = HtOrderInvoice::model()->getTotalsByInvoiceStatus($invoice_id, 1);
        $fail = HtOrderInvoice::model()->getTotalsByInvoiceStatus($invoice_id, 2);
        $order_invoice['success'] = $success;
        $order_invoice['fail'] = $fail;
        $supplier = HtSupplier::model()->findByPk($order_invoice['supplier_id']);
        $order_invoice['supplier_name'] = $supplier['name'];
        $order_invoice['supplier_id'] = $supplier['supplier_id'];

        EchoUtility::echoMsgTF(true, '', $order_invoice);
    }

    //新增对账单
    public function actionAddSupplierInvoice()
    {
        $data = $this->getPostJsonData();
        $supplier_id = (int)$data['supplier_id'];
        $order_invoice = new HtOrderInvoice();
        $order_invoice['supplier_id'] = $supplier_id;
        $order_invoice['invoice_date'] = date('Y-m-d');
        $order_invoice['invoice_doc'] = $data['invoice_doc'];
        $order_invoice->insert();
        //生成对账单编号
        $order_invoice['invoice_sn'] = date('Y-m-d') . '-' . $order_invoice->getPrimaryKey();
        $result = $order_invoice->update();
        EchoUtility::echoMsgTF($result, '新增', Converter::convertModelToArray($order_invoice));
    }

    //上传附件
    public function actionUploadFile()
    {
        $supplier_id = $_POST['supplier_id'];
        $invoice_id = !empty($_POST['invoice_id']) ? $_POST['invoice_id'] : 0;
        $invoice_path = dirname(Yii::app()->BasePath) . '/' . Yii::app()->params['INVOICE_PATH'] . $supplier_id . '/' . date('Ymd') . '/';
        $invoice_base_url = Yii::app()->params['WEB_PREFIX'] . Yii::app()->params['INVOICE_PATH'] . $supplier_id . '/' . date('Ymd') . '/';
        $result = FileUtility::uploadFile($invoice_path, array('pdf', 'xlsx', 'xls'), '请选择pdf或excel文件。', true, true);
        if ($result['code'] == 200) {
            $file = $result['file'];
            //更新对账单附件
            if ($invoice_id) {
                $item = HtOrderInvoice::model()->findByPk($invoice_id);
                $item['invoice_doc'] = $invoice_base_url . $file;
                $item->update();
            }
            EchoUtility::echoCommonMsg(200, '上传成功！',
                                       array('invoice_name' => $file, 'invoice_path' => $invoice_base_url . $file));

        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    //删除附件
    public function actionDeleteFile()
    {
        $data = $this->getPostJsonData();
        $invoice_real_path = dirname(Yii::app()->BasePath) . $data['invoice_doc'];
        $isok = @unlink($invoice_real_path);
        EchoUtility::echoMsgTF($isok, '删除');
    }

    //更新对账单备注
    public function actionUpdateInvoice()
    {
        $data = $this->getPostJsonData();
        $item = HtOrderInvoice::model()->findByPk($data['invoice_id']);
        $item['remark'] = $data['remark'];
        $result = $item->update();
        EchoUtility::echoMsgTF($result, '保存');
    }

    //获取对账单操作历史
    public function actionGetInvoiceOperationList()
    {
        $order_id = (int)Yii::app()->request->getParam('order_id');
        $invoice_history = array();
        if ($order_id) {
            $invoice_history = HtOrderInvoiceHistory::model()->findAll('order_id = ' . $order_id . ' order by insert_time desc');
            $invoice_history = Converter::convertModelToArray($invoice_history);
            if (is_array($invoice_history) && count($invoice_history)) {
                foreach ($invoice_history as &$history) {
                    $order_invoice = HtOrderInvoice::model()->findByPk($history['invoice_id']);
                    $order_info = Yii::app()->order->getBaseInfoWithoutProductDetail($order_id);
                    $history['invoice_sn'] = $order_invoice['invoice_sn'];
                    $history['product_name'] = $order_info['product_name'];
                    $description = $this->getRefundDescription($order_id);
                    $history['order_status'] = $order_info['status_name'].$description;
                }
            }
        }
        EchoUtility::echoMsgTF(true, '', array('data' => $invoice_history));
    }

    //更新对账状态
    public function actionUpdateInvoiceStatus()
    {
        $invoice_id = (int)Yii::app()->request->getParam('invoice_id');
        $data = $this->getPostJsonData();
        $invoice_history = new HtOrderInvoiceHistory();
        $invoice_history['invoice_id'] = $invoice_id;
        $invoice_history['insert_id'] = Yii::app()->user->id;
        $invoice_history['insert_time'] = date('Y-m-d H:i:s');
        //如果原因为空则不修改问题原因
        if ($data['reason']) {
            $columns = array('order_id', 'status', 'reason', 'remark');
        } else {
            $columns = array('order_id', 'status', 'remark');
        }

        ModelHelper::fillItem($invoice_history, $data, $columns);
        $invoice_history->insert();
        //更新订单最终状态
        $result = true;
        $item = HtOrderInvoiceStatus::model()->findByPk($data['order_id']);
        if ($item) {
            $item['invoice_status'] = $data['status'];
            $result = $item->update();
        } else {
            $item = new HtOrderInvoiceStatus();
            $item['invoice_status'] = $data['status'];
            $item['order_id'] = $data['order_id'];
            $result = $item->insert();
        }
        EchoUtility::echoMsgTF($result, '更新');
    }

    //搜索需对账订单
    public function actionSearchInvoiceOrder()
    {
        $return = array();
        $result = array();
        $data = $this->getPostJsonData();
        if (!empty($data['query_filter']) && is_array($data['query_filter']) && $data['query_filter']['has_combination'] == 1) {
            $orders = HtOrder::model()->getOrders($data);
            if (is_array($orders) && count($orders) > 0) {
                foreach ($orders as $order) {
                    $return['order_id'] = $order['order_id'];
                    $return['product_name'] = $order['name'];
                    $special_info = HtProductSpecialCombo::model()->getSpecialDetail($order['product_id'],$order['special_code']);
                    $special_value = '';
                    if($special_info){
                        foreach($special_info[0]['items'] as $special) {
                            $special_value .= $special['cn_name'].' ';
                        }
                    }
                    $return['special_name'] = $special_value;
                    $return['date_added'] = date("Y-m-d", strtotime($order['date_added']));
                    $return['tour_date'] = $order['tour_date'];
                    $return['confirmation_ref'] = $order['confirmation_ref']?$order['confirmation_ref']:($order['hitour_booking_ref']?$order['hitour_booking_ref']:$order['supplier_booking_ref']);
                    $passenger_info = Yii::app()->order->getPassengerInfo($order['order_id']);
                    if (is_array($passenger_info['pax_data']) && count($passenger_info['pax_data']) > 0) {
                        $return['leader_name'] = $passenger_info['pax_data'][0]['en_name'] . '/' . $passenger_info['pax_data'][0]['zh_name'];
                    }
                    //人数
                    if (is_array($passenger_info['pax_quantities']) && count($passenger_info['pax_quantities']) > 0) {
                        $passenger_quantity_str = '';
                        foreach ($passenger_info['pax_quantities'] as $k => $v) {
                            $passenger_quantity_str .= $passenger_info['pax_ticket'][$k]['cn_name'];
                            $passenger_quantity_str .= 'X';
                            $passenger_quantity_str .= $v . ' ';
                        }
                        $return['passenger_quantity_str'] = $passenger_quantity_str;
                    }
                    $invoice_status = HtOrderInvoiceStatus::model()->findByPk($order['order_id']);
                    $return['invoice_status'] = $invoice_status['invoice_status'] ? $invoice_status['invoice_status'] : 0;
                    $description = $this->getRefundDescription($order['order_id']);
                    $return['order_status'] = $order['cn_name'] . $description;
                    if (empty($description)) {
                        $return['status_id'] = $order['status_id'];
                    } else {
                        $return['status_id'] = 999;
                    };
                    $result[] = $return;
                }
            }
        } else {
            $confirmation_ref = $data['confirmation_ref'];
//        $confirmation_ref = 'VVKF006948';
            $c = new CDbCriteria;
            $c->compare('confirmation_ref', $confirmation_ref,true);
            $supplier_order = HtSupplierOrder::model()->find($c);
            $supplier_order_id = $supplier_order['supplier_order_id'];

            if ($supplier_order_id) {
                $order_product = HtOrderProduct::model()->find('supplier_order_id = ' . $supplier_order_id);
                $order_id = $order_product['order_id'];
                $order_info = Yii::app()->order->getBaseInfoWithoutProductDetail($order_id);
                $passenger_info = Yii::app()->order->getPassengerInfo($order_id);
                $return['order_id'] = $order_id;
                $return['confirmation_ref'] = $supplier_order['confirmation_ref'];
                $return['tour_date'] = $order_info['tour_date'];
                $return['product_name'] = $order_info['product_name'];
                $description = $this->getRefundDescription($order_id);
                $return['order_status'] = $order_info['status_name'] . $description;
                if (empty($description)) {
                    $return['status_id'] = $order_info['status_id'];
                } else {
                    $return['status_id'] = 999;
                };
                if (is_array($passenger_info['pax_data']) && count($passenger_info['pax_data']) > 0) {
                    $return['leader_name'] = $passenger_info['pax_data'][0]['en_name'] . '/' . $passenger_info['pax_data'][0]['zh_name'];
                }
                $invoice_status = HtOrderInvoiceStatus::model()->findByPk($order_id);
                $return['invoice_status'] = $invoice_status['invoice_status'] ? $invoice_status['invoice_status'] : 0;
                $return['cost_total'] = $order_product['cost_total'];
                $result[] = $return;
            }
        }

        EchoUtility::echoMsgTF(true, '', $result);
    }

    //查询对账单对账列表
    public function actionGetInvoiceOrderList()
    {
        $invoice_id = (int)Yii::app()->request->getParam('invoice_id');
        $data = $this->getPostJsonData();
        $filter_status = $data['invoice_status'];
        $c = new CDbCriteria;
        $c->addCondition('invoice_id = ' . $invoice_id);
        $c->select = 'order_id';
        $c->distinct = true;
        $invoice_orders = HtOrderInvoiceHistory::model()->findAll($c);
        $invoice_orders = Converter::convertModelToArray($invoice_orders);
        $order_list = array();
        if (is_array($invoice_orders) && count($invoice_orders) > 0) {
            foreach ($invoice_orders as $order) {
                $item = array();
                $order_id = $order['order_id'];
                //对账状态
                $invoice_status = HtOrderInvoiceStatus::model()->findByPk($order_id);
                $item['invoice_status'] = $invoice_status['invoice_status'] ? $invoice_status['invoice_status'] : 0;
                //按对账状态过滤
                if ($filter_status) {
                    if ($filter_status != $item['invoice_status']) continue;
                }
                $orders = HtOrder::model()->getOrders(array('query_filter' => array('filter_order_id' => $order_id, 'has_combination' => 0)));
                //订单信息
                if($orders && count($orders) > 0){
                    $item['order_id'] = $order_id;
                    $item['tour_date'] = $orders[0]['tour_date'];
                    $item['product_name'] = $orders[0]['name'];
                    $special_info = HtProductSpecialCombo::model()->getSpecialDetail($orders[0]['product_id'],$orders[0]['special_code']);
                    $special_value = '';
                    if($special_info){
                        foreach($special_info[0]['items'] as $special) {
                            $special_value .= $special['cn_name'].' ';
                        }
                    }
                    $item['special_name'] = $special_value;
                    $description = $this->getRefundDescription($order_id);
                    $item['order_status'] = $orders[0]['cn_name'] . $description;
                    if (empty($description)) {
                        $item['status_id'] = $orders[0]['status_id'];
                    } else {
                        $item['status_id'] = 999;
                    }
                    $item['date_added'] = date("Y-m-d", strtotime($orders[0]['date_added']));
                    $item['confirmation_ref'] = $orders[0]['confirmation_ref'];
                }


                //领队
                $passenger_info = Yii::app()->order->getPassengerInfo($order_id);
                if (is_array($passenger_info['pax_data']) && count($passenger_info['pax_data']) > 0) {
                    $item['leader_name'] = $passenger_info['pax_data'][0]['en_name'] . '/' . $passenger_info['pax_data'][0]['zh_name'];
                }
                //人数
                if (is_array($passenger_info['pax_quantities']) && count($passenger_info['pax_quantities']) > 0) {
                    $passenger_quantity_str = '';
                    foreach ($passenger_info['pax_quantities'] as $k => $v) {
                        $passenger_quantity_str .= $passenger_info['pax_ticket'][$k]['cn_name'];
                        $passenger_quantity_str .= 'X';
                        $passenger_quantity_str .= $v . ' ';
                    }
                    $item['passenger_quantity_str'] = $passenger_quantity_str;
                }
                array_push($order_list, $item);
            }
        }
        EchoUtility::echoMsgTF(true, '', $order_list);
    }

    //特殊退款状态
    private function getRefundDescription($order_id)
    {
        $payment_history = HtPaymentHistory::model()->findByAttributes(array('order_id' => $order_id, 'pay_or_refund' => 0));
        $return = '';
        if ($payment_history) {
            switch ($payment_history['refund_reason']) {
                case 2:
                    $return = '（部分退订）';
                    break;
                case 3:
                    $return = '（退押金）';
                    break;
                case 4:
                    $return = '（理赔）';
                    break;
                case 16:
                    $return = '（手工记录退款）';
                    break;
                default:
                    $return = '';
            }
        }

        return $return;
    }
}