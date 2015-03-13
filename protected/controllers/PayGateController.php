<?php
/**
 * @project hitour.server
 * @file PayGateController.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 14-5-8 下午1:05
 **/

class PayGateController extends Controller {
    private $order_id = 0;
    private $payment_method = '';
    private $order = null;
    private $callType = 0; // 0: 支付调用; 1: 收单机构异步调用
    private $json = 0;
    private $from_mobile = false;
    private $refund_reason = 1;

    public function filters()
    {
        return array(
//            array(
//                'application.filters.IpFilter + Pay, Refund',
//                'application.filters.LoginFilter + Pay, Refund',
//            ),
        );
    }

    private function readParameter()
    {
        $param = $_REQUEST;
        $is_json = $this->getParam('json', 0);

        if ($is_json == 1) {
            $this->json = 1;
        }
        $this->from_mobile = HTTPRequest::isMobile();

        $this->payment_method = Yii::app()->request->getParam('method');
        $this->order_id = (int)Yii::app()->request->getParam('order_id');
        if (empty($this->payment_method) && !empty($this->order_id)) {
            $order = HtOrder::model()->findByPk($this->order_id);
            if ($order) {
                $this->order = $order->attributes;
                if (empty($param['refundnotify'])) {
                    $this->payment_method = PayUtility::terminalMatch($this->from_mobile, $this->order['payment_method']);
                }else{
                    $this->payment_method = $this->order['payment_method'];
                }
            }
        }

        $payment_method = PayUtility::distinguishPaymentMethod($param);
        if (empty($this->payment_method)) {
            $this->payment_method = $payment_method;
            $this->callType = 1;
        }
        // For compatible the old system.
        if ($this->payment_method == 'alipay_direct') {
            $this->payment_method = 'alipay_pc';
        }
        Yii::log('Received payment method is ['.$this->payment_method.']callType['.$this->callType.']', CLogger::LEVEL_INFO);
    }

    public function init()
    {
        $this->readParameter();

        if (!Yii::app()->hasComponent($this->payment_method)) {
            Yii::log('Not supported payment method['.$this->payment_method.'] be used.', CLogger::LEVEL_WARNING);
            throw new CHttpException(400,Yii::t('yii','指定的支付方式不支持.'));
        }
        if ($this->callType == 0) {
            if (empty($this->order)) {
                throw new CHttpException(400,Yii::t('yii','指定的订单不存在.'));
            }else{
                $order_product = HtOrderProduct::model()->with('product.description')->findByAttributes(array('order_id'=>$this->order_id));
                $order_product = Converter::convertModelToArray($order_product);
                $this->order['product_id'] = $order_product['product_id'];
                $this->order['supplier_id'] = $order_product['product']['supplier_id'];
                Yii::app()->{$this->payment_method}->order_id = $this->order_id;
                Yii::app()->{$this->payment_method}->out_order_id = PayUtility::genOutTradeNo($this->order, $this->payment_method);
                Yii::app()->{$this->payment_method}->total = $this->order['total'];
                Yii::app()->{$this->payment_method}->product_name = $order_product['product']['description']['name'];
                Yii::app()->{$this->payment_method}->time_limit = $this->order['payment_time_limit'];
            }
        }
    }

    public function actionPay()
    {
        if ($this->allowPayment()) {
            $entry = Yii::app()->{$this->payment_method}->buildEntry();
            if (empty($entry)) {
                throw new CHttpException(400,Yii::t('yii','未能发送支付请求.'));
            }else{
                if ($this->payment_method == 'weixinpay_pc') {
                    $this->redirectToWeixinPay($entry);
                }else if (Yii::app()->{$this->payment_method}->formSubmit) {
                    echo $entry;
                }else{
                    $this->redirect($entry);
                }
            }
        }else{
            throw new CHttpException(400,Yii::t('yii','无效的支付请求.'));
        }
    }

