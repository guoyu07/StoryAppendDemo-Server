<?php
/**
 * @project hitour.server
 * @file CheckCommand.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 14-5-29 下午8:34
**/
define('PHP_COMMAND', '/home/app/local/php/bin/php');

class CheckCommand extends CConsoleCommand {
    private $MAX_CHECK_TIMES = 120;
    private $CHECK_INTERVAL = 300;
    private $GtaBooking = null;
    private $ACTION_HANDLE = '';
    private $IS_RUNNING = false;

    public function init()
    {
        $this->GtaBooking = new GtaBooking();
    }

    /**
     * @param int $check_type 0:normally import; 1:import all data; 2:only sighteeing base; 3:only price; 4:only transfer;
     */
    public function actionAutoImport($check_type = 0)
    {
        $need_import = false;

        if (!empty($check_type) && $check_type == 1) {
            $sql = 'DELETE FROM `gta_auto_import` WHERE auto_id>1';
            $ret = Yii::app()->db->createCommand($sql)->execute();
        }
        $sql = 'INSERT IGNORE INTO `gta_auto_import` (city_code,item_id) ';
        $sql .= 'SELECT city_code,supplier_product_id FROM ht_product ';
        $sql .= 'WHERE supplier_id=11 AND status IN (3)';
        $ret = Yii::app()->db->createCommand($sql)->execute();

        $sql = 'SELECT city_code, item_id FROM gta_auto_import WHERE city_code="0" AND item_id="0"';
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        if ($result && count($result) > 0) {
            $need_import = true;
        }

        if ($need_import) {
            $sql = 'UPDATE gta_auto_import SET city_code="1",item_id="1" WHERE city_code="0" AND item_id="0"';
            $isok = Yii::app()->db->createCommand($sql)->execute();
            if (false === $isok) {
                echo 'Error update state failed. ['. $sql .']';
                exit;
            }

            $sql = 'SELECT auto_id, city_code, item_id FROM gta_auto_import WHERE auto_id>0 and status=0 ';
            $sql .= 'ORDER BY insert_time';
            $result = Yii::app()->db->createCommand($sql)->queryAll();
            if ($result && count($result) > 0) {
                foreach($result as $row) {
                    $city_code = $row['city_code'];
                    $item_id = $row['item_id'];

                    if ($check_type <= 1 || $check_type == 2) {
                        $sql = 'UPDATE gta_auto_import SET status=1,update_time=CURRENT_TIMESTAMP() WHERE auto_id="'. $row['auto_id'] .'"';
                        Yii::app()->db->createCommand($sql)->execute();

                        $command = PHP_COMMAND.' entry.php gtaimport sightseeing --city_code="'.$city_code.'" --item_id="'.$item_id.'" --language=en';
                        echo $command . "\n";
                        $ret = system($command);
                        if (!empty($ret)) {
                            echo $ret . "\n";
                        }
                        $command = PHP_COMMAND.' entry.php gtaimport sightseeing --city_code="'.$city_code.'" --item_id="'.$item_id.'" --language=zh';
                        echo $command . "\n";
                        $ret = system($command);
                        if (!empty($ret)) {
                            echo $ret . "\n";
                        }
                    }

                    if ($check_type <= 1 || $check_type == 3) {
                        $command = PHP_COMMAND.' entry.php gtaimport price --city_code="'.$city_code.'" --item_id="'.$item_id.'" --type=1';
                        if ($city_code == 'PAR' && $item_id == 'LIDO07-2ND SHOW') {
                            $command .= ' --child_age=13';
                        }
                        echo $command . "\n";
                        $ret = system($command);
                        if (!empty($ret)) {
                            echo $ret . "\n";
                        }
                    }

                    if ($check_type <= 1 || $check_type == 4) {
                        $command = PHP_COMMAND.' entry.php gtaimport transfer --city_code="'.$city_code.'" --item_id="'.$item_id.'" --language=en';
                        echo $command . "\n";
                        $ret = system($command);
                        if (!empty($ret)) {
                            echo $ret . "\n";
                        }
                        $command = PHP_COMMAND.' entry.php gtaimport transfer --city_code="'.$city_code.'" --item_id="'.$item_id.'" --language=zh';
                        echo $command . "\n";
                        $ret = system($command);
                        if (!empty($ret)) {
                            echo $ret . "\n";
                        }
                    }

                    $sql = 'UPDATE gta_auto_import SET status=2,update_time=CURRENT_TIMESTAMP() WHERE auto_id="'. $row['auto_id'] .'"';
                    Yii::app()->db->createCommand($sql)->execute();
                    if ($check_type < 4) {
                        sleep(1);
                    }
                }
            }

            $sql = 'UPDATE gta_auto_import SET city_code="0",item_id="0" WHERE city_code="1" AND item_id="1"';
            $isok = Yii::app()->db->createCommand($sql)->execute();
            if (false === $isok) {
                echo 'Error update state failed. ['. $sql .']';
                exit;
            }
        }
    }

