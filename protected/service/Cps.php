<?php
/**
 * @project hitour.server
 * @file Cps.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 14-10-16 下午4:09
 **/
class Cps extends CComponent
{
    private $union_name = '';
    private $channel = '';
    private $source = '';
    private $activity = '';
    private $token = '';
    private $secret = '64ef4b6f717a0a4c950ce40b9f220cc9';//md5(hitour.cc)
    public $target = 'http://www.hitour.cc';
    public $charset = 'UTF-8';
    public $cookie_name = 'hitour_union_cps';
    public $cookie_domain = '.hitour.cc';
    public $cookie_maxage = 2592000;
    public $connect_timeout = 3000;
    public $read_timeout = 3000;
    public $limit_ip = false;
    public $sign_check = true;
    private $product_cate = array(
        0 => array('commission'=>'HCA','rate'=>'0.05','name'=>'未分类'),
        1 => array('commission'=>'HCA','rate'=>'0.05','name'=>'单票'),
        2 => array('commission'=>'HCA','rate'=>'0.05','name'=>'组合票'),
        3 => array('commission'=>'HCA','rate'=>'0.05','name'=>'通票'),
        4 => array('commission'=>'HCA','rate'=>'0.05','name'=>'随上随下'),
        5 => array('commission'=>'HCA','rate'=>'0.05','name'=>'一日游'),
        6 => array('commission'=>'HCA','rate'=>'0.05','name'=>'优惠券'),
        7 => array('commission'=>'HCB','rate'=>'0.05','name'=>'酒店'),
        8 => array('commission'=>'HCB','rate'=>'0.10','name'=>'酒店套餐'),
    );

    public $unions = array();

    public function init()
    {
        $this->union_name = isset($_REQUEST['union']) ? $_REQUEST['union'] : '';
    }

    private function getValue($key, $default = '')
    {
        $value = (isset($_REQUEST[$key])) ? $_REQUEST[$key] : $default;
        return $value;
    }

    private function readParam()
    {
        if (isset($this->unions[$this->union_name])) {
            $union = $this->unions[$this->union_name];
            foreach($union['map'] as $key => $mapkey) {
                $this->{$key} = $this->getValue($mapkey, $this->{$key});
            }
            $this->charset         = $this->getValue('charset', $this->charset);
            $this->cookie_maxage   = $this->getValue('cookie_maxage', $this->cookie_maxage);
            $this->connect_timeout = $this->getValue('connect_timeout', $this->connect_timeout);
            $this->read_timeout    = $this->getValue('read_timeout', $this->read_timeout);
            $this->sign_check      = $this->getValue('sign_check', $this->sign_check);
        }
    }

    private function getSign($params)
    {
        ksort($params);
        $signStr = implode(';', $params);
        $sign = md5($signStr . ';' . $this->secret);
        return $sign;
    }

    private function saveCookie($params)
    {
        if (empty($params) || !is_array($params)) {
            return;
        }
        $params['sign'] = $this->getSign($params);
        $isok = setcookie(
            $this->cookie_name,
            json_encode($params),
            time() + $this->cookie_maxage,
            '/',
            $this->cookie_domain
        );
        if (!$isok) {
            Yii::log('Save cookie failed.['.(json_encode($params)).']', CLogger::LEVEL_WARNING);
        }
    }

    private function makeMark($check_domain)
    {
        //1.目标地址URL校验
        if (!strncasecmp($this->target, 'http://', 7) && !strncasecmp($this->target, 'https://', 7)) {
            $this->target = 'http://' . $this->target;
        }
        $vars = parse_url($this->target);
        if (empty($vars['host']) || ($check_domain && false === stripos($vars['host'], $this->cookie_domain))) {
            $this->target = 'http://www.hitour.cc';
        }
        //2.检查必填参数
        if (empty($this->source) || empty($this->token)) {
            Yii::log('['.$this->union_name.']Source or token is empty, do nothing and redirect to home page.', CLogger::LEVEL_WARNING);
            $this->target = 'http://www.hitour.cc';
            return;
        }
        //3.记录Cookie
        $params = array();
        if (isset($this->unions[$this->union_name])) {
            $union = $this->unions[$this->union_name];
            foreach($union['map'] as $key => $mapkey) {
                $params[$key] = $this->{$key};
            }
            $params['union'] = $this->union_name;
            $this->saveCookie($params);
        }
    }

    public function markUnion($check_domain = 0)
    {
        $this->readParam();
        $this->makeMark($check_domain);
        return $this->target;
    }

    private function validCheck($cps_value)
    {
        $cps_vars = (array)(json_decode($cps_value));
        if (!isset($cps_vars['sign'])) {
            return false;
        }
        $sign = $cps_vars['sign'];
        unset($cps_vars['sign']);
        $sign_n = $this->getSign($cps_vars);
        if ($sign != $sign_n) {
            return false;
        }
        return $cps_vars;
    }

    public function getUnionFromCookie()
    {
        $union_name = '';
        if (!isset($_COOKIE[$this->cookie_name])) {
            return $union_name;
        }
        $cps_str = $_COOKIE[$this->cookie_name];
        $cps_vars = $this->validCheck($cps_str);
        if ($cps_vars) {
            $union_name = isset($cps_vars['union']) ? $cps_vars['union'] : '';
        }
        return $union_name;
    }