    private function redirectToWeixinPay($entry)
    {
        $data = $this->initData();
        $data['qrcode_url'] = $this->createQrcodeUrl($entry);
        if (empty($data['qrcode_url'])) {
            $this->redirect($this->createAbsoluteUrl('site/error'));
        }else{
            $this->current_page = 'paygate';
            $this->request_urls = array_merge(
                $this->request_urls,
                array(
                    'orderStatus' => $this->createAbsoluteUrl('payGate/status'),
                    'paySuccess' => $this->createAbsoluteUrl('checkout/success/', ['order_id'=>$this->order_id]),
                )
            );
            $data['order'] = Yii::app()->order->getBaseInfoWithoutProductDetail($this->order_id);
            $this->render('weixinpay_qrcode', $data);
        }
    }

    private function createQrcodeUrl($weixinpay_url)
    {
        $qrcode_url = '';
        $order_data = Yii::app()->order->getOrderDetailForVoucher($this->order_id);
        $order_model = $order_data['order'];
        $qrcode_filename = 'weixinpay_'.substr(md5($weixinpay_url), 0, 8) . '.png';
        $qrcode_filepath = $order_model['voucher_path'] . '/' . $qrcode_filename;
        QRcode::png($weixinpay_url, $qrcode_filepath, QR_ECLEVEL_M, 6);
        if (file_exists($qrcode_filepath)) {
            $qrcode_url = $order_model['voucher_base_url'] . '/'.$qrcode_filename;
        }else{
            Yii::log('Create qrcode for weixinpay failed. ['.$qrcode_filepath.']', CLogger::LEVEL_ERROR);
        }
        return $qrcode_url;
    }

    public function actionRefund()
    {
        if ($this->allowRefund()) {
            $result = Yii::app()->{$this->payment_method}->refund();
            $comment = '返回码['.$result['code'].']说明['.$result['msg'].']';
            if ($result['code'] == 200) {
                $isok = $this->saveRefundHistory($result['data']);
                if (!$isok) {
                    Yii::log('PayNotify: save to payment history failed.',CLogger::LEVEL_ERROR);
                }
                if ($this->refund_reason == 1) {//除正常退款之外, 部分退款及记录等，均不更改订单状态
                    Yii::app()->stateMachine->switchStatus($this->order_id,HtOrderStatus::ORDER_REFUND_PROCESSING, $comment);
                }
                EchoUtility::echoCommonMsg($result['code'], '退款请求成功');
            }else{
                Yii::app()->stateMachine->switchStatus($this->order_id,HtOrderStatus::ORDER_REFUND_FAILED, $comment);
                Yii::log('Send refund request failed. code['.$result['code'].']msg['.$result['msg'].']', CLogger::LEVEL_ERROR);
                EchoUtility::echoCommonMsg($result['code'], '未能发送退款请求');
            }
        }else{
            EchoUtility::echoCommonMsg(400, '无效的退款请求');
        }
    }