    public function actionExpire($type = 'payment')
    {
        switch($type) {
            case 'payment':
                $this->paymentTimeCheck();
                break;
            case 'stock':
                $this->stockCheck();
                break;
            default:
                break;
        }
    }

    private function stockCheck()
    {
        $sql = 'SELECT product_id,all_stock_num,current_stock_num,payment_reservation_duration ';
        $sql .= 'FROM ht_product_stock';
        $result = Yii::app()->db->createCommand($sql)->queryAll();

        if ($result && count($result) > 0) {
            foreach($result as $rkey => $stock) {
                $product_id = $stock['product_id'];
                $duration = $stock['payment_reservation_duration'];

                $sql = 'SELECT op.order_id,op.order_product_id FROM ht_order_product op ';
                $sql .= 'LEFT JOIN ht_order o ON op.order_id=o.order_id ';
                $sql .= 'WHERE op.stock_limited=1 AND op.product_id="' . $product_id . '" ';
                $sql .= 'AND o.status_id=1 ';
                $sql .= 'AND TIMESTAMPDIFF(SECOND,o.date_added,NOW()) > ' . $duration;
                $res = Yii::app()->db->createCommand($sql)->queryAll();
                if ($res && count($res) > 0) {
                    $orders = array();
                    $update_ok_num = 0;
                    foreach($res as $rekey => $order) {
                        array_push($orders, $order['order_id']);
                        $isok = Yii::app()->stateMachine->switchStatus($order['order_id'], HtOrderStatus::ORDER_NOTPAY_EXPIRED, '已过支付时限！');
                        if ($isok) {
                            $update_ok_num++;
                            //$controller->model_catalog_product_stock->recycleStockByOrder($order['order_id']);
                        }
                    }
                    $order_ids = implode(',', $orders);
                    $expire_order_num = count($orders);

                    Yii::log('Expire order recycle:expire_order_num['.$expire_order_num.']update_ok_num['.$update_ok_num.']order_ids['.$order_ids.']', CLogger::LEVEL_INFO);
                }
            }
        }
    }

    private function paymentTimeCheck()
    {
        $cur_time = date('Y-m-d H:i:s', time());
        $sql = 'SELECT order_id FROM ht_order ';
        $sql .= 'WHERE payment_time_limit<="'.$cur_time.'" AND status_id="'.HtOrderStatus::ORDER_CONFIRMED.'"';
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        if ($result && count($result)) {
            foreach($result as $order) {
                Yii::app()->stateMachine->switchStatus($order['order_id'], HtOrderStatus::ORDER_NOTPAY_EXPIRED, '');
            }
        }
    }

    public function actionValid()
    {
        //-------------------------------------
        // GTA商品有效性检查
        //-------------------------------------
        $sql = 'SELECT p.product_id,p.city_code,p.supplier_product_id FROM `ht_product` p ';
        $sql .= 'LEFT JOIN ht_product_tour_operation pt ON p.product_id=pt.product_id ';
        $sql .= 'WHERE pt.product_id IS NULL AND p.supplier_id=11 AND p.status IN (3)';
        $result = Yii::app()->db->createCommand($sql)->queryAll();

        if ($result && count($result) > 0) {
            $message = '<html><head></head><body>';
            $message .= '<br>Hi, hitourer, 以下GTA商品的TourOperation已失效,请尽快检查:<br><br>';
            $message .= '<table border="1px" border-collapse="collapse">';
            $message .= '<tr>';
            $message .= '<td>产品ID</td><td>城市</td><td>GTA_ID</td>';
            $message .= '</tr>';
            foreach($result as $rkey => $product) {
                $product_id = $product['product_id'];
                $city_code = $product['city_code'];
                $item_id = $product['supplier_product_id'];
                $message .= '<tr>';
                $message .= '<td>'.$product_id.'</td><td>'.$city_code.'</td><td>'.$item_id.'</td>';
                $message .= '</tr>';
            }
            $message .= '</table>';
            $message .= '<br>== 此邮件由系统监控程序每天早'.(date('H')).'点自动发送. ==<br>';
            $message .= '</body></html>';

            if (Yii::app()->params['PAYMENT_REALLY']) {
                $to = 'amber@hitour.cc,john@hitour.cc';
                $cc = 'zxc@hitour.cc';
            } else {
                $to = 'zxd@hitour.cc';
                $cc = '';
            }
            $subject = 'GTA商品TourOperation失效报警!!!';
            $body = $message;
            $attachment = array();

            Mail::sendToCustomer($to, $subject, $body, $attachment, 1, $cc);
        }
    }