    public function pushOrder($channel, $order_id, $cookies, $force = 0)
    {
        if (!isset($this->unions[$channel])) {
            Yii::log('Want to push order, but channel['.$channel.'] is not be defined.', CLogger::LEVEL_WARNING);
            return false;
        }

        $order = HtOrder::model()->findByPk($order_id);
        if (!empty($order) && !in_array($order['status_id'],[HtOrderStatus::ORDER_BOOKING_FAILED,HtOrderStatus::ORDER_PAYMENT_FAILED,HtOrderStatus::ORDER_REFUND_FAILED,HtOrderStatus::ORDER_RETURN_FAILED,HtOrderStatus::ORDER_SHIPPING_FAILED,HtOrderStatus::ORDER_STOCK_FAILED,HtOrderStatus::ORDER_CANCELED])) {
            $cps_record = HtCpsHistory::model()->findByAttributes(array(
                'status_id'=>$order['status_id'],
                'channel'=>$channel,
                'order_id'=>$order_id,
            ));
            if (empty($cps_record)) {
                $action = 'pushOrderTo' . ucfirst(strtolower($channel));
                if (method_exists('Cps', $action)) {
                    $cps_vars = $this->parseCookie($order_id, $cookies, $order['status_id']);
                    if (empty($cps_vars)) {
                        Yii::log('Parse order cookie failed,order['.$order_id.']', CLogger::LEVEL_WARNING);
                        return false;
                    }else{
                        return $this->{$action}($order_id, $cps_vars, $order['status_id'], $force);
                    }
                }else{
                    //Yii::log('Not found push order method for channel['.$channel.']', CLogger::LEVEL_WARNING);
                    return false;
                }
            }
        }
        return false;
    }