    public function actionPayNotify()
    {
        Yii::log('['.$this->payment_method.']Payment notify recieved. Client IP['.$_SERVER['REMOTE_ADDR'].']', CLogger::LEVEL_INFO);
        $result = Yii::app()->{$this->payment_method}->payNotify();
        if ($result['code'] == 200) {
            if (empty($result['data']['out_order_id'])) {
                Yii::log('PayNotify: failed because out_trade_no is empty.',CLogger::LEVEL_ERROR);
            }else{
                $retdata = PayUtility::parseOutTradeNo($result['data']['out_order_id']);
                $order_id = (int)$retdata['order_id'];
                $order = HtOrder::model()->findByPk($order_id)->attributes;
                if (empty($order)) {
                    Yii::log('PayNotify: not found order['.$order_id.']', CLogger::LEVEL_ERROR);
                }else{
                    $status = $order['status_id'];
                    if (in_array($status, [
                        HtOrderStatus::ORDER_CONFIRMED,
                        HtOrderStatus::ORDER_NOTPAY_EXPIRED,
                        HtOrderStatus::ORDER_CANCELED])) {
                        Yii::log('PayNotify: order['.$order_id.'],total=' . $result['data']['total'] . ',trade_status=' . $result['data']['trade_status'], CLogger::LEVEL_INFO);
                        $isok = $this->savePaymentHistory($result, HtPaymentHistory::PAYMENT);
                        if (!$isok) {
                            Yii::log('PayNotify: save to payment history failed.',CLogger::LEVEL_ERROR);
                        }
                        if ($status == HtOrderStatus::ORDER_NOTPAY_EXPIRED) {
                            $isok = Yii::app()->stateMachine->switchStatus($order_id,HtOrderStatus::ORDER_PAID_EXPIRED);
                        }else{
                            $isok = Yii::app()->stateMachine->switchStatus($order_id,HtOrderStatus::ORDER_PAYMENT_SUCCESS);
                            if (!$isok) {
                                Yii::log('PayNotify: switch status failed. order_id['.$order_id.']',CLogger::LEVEL_ERROR);
                            }
                        }
                    }else{
                        Yii::log('PayNotify: order['.$order_id.']status_id['.$status.'] is invalid.', CLogger::LEVEL_WARNING);
                    }
                }
            }
        }else{
            Yii::log('PayNotify: failed. code['.$result['code'].']msg['.$result['msg'].']', CLogger::LEVEL_WARNING);
        }
    }

    public function actionRefundNotify()
    {
        Yii::log('['.$this->payment_method.']Refund notify recieved. Client IP['.$_SERVER['REMOTE_ADDR'].']', CLogger::LEVEL_INFO);
        $this->loadRefundHistory();
        $result = Yii::app()->{$this->payment_method}->refundNotify();
        $comment = '返回码['.$result['code'].']说明['.$result['msg'].']';
        if ($result['code'] == 200) {
            $isok = $this->savePaymentHistory($result, HtPaymentHistory::REFUND);
            if (!$isok) {
                Yii::log('Feedback: save to payment history failed.',CLogger::LEVEL_ERROR);
            }
            Yii::app()->stateMachine->switchStatus(Yii::app()->{$this->payment_method}->order_id,HtOrderStatus::ORDER_REFUND_SUCCESS, $comment);
        }else if ($result['code'] >= 400) {
            Yii::app()->stateMachine->switchStatus(Yii::app()->{$this->payment_method}->order_id,HtOrderStatus::ORDER_REFUND_FAILED, $comment);
            Yii::log('Refund notify failed. code['.$result['code'].']msg['.$result['msg'].']', CLogger::LEVEL_ERROR);
        }
    }

    public function actionQuery()
    {
        $order_id = (int)$this->getParam('order_id');
        $out_order_id = $this->getParam('out_order_id');
        $trade_type = $this->getParam('type');

        $payment = HtPaymentHistory::model()->findByAttributes(
            array('order_id'=>$order_id, 'pay_or_refund'=>1)
        );
        if ($trade_type != '01' && $trade_type != '04') {
            $trade_type = '01';
        }
        Yii::app()->{$this->payment_method}->out_order_id = empty($out_order_id) ? PayUtility::genOutTradeNo($this->order, $this->payment_method) : $out_order_id;
        Yii::app()->{$this->payment_method}->trade_time = date('YmdHis', strtotime($payment['trade_time']));
        Yii::app()->{$this->payment_method}->trade_type = $trade_type;
        Yii::app()->{$this->payment_method}->query();
    }