    private function checkProductPrice(&$products, $manager_name, $manager_products)
    {
        $manager = '';
        $vars = explode('@', $manager_name);
        if (is_array($vars) && !empty($vars)) {
            $manager = ucfirst(strtolower($vars[0]));
        }
        if (empty($manager)) {
            return;
        }
        $send_it = false;
        $message = '<html><head></head><body>';
        $message .= '<br><span style="color:blue">Hi, '.$manager.', 以下GTA商品的售卖价已经低于我们的成本价,或已大于GTA价格,请尽快检查:</span><br><br>';
        $message .= '<table border="1px" border-collapse="collapse">';
        $message .= '<tr>';
        $message .= '<td>商品ID</td><td>城市ID</td><td>GTA_ID</td><td>城市</td><td>商品名</td><td>GTA价格</td><td>成本价</td><td>当前售价</td>';
        $message .= '</tr>';
        $message .= '<tr><td colspan="9" style="color:#ff0000">以下GTA商品的售卖价已经低于我们的成本价</td></tr>';
        foreach($products as $pkey => $product) {
            $product_id = $product['product_id'];
            $city_code = $product['city_code'];
            $item_id = $product['supplier_product_id'];
            $city_name = $product['cn_name'];
            $product_name = $product['name'];
            if (empty($manager_products[$product_id]) || !empty($product['mailed_1'])) {
                continue;
            }

            $sql = 'SELECT price_plan_id,from_date,to_date FROM ht_product_price_plan ';
            $sql .= 'WHERE product_id="'.$product_id.'" AND ';
            $sql .= 'from_date<=CURRENT_DATE() AND to_date>=CURRENT_DATE() ORDER BY from_date';
            $planres = Yii::app()->db->createCommand($sql)->queryAll();
            if ($planres && count($planres) > 0) {
                foreach($planres as $plankey => $plan) {
                    $sql = 'SELECT supplier_price, cost_price, price, special_code FROM ht_product_price_plan_item pppi ';
                    $sql .= 'WHERE pppi.price_plan_id="'.$plan['price_plan_id'].'" ';
                    $sql .= 'AND pppi.ticket_id=2 ';
                    //$sql .= 'AND (ABS(supplier_price - price) / price * 100) > 5 ';
                    $sql .= 'AND cost_price > price ';
                    $sql .= 'LIMIT 1';
                    $priceres = Yii::app()->db->createCommand($sql)->queryRow();
                    if ($priceres && count($priceres) > 0) {
                        $message .= '<tr>';
                        $message .= '<td>'.$product_id.'</td>';
                        $message .= '<td>'.$city_code.'</td>';
                        $message .= '<td>'.$item_id.'</td>';
                        $message .= '<td>'.$city_name.'</td>';
                        $message .= '<td>'.$product_name.'</td>';
                        $message .= '<td>'.$priceres['supplier_price'].'</td>';
                        $message .= '<td style="color:#ff0000">'.$priceres['cost_price'].'</td>';
                        $message .= '<td style="color:#ff0000">'.$priceres['price'].'</td>';
                        $message .= '</tr>';
                        $products[$pkey]['mailed_1'] = 1;
                        $send_it = true;
                        break;
                    }
                }
            }
        }
        $message .= '<tr><td colspan="9" style="color:#ff0000">以下GTA商品的售卖价已大于GTA价格</td></tr>';
        foreach($products as $pkey => $product) {
            $product_id = $product['product_id'];
            $city_code = $product['city_code'];
            $item_id = $product['supplier_product_id'];
            $city_name = $product['cn_name'];
            $product_name = $product['name'];
            if (empty($manager_products[$product_id]) || !empty($product['mailed_2'])) {
                continue;
            }

            $sql = 'SELECT price_plan_id,from_date,to_date FROM ht_product_price_plan ';
            $sql .= 'WHERE product_id="'.$product_id.'" AND ';
            $sql .= 'from_date<=CURRENT_DATE() AND to_date>=CURRENT_DATE() ORDER BY from_date';
            $planres = Yii::app()->db->createCommand($sql)->queryAll();
            if ($planres && count($planres) > 0) {
                foreach($planres as $plankey => $plan) {
                    $sql = 'SELECT supplier_price, cost_price, price, special_code FROM ht_product_price_plan_item pppi ';
                    $sql .= 'WHERE pppi.price_plan_id="'.$plan['price_plan_id'].'" ';
                    $sql .= 'AND pppi.ticket_id=2 ';
                    $sql .= 'AND supplier_price < price ';
                    $sql .= 'LIMIT 1';
                    $priceres = Yii::app()->db->createCommand($sql)->queryRow();
                    if ($priceres && count($priceres) > 0) {
                        $message .= '<tr>';
                        $message .= '<td>'.$product_id.'</td>';
                        $message .= '<td>'.$city_code.'</td>';
                        $message .= '<td>'.$item_id.'</td>';
                        $message .= '<td>'.$city_name.'</td>';
                        $message .= '<td>'.$product_name.'</td>';
                        $message .= '<td style="color:#ff0000">'.$priceres['supplier_price'].'</td>';
                        $message .= '<td>'.$priceres['cost_price'].'</td>';
                        $message .= '<td style="color:#ff0000">'.$priceres['price'].'</td>';
                        $message .= '</tr>';
                        $products[$pkey]['mailed_2'] = 1;
                        $send_it = true;
                        break;
                    }
                }
            }
        }
        $message .= '</table>';
        $message .= '<br>== 此邮件由系统监控程序每天早'.(date('H')).'点自动发送. ==<br>';
        $message .= '</body></html>';

        if (!$send_it) {
            return;
        }
        if (Yii::app()->params['PAYMENT_REALLY']) {
            $to = $manager_name;
            $cc = '';
        } else {
            $to = 'zxd@hitour.cc';
            $cc = '';
        }
        $subject = 'GTA商品价格变化报警!!!';
        $body = $message;
        $attachment = array();

        Mail::sendToOP($to, $subject, $body, $attachment, 1, $cc);
    }

