<?php

/**
 * Created by PhpStorm.
 * User: app
 * Date: 14-5-8
 * Time: 下午6:15
 */
class PayUtility
{
    private static $payment_map = array(
        'alipay_pc' => 1,
        'alipay_direct' => 1,
        'alipay_wap' => 2,
        'unionpay_pc' => 3,
        'unionpay_wap' => 4,
        'chinapay_pc' => 5,
        'chinapay_wap' => 6,
        'weixinpay_pc' => 7,
        'weixinpay_wap' => 8,
        'weixinpay_widget' => 9,
        'chinapay_widget' => 10,
        'bocpay_pc' => 11,
        'ccbpay_pc' => 12,
        'alipay_app' => 13,
    );

    public static function genOutTradeNo($order_info, $payment_type)
    {
        $payment_code = empty(self::$payment_map[$payment_type]) ? 0 : self::$payment_map[$payment_type];
        if ($payment_type == 'ccbpay_pc' || $payment_type == 'ccbpay_wap') { //Payment from CCB
            $out_trade_no = sprintf('%02d%04d%08d%016d',
                Yii::app()->params['PAYMENT_REALLY'],
                $order_info['supplier_id'],
                $order_info['product_id'],
                $order_info['order_id']);
        } else if ($payment_type == 'bocpay_pc' || $payment_type == 'bocpay_wap') { //Payment from BOC
            $out_trade_no = sprintf('%02d%02d%04d%08d%016d',
                Yii::app()->params['PAYMENT_REALLY'],
                $payment_code,
                $order_info['supplier_id'],
                $order_info['product_id'],
                $order_info['order_id']);
        } else if ($payment_type == 'weixinpay_pc' || $payment_type == 'weixinpay_widget') { //Payment from weixin
            $out_trade_no = sprintf('%02d%02d%04d%08d%016d',
                Yii::app()->params['PAYMENT_REALLY'],
                $payment_code,
                $order_info['supplier_id'],
                $order_info['product_id'],
                $order_info['order_id']);
        } else if ($payment_type == 'unionpay_pc' || $payment_type == 'unionpay_wap') { //Payment from unionpay
            $out_trade_no = sprintf('%02d%02d%04d%08d%016d',
                Yii::app()->params['PAYMENT_REALLY'],
                $payment_code,
                $order_info['supplier_id'],
                $order_info['product_id'],
                $order_info['order_id']);
        } else if ($payment_type == 'chinapay_pc' || $payment_type == 'chinapay_wap' || $payment_type == 'chinapay_widget') {
            $out_trade_no = sprintf('%02d%02d%04d%08d',
                Yii::app()->params['PAYMENT_REALLY'],
                $payment_code,
                $order_info['supplier_id'],
                $order_info['order_id']);
        } else {
            $out_trade_no = sprintf('%s%02d%02d%04d%08d%016d',
                date('YmdHis', time()),
                Yii::app()->params['PAYMENT_REALLY'],
                $payment_code,
                $order_info['supplier_id'],
                $order_info['product_id'],
                $order_info['order_id']);
        }
        return $out_trade_no;
    }

    public static function parseOutTradeNo($out_trade_no)
    {
        $payment_method = 1;
        if (strlen($out_trade_no) == 30) {
            $payment_method = 4;
        } else if (strlen($out_trade_no) <= 32 && strlen($out_trade_no) >= 17) {
            $payment_method = 2;
        } else if (strlen($out_trade_no) <= 16) {
            $payment_method = 3;
        }
        $result = array();
        if ($payment_method == 2) { //Payment from unionpay
            list($payment, $from, $supplier_id, $product_id, $order_id) =
                sscanf($out_trade_no, '%02d%02d%04d%08d%016d');
            $result['order_time'] = 0;
        } else if ($payment_method == 3) {
            list($payment, $from, $supplier_id, $order_id) =
                sscanf($out_trade_no, '%02d%02d%04d%08d');
            $product_id = 0;
            $result['order_time'] = 0;
        } else if ($payment_method == 4) {
            list($payment, $supplier_id, $product_id, $order_id) =
                sscanf($out_trade_no, '%02d%04d%08d%016d');
            $result['from'] = 12;
            $result['order_time'] = 0;
        } else {
            list($order_time, $payment, $from, $supplier_id, $product_id, $order_id) =
                sscanf($out_trade_no, '%14c%02d%02d%04d%08d%016d');
            $result['order_time'] = $order_time;
        }
        $result['payment'] = $payment;
        $result['from'] = $from;
        $result['supplier_id'] = $supplier_id;
        $result['product_id'] = $product_id;
        $result['order_id'] = $order_id;
        return $result;
    }