    public function actionStatus()
    {
        $order_id = (int)$this->getParam('order_id');
        $order = HtOrder::model()->findByPk($order_id);
        if (!empty($order)) {
            $payment = HtPaymentHistory::model()->findByAttributes(
                array('order_id'=>$order_id, 'pay_or_refund'=>1)
            );
            if (empty($payment)) {
                EchoUtility::echoCommonMsg(300, 'Not found payment history.');
            }else if (in_array($order['status_id'], [HtOrderStatus::ORDER_CONFIRMED,HtOrderStatus::ORDER_CANCELED,HtOrderStatus::ORDER_NOTPAY_EXPIRED,HtOrderStatus::ORDER_PAYMENT_FAILED])) {
                EchoUtility::echoCommonMsg(301, 'Payment not finished.');
            }else{
                EchoUtility::echoCommonMsg(200, 'Paymnet success.');
            }
        }else{
            EchoUtility::echoCommonMsg(302, 'Not found order.');
        }
    }

    private function allowPayment()
    {
        //1. 判断订单是否在未支付状态
        if ($this->order['status_id'] != HtOrderStatus::ORDER_CONFIRMED) {
            Yii::log('Want payment for order['.$this->order['order_id'].'], but status['.$this->order['status_id'].'] is invalid.', CLogger::LEVEL_WARNING);
            return false;
        }
        //2. 判断订单支付金额是否有效
        if ($this->order['total'] == 0.0) {
            Yii::log('Want to pay for order['.$this->order['order_id'].'], total is 0.', CLogger::LEVEL_WARNING);
            if ($this->from_mobile) {
                $this->redirect($this->createUrl('mobile/result', array('order_id'=>$this->order['order_id'])));
            }else{
                $this->redirect($this->createUrl('checkout/success', array('order_id'=>$this->order['order_id'])));
            }
        }
        //3. 判断订单是否进行过实际的支付
        $payment = HtPaymentHistory::model()->findByAttributes(
            array('order_id'=>$this->order['order_id'], 'pay_or_refund'=>1)
        );
        if (!empty($payment)) {
            Yii::log('Repeat payment for order['.$this->order['order_id'].']', CLogger::LEVEL_WARNING);
            return false;
        }
        return true;
    }

    private function allowRefund()
    {
        if (in_array($this->payment_method, array('alipay_wap','alipay_app'))) {
            $this->payment_method = 'alipay_pc';
        }
        Yii::log('Received refund request for order['.$this->order_id.'], check...', CLogger::LEVEL_INFO);
        //1. 判断订单是否在可以退款状态
        if (!Yii::app()->stateMachine->allowRefund($this->order['status_id'])) {
            Yii::log('Want to refund order['.$this->order['order_id'].'], but status['.$this->order['status_id'].'] is invalid.', CLogger::LEVEL_WARNING);
            return false;
        }
        //2. 判断订单是否已有支付记录
        $payment = HtPaymentHistory::model()->findByAttributes(
            array('order_id'=>$this->order['order_id'], 'pay_or_refund'=>1)
        );
        if (empty($payment) && $this->order['total'] > 0.0) {
            Yii::log('Want to refund order['.$this->order['order_id'].'], but not found payment history.', CLogger::LEVEL_WARNING);
            return false;
        }else if ($payment->trade_total == 0.0) {
            Yii::log('Want to refund order['.$this->order['order_id'].'], but payment total is 0.', CLogger::LEVEL_WARNING);
            return false;
        }else{
            $refund_total = $payment->trade_total;
            $amount = Yii::app()->request->getParam('refund_amount');
            if (!empty($amount) && $amount < $payment->trade_total) {
                $refund_total = $amount;
            }else if ($amount > $payment->trade_total) {
                Yii::log('Want to refund order['.$this->order['order_id'].'], but refund amount > payment total.', CLogger::LEVEL_WARNING);
                return false;
            }
            Yii::app()->{$this->payment_method}->trade_id = $payment->trade_id;
            Yii::app()->{$this->payment_method}->total = $payment->trade_total;
            Yii::app()->{$this->payment_method}->charge = $payment->trade_total - $refund_total;
            Yii::app()->{$this->payment_method}->trade_time = $payment->trade_time;
            Yii::app()->{$this->payment_method}->payment_id = $payment->id;
            Yii::app()->{$this->payment_method}->card_number = $payment->card_number;
        }
        //3. 判断订单是否已提交退订记录
        //$return = HtReturn::model()->find(
        //    array('order_id'=>$this->order['order_id'])
        //);
        //if (empty($return)) {
        //    Yii::log('Want to refund order['.$this->order['order_id'].'], but not found return record', CLogger::LEVEL_WARNING);
        //    return false;
        //}else{
        //    Yii::app()->{$this->payment_method}->return_id = $return->return_id;
        //    Yii::app()->{$this->payment_method}->charge = $return->charge_amount;
        //}
        //4. 判断订单支付金额是否有效
        if ($this->order['total'] == 0.0) {
            Yii::log('Want to refund order['.$this->order['order_id'].'], total is 0.', CLogger::LEVEL_WARNING);
            return false;
        }
        //5. 判断罚金金额是否超出订单金额
        //if ($this->order['total'] <= $return['charge_amount']) {
        //    Yii::log('Charge amount exceed this order['.$this->order['order_id'].']\'s total.', CLogger::LEVEL_WARNING);
        //    return false;
        //}
        return true;
    }