    public function actionPrice()
    {
        //-------------------------------------
        // GTA商品价格合理性检查
        //-------------------------------------
        $sql = 'SELECT p.product_id,p.city_code,supplier_product_id,c.cn_name,pd.name ';
        $sql .= 'FROM `ht_product` p ';
        $sql .= 'LEFT JOIN ht_product_description pd ON p.product_id=pd.product_id ';
        $sql .= 'LEFT JOIN ht_city c ON p.city_code=c.city_code ';
        $sql .= 'WHERE p.supplier_id=11 ';
        $sql .= 'AND p.status IN (3) AND pd.language_id=2 ';
        $sql .= 'ORDER BY p.product_id';
        $result = Yii::app()->db->createCommand($sql)->queryAll();

        if ($result && count($result) > 0) {
            $manager_ids = array();
            $sql = 'SELECT product_id,manager_name FROM ht_product_manager';
            $managers = Yii::app()->db->createCommand($sql)->queryAll();
            if ($managers && count($managers) > 0) {
                foreach($managers as $manager) {
                    $manager_name = strtolower($manager['manager_name']);
                    $manager_ids[$manager_name][$manager['product_id']] = 1;
                }
            }
            foreach($manager_ids as $manager_name => $manager_products) {
                $this->checkProductPrice($result, $manager_name, $manager_products);
            }
            $manager_products = array();
            foreach($result as $product) {
                $manager_products['zxc@hitour.cc'][$product['product_id']] = 1;
            }
            $this->checkProductPrice($result, $manager_name, $manager_products);
        }
    }

    public function actionBooking($type = HtProductShippingRule::BT_GTA, $order_id = '', $force = 0)
    {
        $method_check = (strtolower($type)) . 'BookingCheck';
        if (method_exists('CheckCommand', $method_check)) {
            $this->{$method_check}($order_id, $force);
        }else{
            Yii::log('Booking check method['.$method_check.'] is not found.', CLogger::LEVEL_INFO);
        }
    }

    private function cpicBookingCheck($order_id = '', $force = 0){
        //TODO:
    }

    private function gtaBookingCheck($order_id = '', $force = 0)
    {
        //-------------------------------------
        // GTA订单状态检查
        //-------------------------------------
        $sql = 'SELECT o.order_id, op.product_id, op.order_product_id, op.supplier_order_id,op.tour_date ';
        $sql .= 'FROM `ht_order` o ';
        $sql .= 'LEFT JOIN ht_order_product op ON o.order_id=op.order_id ';
        $sql .= 'LEFT JOIN ht_product p ON op.product_id=p.product_id ';
        $sql .= 'WHERE p.supplier_id=11 ';
        if (!empty($order_id)) {
            $sql .= 'AND o.order_id IN ('. $order_id .') ';
        }
        $sql .= 'AND o.status_id in ('.HtOrderStatus::ORDER_WAIT_CONFIRMATION.','.HtOrderStatus::ORDER_BOOKING_FAILED.','.HtOrderStatus::ORDER_STOCK_FAILED.') ';
        if (empty($force)) {
            $sql .= 'AND TIMESTAMPDIFF(SECOND,o.date_modified,NOW()) > '. $this->CHECK_INTERVAL;
        }
        $result = Yii::app()->db->createCommand($sql)->queryAll();

        if ($result && count($result) > 0) {
            foreach($result as $rkey => $order) {
                $order_id = $order['order_id'];
                $supplier_order_id = $order['supplier_order_id'];
                $tour_date = $order['tour_date'];

                if (!empty($supplier_order_id)) {
                    $needCheck = $this->needStatusCheck($order_id, $supplier_order_id, $tour_date);
                    if (!$needCheck && empty($force)) {
                        continue;
                    }else{
                        Yii::log('[Booking check]: auto check start for order_id['. $order_id .']', CLogger::LEVEL_INFO);
                    }

                    $order= Yii::app()->order->getOrderDetailForVoucher($order_id);
                    $retdata = $this->GtaBooking->checkBooking($order['order'], $order['order_products'][0]);
                    if (empty($retdata['status'])) {
                        Yii::log('[Booking check]: order_id['. $order_id .'] status is null, reference['. $supplier_order_id .']',CLogger::LEVEL_INFO);
                        continue;
                    }else{
                        Yii::log('[Booking check]: order_id['. $order_id .'] status is ['. $retdata['status'] .'], reference['. $supplier_order_id .']',CLogger::LEVEL_INFO);
                    }
                }else{
                    $retdata['status'] = 'None';
                }

                switch(trim($retdata['status'])) {
                    case 'Fail': //GTA数据读取失败,不做处理
                        break;
                    case 'F': //GTA返回失败错误码, 发邮件给OP
                        $this->sendMailToOP($order_id, $retdata['status']);
                        break;
                    case 'CP': //GTA返回结果是订单待确认,不做处理
                        break;
                    case 'X': //GTA返回结果是订单已取消,发邮件给OP
                        $this->sendMailToOP($order_id, $retdata['status']);
                        break;
                    case 'None': //GTA没有找到这个订单,需要重新下单
                        $bookingResult = Yii::app()->booking->bookingOrder($order_id);
                        if ($bookingResult['code'] == 200) {
                            $isok = Yii::app()->stateMachine->switchStatus($order_id,HtOrderStatus::ORDER_TO_DELIVERY);
                            Yii::log('[Booking check]: Order[' . $order_id . '] booking successfully.', CLogger::LEVEL_INFO);
                        }else{
                            Yii::log('[Booking check]: Order[' . $order_id . '] booking failed, code['. $bookingResult['code'] .']msg['.$bookingResult['msg'].'].', CLogger::LEVEL_ERROR);
                        }
                        break;
                    case 'C': //GTA返回结果是订单已完成,需要更改订单状态并发货
                        $retdata['current_status'] = $retdata['status'];
                        $gtaResult = $this->GtaBooking->updateSupplierOrder($supplier_order_id, $retdata);
                        if ($gtaResult) {
                            $isok = Yii::app()->stateMachine->switchStatus($order_id,HtOrderStatus::ORDER_TO_DELIVERY);
                            Yii::log('[Booking check]: Order[' . $order_id . '] booking successfully.',CLogger::LEVEL_INFO);
                        }else{
                            Yii::log('[Booking check]: Order[' . $order_id . '] booking successfully but update GTA order status failed.',CLogger::LEVEL_INFO);
                        }
                        break;
                    default:
                        Yii::log('[Booking check]: order_id['. $order_id .'] status is ['. $retdata['status'] .'], unknown',CLogger::LEVEL_INFO);
                        break;
                }
                $this->saveCheckData(1, $supplier_order_id, $order_id, $retdata);
            }
        }
    }

