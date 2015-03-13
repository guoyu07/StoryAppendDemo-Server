<?php
/**
 * Created by PhpStorm.
 * User: app
 * Date: 14-5-24
 * Time: 下午6:45
 */
/**
 * @project hitour.server
 * @file test.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 14-5-24 下午6:45
**/

class TestCommand extends CConsoleCommand {

    public function usageError($message)
    {
        echo("Error: $message\n\n".$this->getHelp()."\n");
        exit(1);
    }

    public function runError($message)
    {
        echo("Error: $message\n\n");
        exit(1);
    }

    public function init()
    {
        return true;
    }

    public function actionIndex()
    {
        echo 'Usage: '."\n\n";
        echo '    php entry.php test booking [--order_id]' . "\n";
        echo '    php entry.php test shipping [--order_id]' . "\n";
        echo "\n";
    }

    public function actionBooking($order_id)
    {
        $result = Yii::app()->booking->bookingOrder($order_id);
        var_dump($result);
    }

    public function actionShipping($order_id)
    {
        $shippingResult = Yii::app()->shipping->shippingOrder($order_id);
        if ($shippingResult['code'] == 200) {
            $isok = Yii::app()->stateMachine->switchStatus($order_id,HtOrderStatus::ORDER_SHIPPED);
            Yii::log('[Shipping check]: Order[' . $order_id . '] shipping successfully.', CLogger::LEVEL_INFO);
        }else{
            $isok = Yii::app()->stateMachine->switchStatus($order_id,HtOrderStatus::ORDER_SHIPPING_FAILED);
            Yii::log('[Shipping check]: Order[' . $order_id . '] shipping failed. code['.$shippingResult['code'].']', CLogger::LEVEL_ERROR);
        }
        var_dump($isok);
    }

    public function actionReturn($order_id)
    {
        $result = Yii::app()->returning->returnRequest($order_id);
        var_dump($result);
    }

    public function actionRefund($order_id)
    {
        Yii::log('Ready to refund for order['.$order_id.'].', CLogger::LEVEL_INFO);
        $data = array_merge(array(), array('order_id'=>$order_id));
        $url = Yii::app()->createAbsoluteUrl('payGate/refund', $data);
        Yii::log('Refund request url:['.$url.']', CLogger::LEVEL_INFO);
        $result = HTTPRequest::asyncRequest($url, 10);
        var_dump($result);
    }

    public function actionClearPrice()
    {
        $sql = 'SELECT product_id FROM ht_product WHERE supplier_id=11 AND status=3';
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        if ($result && count($result) > 0) {
            foreach($result as $pkey => $product) {
                $sql = 'SELECT price_plan_id FROM ht_product_price_plan WHERE product_id="'.$product['product_id'].'"';
                $res = Yii::app()->db->createCommand($sql)->queryAll();
                if ($res && count($res) > 0) {
                    foreach($res as $rkey => $plan) {
                        $sql = 'DELETE FROM ht_product_price_plan_item WHERE price_plan_id="'.$plan['price_plan_id'].'"';
                        Yii::app()->db->createCommand($sql)->execute();
                    }
                    $sql = 'DELETE FROM ht_product_price_plan WHERE price_plan_id="'.$plan['price_plan_id'].'"';
                    Yii::app()->db->createCommand($sql)->execute();
                }
            }
        }
    }

    public function actionCheckPrice()
    {
        $sql = 'SELECT product_id FROM ht_product WHERE supplier_id=11 AND status=3';
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        if ($result && count($result) > 0) {
            foreach($result as $pkey => $product) {
                $sql = 'SELECT price_plan_id FROM ht_product_price_plan ';
                $sql .= 'WHERE product_id="'.$product['product_id'].'" AND special_codes<>"" AND special_codes<>"hitour"';
                $res = Yii::app()->db->createCommand($sql)->queryAll();
                if ($res && count($res) > 0) {
                    foreach($res as $rkey => $plan) {
                        $sql = 'SELECT item_id FROM ht_product_price_plan_item ';
                        $sql .= 'WHERE price_plan_id="'.$plan['price_plan_id'].'" AND ticket_id=2 AND special_code=""';
                        $cres = Yii::app()->db->createCommand($sql)->queryAll();
                        if ($cres) {
                            echo $product['product_id'].",";
                        }
                    }
                }
            }
        }
        echo "\n\n";
    }

    public function actionSwitchStatus($order_id, $status_id)
    {
        $isok = Yii::app()->stateMachine->switchStatus($order_id, $status_id);
        var_dump($isok);
    }