    private function savePaymentHistory($result, $mode)
    {
        if (empty($result) || (empty($result['data']) && empty($result['params']))) {
            return false;
        }

        //转换支付结果参数为系统可识别内容
        if (isset($result['params'])) {
            $params = $result['params'];
            $order_info = $this->getParamFromHistory($params['order_id'], $params['trade_no'], HtPaymentHistory::PAYMENT);
        }else{
            $params = PayUtility::parsePaymentReturn($result['data']);
            $order_info = PayUtility::parseOutTradeNo($params['out_order_id']);
            if ($order_info['product_id'] == 0) {
                $order_product = HtOrderProduct::model()->findByAttributes(
                    ['order_id'=>$order_info['order_id']]
                );
                if ($order_product) {
                    $order_info['product_id'] = $order_product['product_id'];
                }
            }
        }

        $payment_history = HtPaymentHistory::model()->findByAttributes(
            ['order_id'=>$order_info['order_id'], 'pay_or_refund'=>$mode]
        );
        if ($payment_history && count($payment_history) > 0) {
            Yii::log('Payment history already exist. order_id['.$order_info['order_id'].']', CLogger::LEVEL_WARNING);
            if ($mode == HtPaymentHistory::PAYMENT) {
                return false;
            }else{
                Yii::app()->{$this->payment_method}->order_id = $order_info['order_id'];
                $payment_history['supplier_id'] = $order_info['supplier_id'];
                $payment_history['product_id'] = $order_info['product_id'];
                $payment_history['trade_id'] = $params['trade_no'];
                $payment_history['notify_id'] = $params['notify_id'];
                $payment_history['trade_total'] = $params['total'];
                $payment_history['buyer_id'] = isset($params['buyer_id']) ? $params['buyer_id'] : '';
                $payment_history['buyer_email'] = isset($params['buyer_email']) ? $params['buyer_email'] : '';
                $payment_history['trade_time'] = $params['notify_time'];
                $payment_history['card_number'] = $params['card_number'];
                $payment_history['raw_data'] = json_encode($params);
                $isok = $payment_history->update();
                return $isok;
            }
        }else{
            $payment_history = new HtPaymentHistory();
            $payment_history['pay_or_refund'] = $mode;
            $payment_history['payment_really'] = Yii::app()->params['PAYMENT_REALLY'];
            $payment_history['payment_type'] = $this->payment_method;
            $payment_history['supplier_id'] = $order_info['supplier_id'];
            $payment_history['order_id'] = $order_info['order_id'];
            $payment_history['product_id'] = $order_info['product_id'];
            $payment_history['trade_id'] = $params['trade_no'];
            $payment_history['notify_id'] = $params['notify_id'];
            $payment_history['trade_total'] = $params['total'];
            $payment_history['card_number'] = $params['card_number'];
            $payment_history['buyer_id'] = $params['buyer_id'];
            $payment_history['buyer_email'] = $params['buyer_email'];
            $payment_history['trade_time'] = $params['notify_time'];
            $payment_history['raw_data'] = json_encode($params);
            $isok = $payment_history->insert();
            if (!$isok) {
                $error = $payment_history->getErrors();
                Yii::log('Save payment_history failed.error['.(json_encode($error)).']', CLogger::LEVEL_ERROR);
            }
            return $isok;
        }
    }