    public function actionShipping($order_id = '', $force = 0)
    {
        //-------------------------------------
        // 订单状态检查
        //-------------------------------------
        $sql = 'SELECT o.order_id, op.product_id, op.order_product_id, op.supplier_order_id,op.tour_date ';
        $sql .= 'FROM `ht_order` o ';
        $sql .= 'LEFT JOIN ht_order_product op ON o.order_id=op.order_id ';
        $sql .= 'LEFT JOIN ht_product p ON op.product_id=p.product_id ';
        $sql .= 'LEFT JOIN ht_product_shipping_rule psr ON op.product_id=psr.product_id ';
        $sql .= 'WHERE booking_type IN ("GTA","HITOUR","STOCK","CPIC") ';
        if (!empty($order_id)) {
            $sql .= 'AND o.order_id="'.$order_id.'" ';
        }
        if (!$force) {
            $sql .= 'AND o.status_id in ('.HtOrderStatus::ORDER_TO_DELIVERY.') ';
            $sql .= 'AND TIMESTAMPDIFF(SECOND,o.date_modified,NOW()) > '. $this->CHECK_INTERVAL;
        }
        $sql .= ' GROUP BY o.order_id';
        $result = Yii::app()->db->createCommand($sql)->queryAll();

        if ($result && count($result) > 0) {
            foreach($result as $rkey => $order) {
                $order_id = $order['order_id'];
                $shippingResult = Yii::app()->shipping->shippingOrder($order_id);
                if ($shippingResult['code'] == 200) {
                    $isok = Yii::app()->stateMachine->switchStatus($order_id,HtOrderStatus::ORDER_SHIPPED);
                    Yii::log('[Shipping check]: Order[' . $order_id . '] shipping successfully.', CLogger::LEVEL_INFO);
                }else{
                    $isok = Yii::app()->stateMachine->switchStatus($order_id,HtOrderStatus::ORDER_SHIPPING_FAILED);
                    Yii::log('[Shipping check]: Order[' . $order_id . '] shipping failed. code['.$shippingResult['code'].']', CLogger::LEVEL_ERROR);
                }
            }
        }
    }

    public function actionOrderAuto()
    {
        //-------------------------------------
        // 订单状态检查
        //-------------------------------------
        $sql = 'SELECT auto_id,order_id,status,comment,action FROM ht_order_auto WHERE status=0';
        $result = Yii::app()->db->createCommand($sql)->queryAll();

        if ($result && count($result) > 0) {
            foreach($result as $rkey => $order) {
                $order_id = $order['order_id'];
                $sql = 'UPDATE ht_order_auto SET status=1,date_modified=NOW() WHERE auto_id="'.$order['auto_id'].'"';
                Yii::app()->db->createCommand($sql)->execute();
                $action = $order['action'];
                switch($action) {
                    case 'shipping':
                        $shippingResult = Yii::app()->shipping->shippingOrder($order_id);
                        if ($shippingResult['code'] == 200) {
                            $isok = Yii::app()->stateMachine->switchStatus($order_id,HtOrderStatus::ORDER_SHIPPED, $order['comment']);
                            Yii::log('[Shipping check]: Order[' . $order_id . '] shipping successfully.', CLogger::LEVEL_INFO);
                        }else{
                            $isok = Yii::app()->stateMachine->switchStatus($order_id,HtOrderStatus::ORDER_SHIPPING_FAILED, $order['comment']);
                            Yii::log('[Shipping check]: Order[' . $order_id . '] shipping failed. code['.$shippingResult['code'].']', CLogger::LEVEL_ERROR);
                        }
                        break;
                    default:
                        break;
                }
                $sql = 'UPDATE ht_order_auto SET status=2,date_modified=NOW() WHERE auto_id="'.$order['auto_id'].'"';
                Yii::app()->db->createCommand($sql)->execute();
            }
        }
    }