    public function actionCheckCover($product_id = 0)
    {
        $sql = 'SELECT product_id FROM ht_product WHERE status=3 ';
        if (!empty($product_id)) {
            $sql .= 'AND product_id="'.$product_id.'"';
        }
        $products = Yii::app()->db->createCommand($sql)->queryAll();
        if ($products && is_array($products)) {
            foreach($products as $product_key => $product) {
                if (empty($product))
                    continue;

                $product_id = $product['product_id'];
                $product = Yii::app()->product->getBaseData($product_id);
                if (empty($product['images']['cover']['image_url'])) {
                    echo $product_id."\n";
                }
            }
        }
    }


    public function actionSms($type = 'send', $batch = 0, $to = '', $content = '', $cardno = '', $cardpwd = '', $num = 2000, $once = 1)
    {
        $result = array();
        $sms = new Sms();
        switch ($type) {
            case 'register':
                $result = $sms->register();
                break;
            case 'send':
                if (empty($batch)) {
                    $result = $sms->send($to, $content);
                }else{
                    $result = $this->sendToAll($to, $content, $batch, $num, $once);
                }
                break;
            case 'receive':
                $result = $sms->receive();
                if ($result['code'] == 200) {
                    $this->saveSmsReply($result['data']);
                }
                break;
            case 'balance':
                $result = $sms->balance();
                break;
            case 'chargup':
                $result = $sms->chargUp($cardno, $cardpwd);
                break;
            default:
                break;
        }
        var_dump($result);
    }

    private function saveSmsReply($data)
    {
        $rows = explode('\n', $data);
        if (!empty($rows) && count($rows) > 0) {
            foreach($rows as $rkey => $reply) {
                $items = explode(',', $reply);
                if (empty($items[1]) || empty($items[2]) || empty($items[3])) {
                    continue;
                }
                $newReply = new HtSmsReply();
                $newReply['mo_id'] = $items[0];
                $newReply['special_service'] = $items[1];
                $newReply['mobile'] = $items[2];
                $newReply['content'] = iconv("GB2312", "UTF-8//IGNORE", urldecode($items[3]));
                $newReply['reply_time'] = $items[4];
                $newReply->insert();
            }
        }
    }

    private function sendToAll($to, $content, $batch, $num = 2000, $once = 1)
    {
        $mobile = array();
        if (empty($to)) {
            $sql = 'SELECT contacts_telephone FROM ht_order ORDER BY order_id';
            $phones = Yii::app()->db->createCommand($sql)->queryAll();
            if ($phones && is_array($phones)) {
                foreach($phones as $pkey => $phone) {
                    if (!$this->validateTelephone($phone['contacts_telephone']))
                        continue;
                    $mobile[$phone['contacts_telephone']] = 1;
                }
            }
        }else{
            $phones = explode(',', $to);
            foreach($phones as $phone) {
                $mobile[$phone] = 1;
            }
        }
        if (!empty($mobile)) {
            $sql = 'SELECT mobile,content FROM ht_sms_reply';
            $phones = Yii::app()->db->createCommand($sql)->queryAll();
            if ($phones && is_array($phones)) {
                foreach($phones as $rkey => $phone) {
                    $reply = strtoupper(trim($phone['content']));
                    if (isset($mobile[$phone['mobile']]) && $reply == 'TD') {
                        unset($mobile[$phone['mobile']]);
                    }
                }
            }
            $sql = 'SELECT mobile FROM ht_sms_history WHERE batch_id="'.$batch.'"';
            $phones = Yii::app()->db->createCommand($sql)->queryAll();
            if ($phones && is_array($phones)) {
                foreach($phones as $pkey => $phone) {
                    $ephones = explode(',', $phone['mobile']);
                    if (empty($ephones))
                        continue;
                    foreach($ephones as $ephone) {
                        if (isset($mobile[$ephone])) {
                            unset($mobile[$ephone]);
                        }
                    }
                }
            }
        }
        if (!empty($mobile)) {
            $sms = new Sms();
            $i = 0;
            $sendarr = array();
            foreach($mobile as $phone => $val) {
                array_push($sendarr, $phone);
                if (++$i >= $num) {
                    $to = implode(',', $sendarr);
                    $sendarr = array();
                    $i = 0;
                    $sms->send($to, $content, '', '', $batch);
                    if ($once) {
                        break;
                    }
                }
            }
            if ($i > 0 && $i < $num) {
                $to = implode(',', $sendarr);
                $sms->send($to, $content, '', '', $batch);
            }
        }
    }

    private function validateTelephone($mobile_phone)
    {
        $result = preg_match("/^1[3458][0-9]{9}/",$mobile_phone);
        return $result;
    }