    private function getParamFromHistory($order_id, $trade_no, $mode)
    {
        $order_info['order_id'] = $order_id;
        $result = HtPaymentHistory::model()->findByAttributes(
            array('order_id'=>$order_id,'trade_id'=>$trade_no,'pay_or_refund'=>$mode)
        );
        if ($result && count($result) > 0) {
            $order_info['supplier_id'] = $result['supplier_id'];
            $order_info['product_id'] = $result['product_id'];
        }else{
            $order_info['supplier_id'] = 0;
            $order_info['product_id'] = 0;
        }
        return $order_info;
    }

    private function saveRefundHistory($param)
    {
        $data = array_merge($this->getActionParams(), $param);
        $result = HtPaymentHistory::model()->findByAttributes(
            array('order_id'=>$this->order_id,'pay_or_refund'=>HtPaymentHistory::REFUND)
        );
        if (empty($result)) {
            $payment_history = new HtPaymentHistory();
            $payment_history['pay_or_refund'] = HtPaymentHistory::REFUND;
            $payment_history['payment_really'] = Yii::app()->params['PAYMENT_REALLY'];
            $payment_history['payment_type'] = $this->payment_method;
            $payment_history['supplier_id'] = 0;
            $payment_history['order_id'] = $this->order_id;
            $payment_history['product_id'] = 0;
            $payment_history['refund_order_id'] = isset($data['refund_order_id']) ? $data['refund_order_id'] : '';
            $payment_history['trade_id'] = isset($data['trade_id']) ? $data['trade_id'] : '';
            $payment_history['notify_id'] = '';
            $payment_history['trade_total'] = isset($data['refund_amount']) ? $data['refund_amount'] : 0;
            $payment_history['comment'] = isset($data['comment']) ? $data['comment'] : '';
            $payment_history['refund_reason'] = isset($data['reason']) ? $data['reason'] : 1;
            $payment_history['buyer_id'] = '';
            $payment_history['buyer_email'] = '';
            $payment_history['trade_time'] = date('Y-m-d H:i:s', empty($data['trade_time']) ? time() : strtotime($data['trade_time']));
            $payment_history['raw_data'] = '';
            $isok = $payment_history->insert();
            if (!$isok) {
                $error = $payment_history->getErrors();
                Yii::log('Save payment_history failed.error['.(json_encode($error)).']', CLogger::LEVEL_ERROR);
            }
            $this->refund_reason = $payment_history['refund_reason'];
            return $isok;
        }
    }

    private function loadRefundHistory()
    {
        $result = HtPaymentHistory::model()->findByAttributes(
            array('order_id'=>$this->order_id,'pay_or_refund'=>HtPaymentHistory::REFUND)
        );
        if ($result && count($result) > 0) {
            Yii::app()->{$this->payment_method}->refund_order_id = $result['refund_order_id'];
        }
        $result = HtPaymentHistory::model()->findByAttributes(
            array('order_id'=>$this->order_id,'pay_or_refund'=>HtPaymentHistory::PAYMENT)
        );
        if ($result && count($result) > 0) {
            Yii::app()->{$this->payment_method}->trade_time = $result['trade_time'];
        }
    }

}