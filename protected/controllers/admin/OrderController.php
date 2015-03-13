<?php

class OrderController extends AdminController
{
    public $layout = '//layouts/fullwidth';

    public function actionIndex()
    {
        $this->pageTitle = '订单管理';

        $this->layout = '//layouts/common';

        $request_urls = array(
            'getSuppliers' => $this->createUrl('supplier/getSuppliers'),
            'getOrderTotals' => $this->createUrl('order/getOrderTotalsAndSupplierTotals'),
            'searchResult' => $this->createUrl('order/search'),
            'edit' => $this->createUrl('order/edit', array('order_id' => '0000', 'search' => '')),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('summary_list');
    }

    public function actionSearch()
    {
        $this->pageTitle = '订单管理';
        $this->layout = '//layouts/common';

        $request_urls = array(
            'getSuppliers' => $this->createUrl('supplier/getSuppliers'),
            'getOrderTotals' => $this->createUrl('order/getOrderTotals'),
            'getOrderList' => $this->createUrl('order/getOrderList'),
            'searchResult' => $this->createUrl('order/search'),
            'edit' => $this->createUrl('order/edit', array('order_id' => '0000', 'search' => '')),
            'getOrderStatusList' => $this->createUrl('order/getOrderStatusList'),
            'getUnShippedOrderCostAmount' => $this->createUrl('order/getUnShippedOrderCostAmount',
                                                              array('supplier_id' => ''))
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('search_list');
    }

    public function actionEdit()
    {
        $this->pageTitle = '编辑订单';
        $this->layout = '//layouts/common';

        $order_id = $this->getOrderID();
        $voucher_name = Yii::app()->request->getParam('voucher_name');

        $request_urls = array(
            'listResult' => $this->createUrl('order/search', array('search' => '')),

            'getOrderDetail' => $this->createUrl('order/getOrderDetail', array('order_id' => $order_id)),
            'orderComments' => $this->createUrl('order/OrderComments', array('order_id' => $order_id)),
            'orderPayInfo' => $this->createUrl('order/OrderPayInfo', array('order_id' => $order_id)),

            'getDepartures' => $this->createUrl('order/getDeparturesByDate', array('order_id' => $order_id)),

            'updateShipping' => $this->createUrl('order/saveShippingInfo', array('order_id' => $order_id)),
            'confirmUpdateShipping' => $this->createUrl('order/saveShippingInfo',
                                                        array('confirm' => 1, 'order_id' => $order_id)),
            'updateTourDate' => $this->createUrl('order/saveTourDate', array('order_id' => $order_id)),
            'updatePassengers' => $this->createUrl('order/savePassengers', array('order_id' => $order_id)),
            'getPassenger' => $this->createUrl('order/getPassenger', array('order_id' => $order_id)),
            'updateContact' => $this->createUrl('order/saveContacts', array('order_id' => $order_id)),

            'editCouponUrl' => $this->createUrl('coupon/edit', array('coupon_id' => '')),

            'uploadVoucher' => $this->createUrl('order/uploadVoucher', array('order_id' => $order_id)),
            'deleteVoucher' => $this->createUrl('order/deleteVoucher',
                                                array('order_id' => $order_id, 'voucher_name' => $voucher_name)),

            'doShipping' => $this->createUrl('order/doShipping', array('order_id' => $order_id)),
            'rebookingOrder' => $this->createUrl('order/rebookingOrder', array('order_id' => $order_id)),
            'bookingOrder' => $this->createUrl('order/bookingOrder', array('order_id' => $order_id)),

            'refundOrder' => $this->createUrl('order/refundOrder', array('order_id' => $order_id)),
            'returnOrder' => $this->createUrl('order/returnOrder', array('order_id' => $order_id)),
            'saveRefund' => $this->createUrl('order/saveRefund', array('order_id' => $order_id)),
            'refuseReturn' => $this->createUrl('order/refuseReturn', array('order_id' => $order_id)),
            'sendOrderProcessing' => $this->createUrl('order/sendOrderProcessing', array('order_id' => $order_id)),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('edit');
    }

    private function getOrderOfType($type = 'array')
    {

        $c = new CDbCriteria();
        $c->select = 'supplier_id,name,cn_name';
        $c->order = 'name ASC';
        $data = HtSupplier::model()->findAll($c);
        $supplier_list = Converter::convertModelToArray($data);

        $return = array();
        $total = array(
            'notshipped' => 0,
            'needrefund' => 0,
            'question' => 0,
            'todo' => 0
        );
        foreach ($supplier_list as $key => $value) {
            $ikey = $type == 'array' ? $key : $value['supplier_id'];
            $return[$ikey]['supplier_id'] = $value['supplier_id'];
            $return[$ikey]['name'] = $value['name'];
            $return[$ikey]['cn_name'] = $value['cn_name'];
            //待发货
            $filter = array(
                'filter_supplier_id' => $value['supplier_id'],
                'filterNotShipped' => '1'
            );
            $order_total = HtOrder::model()->getMainTotalOrders($filter);
            //$order_total = rand(0, 3000);
            $return[$ikey]['notshipped'] = $order_total;
            $total['notshipped'] += $order_total;
            //待退款
            $filter = array(
                'filter_supplier_id' => $value['supplier_id'],
                'filterNeedRefund' => '1'
            );
            $order_total = HtOrder::model()->getMainTotalOrders($filter);
            //$order_total = rand(0, 3000);
            $return[$ikey]['needrefund'] = $order_total;
            $total['needrefund'] += $order_total;
            //问题
            $filter = array(
                'filter_supplier_id' => $value['supplier_id'],
                'filterQuestion' => '1'
            );
            $order_total = HtOrder::model()->getMainTotalOrders($filter);
            //$order_total = rand(0, 3000);
            $return[$ikey]['question'] = $order_total;
            $total['question'] += $order_total;
            //待办
            $filter = array(
                'filter_supplier_id' => $value['supplier_id'],
                'filterToDo' => '1'
            );
            $order_total = HtOrder::model()->getMainTotalOrders($filter);
            //$order_total = rand(0, 3000);
            $return[$ikey]['todo'] = $order_total;
            $total['todo'] += $order_total;
        }

        if ($type == 'object') $return['total'] = $total;

        return $return;

    }

    public function actionGetOrderTotals()
    {
        $data = $this->getPostJsonData();
        $result['data'] = HtOrder::model()->getTotalOrderCounts($data['query_filter'], []);
        EchoUtility::echoMsgTF(true, '获取订单数量列表成功!', $result);
    }

    public function actionGetOrderTotalsAndSupplierTotals()
    {
        $data = $this->getPostJsonData();
        $result['data'] = HtOrder::model()->getTotalOrderCounts($data['query_filter'], $data['sort']);

        $all_counts = [];
        if ($data['query_filter']['filter_supplier_id'] != 0) {
            $data['query_filter']['filter_supplier_id'] = 0;
            $all_counts = HtOrder::model()->getTotalOrderCounts($data['query_filter'], $data['sort']);
        } else {
            $all_counts = $result['data'];
        }

        $supplierTotal['name'] = '所有供应商';
        $supplierTotal['supplier_id'] = 0;
        $supplierTotal['need_refund'] = 0;
        $supplierTotal['not_shipped'] = 0;
        $supplierTotal['question'] = 0;
        $supplierTotal['todo'] = 0;
        foreach ($all_counts as $order) {
            $supplierTotal['need_refund'] += $order['need_refund'];
            $supplierTotal['not_shipped'] += $order['not_shipped'];
            $supplierTotal['question'] += $order['question'];
            $supplierTotal['todo'] += $order['todo'];
        }
        array_unshift($result['data'], $supplierTotal);
        EchoUtility::echoMsgTF(true, '获取订单数量列表成功!', $result);
    }

    public function actionGetOrderList()
    {
        $data = $this->getPostJsonData();
        $order_total = HtOrder::model()->getOrders($data, 1);
        $orders = HtOrder::model()->getOrders($data);
        $return['data'] = $orders;
        $return['total_count'] = $order_total[0]['total'];
        $return['insurance_code_unused'] = HtInsuranceCode::model()->count('order_id = 0');
        echo CJSON::encode(array('code' => 200, 'msg' => '获取订单列表成功！', 'data' => $return));
    }

    public function actionGetOrderStatusList()
    {
        $status = HtOrderStatus::model()->findAll();
        $status = Converter::convertModelToArray($status);
        $return['status'] = $status;
        EchoUtility::echoMsgTF(true, '获取订单状态列表成功!', $return);
    }

    public function actionGetOrderDetail()
    {
        $order_id = $this->getOrderID();

        //1. 订单基本信息
        $baseInfo = Yii::app()->order->getBaseInfo($order_id);
        if (empty($baseInfo)) {
            echo CJSON::encode(array('code' => 400, 'msg' => '订单不存在！'));

            return;
        }

        //  get those of every product
        if ($baseInfo['product_count'] > 1) {
            for ($i = 0; $i < 4; $i++) {
                foreach ($baseInfo['products']['group_' . $i] as &$product) {
                    list ($rule_desc, $special_codes) = $this->getMoreInfoOnProduct($product['product_id']);
                    $product['rule_desc'] = $rule_desc;
                    $product['special_info'] = $special_codes;
                    if (0 == $i) {
                        $product['pricePlanInfo'] = Yii::app()->order->getPricePlanInfo($product);
                    }
                }
            }
        } else {
            list ($rule_desc, $special_codes) = $this->getMoreInfoOnProduct($baseInfo['product']['product_id']);
            $baseInfo['product']['rule_desc'] = $rule_desc;
            $baseInfo['product']['special_info'] = $special_codes;
//            $baseInfo['product']['pricePlanInfo'] = Yii::app()->order->getPricePlanInfo($baseInfo['product']);
        }

        //2. 旅客信息
        $passengerInfo = Yii::app()->order->getPassengerTotalInfo($order_id);
        //3. 联系人信息
        $contactsInfo = Yii::app()->order->getContactsInfo($order_id);
        //4. 订单历史
        $statusHistory = Yii::app()->order->getStatusHistory($order_id);
        //5. 发货规则
        $shippingInfo = Yii::app()->order->getShippingInfo($order_id);
        //6. 营销活动信息
        $activityInfo = Yii::app()->order->getActivityInfo($baseInfo);

        $return['baseInfo'] = $baseInfo;
        $return['passengerInfo'] = $passengerInfo;
        $return['contactsInfo'] = $contactsInfo;
        $return['statusHistory'] = $statusHistory;
        $return['shippingInfo'] = $shippingInfo;
        $return['activityInfo'] = $activityInfo;
        $return['canShipping'] = Yii::app()->order->checkOrderShipping($order_id);
        $return['canReturn'] = Yii::app()->order->checkOrderReturn($order_id);
        $return['canReShipping'] = Yii::app()->order->checkOrderReShipping($order_id);
        $return['needBooking'] = Yii::app()->order->checkOrderBooking($order_id);
        $return['needReBooking'] = Yii::app()->order->checkOrderReBooking($order_id);

        echo CJSON::encode(array('code' => 200, 'msg' => '获取订单详情成功！', 'data' => $return));
    }

    private function getMoreInfoOnProduct($product_id)
    {
        $rule_desc = Yii::app()->product->getRuleDesc($product_id);
        $special_codes = HtProductSpecialCombo::model()->getSpecialDetail($product_id);

        return [$rule_desc, $special_codes];
    }

    public function actionOrderComments()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $order_id = $this->getOrderID();

        if ($request_method == 'get') {
            $c = new CDbCriteria();
            $c->select = '*';
            $c->addCondition('order_id =' . $order_id);
            $c->order = 'date_added DESC';
            $data = HtOrderComment::model()->with('complaint')->findAll($c);
            $return = Converter::convertModelToArray($data);
            foreach ($return as $k => $v) {
                switch ($v['proc_status']) {
                    case 1:
                        $return[$k]['status_name'] = '待处理';
                        break;
                    case 2:
                        $return[$k]['status_name'] = '已处理';
                        break;
                    default:
                        $return[$k]['status_name'] = '备注';
                }
                $user = User::model()->findByPk($v['user_id']);
                $return[$k]['user_name'] = $user['screen_name'];
            }
            echo CJSON::encode(array('code' => 200, 'msg' => '获取订单备注成功！', 'data' => $return));
        } else if ($request_method == 'post') {
            $data = $this->getPostJsonData();
            $item = HtOrderComment::model()->findByPk($data['comment_id']);
            if (empty($item)) {
                $item = new HtOrderComment();
                $item['order_id'] = $order_id;
                $item['user_id'] = Yii::app()->user->id;
                $item['comment'] = $data['comment'];
                $item['proc_status'] = $data['proc_status'];
                $item['date_added'] = date("Y-m-d H:i:s", time());
                $item['date_modified'] = date("Y-m-d H:i:s", time());
                $item['date_proc'] = date($data['date_proc']);
                $item['type'] = $data['type'];
                $result = $item->insert();
                $returnComplaint = array();
                if($item['type']=='7') {
                    foreach ($data['complaint'] as $k => $v) {
                        $complaint = new HtOrderCommentComplaint();
                        $complaint['comment_id'] = $item->getPrimaryKey();
                        $complaint['complaint_type'] = $v['complaint_type'];
                        $complaint['complaint_md'] = $v['complaint_md'];
                        $complaint['detail_type'] = $v['detail_type'];
                        $complaint['detail_md'] = $v['detail_md'];
                        $resultComplaint = $complaint->insert();
                        $result = $result && $resultComplaint;
                        array_push($returnComplaint,HtOrderCommentComplaint::model()->findByPk($complaint->getPrimaryKey()));
                    }
                }
                $user = User::model()->findByPk(Yii::app()->user->id);
                EchoUtility::echoMsgTF($result, '添加',
                                       array('comment_id' => $item->getPrimaryKey(), 'user_id' => Yii::app()->user->id, 'user_name' => $user['screen_name'],
                                           'comment' => $data['comment'], 'proc_status' => $data['proc_status'], 'date_added' => date("Y-m-d H:i:s",
                                                                                                                                      time()),
                                           'date_proc' => $data['date_proc'], 'type' => $data['type'], 'date_modified' => date("Y-m-d H:i:s", time()),
                                           'complaint' => $returnComplaint));
            } else {
                $item['proc_status'] = $data['proc_status'];
                $item['date_modified'] = date("Y-m-d H:i:s", time());
//                $item['user_id'] = Yii::app()->user->id;
                $item['type'] = $data['type'];
                $item['comment'] = $data['comment'];
                $item['date_proc'] = date($data['date_proc']);
                $result = $item->update();
                $returnComplaint = array();
                //删除废除的投诉条目
                $complaintIds = array();
                foreach ($data['complaint'] as $k => $v) {
                    if(!empty($v['complaint_id'])) {
                        array_push($complaintIds, $v['complaint_id']);
                    }
                }
                $currentComplaint = HtOrderCommentComplaint::model()->findAll('comment_id = '.$data['comment_id']);
                foreach ($currentComplaint as $k => $v) {
                    if(!in_array($v['complaint_id'], $complaintIds)) {
                        HtOrderCommentComplaint::model()->deleteByPk($v['complaint_id']);
                    }
                }
                if($item['type']=='7') {
                    foreach ($data['complaint'] as $k => $v) {
                        if(empty($v['complaint_id'])) {
                            $complaint = new HtOrderCommentComplaint();
                            $complaint['comment_id'] = $item->getPrimaryKey();
                            $complaint['complaint_type'] = $v['complaint_type'];
                            $complaint['complaint_md'] = $v['complaint_md'];
                            $complaint['detail_type'] = $v['detail_type'];
                            $complaint['detail_md'] = $v['detail_md'];
                            $resultComplaint = $complaint->insert();
                            $result = $result && $resultComplaint;
                            array_push($returnComplaint,HtOrderCommentComplaint::model()->findByPk($complaint->getPrimaryKey()));
                        } else {
                            $complaint = HtOrderCommentComplaint::model()->findByPk($v['complaint_id']);
                            $complaint['complaint_type'] = $v['complaint_type'];
                            $complaint['complaint_md'] = $v['complaint_md'];
                            $complaint['detail_type'] = $v['detail_type'];
                            $complaint['detail_md'] = $v['detail_md'];
                            $resultComplaint = $complaint->update();
                            $result = $result && $resultComplaint;
                            array_push($returnComplaint,HtOrderCommentComplaint::model()->findByPk($complaint->getPrimaryKey()));
                        }

                    }
                }
                EchoUtility::echoMsgTF($result, '修改', array('comment' => $item['comment'], 'comment_id' => $item['comment_id'],
                                                            'date_added' => $item['date_added'], 'date_modified' => $item['date_modified'],
                                                            'date_proc' => $item['date_proc'], 'order_id' => $item['order_id'],
                                                            'proc_status' => $item['proc_status'], 'type' => $item['type'],
                                                            'user_id' => $item['user_id'], 'complaint'=>$returnComplaint));
            }
        } else {
            $comment_id = (int)$this->getParam('comment_id');

            $result = HtOrderComment::model()->deleteByPk($comment_id);

            $resultComplaint = HtOrderCommentComplaint::model()->deleteAll('comment_id = '.$comment_id);

            EchoUtility::echoMsgTF($result > 0, '删除');
        }
    }

    public function actionOrderPayInfo()
    {
        $order_id = $this->getOrderID();
        $order = HtOrder::model()->findByPk($order_id);
        if (!$order) {
            echo CJSON::encode(array('code' => 400, 'msg' => '没有找到订单信息！'));
            return;
        }
        $pay_list = PayUtility::paymentMethods();
        if ($order['payment_method'] == 'alipay_direct') {
            $order['payment_method'] = 'alipay_pc';
        }
        $return['payment_method'] = $pay_list[$order['payment_method']]['title']; //支付方式
        $payment_history = HtPaymentHistory::model()->find('order_id = ' . $order_id);
        if (!$payment_history) {
            echo CJSON::encode(array('code' => 400, 'msg' => '没有支付信息！'));

            return;
        }
        $return['pay_price'] = $payment_history['trade_total']; //支付金额
        $return['trade_id'] = $payment_history['trade_id']; //交易编号
        $data = HtOrderProduct::model()->findAll('order_id = ' . $order_id);
        $order_products = Converter::convertModelToArray($data);
        if (!empty($order_products) && is_array($order_products)) {
            $return['product_price_total'] = $order['total']; //订购商品总金额
            $ticket_price = array();
            foreach($order_products as $order_product) {
                $data = HtOrderProductPrice::model()->with('ticket_type')->findAll('order_product_id = ' . $order_product['order_product_id']);
                $order_product_price = Converter::convertModelToArray($data);
                foreach ($order_product_price as $item) {
                    $price = array();
                    $price['ticket_name'] = $item['ticket_type']['cn_name'];
                    $price['quantity'] = $item['quantity'];
                    $price['ticket_price'] = $item['price'];
                    $price['ticket_price_total'] = $item['quantity'] * $item['price'];
                    $ticket_price[] = $price;
                }
            }
            $return['ticket_price'] = $ticket_price;
        }

        $data = HtCouponHistory::model()->with('coupon')->findAll('order_id = ' . $order_id);
        $coupon_info = Converter::convertModelToArray($data);
        foreach ($coupon_info as $k => $v) {
            $coupon_info[$k]['code'] = $v['coupon']['code'];
            unset($coupon_info[$k]['coupon']);
        }
        $return['coupon_info'] = $coupon_info;
        //退款信息
        $return['remain_refund_amount'] = $return['pay_price'];
        $refund_result = HtPaymentHistory::model()->findAll('order_id = ' . $this->getOrderID() . ' AND pay_or_refund = 0');
        $refund_info = Converter::convertModelToArray($refund_result);

        if (is_array($refund_info) && count($refund_info) > 0) {
            $refund_total = 0;
            foreach ($refund_info as &$refund) {
                $refund_total += $refund['trade_total'];
                switch ($refund['refund_reason']) {
                    case 1:
                        $refund['refund_reason_desc'] = '正常退订';
                        break;
                    case 2:
                        $refund['refund_reason_desc'] = '部分退订';
                        break;
                    case 3:
                        $refund['refund_reason_desc'] = '退押金';
                        break;
                    case 4:
                        $refund['refund_reason_desc'] = '理赔';
                        break;
                    case 16:
                        $refund['refund_reason_desc'] = '退款记录';
                        $return['refund_info']['refund_type'] = '已经退款，需要记录';
                        break;
                    default:
                        $refund['refund_reason_desc'] = '未知原因';
                }
            }
            $return['remain_refund_amount'] = $return['pay_price'] - $refund_total;

        }
        $order = HtOrder::model()->findByPk($order_id);
        $now_status = $order->status_id;
        if ($now_status == HtOrderStatus::ORDER_RETURN_CONFIRMED) {
            $return['refund_info']['refund_type'] = '由系统退款';
        }
        if ($now_status == HtOrderStatus::ORDER_RETURN_REJECTED) {
            $return['refund_info']['refund_type'] = '拒绝退款';
        }
        $return['refund_info']['refund_history'] = $refund_info;
        echo CJSON::encode(array('code' => 200, 'msg' => '获取支付信息成功！', 'data' => $return));

    }

    private function getOrderID()
    {
        return (int)Yii::app()->request->getParam('order_id');
    }

    public function actionSaveTourDate()
    {
        $data = $this->getPostJsonData();
        $item = HtOrderProduct::model()->find('order_id = ' . $this->getOrderID() . ' AND product_id = ' . $data['product_id']);
        $item['tour_date'] = $data['tour_date'];
        $item['departure_code'] = $data['departure_code'];
        $item['departure_time'] = $data['time'];
        $return = $item->update();
        EchoUtility::echoMsgTF($return, '更新');
    }

    public function actionSaveContacts()
    {
        $data = $this->getPostJsonData();
        $item = HtOrder::model()->find('order_id = ' . $this->getOrderID());
        $item['contacts_name'] = $data['contacts_name'];
        $item['contacts_telephone'] = $data['contacts_telephone'];
        $item['contacts_email'] = $data['contacts_email'];
        $return = $item->update();
        EchoUtility::echoMsgTF($return, '更新联系人信息');
    }

    public function actionSavePassengers()
    {
        $result = array('code' => 200, 'msg' => '保存出行人信息成功！');

        $passengers = $this->getPostJsonData();
        if (empty($passengers)) {
            $result = array('code' => 400, 'msg' => '保存出行人信息失败：数据为空！');
        } else {
            foreach ($passengers as $pkey => $passenger) {
                $isok = HtPassenger::model()->updateMe($passenger);
                if ($isok === false) {
                    $result = array('code' => 500, 'msg' => '保存出行人信息失败：更新数据出错！');
                }
            }
        }
        echo CJSON::encode($result);
    }

    public function actionGetPassenger()
    {
        $passengerInfo = array();
        $order_id = $this->getOrderID();
        $data = $this->getPostJsonData();
        $order_product = HtOrderProduct::model()->findByAttributes(array('order_id'=>$order_id,'product_id'=>$data['product_id']));
        $order_passenger = HtOrderPassenger::model()->findAllByOrder($order_id,$order_product['order_product_id']);
        $passengerInfo['pax_data'] = array();
        foreach($order_passenger as $p){
            if($p['passenger_id'] == $data['passenger_id']){
                array_push($passengerInfo['pax_data'],$p);
            }
        }
        $passengerInfo = array_merge($passengerInfo,
                                     HtProductPassengerRule::model()->getPassengerRule($data['product_id']));
        $passengerInfo['pax_quantities'] = HtOrderProductPrice::model()->calcRealQuantities($order_product['order_product_id'],
                                                                                            $data['product_id']);
        $tickets = HtTicketType::model()->findAll();
        foreach (Converter::convertModelToArray($tickets) as $ticket) {
            $passengerInfo['pax_ticket'][$ticket['ticket_id']]['cn_name'] = $ticket['cn_name'];
            $passengerInfo['pax_ticket'][$ticket['ticket_id']]['en_name'] = $ticket['en_name'];
        }
        echo CJSON::encode(array('code' => 200, 'msg' => '获取出行人信息成功！', 'data' => $passengerInfo));
    }

    public function actionUploadVoucher()
    {
        $item = HtOrder::model()->findByPk($_POST['order_id']);
        $voucher_path = trim($item->attributes['voucher_path']);

        $result = FileUtility::uploadFile($voucher_path, array('pdf', 'png', 'jpg'), '请选择pdf/png/jpg文件。', true, true);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $info = pathinfo($file);
            if($info['extension'] == 'png' || $info['extension'] == 'jpg') {
                $img_file = $voucher_path . $file;

                $pdf_filename = $info['filename'] . '.pdf';
                $pdf_file = $voucher_path . $pdf_filename;

                system("convert \"$img_file\" \"$pdf_file\"");
                $file = $pdf_filename;
            }

            EchoUtility::echoCommonMsg(200, '上传成功！',
                                       array('voucher_name' => $file, 'voucher_url' => $item->attributes['voucher_base_url'] . $file));
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionDeleteVoucher()
    {
        $data = $this->getPostJsonData();
        $item = HtOrder::model()->findByPk($data['order_id']);
        $voucher_real_path = $item->attributes['voucher_path'] . $data['voucher_name'];
        $isok = @unlink($voucher_real_path);
        if ($isok || !file_exists($voucher_real_path)) {
            $order_data = HtOrder::model()->findByPk($data['order_id']);
            $order_data = Converter::convertModelToArray($order_data);
            if (!empty($order_data)) {
                $result = $this->clearVoucherData($data['supplier_order_id'], $data['voucher_name']);
            } else {
                $result = array('code' => 401, 'msg' => '更新供应商订单失败!');
            }
        } else {
            $result = array('code' => 400, 'msg' => '删除失败!');
        }
        echo CJSON::encode($result);
    }

    public function actionGetDeparturesByDate()
    {
        $data = $this->getPostJsonData();
        $result['departure_list'] = Yii::app()->product->getDepartureListByDate($data['product_id'],
                                                                                $data['tour_date']);
        echo CJSON::encode(array('code' => 200, 'msg' => '获取departure list成功！', 'data' => $result));
    }

    private function clearVoucherData($supplier_order_id, $voucher_name)
    {
        $result = array('code' => 200, 'msg' => '删除成功!');
        $supplier_order = HtSupplierOrder::model()->findByPk($supplier_order_id);
        if (!empty($supplier_order)) {
            $new_vouchers = array();
            $vouchers = CJSON::decode($supplier_order['voucher_ref']);
            if (!empty($vouchers)) {
                foreach ($vouchers as $voucher) {
                    if (trim($voucher) != trim($voucher_name)) {
                        array_push($new_vouchers, $voucher);
                    }
                }
            }
            $supplier_order['voucher_ref'] = CJSON::encode($new_vouchers);
            $isok = $supplier_order->update();
            if ($isok === false) {
                Yii::log('Delete voucher[' . $voucher_name . '] ok, but update DB failed.');
                $result = array('code' => 500, 'msg' => '删除成功，更新数据失败!');
            }
        } else {
            $result = array('code' => 402, 'msg' => '未找到对应的供商订单!');
        }

        return $result;
    }

    public function actionSaveShippingInfo()
    {
        $data = $this->getPostJsonData();
        $confirm = Yii::app()->request->getParam('confirm');
        $order_products = Yii::app()->order->getProductsOfOrder($this->getOrderID());

        // TODO handle order that have more than one products

        $order_product = $order_products[0];

        if (isset($order_product['product']['supplier_id'])) {
            $result = $this->saveShippingData($data, $confirm);
            $stocked = Yii::app()->order->checkOrderStocked($this->getOrderID());
            if ($stocked) {
                Yii::app()->stateMachine->switchStatus($this->getOrderID(), HtOrderStatus::ORDER_TO_DELIVERY);
            }
        } else {
            $result = array('code' => 300, 'msg' => '订单不存在或不需要编辑发货信息');
        }
        EchoUtility::echoCommonMsg($result['code'], $result['msg']);
    }

    public function actionDoShipping()
    {
        $order_id = $this->getOrderID();
        $with_pdf = Yii::app()->request->getParam('with_pdf');
        $canShipping = Yii::app()->order->checkOrderShipping($order_id);
        $canReShipping = Yii::app()->order->checkOrderReShipping($order_id);
        if ($canShipping || $canReShipping) {
            $comment = ($canShipping && !$canReShipping) ? '发货:' : '再次发货:'; //再次发货时需要更新状态
            if (Yii::app()->shipping->needBackendShipping($order_id)) {
                $isok = Yii::app()->shipping->backendShipping($order_id, $comment);
                if ($isok) {
                    $result = array('code' => 200, 'msg' => '发货指令已提交，此订单需要较长的发货时间，请５分钟后回来查看');
                }else{
                    $result = array('code' => 400, 'msg' => '发货指令提交失败');
                }
            }else{
                $result = Yii::app()->shipping->shippingOrder($order_id,false,$with_pdf);
                $comment .= '返回码[' . $result['code'] . ']说明[' . $result['msg'] . ']';
                if ($result['code'] == 200) {
                    $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_SHIPPED, $comment);
                    Yii::log('[Shipping action]: Order[' . $order_id . '] shipping successfully.', CLogger::LEVEL_INFO);
                } else {
                    $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_SHIPPING_FAILED,
                        $comment);
                    Yii::log('[Shipping action]: Order[' . $order_id . '] shipping failed. code[' . $result['code'] . ']',
                        CLogger::LEVEL_ERROR);
                }
            }
        } else {
            $result = array('code' => 300, 'msg' => '不满足发货条件,请检查');
        }

        echo CJSON::encode($result);
    }

    public function actionGetUnShippedOrderCostAmount()
    {
        $supplier_id = Yii::app()->request->getParam('supplier_id');
        $result = HtOrder::model()->getUnShippedOrderCostAmount($supplier_id);
        echo CJSON::encode(array('code' => 200, 'msg' => '获取信息成功！', 'data' => $result));
    }

    private function saveShippingData($data, $confirm = 0)
    {
        $result = array('code' => 200, 'msg' => '保存发货信息成功！');
        $return = true;
        if (empty($data) || !is_array($data)) {
            $return = false;
        }
        foreach ($data as $product_id => $supplier_order) {
            if($supplier_order['supplier_order_id']){
                $item = HtSupplierOrder::model()->findByPk($supplier_order['supplier_order_id']);
                if (empty($item)) {
                    $return = false;
                    break;
                }
                if ($supplier_order['supplier_booking_ref']) {
                    $supplier_booking_ref = $supplier_order['supplier_booking_ref'];
                    $booking_ref_item = HtSupplierOrder::model()->find("supplier_booking_ref = '$supplier_booking_ref'");
                    if ($booking_ref_item) {
                        $order_product = HtOrderProduct::model()->find("supplier_order_id = " . $booking_ref_item['supplier_order_id']);
                        if ($order_product['order_id'] && ($booking_ref_item['supplier_order_id'] != $supplier_order['supplier_order_id'])) {
                            if ($confirm != 1) {
                                return array('code' => 303, 'msg' => 'booking ID与订单' . $order_product['order_id'] . '中的重复，是否继续？');
                            }
                        }
                    }
                    $item['supplier_booking_ref'] = $supplier_order['supplier_booking_ref'];
                }
                if (is_array($supplier_order['confirmation_ref']) && $supplier_order['confirmation_ref'][0] != '') {
                    $p_confirmation_ref = array();
                    $p_order_id = array();
                    foreach ($supplier_order['confirmation_ref'] as $confirmation_ref) {
                        $criteria = new CDbCriteria;
                        $criteria->compare('confirmation_ref', $confirmation_ref, true);
                        $confirmation_item = HtSupplierOrder::model()->find($criteria);
                        if ($confirmation_item) {
                            $order_product = HtOrderProduct::model()->find("supplier_order_id = " . $confirmation_item['supplier_order_id']);
                            if ($order_product['order_id'] && ($confirmation_item['supplier_order_id'] != $supplier_order['supplier_order_id'])) {
                                $p_order_id[] = $order_product['order_id'];
                                $p_confirmation_ref[] = $confirmation_ref;
                            }
                        }
                    }
                    if (count($p_confirmation_ref) > 0) {
                        if ($confirm != 1) {
                            return array('code' => 303,
                                'msg' => 'confirmation code:' . implode(',', $p_confirmation_ref) . '与订单' . implode(',',
                                                                                                                    $p_order_id) . '中的重复，是否继续？');
                        }
                    }
                    $item['confirmation_ref'] = implode(',', $supplier_order['confirmation_ref']);
                }
                if (is_array($supplier_order['voucher_ref']) && count($supplier_order['voucher_ref']) > 0) {
                    $voucher_list = array();
                    foreach ($supplier_order['voucher_ref'] as $voucher) {
                        $voucher_list[] = trim($voucher['voucher_name']);
                    }
                    $item['voucher_ref'] = CJSON::encode($voucher_list);
                }
                $item['additional_info'] = $supplier_order['additional_info'];
                $item['current_status'] = HtSupplierOrder::CONFIRMED;
                $r = $item->update();
                $return = $return && $r;
            }

            //update OrderProduct
            $op = HtOrderProduct::model()->findByAttributes(array('order_id'=>$this->getOrderID(),'product_id'=>$product_id));
            ModelHelper::updateItem($op, $supplier_order, array('tour_date', 'departure_code', 'departure_time'));


        }

        if (!$return) {
            $result = array('code' => 300, 'msg' => '保存发货信息失败！');
        }

        return $result;
    }

    public function actionReturnOrder()
    {
        $result = array('code' => 200, 'msg' => '退货成功');
        $order_id = $this->getOrderId();

        if (empty($order_id)) {
            $result = array('code' => 400, 'msg' => '退货未成功:没有指定订单号');
        } else {
            $refund_param = $this->getPostJsonData();
            if ($refund_param['reason'] == 1) {//正常退款，需要执行退货流程
                $result = Yii::app()->returning->returnRequest($order_id, 1);
                if ($result['code'] < 400 && $result['code'] != 200) {
                    $result = Yii::app()->returning->returnConfirm($order_id);
                }
            }
            //除正常退款之外均为部分退款，不用执行退货流程，且不更改订单状态
            if ($refund_param['reason'] > 1 || $result['code'] == 200) {
                $result = Yii::app()->returning->refundOrder($order_id, $refund_param);
            }
        }
        EchoUtility::echoCommonMsg($result['code'], $result['msg']);
    }

    public function actionSaveRefund()
    {
        $result = array('code' => 200, 'msg' => '保存退订记录成功');
        $order_id = $this->getOrderId();

        if (empty($order_id)) {
            $result = array('code' => 400, 'msg' => '保存退订记录未成功:没有指定订单号');
        } else {
            $data = $this->getPostJsonData();
            $res = Yii::app()->returning->saveRefund($order_id, $data);
            if ($res['code'] >= 300) {
                $result = $res;
            }
        }
        EchoUtility::echoCommonMsg($result['code'], $result['msg']);
    }

    public function actionRefuseReturn()
    {
        $result = array('code' => 200, 'msg' => '保存订单状态成功');
        $order_id = $this->getOrderId();

        if (empty($order_id)) {
            $result = array('code' => 400, 'msg' => '保存订单状态未成功:没有指定订单号');
        } else {
            $result = Yii::app()->returning->refuseReturn($order_id);
        }
        EchoUtility::echoCommonMsg($result['code'], $result['msg']);
    }

    //退款接口
    public function actionRefundOrder()
    {
        $result = array('code' => 200, 'msg' => '保存成功');
        $order_id = $this->getOrderId();
        if (empty($order_id)) {
            $result = array('code' => 400, 'msg' => '没有指定订单号');
        }
        $data = $this->getPostJsonData();

        switch ($data['return_type']) {
            case 'return_and_refund':
                $result = Yii::app()->returning->returnRequest($order_id, 1);
                if ($result['code'] < 400 && $result['code'] != 200) {
                    $result = Yii::app()->returning->returnConfirm($order_id);
                };
            //break 继续执行退款
            case 'partial_refund':
                //除正常退款之外均为部分退款，不用执行退货流程，且不更改订单状态
                if ($data['reason'] > 1 || $result['code'] == 200) {
                    $result = Yii::app()->returning->refundOrder($order_id, $data);
                };
                break;
            case 'record_refund':
                $res = Yii::app()->returning->saveRefund($order_id, $data);
                if ($res['code'] >= 300) {
                    $result = $res;
                };
                break;
            case 'refuse_refund':
                $result = Yii::app()->returning->refuseReturn($order_id);
                break;
        }
        EchoUtility::echoCommonMsg($result['code'], $result['msg']);
    }

    public function actionBookingOrder()
    {
        $result = array('code' => 200, 'msg' => '已执行预定');
        $order_id = $this->getOrderId();

        if (empty($order_id)) {
            $result = array('code' => 400, 'msg' => '执行预定未成功：没有指定订单号');
        } else {
            $order = HtOrder::model()->findByPk($order_id)->attributes;
            if ($order['status_id'] != HtOrderStatus::ORDER_PAYMENT_SUCCESS) {
                $result = array('code' => 300, 'msg' => '执行预定未成功：当前订单状态不允许发起预定');
            } else {
                $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_WAIT_CONFIRMATION);
                if (!$isok) {
                    $result = array('code' => 401, 'msg' => '执行预定未成功：无法更改状态');
                }
            }
        }
        EchoUtility::echoCommonMsg($result['code'], $result['msg']);
    }