    private function parseCookie($order_id, $cookies, $status_id)
    {
        $cookies = (array)json_decode($cookies);
        if (empty($cookies[$this->cookie_name])) {
            Yii::log('Not found cookie['.$this->cookie_name.'] for order['.$order_id.']status['.$status_id.']', CLogger::LEVEL_WARNING);
            return false;
        }
        $cps_str = $cookies[$this->cookie_name];
        $cps_vars = $this->validCheck($cps_str);
        if (empty($cps_vars['token'])) {
            Yii::log('Not found token for push order. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
            return false;
        }
        return $cps_vars;
    }

    private function getCommission($cate)
    {
        if (!empty($this->product_cate[$cate])) {
            return $this->product_cate[$cate]['commission'];
        }else{
            return 'HCA';
        }
    }

    private function pushOrderToYiqifa($order_id, $cps_vars, $status_id)
    {
        $sql = 'SELECT o.order_id, total, payment_method, date_added, date_modified, en_name_customer ';
        $sql .= 'FROM ht_order o ';
        $sql .= 'LEFT JOIN ht_order_status os ON o.status_id=os.order_status_id ';
        $sql .= 'WHERE o.order_id="'.$order_id.'"';
        $order = Yii::app()->db->createCommand($sql)->queryRow();
        if (empty($order)) {
            Yii::log('Not found order for push order. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
            return false;
        }

        if ($status_id == HtOrderStatus::ORDER_CONFIRMED) {
            $data = array('orders' => array());
            $products = array();
            $sql = 'SELECT op.product_id,pd.name,opp.quantity,opp.price,p.type ';
            $sql .= 'FROM ht_order_product op ';
            $sql .= 'LEFT JOIN ht_order_product_price opp ON op.order_product_id=opp.order_product_id ';
            $sql .= 'LEFT JOIN ht_product_description pd ON op.product_id=pd.product_id ';
            $sql .= 'LEFT JOIN ht_product p ON op.product_id=p.product_id ';
            $sql .= 'WHERE op.order_id="'.$order_id.'" AND pd.language_id=2 AND opp.quantity>0';
            $prices = Yii::app()->db->createCommand($sql)->queryAll();
            if (!empty($prices)) {
                $price_total = 0;
                foreach($prices as $pkey => $price) {
                    $price_total += $price['price'] * $price['quantity'];
                }
                foreach($prices as $pkey => $price) {
                    $product['productNo'] = $price['product_id'];
                    $product['name'] = $price['name'];
                    $product['amount'] = $price['quantity'];
                    $product['price'] = $price['price'] * (1 - (($price_total - $order['total'])/$price_total));
                    $product['category'] = $price['type'];
                    $product['commissionType'] = $this->getCommission($price['type']);
                    array_push($products, $product);
                }
            }
            array_push($data['orders'], array(
                'orderNo' => $order_id,
                'orderTime' => $order['date_added'],
                'updateTime' => $order['date_modified'],
                'campaignId' => $cps_vars['activity'],
                'feedback' => $cps_vars['token'],
                'orderStatus' => $order['en_name_customer'],
                'paymentStatus' => 0,
                'paymentType' => $order['payment_method'],
                'products' => $products,
            ));
        }else{
            $data = array('orderStatus' => array());
            array_push($data['orderStatus'], array(
                'orderNo' => $order_id,
                'updateTime' => $order['date_modified'],
                'feedback' => $cps_vars['token'],
                'orderStatus' => $cps_vars['en_name_customer'],
                'paymentStatus' => 1,
                'paymentType' => $order['payment_method'],
            ));
        }
        if (!empty($data)) {
            $push_url = 'http://o.yiqifa.com/servlet/handleCpsInterIn?';
            $params['interId'] = $this->unions['yiqifa']['partner'];
            $params['json'] = json_encode($data);
            $result = HTTPRequest::request($push_url . http_build_query($params));
            if ($result['Status'] == 'OK') {
                if ($result['content'] == '0') {
                    $this->savePushOrderHistory('yiqifa', $order_id, $status_id, $params);
                }else{
                    Yii::log('Push order to yiqifa, he said failed, order_id['.$order_id.']status_id['.$status_id.']result['.$result['content'].']data['.http_build_query($params).']', CLogger::LEVEL_WARNING);
                }
            }else{
                Yii::log('Push order to yiqifa, request failed. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
            }
        }
        return false;
    }

    private function pushOrderToWeiyi($order_id, $cps_vars, $status_id, $force = 0)
    {
        $sql = 'SELECT o.order_id, total, payment_method, date_added, date_modified, customer_id ';
        $sql .= 'FROM ht_order o ';
        $sql .= 'WHERE o.order_id="'.$order_id.'"';
        $order = Yii::app()->db->createCommand($sql)->queryRow();
        if (empty($order)) {
            Yii::log('Not found order for push order. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
            return false;
        }

        if (!empty($force) || $status_id == HtOrderStatus::ORDER_CONFIRMED) {
            $data = array(
                'mid' => 'hitour_wy',
                'odate' => date('YmdHis', strtotime($order['date_added'])),
                'cid' => $cps_vars['token'],
                'bid' => $order['customer_id'],
                'oid' => $order_id,
                'pid' => '',
                'ptype' => '',
                'pnum' => '',
                'price' => '',
                'ostat' => $status_id,
            );
            $sql = 'SELECT op.product_id,pd.name,opp.quantity,opp.price,p.type ';
            $sql .= 'FROM ht_order_product op ';
            $sql .= 'LEFT JOIN ht_order_product_price opp ON op.order_product_id=opp.order_product_id ';
            $sql .= 'LEFT JOIN ht_product_description pd ON op.product_id=pd.product_id ';
            $sql .= 'LEFT JOIN ht_product p ON op.product_id=p.product_id ';
            $sql .= 'WHERE op.order_id="'.$order_id.'" AND pd.language_id=2 AND opp.quantity>0';
            $prices = Yii::app()->db->createCommand($sql)->queryAll();
            if (!empty($prices)) {
                $price_total = 0;
                foreach($prices as $pkey => $price) {
                    $price_total += $price['price'] * $price['quantity'];
                }
                foreach($prices as $pkey => $price) {
                    $data['pid']   .= $price['product_id'] . '|';
                    $data['ptype'] .= $this->getCommission($price['type']) . '|';
                    $data['pnum']  .= $price['quantity'] . '|';
                    $data['price'] .= ($price['price'] * (1 - (($price_total - $order['total'])/$price_total))) . '|';
                }
            }
        }
        if (!empty($data)) {
            foreach($data as $dkey => &$dval) {
                $dval = trim($dval, '|');
            }
            $push_url = 'http://track.weiyi.com/orderpush.aspx?';
            $result = HTTPRequest::request($push_url . http_build_query($data));
            if ($result['Status'] == 'OK') {
                if (false !== stripos($result['content'], 'success')) {
                    $this->savePushOrderHistory('weiyi', $order_id, $status_id, $data);
                }else{
                    Yii::log('Push order to weiyi, he said failed, order_id['.$order_id.']status_id['.$status_id.']result['.$result['content'].']data['.http_build_query($data).']', CLogger::LEVEL_WARNING);
                }
            }else{
                Yii::log('Push order to weiyi, request failed. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
            }
        }
        return false;
    }

    private function pushOrderToLinktech($order_id, $cps_vars, $status_id, $force = 0)
    {
        $sql = 'SELECT o.order_id, total, payment_method, date_added, date_modified, customer_id ';
        $sql .= 'FROM ht_order o ';
        $sql .= 'WHERE o.order_id="'.$order_id.'"';
        $order = Yii::app()->db->createCommand($sql)->queryRow();
        if (empty($order)) {
            Yii::log('Not found order for push order. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
            return false;
        }

        if (!empty($force) || $status_id == HtOrderStatus::ORDER_CONFIRMED) {
            $data = array(
                'm_id' => 'hitour',
                'a_id' => $cps_vars['token'],
                'mbr_id' => $order['customer_id'],
                'o_cd' => $order_id,
                'p_cd' => '',
                'it_cnt' => '',
                'price' => '',
                'c_cd' => '',
            );
            $sql = 'SELECT op.product_id,pd.name,opp.quantity,opp.price,p.type ';
            $sql .= 'FROM ht_order_product op ';
            $sql .= 'LEFT JOIN ht_order_product_price opp ON op.order_product_id=opp.order_product_id ';
            $sql .= 'LEFT JOIN ht_product_description pd ON op.product_id=pd.product_id ';
            $sql .= 'LEFT JOIN ht_product p ON op.product_id=p.product_id ';
            $sql .= 'WHERE op.order_id="'.$order_id.'" AND pd.language_id=2 AND opp.quantity>0';
            $prices = Yii::app()->db->createCommand($sql)->queryAll();
            if (!empty($prices)) {
                $price_total = 0;
                foreach($prices as $pkey => $price) {
                    $price_total += $price['price'] * $price['quantity'];
                }
                foreach($prices as $pkey => $price) {
                    $data['p_cd']   .= $price['product_id'] . '||';
                    $data['it_cnt'] .= $price['quantity'] . '||';
                    $data['price']  .= ($price['price'] * (1 - (($price_total - $order['total'])/$price_total))) . '||';
                    $data['c_cd']   .= $price['type'] . '||';
                }
            }
        }
        if (!empty($data)) {
            foreach($data as $dkey => &$dval) {
                $dval = trim($dval, '||');
            }
            $push_url = 'http://service.linktech.cn/purchase_cps.php?';
            $result = HTTPRequest::request($push_url . http_build_query($data));
            if ($result['Status'] == 'OK') {
                $this->savePushOrderHistory('linktech', $order_id, $status_id, $data);
                Yii::log('Push order to linktech finished, order_id['.$order_id.']status_id['.$status_id.']result['.$result['content'].']data['.http_build_query($data).']', CLogger::LEVEL_INFO);
            }else{
                Yii::log('Push order to linktech, request failed. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
            }
        }
        return false;
    }

    private function pushOrderToDuomai($order_id, $cps_vars, $status_id, $force = 0)
    {
        $sql = 'SELECT o.order_id, total, payment_method, date_added, date_modified, customer_id ';
        $sql .= 'FROM ht_order o ';
        $sql .= 'WHERE o.order_id="'.$order_id.'"';
        $order = Yii::app()->db->createCommand($sql)->queryRow();
        if (empty($order)) {
            Yii::log('Not found order for push order. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
            return false;
        }

        if (!empty($force) || $status_id == HtOrderStatus::ORDER_CONFIRMED) {
            $data = array(
                'hash' => '50f5d39793657b3295d7d58529104ef7',
                'euid' => $cps_vars['token'],
                'order_sn' => $order_id,
                'order_time' => date('Y-m-d H:i:s', strtotime($order['date_added'])),
                'order_price' => $order['total'],
                'is_new_custom' => 0,
                'channel' => (false !== stripos($order['payment_method'], '_pc')) ? 0 : 1,
                'status' => $status_id,
                'goods_id' => '',
                'goods_name' => '',
                'goods_price' => '',
                'goods_ta' => '',
                'goods_cate' => '',
                'goods_cate_name' => '',
                'totalPrice' => '',
                'rate' => '',
                'commission' => '',
                'commission_type' => '',
            );
            $sql = 'SELECT op.product_id,pd.name,opp.quantity,opp.price,p.type ';
            $sql .= 'FROM ht_order_product op ';
            $sql .= 'LEFT JOIN ht_order_product_price opp ON op.order_product_id=opp.order_product_id ';
            $sql .= 'LEFT JOIN ht_product_description pd ON op.product_id=pd.product_id ';
            $sql .= 'LEFT JOIN ht_product p ON op.product_id=p.product_id ';
            $sql .= 'WHERE op.order_id="'.$order_id.'" AND pd.language_id=2 AND opp.quantity>0';
            $prices = Yii::app()->db->createCommand($sql)->queryAll();
            if (!empty($prices)) {
                $price_total = 0;
                foreach($prices as $pkey => $price) {
                    $price_total += $price['price'] * $price['quantity'];
                }
                foreach($prices as $pkey => $price) {
                    $data['goods_id']        .= $price['product_id'] . '|';
                    $data['goods_name']      .= $price['name'] . '|';
                    $data['goods_price']     .= $price['price'] . '|';
                    $data['goods_ta']        .= $price['quantity'] . '|';
                    $data['goods_cate']      .= $price['type'] . '|';
                    $data['goods_cate_name'] .= $this->product_cate[$price['type']] . '|';
                    $data['totalPrice']      .= ($price['price'] * $price['quantity'] * (1 - (($price_total - $order['total'])/$price_total))) . '|';
                    $data['rate']            .= (empty($this->product_cate[$price['type']]) ? '0' : $this->product_cate[$price['type']]['rate']) . '|';
                    $data['commission']      .= (empty($this->product_cate[$price['type']]) ? '0' : $data['totalPrice'] * $this->product_cate[$price['type']]['rate']) . '|';
                    $data['commission_type'] .= $this->getCommission($price['type']) . '|';
                }
            }
        }
        if (!empty($data)) {
            foreach($data as $dkey => &$dval) {
                $dval = trim($dval, '|');
            }
            $push_url = 'http://www.duomai.com/api/order.php?';
            $result = HTTPRequest::request($push_url . http_build_query($data));
            if ($result['Status'] == 'OK') {
                if (false !== stripos($result['content'], '推送成功')) {
                    $this->savePushOrderHistory('duomai', $order_id, $status_id, $data);
                }else{
                    Yii::log('Push order to duomai, he said failed, order_id['.$order_id.']status_id['.$status_id.']result['.$result['content'].']data['.http_build_query($data).']', CLogger::LEVEL_WARNING);
                }
            }else{
                Yii::log('Push order to duomai, request failed. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
            }
        }
        return false;
    }

    private function savePushOrderHistory($channel, $order_id, $status_id, $data)
    {
        $cps_history = new HtCpsHistory();
        $cps_history['channel'] = $channel;
        $cps_history['order_id'] = $order_id;
        $cps_history['status_id'] = $status_id;
        $cps_history['content'] = json_encode($data);
        $isok = $cps_history->insert();
        if (!$isok) {
            Yii::log('Push order to '.$channel.' ok, but save history failed. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
            return false;
        }
        return true;
    }

    public function search()
    {
        $result = array();
        $this->channel = $this->union_name;
        if (empty($this->channel)) {
            $this->channel = isset($_REQUEST['channel']) ? $_REQUEST['channel'] : '';
        }
        if (empty($this->channel)) {
            $result = array('code' => 400, 'msg' => 'Channel is empty.');
        }else{
            $action = 'getOrderFor' . ucfirst(strtolower($this->channel));
            if (method_exists('Cps', $action)) {
                $data = $this->{$action}();
                $result = array('code' => 200, 'msg' => 'SUCCESS', 'data' => $data);
            }else{
                $result = array(400, 'Not supported channel['.$this->channel.']');
            }
        }
        return $result;
    }

    public function getOrderForAdarrive()
    {
        $orders = array();
        $sdate = isset($_REQUEST['sdate']) ? $_REQUEST['sdate'] : '';
        $edate = isset($_REQUEST['edate']) ? $_REQUEST['edate'] : '';

        if (empty($sdate) || empty($edate)) {
            return $orders;
        }

        $result = $this->getOrdersByChannel('adarrive', $sdate, $edate);
        if (!empty($result)) {
            foreach($result as $row) {
                $order_id = $row['order_id'];
                $status_id = $row['status_id'];
                $order = array();
                $cookies = (array)json_decode($row['cookies']);
                if (empty($cookies[$this->cookie_name])) {
                    Yii::log('Not found cookie['.$this->cookie_name.'] for order['.$order_id.']status['.$status_id.']', CLogger::LEVEL_WARNING);
                    continue;
                }
                $cps_str = $cookies[$this->cookie_name];
                $cps_vars = $this->validCheck($cps_str);
                if (empty($cps_vars['token'])) {
                    Yii::log('Not found token for get order. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
                    continue;
                }

                $quantity = 0;
                $prices = $this->getPricesByOrder($order_id);
                if (!empty($prices)) {
                    foreach($prices as $pkey => $price) {
                        $quantity += $price['quantity'];
                    }
                }
                if (empty($quantity)) {
                    Yii::log('Quantify of order is empty. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
                    continue;
                }

                $order['orderNumber'] = $order_id;
                $order['orderTime'] = $row['date_added'];
                $order['orderCate'] = $row['type'];
                $order['productName'] = $row['name'];
                $order['quantity'] = $quantity;
                $order['orderTotal'] = number_format($row['total'],2,'.','');
                $order['orderStatus'] = $row['en_name_customer'];
                $order['sid'] = $cps_vars['token'];
                $order['commission'] = ($row['status_id']==HtOrderStatus::ORDER_SHIPPED)?(number_format($row['total'] * 5 / 100,2,'.','')):0;
                array_push($orders, $order);
            }
        }
        return $orders;
    }

    public function getOrderForYiqifa()
    {
        $orders = array();
        $sdate = isset($_REQUEST['orderStartTime']) ? $_REQUEST['orderStartTime'] : '';
        $edate = isset($_REQUEST['orderEndTime']) ? $_REQUEST['orderEndTime'] : '';
        $cid   = isset($_REQUEST['cid']) ? $_REQUEST['cid'] : '';
        $mid   = isset($_REQUEST['mid']) ? $_REQUEST['mid'] : '';

        if (empty($sdate) || empty($edate)) {
            return $orders;
        }

        $output_txt = '';
        $result = $this->getOrdersByChannel('yiqifa', date('Y-m-d H:i:s',$sdate), date('Y-m-d H:i:s',$edate - 86400));
        if (!empty($result)) {
            $data = array('orders' => array());
            foreach($result as $row) {
                $order_id = $row['order_id'];
                $status_id = $row['status_id'];
                $cookies = (array)json_decode($row['cookies']);
                if (empty($cookies[$this->cookie_name])) {
                    Yii::log('Not found cookie['.$this->cookie_name.'] for order['.$order_id.']status['.$status_id.']', CLogger::LEVEL_WARNING);
                    continue;
                }
                $cps_str = $cookies[$this->cookie_name];
                $cps_vars = $this->validCheck($cps_str);
                if (empty($cps_vars['token'])) {
                    Yii::log('Not found token for get order. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
                    continue;
                }
                if ($cps_vars['activity'] != $cid) {
                    continue;
                }

                $quantity = 0;
                $products = array();
                $prices = $this->getPricesByOrder($order_id);
                if (!empty($prices)) {
                    $price_total = 0;
                    foreach($prices as $pkey => $price) {
                        $price_total += $price['price'] * $price['quantity'];
                    }
                    foreach($prices as $pkey => $price) {
                        $product['productNo'] = $price['product_id'];
                        $product['name'] = $price['name'];
                        $product['amount'] = $price['quantity'];
                        $product['price'] = $price['price'] * (1 - (($price_total - $row['total'])/$price_total));
                        $product['category'] = $price['type'];
                        $product['commissionType'] = $this->getCommission($price['type']);
                        array_push($products, $product);
                    }
                }
                array_push($data['orders'], array(
                    'orderNo' => $order_id,
                    'orderTime' => $row['date_added'],
                    'updateTime' => $row['date_modified'],
                    'campaignId' => $cps_vars['activity'],
                    'feedback' => $cps_vars['token'],
                    'orderStatus' => $row['en_name_customer'],
                    'paymentStatus' => 0,
                    'paymentType' => $row['payment_method'],
                    'products' => $products,
                ));
            }
        }
        return json_encode($data);
    }

    public function getOrderForWeiyi()
    {
        $orders = array();
        $sdate = isset($_REQUEST['starttime']) ? $_REQUEST['starttime'] : '';
        $edate = isset($_REQUEST['endtime']) ? $_REQUEST['endtime'] : '';

        if (empty($sdate) || empty($edate)) {
            return $orders;
        }

        $output_txt = '';
        $result = $this->getOrdersByChannel('weiyi', $sdate, $edate);
        if (!empty($result)) {
            foreach($result as $row) {
                $order_id = $row['order_id'];
                $status_id = $row['status_id'];
                $cookies = (array)json_decode($row['cookies']);
                if (empty($cookies[$this->cookie_name])) {
                    Yii::log('Not found cookie['.$this->cookie_name.'] for order['.$order_id.']status['.$status_id.']', CLogger::LEVEL_WARNING);
                    continue;
                }
                $cps_str = $cookies[$this->cookie_name];
                $cps_vars = $this->validCheck($cps_str);
                if (empty($cps_vars['token'])) {
                    Yii::log('Not found token for get order. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
                    continue;
                }

                $quantity = 0;
                $prices = $this->getPricesByOrder($order_id);
                if (!empty($prices)) {
                    $price_total = 0;
                    foreach($prices as $pkey => $price) {
                        $price_total += $price['price'] * $price['quantity'];
                    }
                    foreach($prices as $pkey => $price) {
                        $output_txt .= date('Y-m-d H:i:s', strtotime($row['date_added'])) . "\t";
                        $output_txt .= $cps_vars['token'] . "\t";
                        $output_txt .= $row['customer_id'] . "\t";
                        $output_txt .= $order_id . "\t";
                        $output_txt .= $price['product_id'] . "\t";
                        $output_txt .= $this->getCommission($price['type']) . "\t";
                        $output_txt .= $price['quantity'] . "\t";
                        $output_txt .= ($price['price'] * (1 - (($price_total - $row['total'])/$price_total))) . "\t";
                        $output_txt .= $status_id . "\t";
                    }
                }
                $output_txt .= "\n";
            }
        }
        return $output_txt;
    }

    public function getOrderForLinktech()
    {
        $orders = array();
        $sdate = isset($_REQUEST['date']) ? $_REQUEST['date'] : '';
        $edate = $sdate;

        if (empty($sdate) || empty($edate)) {
            return $orders;
        }

        $output_txt = '';
        $result = $this->getOrdersByChannel('linktech', $sdate, $edate);
        if (!empty($result)) {
            foreach($result as $row) {
                $order_id = $row['order_id'];
                $status_id = $row['status_id'];
                $cookies = (array)json_decode($row['cookies']);
                if (empty($cookies[$this->cookie_name])) {
                    Yii::log('Not found cookie['.$this->cookie_name.'] for order['.$order_id.']status['.$status_id.']', CLogger::LEVEL_WARNING);
                    continue;
                }
                $cps_str = $cookies[$this->cookie_name];
                $cps_vars = $this->validCheck($cps_str);
                if (empty($cps_vars['token'])) {
                    Yii::log('Not found token for get order. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
                    continue;
                }

                $quantity = 0;
                $prices = $this->getPricesByOrder($order_id);
                if (!empty($prices)) {
                    $price_total = 0;
                    foreach($prices as $pkey => $price) {
                        $price_total += $price['price'] * $price['quantity'];
                    }
                    foreach($prices as $pkey => $price) {
                        $output_txt .= '2' . "\t";
                        $output_txt .= date('His', strtotime($row['date_added'])) . "\t";
                        $output_txt .= $cps_vars['token'] . "\t";
                        $output_txt .= $order_id . "\t";
                        $output_txt .= $price['product_id'] . "\t";
                        $output_txt .= $row['customer_id'] . "\t";
                        $output_txt .= $price['quantity'] . "\t";
                        $p_price = $price['price'] * (1 - (($price_total - $row['total'])/$price_total));
                        $output_txt .= number_format($p_price,2,'.','') . "\t";
                        $output_txt .= $price['type'] . "\n";
                    }
                }
            }
        }
        return $output_txt;
    }

    public function getOrderForDuomai()
    {
        $orders = array();
        $sdate = isset($_REQUEST['stime']) ? $_REQUEST['stime'] : '';
        $edate = isset($_REQUEST['etime']) ? $_REQUEST['etime'] : '';
        $order_id = isset($_REQUEST['sn']) ? $_REQUEST['sn'] : '';

        if (empty($sdate) || empty($edate)) {
            return $orders;
        }

        $result = $this->getOrdersByChannel('duomai', date('YmdHis',$sdate), date('YmdHis',$edate), $order_id);
        if (!empty($result)) {
            foreach($result as $row) {
                $order_id = $row['order_id'];
                $status_id = $row['status_id'];
                $order = array();
                $cookies = (array)json_decode($row['cookies']);
                if (empty($cookies[$this->cookie_name])) {
                    Yii::log('Not found cookie['.$this->cookie_name.'] for order['.$order_id.']status['.$status_id.']', CLogger::LEVEL_WARNING);
                    continue;
                }
                $cps_str = $cookies[$this->cookie_name];
                $cps_vars = $this->validCheck($cps_str);
                if (empty($cps_vars['token'])) {
                    Yii::log('Not found token for get order. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
                    continue;
                }

                $order['euid'] = $cps_vars['token'];
                $order['mid'] = '';
                $order['order_sn'] = $order_id;
                $order['order_time'] = date('Y-m-d H:i:s', strtotime($row['date_added']));
                $order['order_price'] = $row['total'];
                $order['is_new_custom'] = 0;
                $order['order_channel'] = (false !== stripos($row['payment_method'], '_pc')) ? 0 : 1;
                $order['status'] = $status_id;
                $order['details'] = array();
                $order['commission'] = ($row['status_id']==HtOrderStatus::ORDER_SHIPPED)?(number_format($row['total'] * 5 / 100,2,'.','')):0;
                $prices = $this->getPricesByOrder($order_id);
                if (!empty($prices)) {
                    $price_total = 0;
                    foreach($prices as $pkey => $price) {
                        $price_total += $price['price'] * $price['quantity'];
                    }
                    foreach($prices as $pkey => $price) {
                        $product = array();
                        $product['goods_id']        = $price['product_id'];
                        $product['goods_name']      = $price['name'];
                        $product['goods_price']     = $price['price'];
                        $product['goods_ta']        = $price['quantity'];
                        $product['goods_cate']      = $price['type'];
                        $product['goods_cate_name'] = $this->product_cate[$price['type']];
                        $product['totalPrice']      = ($price['price'] * $price['quantity'] * (1 - (($price_total - $row['total'])/$price_total)));
                        $product['rate']            = (empty($this->product_cate[$price['type']]) ? '0' : $this->product_cate[$price['type']]['rate']) . '|';
                        $product['commission']      = (empty($this->product_cate[$price['type']]) ? '0' : $product['totalPrice'] * $this->product_cate[$price['type']]['rate']) . '|';
                        $product['commission_type'] = $this->getCommission($price['type']) . '|';
                        array_push($order['details'], $product);
                    }
                }
                array_push($orders, $order);
            }
        }
        echo CJSON::encode(array('success'=>1, 'orders'=>$orders));
        return '';
    }

    public function getOrderForAll()
    {
        $orders = array();
        $sdate = isset($_REQUEST['sdate']) ? $_REQUEST['sdate'] : '';
        $edate = isset($_REQUEST['edate']) ? $_REQUEST['edate'] : '';

        if (empty($sdate) || empty($edate)) {
            return $orders;
        }

        $output_txt = '';
        $order_array = array();
        $result = $this->getOrdersByChannel('all', $sdate, $edate);
        if (!empty($result)) {
            foreach($result as $row) {
                $order_id = $row['order_id'];
                $status_id = $row['status_id'];
                if (isset($order_array[$order_id])) {
                    continue;
                }else{
                    $order_array[$order_id] = 1;
                }
                $cookies = (array)json_decode($row['cookies']);
                if (empty($cookies[$this->cookie_name])) {
                    Yii::log('Not found cookie['.$this->cookie_name.'] for order['.$order_id.']status['.$status_id.']', CLogger::LEVEL_WARNING);
                    continue;
                }
                $cps_str = $cookies[$this->cookie_name];
                $cps_vars = $this->validCheck($cps_str);
                if (empty($cps_vars['token'])) {
                    Yii::log('Not found token for get order. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
                    continue;
                }

                $quantity = 0;
                $product_id = '';
                $product_name = '';
                $prices = $this->getPricesByOrder($order_id);
                if (!empty($prices)) {
                    $price_total = 0;
                    foreach($prices as $pkey => $price) {
                        $price_total += $price['price'] * $price['quantity'];
                        $quantity += $price['quantity'];
                        if ($pkey == 0) {
                            $product_id = $price['product_id'];
                            $product_name = $price['name'];
                        }
                    }
                }
                $output_txt .= $order_id . ',';
                $output_txt .= $row['date_added'] . ',';
                $output_txt .= $row['tour_date'] . ',';
                $output_txt .= $row['cn_name_customer'] . ',';
                $output_txt .= $row['total'] . ',';
                $output_txt .= $row['cn_name'] . ',';
                $output_txt .= $quantity . ',';
                $output_txt .= $row['channel'] . ',';
                $output_txt .= $product_name . ',';
                $output_txt .= $product_id . ',';
                $output_txt .= "\n";
            }
        }
        return $output_txt;
    }

    private function getOrdersByChannel($channel, $sdate, $edate, $order_id = 0)
    {
        $stime = date('Y-m-d H:i:s', strtotime($sdate));
        $etime = date('Y-m-d H:i:s', strtotime($edate) + 86400);
        $sql = 'SELECT o.order_id, o.total, payment_method, o.date_added, o.date_modified, ';
        $sql .= 'o.status_id, o.customer_id, en_name_customer, ot.cookies, pd.name, p.type, ';
        $sql .= 'ot.channel, op.tour_date, c.cn_name, cn_name_customer ';
        $sql .= 'FROM ht_order_trace ot ';
        $sql .= 'LEFT JOIN ht_order o ON ot.order_id=o.order_id ';
        $sql .= 'LEFT JOIN ht_order_product op ON o.order_id=op.order_id ';
        $sql .= 'LEFT JOIN ht_product p ON op.product_id=p.product_id ';
        $sql .= 'LEFT JOIN ht_product_description pd ON op.product_id=pd.product_id ';
        $sql .= 'LEFT JOIN ht_order_status os ON o.status_id=os.order_status_id ';
        $sql .= 'LEFT JOIN ht_city c ON p.city_code=c.city_code ';
        $sql .= 'WHERE o.date_added>="'.$stime.'" AND o.date_added<="'.$etime.'" ';
        $sql .= 'AND pd.language_id=2 ';
        if ($channel != 'all') {
            $sql .= 'AND ot.channel="'.$channel.'" ';
        }
        if (!empty($order_id)) {
            $sql .= 'AND o.order_id="'.$order_id.'" ';
        }
        $sql .= 'GROUP BY o.order_id';
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        return $result;
    }

    private function getPricesByOrder($order_id)
    {
        $sql = 'SELECT op.product_id,pd.name,opp.quantity,opp.price,p.type FROM ht_order_product op ';
        $sql .= 'LEFT JOIN ht_order_product_price opp ON op.order_product_id=opp.order_product_id ';
        $sql .= 'LEFT JOIN ht_product_description pd ON op.product_id=pd.product_id ';
        $sql .= 'LEFT JOIN ht_product p ON op.product_id=p.product_id ';
        $sql .= 'WHERE op.order_id="'.$order_id.'" AND pd.language_id=2 AND opp.quantity>0';
        $prices = Yii::app()->db->createCommand($sql)->queryAll();
        return $prices;
    }

    public function getOrderNormal($channel, $sdate, $edate)
    {
        $orders = array();
        if (empty($channel) || empty($sdate) || empty($edate)) {
            return $orders;
        }

        $output_txt = '';
        $result = $this->getOrdersByChannel($channel, $sdate, $edate);
        if (!empty($result)) {
            foreach($result as $row) {
                $order_id = $row['order_id'];
                $status_id = $row['status_id'];
                if (isset($orders[$order_id])) {
                    continue;
                }else{
                    $orders[$order_id] = 1;
                }
                $cookies = (array)json_decode($row['cookies']);
                if (empty($cookies[$this->cookie_name])) {
                    Yii::log('Not found cookie['.$this->cookie_name.'] for order['.$order_id.']status['.$status_id.']', CLogger::LEVEL_WARNING);
                    continue;
                }
                $cps_str = $cookies[$this->cookie_name];
                $cps_vars = $this->validCheck($cps_str);
                if (empty($cps_vars['token'])) {
                    Yii::log('Not found token for get order. order_id['.$order_id.']status_id['.$status_id.']', CLogger::LEVEL_WARNING);
                    continue;
                }
                if ($status_id != 3) {
                    continue;
                }
                $coupon_amount = 0;
                $sql = 'SELECT amount FROM ht_coupon_history WHERE order_id="'.$order_id.'"';
                $res = $prices = Yii::app()->db->createCommand($sql)->queryRow();
                if ($res) {
                    $coupon_amount = $res['amount'];
                }

                $quantity = 0;
                $prices = $this->getPricesByOrder($order_id);
                if (!empty($prices)) {
                    $products = array();
                    foreach($prices as $pkey => $price) {
                        $products[$price['product_id']]['price_total'] += $price['price'] * $price['quantity'];
                        $products[$price['product_id']]['type'] = $price['type'];
                    }
                    uasort($products, array('Cps', 'cmpPrices'));
                    foreach($products as $product_id => $product) {
                        if (empty($product['price_total'])) {
                            continue;
                        }
                        $output_txt .= date('Ymd', strtotime($row['date_added'])) . ",";
                        $output_txt .= $order_id . ",";
                        $output_txt .= $cps_vars['token'] . ",";
                        $output_txt .= $row['total'] . ",";
                        $output_txt .= $coupon_amount . ",";
                        $output_txt .= $product_id . ",";
                        $output_txt .= $product['price_total'] . ",";
                        $price_r = $product['price_total'] + $coupon_amount;
                        if ($price_r <= 0) {
                            $coupon_amount = $price_r;
                            $price_r = 0;
                        }
                        $output_txt .= $this->getCommission($product['type']) . ',';
                        $output_txt .= (empty($this->product_cate[$product['type']]) ? '0' : $this->product_cate[$product['type']]['rate']) . ',';
                        $output_txt .= (empty($this->product_cate[$product['type']]) ? '0' : $price_r * $this->product_cate[$product['type']]['rate']) . ',';
                        $output_txt .= "\n";
                    }
                }
            }
        }
        return $output_txt;
    }

    private function cmpPrices($a, $b)
    {
        if ($a['price_total'] == $b['price_total']) {
            return 0;
        }
        return ($a['price_total'] > $b['price_total']) ? +1 : -1;
    }
}