    public static function parsePaymentReturn($paras)
    {
        if (isset($paras['PAYMENT'])) {
            $paras['out_order_id'] = $paras['ORDERID'];
            $paras['total'] = $paras['PAYMENT'];
            $paras['trade_no'] = '';
            $paras['notify_id'] = '';
            $paras['card_number'] = '';
            $paras['buyer_id'] = '';
            $paras['buyer_email'] = '';
            $paras['notify_time'] = date('Y-m-d H:i:s', strtotime($paras['txnTime']));
        } else if (isset($paras['txnAmt'])) {
            $paras['out_order_id'] = $paras['orderId'];
            $paras['total'] = $paras['txnAmt'];
            $paras['trade_no'] = $paras['queryId'];
            $paras['notify_id'] = $paras['traceNo'];
            $paras['card_number'] = $paras['cardNo'];
            $paras['buyer_id'] = '';
            $paras['buyer_email'] = '';
            $paras['notify_time'] = date('Y-m-d H:i:s', strtotime($paras['txnTime']));
        } else if (isset($paras['mch_id'])) {
            $paras['out_order_id'] = $paras['out_trade_no'];
            if (isset($paras['refund_fee_0'])) {
                $paras['total'] = number_format($paras['refund_fee_0'] / 100, 2, '.', '');
            }else{
                $paras['total'] = number_format($paras['total_fee'] / 100, 2, '.', '');
            }
            $paras['trade_no'] = $paras['transaction_id'];
            if (isset($paras['refund_id_0'])) {
                $paras['notify_id'] = $paras['refund_id_0'];
            }else{
                $paras['notify_id'] = '';
            }
            $paras['card_number'] = '';
            $paras['buyer_id'] = '';
            $paras['buyer_email'] = '';
            if (isset($paras['time_end'])) {
                $paras['notify_time'] = date('Y-m-d H:i:s', strtotime($paras['time_end']));
            }else{
                $paras['notify_time'] = date('Y-m-d H:i:s');
            }
        } else if (isset($paras['qn'])) {
            $paras['out_order_id'] = $paras['orderNumber'];
            $paras['total'] = number_format($paras['settleAmount'] / 100, 2, '.', '');
            $paras['trade_no'] = $paras['qn'];
            $paras['notify_id'] = 0;
            $paras['card_number'] = '';
            $paras['buyer_id'] = '';
            $paras['buyer_email'] = '';
            $paras['notify_time'] = date('Y-m-d H:i:s', strtotime($paras['orderTime']));
        } else if (isset($paras['qid'])) {
            $paras['out_order_id'] = $paras['orderNumber'];
            $paras['total'] = number_format($paras['orderAmount'] / 100, 2, '.', '');
            $paras['trade_no'] = $paras['qid'];
            $paras['notify_id'] = 0;
            $paras['card_number'] = '';
            $paras['buyer_id'] = '';
            $paras['buyer_email'] = '';
            list($month, $day, $hour, $minute, $second) = sscanf($paras['traceTime'], '%2s%2s%2s%2s%2s');
            $notify_time = mktime($hour, $minute, $second, $month, $day, date('Y'));
            $paras['notify_time'] = date('Y-m-d H:i:s', $notify_time);
        } else if (isset($paras['orderno'])) {
            $paras['out_order_id'] = $paras['orderno'];
            $paras['total'] = number_format($paras['amount'] / 100, 2, '.', '');
            $paras['trade_no'] = $paras['orderno'];
            $paras['notify_id'] = 0;
            $paras['card_number'] = '';
            $paras['buyer_id'] = '';
            $paras['buyer_email'] = '';
            $paras['notify_time'] = date('Y-m-d H:i:s', time());
        } else if (isset($paras['OrderId'])) {
            $paras['out_order_id'] = $paras['OrderId'];
            $paras['total'] = number_format($paras['RefundAmout'] / 100, 2, '.', '');
            $paras['trade_no'] = $paras['MerID'];
            $paras['notify_id'] = 0;
            $paras['card_number'] = '';
            $paras['buyer_id'] = '';
            $paras['buyer_email'] = '';
            $paras['notify_time'] = date('Y-m-d H:i:s', time());
        } else if (isset($paras['out_trade_no'])) {
            $paras['out_order_id'] = $paras['out_trade_no'];
            $paras['total'] = $paras['total_fee'];
            $paras['notify_time'] = date('Y-m-d H:i:s', time());
            $paras['card_number'] = '';
        } else {
            $paras['out_order_id'] = 0;
            $paras['total'] = 0;
            $paras['trade_no'] = '';
            $paras['notify_id'] = 0;
            $paras['card_number'] = '';
            $paras['buyer_id'] = '';
            $paras['buyer_email'] = '';
            $paras['notify_time'] = date('Y-m-d H:i:s', time());
        }

        return $paras;
    }