    public function actionRebookingOrder()
    {
        $result = array('code' => 200, 'msg' => '重新预定成功');
        $order_id = $this->getOrderId();

        if (empty($order_id)) {
            $result = array('code' => 400, 'msg' => '重新预定未成功:没有指定订单号');
        } else {
            $order = HtOrder::model()->findByPk($order_id)->attributes;
            if ($order['status_id'] != HtOrderStatus::ORDER_STOCK_FAILED) {
                $result = array('code' => 300, 'msg' => '不需要重新预定');
            } else {
                $result_total = array();
                $shippingInfo = Yii::app()->order->getShippingInfo($order_id);
                foreach ($shippingInfo as $product_id => $info) {
                    if ($info['shipping_rule']['booking_type'] == 'STOCK') {
                        $voucher_num = count($info['supplier_order']['voucher_ref']);
                        Yii::log('Rebooking for order[' . $order_id . ']product_id[' . $product_id . ']voucher_num[' . $voucher_num . ']ticket_num[' . $info['ticket_num'] . ']',
                                 CLogger::LEVEL_INFO);
                        if ($voucher_num != $info['ticket_num']) {
                            $result_total['product_id'] = Yii::app()->booking->bookingOrder($order_id, $product_id);
                        }
                    }
                }
                $result = Converter::mergeResult($result_total);
                if ((empty($result_total) || $result['code'] == 200) && Yii::app()->order->checkOrderStocked($order_id)) {
                    $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_TO_DELIVERY);
                }
            }
        }
        EchoUtility::echoCommonMsg($result['code'], $result['msg']);
    }

    public function actionSendOrderProcessing()
    {
        $result = array('code' => 200, 'msg' => '发送订单处理中邮件成功');
        $order_id = $this->getOrderId();

        if (empty($order_id)) {
            $result = array('code' => 400, 'msg' => '发送订单处理中邮件未成功:没有指定订单号');
        } else {
            $order_data = Yii::app()->order->getOrderDetailForVoucher($order_id);
            $isok = Yii::app()->notify->notifyCustomer($order_data);
            if (!$isok) {
                $result = array('code' => 401, 'msg' => '发送订单处理中邮件未成功:邮箱可能不存在');
            }
        }
        EchoUtility::echoCommonMsg($result['code'], $result['msg']);
    }
}