    public function actionPaymentProductID()
    {
        $sql = 'SELECT id,order_id FROM ht_payment_history WHERE product_id=0';
        $phistory = Yii::app()->db->createCommand($sql)->queryAll();
        if ($phistory && is_array($phistory)) {
            foreach($phistory as $pkey => $history) {
                if (empty($history) || empty($history['order_id']))
                    continue;
                $sql = 'SELECT product_id FROM ht_order_product WHERE order_id="'.$history['order_id'].'"';
                $order_product = Yii::app()->db->createCommand($sql)->queryRow();
                if ($order_product) {
                    $sql = 'UPDATE ht_payment_history SET product_id="'.$order_product['product_id'].'" ';
                    $sql .= 'WHERE id="'.$history['id'].'"';
                    Yii::app()->db->createCommand($sql)->execute();
                }
            }
        }
    }

    public function actionTestMail()
    {
        $to = 'zxd@.hitour.cc;zxdinnet@ gmail.com;;';
        $cc = '';
        $message = '邮件测试发送';

        $subject = '邮件测试发送';
        $body = $message;
        $attachment = array();

        Mail::sendToOP($to, $subject, $body, $attachment, 1, $cc);
    }

    public function actionCpicAdd()
    {
        $order_id           = 12345;
        $supplier_order_id  = 54321;
        $booking_ref        = '5899277217119F5432343';
        $city_code          = 'LON';
        $item_id            = 'JXLU15_37';
        $special_code       = '';
        $tour_date          = '2014-11-16';
        $passengers         = array(
            array('order_passenger_id' => 1231231,'zh_name'=>'张三','passport_number'=>'G2123132','gender'=>1,'birth_date'=>'1975-10-22'),
        );

        Yii::app()->cpic->setTransType(CPICService::$TYPE_INSURE);
        $cpic_order_id = $booking_ref . '_' . $passengers[0]['order_passenger_id'];
        $result = Yii::app()->cpic->addBooking(
            $order_id, $booking_ref, $city_code, $item_id, $special_code, $tour_date,
            $passengers[0]
        );
        if (empty($result) || !isset($result['status'])) {
            $ret['code'] = 300;
            $ret['msg'] = 'Sent CPIC booking, but no response';
        }else if ($result['status'] == 'Fail') {
            $ret['code'] = 301;
            $ret['msg'] = $result['status_desc'];
        }else if ($result['status'] > 1) {
            $ret['code'] = 500;
            $ret['msg'] = $result['status_desc'];
        }else if ($result['status'] == 1) {
            $ret['code'] = 200;
            $ret['msg'] = $result['status_desc'];
        }
        print_r($ret);
    }

    public function actionCpicQuery()
    {
        $order_id    = 12345;
        $booking_ref = '5899277217119F5432343';
        $pol_number  = '150421400001423';
        $date = '2014-10-23';
        $time = '15:48:57';
        $result = Yii::app()->cpic->searchBooking(
            $order_id, $booking_ref, $pol_number, $date, $time
        );

    }

    public function actionCpicCancel()
    {
        $order_id    = 12345;
        $booking_ref = '5899277217119F5432343';
        $pol_number  = '150421400001423';
        $result = Yii::app()->cpic->cancelBooking(
            $order_id, $booking_ref, $pol_number
        );

    }

    public function actionClearCache() {
        Yii::app()->cache->flush();
    }

    public function actionGetOrderOut($order_id, $method = '') {
        $sql = 'SELECT * FROM ht_order WHERE order_id="'.$order_id.'"';
        $order = Yii::app()->db->createCommand($sql)->queryRow();
        if (empty($order)) {
            echo "Not found order[".$order_id."]\n\n";
        }else{
            $order_product = HtOrderProduct::model()->with('product.description')->findByAttributes(array('order_id'=>$order_id));
            $order_product = Converter::convertModelToArray($order_product);
            $order['product_id'] = $order_product['product_id'];
            $order['supplier_id'] = $order_product['product']['supplier_id'];

            $out_order_id = PayUtility::genOutTradeNo($order, $order['payment_method']);
            echo 'Order['.$order_id.'] out_order_id is ['.$out_order_id.']'."\n\n";
        }
    }

    public function actionExportUnion($union, $sdate, $edate)
    {
        $text = Yii::app()->cps->getOrderNormal($union, $sdate, $edate);
        if (!empty($text)) {
            file_put_contents('/home/app/opdir/channel/'.$union.'_'.$sdate.'_'.$edate.'.csv', $text);
        }else{
            echo 'Empty result.'."\n";
        }
    }
}