    public static function filterString($str)
    {
        $res = str_replace('&', '_', $str);
        return $res;
    }

    public function actionPaymentMethods()
    {
        $components = Yii::app()->getComponents(false);
        $payment_methods = array();
        foreach ($components as $k => $c) {
            if (is_array($c)) {
                if (isset($c['class']) && strpos($c['class'], 'application.components.payment.') !== false) {
                    $payment_methods[$k] = array('payment_code' => $k, 'title' => $c['title'], 'logo' => $c['logo'], 'mobile' => $c['mobile'], 'bank' => $c['bank']);
                }
            }
        }

        echo json_encode($payment_methods);
    }


    public static function paymentMethods()
    {
        $components = Yii::app()->getComponents(false);
        $payment_methods = array();
        foreach ($components as $k => $c) {
            if (is_array($c)) {
                if (isset($c['class']) && strpos($c['class'], 'application.components.payment.') !== false) {
                    $payment_methods[$k] = array('payment_method' => $k, 'title' => $c['title'], 'logo' => $c['logo'], 'mobile' => $c['mobile'], 'bank' => $c['bank']);
                }
            }
        }

        return $payment_methods;
    }

    public static function convert($currencies, $value, $from, $to = 'CNY')
    {
        if (isset($currencies[$from])) {
            $from = $currencies[$from]['value'];
        } else {
            $from = 0;
        }

        if (isset($currencies[$to])) {
            $to = $currencies[$to]['value'];
        } else {
            $to = 0;
        }

        if ($from == 0 || $to == 0) {
            return $value;
        }
        return $value * ($to / $from);
    }

    public static function distinguishPaymentMethod($param)
    {
        $payment_method = '';
        if (!empty($param['method'])) {
            $payment_method = $param['method'];
        }else if (isset($param['extra_common_param'])) {
            $payment_method = $param['extra_common_param'];
        }else if (isset($param['reqReserved'])) {
            $payment_method = $param['reqReserved'];
        }else if (isset($param['Priv1'])){
            $payment_method = 'chinapay_pc';
        }else if (isset($param['orderNumber'])){
            $payment_method = 'chinapay_wap';
        }else if (isset($param['notify_data'])){
            $payment_method = 'alipay_wap';
        }else if (isset($param['orderAmount'])) {
            $payment_method = 'unionpay_pc';
        }else if (isset($param['notify_time']) && isset($param['batch_no'])) {
            $payment_method = 'alipay_pc';
        }else if (isset($param['weixinpay_pc'])) {
            $payment_method = 'weixinpay_pc';
        }else if (isset($param['weixinpay_widget'])) {
            $payment_method = 'weixinpay_widget';
        }else if (isset($param['txnAmt'])) {
            $payment_method = 'bocpay_pc';
        }else if (isset($param['PAYMENT'])) {
            $payment_method = 'ccbpay_pc';
        }else if (isset($param['payment_type'])) {
            $payment_method = 'alipay_app';
        }
        return $payment_method;
    }

    public static function terminalMatch($ismobile, $payment_method)
    {
        $matched_method = $payment_method;
        if ($ismobile && $payment_method == 'alipay_pc') {
            $matched_method = 'alipay_wap';
        }else if (!$ismobile && ($payment_method == 'alipay_wap' || $payment_method == 'alipay_app')) {
            $matched_method = 'alipay_pc';
        }
        if ($ismobile && $payment_method == 'weixinpay_pc') {
            $matched_method = 'weixinpay_widget';
        }else if (!$ismobile && $payment_method == 'weixinpay_widget') {
            $matched_method = 'weixinpay_pc';
        }
        return $matched_method;
    }
} 