    // 计算指定的GTA订单在当前时间点是否需要发送状态查询
    private function needStatusCheck($order_id, $reference, $tour_date)
    {
        if (empty($reference)) {
            return false;
        }

        //设置检查截止时间为TourDate当天时间早上7点(北京时间下午4点伦敦方面开始工作)
        $end_check_time = strtotime($tour_date) + 3600 * 7;
        $current_time = time();

        //提取状态查询历史记录
        $sql = 'SELECT id,status,check_time FROM gta_order_check WHERE booking_reference="'. $reference .'" ORDER BY check_time DESC';
        $result = Yii::app()->db->createCommand($sql)->queryRow();
        if (empty($result)) {
            return true;
        }else {
            $checked_num = count($result);
            //每6个小时检查一次
            $last_check_time = strtotime($result['check_time']);
            $need_check_time = $last_check_time + 3600;
            if ($last_check_time <= $end_check_time && $checked_num < $this->MAX_CHECK_TIMES && $result['status'] != 'F') {
                if ($need_check_time >= ($current_time - $this->CHECK_INTERVAL - 1) &&
                    $need_check_time <= ($current_time + $this->CHECK_INTERVAL - 1)) {
                    return true;
                }
            }else if ($result['status'] != 'Mail' && ($need_check_time > $end_check_time || $checked_num == $this->MAX_CHECK_TIMES || $result['status'] == 'F')) {
                //查询次数达到上限或GTA明确返回有错误发生,发送通知给OP进入人工处理流程
                $this->sendMailToOP($order_id, $result['status']);
                $data['status'] = 'Mail';
                $this->saveCheckData(1, $reference, $order_id, $data);
            }
        }
        return false;
    }

    private function saveCheckData($check_type, $supplier_order_id, $order_id, $retdata)
    {
        $sql = 'INSERT INTO gta_order_check SET ';
        $sql .= 'check_type="'. $check_type .'",';
        $sql .= 'booking_reference="'. $supplier_order_id .'",';
        $sql .= 'order_id="'. $order_id .'",';
        $sql .= 'api_reference="'. (isset($retdata['api_reference'])?$retdata['api_reference']:'') .'",';
        $sql .= 'status="'. (trim($retdata['status'])) .'",';
        $sql .= 'status_desc="'. (isset($retdata['status_desc'])?$retdata['status_desc']:'') .'",';
        $sql .= 'creation_date="'. (isset($retdata['creation_date'])?$retdata['creation_date']:'') .'"';
        $isok = Yii::app()->db->createCommand($sql)->execute();
        return $isok;
    }

    public function actionReturning($type = 'GTA')
    {
        switch($type) {
            case HtProductShippingRule::BT_GTA:
                $this->gtaReturningCheck();
                break;
            case HtProductShippingRule::BT_CPIC:
                $this->cpicReturningCheck();
                break;
            case '':
            default:
                break;
        }
    }

    private function gtaReturningCheck()
    {
        //-------------------------------------
        // GTA订单状态检查
        //-------------------------------------
        $sql = 'SELECT o.order_id, op.product_id, op.order_product_id, op.supplier_order_id,op.tour_date ';
        $sql .= 'FROM `ht_order` o ';
        $sql .= 'LEFT JOIN ht_order_product op ON o.order_id=op.order_id ';
        $sql .= 'LEFT JOIN ht_product p ON op.product_id=p.product_id ';
        $sql .= 'WHERE p.supplier_id=11 ';
        $sql .= 'AND o.status_id in ('.HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION.','.HtOrderStatus::ORDER_RETURN_CONFIRMED.','.HtOrderStatus::ORDER_RETURN_FAILED.') ';
        $sql .= 'AND TIMESTAMPDIFF(SECOND,o.date_modified,NOW()) > '. $this->CHECK_INTERVAL;
        $result = Yii::app()->db->createCommand($sql)->queryAll();

        if ($result && count($result) > 0) {
            foreach($result as $rkey => $order) {
                $order_id = $order['order_id'];
                $supplier_order_id = $order['supplier_order_id'];

                $order = Yii::app()->order->getOrderDetailForVoucher($order_id);

                $retdata = $this->GtaBooking->checkBooking($order['order'], $order['order_products'][0]);
                if (empty($retdata['status'])) {
                    Yii::log('[Returning check]: order_id['. $order_id .'] status is null, reference['. $supplier_order_id .']', CLogger::LEVEL_WARNING);
                    continue;
                }else{
                    Yii::log('[Returning check]: order_id['. $order_id .'] status is ['. $retdata['status'] .'], reference['. $supplier_order_id .']', CLogger::LEVEL_INFO);
                }

                switch(trim($retdata['status'])) {
                    case 'Fail': //GTA数据读取失败,不做处理
                        break;
                    case 'F': //GTA返回失败错误码, 订单未成功直接退款
                        $this->sendMailToOP($order_id, $retdata['status']);
                        break;
                    case 'CP': //GTA返回结果是订单待确认,直接退
                        $this->returnOrder($order);
                        break;
                    case 'X': //GTA返回结果是订单已取消,改状态并退款
                        $this->refundOrder($order);
                        break;
                    case 'None': //GTA没有找到这个订单,改状态并退款
                        $this->refundOrder($order);
                        break;
                    case 'C': //GTA返回结果是订单已完成,尝试退
                        $this->returnOrder($order);
                        break;
                    default:
                        Yii::log('[Returning check]: order_id['. $order_id .'] status is ['. $retdata['status'] .'], unknown', CLogger::LEVEL_WARNING);
                        break;
                }
                $this->saveCheckData(2, $supplier_order_id, $order_id, $retdata);
            }
        }
    }

    public function actionRefund($order_id = 0, $force = 0)
    {
        //-------------------------------------
        // 退款状态检查
        //-------------------------------------
        $sql = 'SELECT o.order_id, op.product_id, op.order_product_id, op.supplier_order_id,op.tour_date ';
        $sql .= 'FROM `ht_order` o ';
        $sql .= 'LEFT JOIN ht_order_product op ON o.order_id=op.order_id ';
        $sql .= 'LEFT JOIN ht_product p ON op.product_id=p.product_id ';
        $sql .= 'WHERE o.status_id in ('.HtOrderStatus::ORDER_REFUND_PROCESSING.') ';
        $sql .= 'AND o.payment_method IN ("weixinpay_widget","weixinpay_pc","bocpay_pc","bocpay_wap") ';
        if (!empty($order_id)) {
            $sql .= 'AND o.order_id="'.$order_id.'" ';
        }
        if (!$force) {
            $sql .= 'AND TIMESTAMPDIFF(SECOND,o.date_modified,NOW()) > '. $this->CHECK_INTERVAL;
        }
        $result = Yii::app()->db->createCommand($sql)->queryAll();

        if ($result && count($result) > 0) {
            foreach($result as $rkey => $order) {
                $order_id = $order['order_id'];

                $url = Yii::app()->createAbsoluteUrl('payGate/refundNotify/order_id/'.$order_id);
                $result = HTTPRequest::asyncRequest($url.'?refundnotify=1', 10);
            }
        }
    }

    private function returnOrder($order)
    {
        $order_id = $order['order']['order_id'];
        $res = $this->GtaBooking->returnRequest($order);
        if ($res['code'] == 200) {
            //退货成功,更改订单状态
            Yii::log('Cancel booking for order['.$order_id.'] success. code['.$res['code'].']msg['.$res['msg'].']', CLogger::LEVEL_INFO);
            $this->refundOrder($order);
        }else{
            Yii::log('Try to cancel booking for order['.$order_id.'] failed. code['.$res['code'].']msg['.$res['msg'].']', CLogger::LEVEL_ERROR);
        }
    }

    private function refundOrder($order)
    {
        $order_id = $order['order']['order_id'];
        $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_RETURN_CONFIRMED);
        if ($order['order']['total'] == 0.0) {
            Yii::log('Not need refund order['.$order_id.'], because order total is 0.', CLogger::LEVEL_INFO);
            $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_REFUND_SUCCESS);
        }else{
            Yii::app()->returning->refundOrder($order_id);
        }
    }

    private function sendMailToOP($order_id, $status)
    {

        $message = '<br>Hi, hitourer, 以下订单需要处理:<br><br>';
        $message .= '<br>订单ID: '. $order_id .'<br>';
        $message .= '<br>GTA状态: '. $status .'<br>';
        $message .= '<br>GTA订单自动处理超时,或出现问题, 请检查!';

        $to = 'zxd@hitour.cc';//'op@hitour.cc';
        $cc = 'zxd@hitour.cc';
        $subject = 'GTA订单自动处理超时报警!!!';
        $body = $message;
        $attachment = array();


        $mailSetting = HtEmailSetting::model()->findByAttributes(array('setting_name' => 'op'))->getAttributes();
        Mail::sendBySetting($mailSetting, $to, $subject, $body, $attachment, 1, $cc);
    }

    public function actionCurrency()
    {
        if (extension_loaded('curl')) {
            $data = array();

            $sql = 'SELECT * FROM ht_currency WHERE code != "CNY"';
            $result = Yii::app()->db->createCommand($sql)->queryAll();
            if ($result && count($result) > 0) {
                foreach ($result as $row) {
                    $data[] = 'CNY' . $row['code'] . '=X';
                }
            }

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, 'http://download.finance.yahoo.com/d/quotes.csv?s=' . implode(',', $data) . '&f=sl1&e=.csv');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $content = curl_exec($curl);

            curl_close($curl);

            $lines = explode("\n", trim($content));

            foreach ($lines as $line) {
                $currency = CodeUtility::utf8_substr($line, 4, 3);
                $value = CodeUtility::utf8_substr($line, 11, 6);

                if ((float)$value) {
                    $sql = 'UPDATE ht_currency SET value = "' . (float)$value . '", date_modified = "' . (date('Y-m-d H:i:s')) . '" WHERE code = "' . ($currency) . '"';
                    $isok = Yii::app()->db->createCommand($sql)->execute();
                }
            }

            $sql = 'UPDATE ht_currency SET value = "1.00000", date_modified = "' . (date('Y-m-d H:i:s')) . '" WHERE code = "CNY"';
            $isok = Yii::app()->db->createCommand($sql)->execute();
        }
    }

    //快到期商品给供应商负责人发邮件
    public function actionOverdueProducts()
    {
        //取所有上架商品
        $c = new CDbCriteria();
        $c->select = 'product_id';
        $c->addCondition('status = 3');
        $products = HtProduct::model()->findAll($c);
        $products = Converter::convertModelToArray($products);
        $need_sendmail_products = array();
        //取价格计划即将到期或已经过期的商品
        if(is_array($products) && count($products) > 0){
            foreach($products as $product){
                $plans = HtProductPricePlan::model()->findAll('product_id=' . $product['product_id']);
                $plans = Converter::convertModelToArray($plans);
                if(is_array($plans) && count($plans) > 0){
                    if($plans[0]['valid_region'] == 0){//整个区间
                        $date_rule = HtProductDateRule::model()->findByPk($product['product_id']);
                        if($date_rule){
                            $to_date = strtotime($date_rule['to_date']);
                            $today = strtotime(date('Y-m-d',time()));
                            if(($to_date - $today)/86400 <= 15){
                                $need_sendmail_products[$product['product_id']] = $date_rule['to_date'];
                            }
                        }
                    }else{
                        $to_dates = array();
                        foreach($plans as $plan){
                            $to_dates[] = $plan['to_date'];
                        }
                        array_multisort($to_dates,SORT_DESC,$plans);
                        $to_date = strtotime($plans[0]['to_date']);
                        $today = strtotime(date('Y-m-d',time()));
                        if(($to_date - $today)/86400 <= 15){
                            $need_sendmail_products[$product['product_id']] = $plans[0]['to_date'];
                        }
                    }
                }
            }
        }
        //发送邮件给供应商负责人
        if(count($need_sendmail_products) > 0){
            $mail_list = array();
            foreach($need_sendmail_products as $n=>$m){
                $product_manager = HtProductManager::model()->find('product_id = '.$n);
                if($product_manager['manager_name']){
                    $mail_list[$product_manager['manager_name']][] = "产品ID:$n,售卖截止时间为$m,点击<a href='http://backend.hitour.cc/admin/product/edit?product_id=".$n."#/editProductPrice'>编辑</a>产品价格体系";
                }
            }
            if(count($mail_list) > 0){
                foreach($mail_list as $k=>$v){
                    Mail::sendToOP($k, '售卖即将过期或已过期商品', implode('<br>',$v), 1, '');
                }
            }
        }
    }

    public function actionCps($order_id = 0, $force = 0)
    {
        $sql = 'SELECT order_id, channel, cookies FROM ht_order_trace ';
        $sql .= 'WHERE channel<>"" ';
        if (!empty($order_id) && !empty($force)) {
            $sql .= 'AND order_id="'.$order_id.'" ';
        }else{
            $sql .= 'AND TIMESTAMPDIFF(HOUR,insert_time,NOW()) < 48';
        }
        $result = Yii::app()->db->createCommand($sql)->queryAll();

        if ($result && count($result) > 0) {
            foreach($result as $rkey => $order) {
                $channel = $order['channel'];
                $order_id = $order['order_id'];
                $cookies = $order['cookies'];

                Yii::app()->cps->pushOrder($channel, $order_id, $cookies, $force);
            }
        }
    }

    public function beforeAction($action, $params)
    {
        $this->ACTION_HANDLE = dirname(__FILE__) . DIRECTORY_SEPARATOR . '.check_' . $action;
        if (file_exists($this->ACTION_HANDLE)) {
            $actionState = (int)(file_get_contents($this->ACTION_HANDLE));
            if ($actionState != 1) {
                file_put_contents($this->ACTION_HANDLE, '1');
            }else{
                $this->IS_RUNNING = true;
                return false;
            }
        }else{
            file_put_contents($this->ACTION_HANDLE, '1');
        }
        return true;
    }

    public function afterAction($action, $params, $exitCode = 0)
    {
        if (!$this->IS_RUNNING) {
            file_put_contents($this->ACTION_HANDLE, '0');
        }
        return true;
    }
}
