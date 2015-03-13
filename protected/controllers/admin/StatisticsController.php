<?php

class StatisticsController extends AdminController
{
    public $layout = '//layouts/common';
    protected $query = array();

    public function actionIndex()
    {
        $this->pageTitle = '订单统计';

        $request_urls = array(
            'orderSearch' => $this->createUrl('statistics/orderSearch', array('type' => '')),
            'orderSummary' => $this->createUrl('statistics/orderSummary'),
            'orderListByDate' => $this->createUrl('statistics/orderListByDate', array('type' => '')),
            'orderComplaintByDate' => $this->createUrl('statistics/orderComplaintByDate'),
            'orderReturnByDate' => $this->createUrl('statistics/orderReturnByDate'),
            'getActivityList' => $this->createUrl('statistics/getActivityList'),
            'getActivityOrderList' => $this->createUrl('statistics/getActivityOrderList'),
            'getActivityOrderSummary' => $this->createUrl('statistics/getActivityOrderSummary'),
            'getUserAnalysis' => $this->createUrl('statistics/getUserAnalysis'),
            'getUserAnalysisSummary' => $this->createUrl('statistics/getUserAnalysisSummary'),
            'getFeedback' => $this->createUrl('productAsk/get'),
            'saveFeedback' => $this->createUrl('productAsk/save'),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );
        $this->render('index');
    }

    public function actionOrderSearch()
    {
        global $post_data;
        global $count_prop;

        $type = Yii::app()->request->getParam('type');
        $result = array();
        $post_data = $this->getPostJsonData();
        $connection = Yii::app()->db;

        $this->getSimplifiedOrderQuery($post_data, $type);
        $query = "SELECT ";

        switch($type) {
            case 'country' :
                $query .= "co.cn_name name,";
                break;
            case 'city' :
                $query .= "c.cn_name name,";
                break;
            case 'supplier' :
                $query .= "s.name name,";
                break;
            case 'product' :
                $query .= "pd.name name, p.product_id product_id, c.cn_name city_name,";
                break;
        }

        $query .= " o.order_id, o.total
            {$this->query['from']}
            {$this->query['where']}
            GROUP BY o.order_id
        ";

        $command = $connection->createCommand($query);
        $data_set = $command->queryAll();
        $count_prop = "{$type}_orders";
        $total_prop = "{$type}_amount";

        $aggregated_set = array();

        foreach($data_set as $data) {
            if(isset($aggregated_set[$data['name']])) {
                $aggregated_set[$data['name']][$count_prop]++;
                $aggregated_set[$data['name']][$total_prop] += $data['total'];
            } else {
                $aggregated_set[$data['name']] = ['name' => $data['name'], $count_prop => 1, $total_prop => $data['total']];

                if($type == 'product') {
                    $aggregated_set[$data['name']]['city_name'] = $data['city_name'];
                    $aggregated_set[$data['name']]['product_id'] = $data['product_id'];
                }
            }
        }

        function cmp($a, $b) {
            global $post_data;
            global $count_prop;

            if(isset($post_data['sort'])) {
                foreach($post_data['sort'] as $order_field => $order_dir) {
                    if($a[$order_field] == $b[$order_field]) continue;
                    if($a[$order_field] < $b[$order_field]) {
                        return $order_dir == 1 ? -1 : 1;
                    } else if($a[$order_field] > $b[$order_field]) {
                        return $order_dir == 1 ? 1 : -1;
                    }
                }
            } else {
                if ($a[$count_prop] == $b[$count_prop]) {
                    return 0;
                } else {
                    return ($a[$count_prop] > $b[$count_prop]) ? -1 : 1;
                }
            }
        }

        usort($aggregated_set, "cmp");

        $result['total_count'] = count($aggregated_set);

        $result['data'] = array_values($aggregated_set);
        if(isset($post_data['paging'])) {
            $result['data'] = array_slice($result['data'], $post_data['paging']['start'], $post_data['paging']['limit']);
        }

        EchoUtility::echoMsgTF(true, '获取订单统计', $result);
    }

    public function actionOrderListByDate()
    {
        $post_data = $this->getPostJsonData();
        $connection = Yii::app()->db;

        $this->getSimplifiedOrderQuery($post_data, false);
        $query = "SELECT UNIX_TIMESTAMP( DATE(o.date_modified) ) order_date, date_format(o.date_modified, '%Y-%m-%d') date_added, count(distinct o.order_id) sub_orders, sum(op.total) sub_amount
            {$this->query['from']}
            {$this->query['where']}
            GROUP BY order_date
        ";

        $command = $connection->createCommand($query);
        $results = $command->queryAll();

        $refine_results = array();
        foreach($results as $r) {
            $refine_results[$r['date_added']] = $r;
        }

        $start_date = $post_data['query_filter']['date_start'];
        $stop_date = $post_data['query_filter']['date_end'];

        for($d = $start_date; $d <= $stop_date;) {
            if(!isset($refine_results[$d])) {
                $refine_results[$d]['order_date'] = strtotime($d);
                $refine_results[$d]['date_added'] = $d;
                $refine_results[$d]['sub_orders'] = 0;
                $refine_results[$d]['sub_amount'] = 0;
            }
            $d = date('Y-m-d', strtotime('+1 day', strtotime($d)));
        }

        function cpm($v1, $v2)
        {
            return $v1['order_date'] - $v2['order_date'];
        }

        usort($refine_results, 'cpm');

        EchoUtility::echoMsgTF(true, '获取订单统计', array_values($refine_results));
    }

    public function actionOrderComplaintByDate()
    {
        $data = $this->getPostJsonData();
        $connection = Yii::app()->db;

        $query = "SELECT UNIX_TIMESTAMP( DATE(oc.date_added) ) complaint_date, date_Format(oc.date_added, '%Y-%m-%d') date_added, count(distinct oc.comment_id ) sub_complaint FROM ht_order_comment AS oc ";
        //from
        $query .= "JOIN `ht_order_product` AS op
                   ON op.order_id = oc.order_id ";

        if(!empty($data['query_filter'])) {
            $query .= "JOIN `ht_product` AS p
                       ON op.product_id = p.product_id ";
        }
        $date_type = !empty($data['query_filter']['date_type']) ? 'o.' . $data['query_filter']['date_type'] : 'oc.date_added';
        if(!empty($data['query_filter']['date_start'])) {
            $query .= " AND {$date_type} >= '{$data['query_filter']['date_start']} 00:00:00'";
        }
        if(!empty($data['query_filter']['date_end'])) {
            $query .= " AND {$date_type} <= '{$data['query_filter']['date_end']} 23:59:59'";
        }
        if(!empty($data['query_filter']['product_id'])) {
            $query .= "JOIN ht_product_description AS pd
                       ON p.product_id = pd.product_id AND pd.language_id = 2
                       JOIN `ht_city` AS c
                       ON p.city_code = c.city_code ";
        } else {
            if(!empty($data['query_filter']['country_code']) || !empty($data['query_filter']['city_code'])) {
                $query .= "JOIN `ht_city` AS c
                           ON p.city_code = c.city_code ";
            }
            if(!empty($data['query_filter']['country_code'])) {
                $query .= "JOIN `ht_country` AS co
                           ON c.country_code = co.country_code ";
            }
            if(!empty($data['query_filter']['supplier_id'])) {
                $query .= "JOIN `ht_supplier` AS s
                           ON p.supplier_id = s.supplier_id ";
            }
        }
        //where
        if(!empty($data['query_filter']['product_id'])) {
            $query .= " AND (pd.name LIKE '%" . $data['query_filter']['product_id'] . "%'";
            $query .= " OR pd.product_id = '" . $data['query_filter']['product_id'] . "')";
        } else {
            if(!empty($data['query_filter']['country_code'])) {
                $query .= " AND c.country_code = '" . $data['query_filter']['country_code'] . "'";
            }
            if(!empty($data['query_filter']['city_code'])) {
                $query .= " AND c.city_code = '" . $data['query_filter']['city_code'] . "'";
            }
            if(!empty($data['query_filter']['supplier_id'])) {
                $query .= " AND p.supplier_id = " . $data['query_filter']['supplier_id'];
            }
        }
        if(!empty($data['query_filter']['product_type_id'])) {
            $query .= " AND p.type = " . $data['query_filter']['product_type_id'];
        }
        $query .= " WHERE oc.type = 7 GROUP BY complaint_date";

        $command = $connection->createCommand($query);
        $results = $command->queryAll();

        $refine_results = array();
        foreach($results as $r) {
            $refine_results[$r['date_added']] = $r;
        }

        $start_date = $data['query_filter']['date_start'];
        $stop_date = $data['query_filter']['date_end'];

        for($d = $start_date; $d <= $stop_date;) {
            if(!isset($refine_results[$d])) {
                $refine_results[$d]['complaint_date'] = strtotime($d);
                $refine_results[$d]['date_added'] = $d;
                $refine_results[$d]['sub_complaint'] = 0;
            }
            $d = date('Y-m-d', strtotime('+1 day', strtotime($d)));
        }

        function cpm($v1, $v2)
        {
            return $v1['complaint_date'] - $v2['complaint_date'];
        }

        usort($refine_results, 'cpm');

        EchoUtility::echoMsgTF(true, '获取投诉统计', array_values($refine_results));
    }

    public function actionOrderReturnByDate()
    {
        $data = $this->getPostJsonData();
        $connection = Yii::app()->db;

        $query = "SELECT UNIX_TIMESTAMP( DATE(o.date_modified) ) return_date, date_Format(o.date_modified, '%Y-%m-%d') date_return, count(distinct o.order_id ) sub_return FROM ht_order AS o ";

        //from
        $query .= "JOIN `ht_order_product` AS op
                   ON op.order_id = o.order_id ";

        if(!empty($data['query_filter'])) {
            $query .= "JOIN `ht_product` AS p
                       ON op.product_id = p.product_id ";
        }
        $date_type = !empty($data['query_filter']['date_type']) ? 'o.' . $data['query_filter']['date_type'] : 'o.date_added';
        if(!empty($data['query_filter']['date_start'])) {
            $query .= " AND {$date_type} >= '{$data['query_filter']['date_start']} 00:00:00'";
        }
        if(!empty($data['query_filter']['date_end'])) {
            $query .= " AND {$date_type} <= '{$data['query_filter']['date_end']} 23:59:59'";
        }
        if(!empty($data['query_filter']['product_id'])) {
            $query .= "JOIN ht_product_description AS pd
                       ON p.product_id = pd.product_id AND pd.language_id = 2
                       JOIN `ht_city` AS c
                       ON p.city_code = c.city_code ";
        } else {
            if(!empty($data['query_filter']['country_code']) || !empty($data['query_filter']['city_code'])) {
                $query .= "JOIN `ht_city` AS c
                           ON p.city_code = c.city_code ";
            }
            if(!empty($data['query_filter']['country_code'])) {
                $query .= "JOIN `ht_country` AS co
                           ON c.country_code = co.country_code ";
            }
            if(!empty($data['query_filter']['supplier_id'])) {
                $query .= "JOIN `ht_supplier` AS s
                           ON p.supplier_id = s.supplier_id ";
            }
        }
        //where
        if(!empty($data['query_filter']['product_id'])) {
            $query .= " AND (pd.name LIKE '%" . $data['query_filter']['product_id'] . "%'";
            $query .= " OR pd.product_id = '" . $data['query_filter']['product_id'] . "')";
        } else {
            if(!empty($data['query_filter']['country_code'])) {
                $query .= " AND c.country_code = '" . $data['query_filter']['country_code'] . "'";
            }
            if(!empty($data['query_filter']['city_code'])) {
                $query .= " AND c.city_code = '" . $data['query_filter']['city_code'] . "'";
            }
            if(!empty($data['query_filter']['supplier_id'])) {
                $query .= " AND p.supplier_id = " . $data['query_filter']['supplier_id'];
            }
        }
        if(!empty($data['query_filter']['product_type_id'])) {
            $query .= " AND p.type = " . $data['query_filter']['product_type_id'];
        }
        $query .= " WHERE o.status_id = 11 GROUP BY return_date";

        $command = $connection->createCommand($query);
        $results = $command->queryAll();

        $refine_results = array();
        foreach($results as $r) {
            $refine_results[$r['date_return']] = $r;
        }

        $start_date = $data['query_filter']['date_start'];
        $stop_date = $data['query_filter']['date_end'];

        for($d = $start_date; $d <= $stop_date;) {
            if(!isset($refine_results[$d])) {
                $refine_results[$d]['return_date'] = strtotime($d);
                $refine_results[$d]['date_return'] = $d;
                $refine_results[$d]['sub_return'] = 0;
            }
            $d = date('Y-m-d', strtotime('+1 day', strtotime($d)));
        }

        function cpm($v1, $v2)
        {
            return $v1['return_date'] - $v2['return_date'];
        }

        usort($refine_results, 'cpm');

        EchoUtility::echoMsgTF(true, '获取退订统计', array_values($refine_results));
    }

    public function actionOrderSummary()
    {
        $post_data = $this->getPostJsonData();
        $this->getSimplifiedOrderQuery($post_data);
        $connection = Yii::app()->db;

        $query = "SELECT o.total total_amount
            {$this->query['from']}
            {$this->query['where']}
            GROUP BY o.order_id
        ";
        $command = $connection->createCommand($query);

        $query_result = $command->queryAll();
        $result = array(
            'total_orders' => 0,
            'total_amount' => 0
        );
        for($i = 0, $len = count($query_result); $i < $len; $i++) {
            $result['total_orders'] ++;
            $result['total_amount'] += $query_result[$i]['total_amount'];
        }

        EchoUtility::echoMsgTF(!empty($result), '获取订单总计', $result);
    }

    private function getSimplifiedOrderQuery($data, $type = false) {
        $this->getBasicOrderQuery($data);

        $this->query['from'] = "
            FROM `ht_order` AS o
            JOIN `ht_customer` AS u
            ON o.customer_id = u.customer_id
            JOIN `ht_order_product` AS op
            ON o.order_id = op.order_id
        ";

        if(!empty($data['query_filter']) || $type) {
            $this->query['from'] .= "
            JOIN `ht_product` AS p
            ON op.product_id = p.product_id
            ";
        }
        if(!empty($data['query_filter']['product_id']) || $type == 'product') {
            $this->query['from'] .= "
            JOIN ht_product_description AS pd
            ON p.product_id = pd.product_id AND pd.language_id = 2
            JOIN `ht_city` AS c
            ON p.city_code = c.city_code
            ";
        } else {
            if(!empty($data['query_filter']['country_code']) || $type == 'country' || !empty($data['query_filter']['city_code']) || $type == 'city') {
                $this->query['from'] .= "
                    JOIN `ht_city` AS c
                    ON p.city_code = c.city_code
                ";
            }
            if(!empty($data['query_filter']['country_code']) || $type == 'country') {
                $this->query['from'] .= "
                    JOIN `ht_country` AS co
                    ON c.country_code = co.country_code
                ";
            }
            if(!empty($data['query_filter']['supplier_id']) || $type == 'supplier') {
                $this->query['from'] .= "
                    JOIN `ht_supplier` AS s
                    ON p.supplier_id = s.supplier_id
                ";
            }
        }
    }

    private function getBasicOrderQuery($data, $status_type = 1)
    {
        $this->query['from'] = "
            FROM `ht_order` AS o
            JOIN `ht_customer` AS u
            ON o.customer_id = u.customer_id
            JOIN `ht_order_product` AS op
            ON o.order_id = op.order_id
            JOIN `ht_product` AS p
            ON op.product_id = p.product_id
            JOIN `ht_city` AS c
            ON p.city_code = c.city_code
            JOIN `ht_supplier` AS s
            ON p.supplier_id = s.supplier_id
            JOIN ht_product_description AS pd
            ON p.product_id = pd.product_id AND pd.language_id = 2
        ";

        //WHERE
        $grid_condition = '';
        if($status_type == 1) {
            $this->query['where'] = " WHERE o.status_id IN (21,4,2,3,5,17,6,23,8,10) AND";
        } else if($status_type == 2) {
            $this->query['where'] = " WHERE o.status_id IN (1,22,7,25,26,9,11) AND";
        } else {
            $this->query['where'] = " WHERE";
        }
        $this->query['where'] .= " u.email NOT LIKE '%hitour.cc%' ";

        $date_type = !empty($data['query_filter']['date_type']) ? 'o.' . $data['query_filter']['date_type'] : 'o.date_added';
        if(!empty($data['query_filter']['date_start'])) {
            $grid_condition .= " AND {$date_type} >= '{$data['query_filter']['date_start']} 00:00:00'";
        }
        if(!empty($data['query_filter']['date_end'])) {
            $grid_condition .= " AND {$date_type} <= '{$data['query_filter']['date_end']} 23:59:59'";
        }
        if(!empty($data['query_filter']['product_type_id'])) {
            $grid_condition .= " AND p.type = " . $data['query_filter']['product_type_id'];
        }
        if(!empty($data['query_filter']['activity_id'])) {
            $grid_condition .= " AND o.activity_id = " . $data['query_filter']['activity_id'];
        }

        if(!empty($data['query_filter']['product_id'])) {
            $grid_condition .= " AND (pd.name LIKE '%" . $data['query_filter']['product_id'] . "%'";
            $grid_condition .= " OR pd.product_id = '" . $data['query_filter']['product_id'] . "')";
        } else {
            if(!empty($data['query_filter']['country_code'])) {
                $grid_condition .= " AND c.country_code = '" . $data['query_filter']['country_code'] . "'";
            }
            if(!empty($data['query_filter']['city_code'])) {
                $grid_condition .= " AND c.city_code = '" . $data['query_filter']['city_code'] . "'";
            }
            if(!empty($data['query_filter']['supplier_id'])) {
                $grid_condition .= " AND p.supplier_id = " . $data['query_filter']['supplier_id'];
            }
        }

        $this->query['where'] .= $grid_condition;

        //PAGING
        if(isset($data['paging'])) {
            $this->query['limit'] = " LIMIT " . $data['paging']['start'] . ', ' . $data['paging']['limit'];
        }

        //SORTING
        if(isset($data['sort'])) {
            $this->query['order'] = '';
            foreach($data['sort'] as $order_field => $order_dir) {
                $this->query['order'] .= ', ' . $order_field . ' ' . ($order_dir == 1 ? 'ASC' : 'DESC');
            }
            $this->query['order'] = " ORDER BY " . substr($this->query['order'], 2);
        }
    }

    //取活动列表（以后有活动管理时迁移走）
    public function actionGetActivityList()
    {
        $activities = HtActivity::model()->findAll();
        EchoUtility::echoMsgTF(true, '获取活动列表', Converter::convertModelToArray($activities));
    }

    //获取活动订单统计
    public function actionGetActivityOrderList()
    {
        $data = $this->getPostJsonData();
        //取最新活动
        if(empty($data['query_filter']['activity_id'])) {
            $c = new CDbCriteria();
            $c->order = 'start_date DESC';
            $activities = HtActivity::model()->findAll($c);
            if($activities) {
                $data['query_filter']['activity_id'] = $activities[0]['activity_id'];
                $data['query_filter']['date_start'] = $activities[0]['start_date'];
                $data['query_filter']['date_end'] = $activities[0]['end_date'];
            } else {
                EchoUtility::echoMsgTF(false, '获取最新活动失败');
            }
        }
        $this->getActivityOrderQuery($data);

        $connection = Yii::app()->db;
        $sql_head = 'select t.*,t.failed_orders/(t.product_orders+t.failed_orders) as problem_order_rate ';
        $data_sql = $sql_head . $this->query['where'] . $this->query['order'] . $this->query['limit'];
        $command = $connection->createCommand($data_sql);
        $base_data = $command->queryAll();
        $result['data'] = $base_data;
        $sql_head = 'select count(1) ';
        $command = $connection->createCommand($sql_head . $this->query['where']);
        $result['total_count'] = $command->queryColumn();
        EchoUtility::echoMsgTF(true, '获取活动订单统计', $result);
    }

    //获取活动订单总计
    public function actionGetActivityOrderSummary()
    {
        $data = $this->getPostJsonData();
        //取最新活动
        if(empty($data['query_filter']['activity_id'])) {
            $c = new CDbCriteria();
            $c->order = 'start_date DESC';
            $activities = HtActivity::model()->findAll($c);
            if($activities) {
                $data['query_filter']['activity_id'] = $activities[0]['activity_id'];
            } else {
                EchoUtility::echoMsgTF(false, '获取最新活动失败');
            }
        }

        $this->getActivityOrderQuery($data);

        $connection = Yii::app()->db;
        $sql_head = 'select sum(t.product_orders) as total_success_orders,
                     sum(t.product_amount) as total_success_amount,
                     sum(t.failed_orders)/(sum(t.product_orders)+sum(t.failed_orders)) as problem_order_rate,
                     sum(t.failed_orders) as problem_order_counts,
                     sum(t.product_orders)+sum(t.failed_orders) as total_orders ';
        $data_sql = $sql_head . $this->query['where'] . $this->query['order'] . $this->query['limit'];
        $command = $connection->createCommand($data_sql);
        $total_data = $command->queryRow();

        EchoUtility::echoMsgTF($total_data, '获取订单总计', $total_data);
    }

    private function getActivityOrderQuery($data)
    {
        $sql_body = '';
        $sql_body .= "from (
                    select
                        op.product_id,
                        pd.name,
                        sum(if(o.status_id in (21 , 4, 2, 3, 5, 17, 6, 23, 8, 10), 1, 0)) product_orders,
                        sum(if(o.status_id in (1,22,7,25,26,9,11), 1, 0)) failed_orders,
                        sum(if(o.status_id in (21 , 4, 2, 3, 5, 17, 6, 23, 8, 10), op.total, 0)) product_amount,
                        sum(if(o.status_id in (1,22,7,25,26,9,11), op.total, 0)) failed_amount
                    from
                        ht_order_product op
                    left join ht_order o ON op.order_id = o.order_id
                    left join ht_product_description pd ON op.product_id = pd.product_id
                        and pd.language_id = 2 WHERE 1=1 ";

        if(!empty($data['query_filter']['date_start'])) {
            $sql_body .= " AND o.date_modified >= '" . $data['query_filter']['date_start'] . " 00:00:00'";
        }
        if(!empty($data['query_filter']['date_end'])) {
            $sql_body .= " AND o.date_modified <= '" . $data['query_filter']['date_end'] . " 23:59:59'";
        };
        if(!empty($data['query_filter']['activity_id'])) {
            $sql_body .= " AND o.activity_id = " . $data['query_filter']['activity_id'];
        }
        if(!empty($data['query_filter']['product_id'])) {
            $sql_body .= " AND (pd.name LIKE '%" . $data['query_filter']['product_id'] . "%' ";
            $sql_body .= "or pd.product_id = '" . $data['query_filter']['product_id'] . "')";
        }
        $sql_body .= ' group by op.product_id) as t ';
        $this->query['where'] = $sql_body;

        //PAGING
        if(isset($data['paging'])) {
            $this->query['limit'] = " LIMIT " . $data['paging']['start'] . ', ' . $data['paging']['limit'];
        }

        //SORTING
        if(isset($data['sort'])) {
            $this->query['order'] = '';
            foreach($data['sort'] as $order_field => $order_dir) {
                $this->query['order'] .= ', ' . $order_field . ' ' . ($order_dir == 1 ? 'ASC' : 'DESC');
            }
            $this->query['order'] = " ORDER BY " . substr($this->query['order'], 2);
        }
    }

    //按指标用户分析
    public function actionGetUserAnalysis()
    {
        $data = $this->getPostJsonData();
        $target = $data['query_filter']['target'];
        switch($target) {
            case 1:
                $function_name = 'getNewRegisterUserCount';
                break;
            case 2:
                $function_name = 'getNewOrderUserCount';
                break;
            case 3:
                $function_name = 'getValidOrderUserCount';
                break;
            case 4:
                $function_name = 'getFirstValidOrderUserCount';
                break;
            case 5:
                $function_name = 'getReturningUserCount';
                break;
            case 6:
                $function_name = 'getFirstValidOrderUserRate';
                break;
            case 7:
                $function_name = 'getFailedOrderUserRate';
                break;
            case 8:
                $function_name = 'getUserAvgValidAmount';
                break;
            default:
                $function_name = 'getNewRegisterUserCount';
        }
        $query = $data['query_filter'];
        $base_data = $this->$function_name($query, 1);
        $compare_data = [];
        if($query['compare_from_date'] || $query['compare_to_date']) {
            $query['from_date'] = $query['compare_from_date'];
            $query['to_date'] = $query['compare_to_date'];
            $compare_data = $this->$function_name($query, 1);
        }
        if(empty($compare_data)) {
            $return = array('base_data' => $base_data);
        } else {
            $return = array('base_data' => $base_data, 'compare_data' => $compare_data);
        }
        EchoUtility::echoMsgTF(true, '获取数据', $return);
    }

    //用户分析总计
    public function actionGetUserAnalysisSummary()
    {
        $data = $this->getPostJsonData();
        $query = $data['query_filter'];
        $return = array();
        $target_function = array('1' => 'getNewRegisterUserCount', '2' => 'getNewOrderUserCount', '3' => 'getValidOrderUserCount', '4' => 'getFirstValidOrderUserCount',
            '5' => 'getReturningUserCount', '6' => 'getFirstValidOrderUserRate', '7' => 'getFailedOrderUserRate', '8' => 'getUserAvgValidAmount',);
        foreach($target_function as $k => $v) {
            $base_data = $this->$v($query, 2);
            $compare_data = [];
            if($query['compare_from_date'] || $query['compare_to_date']) {
                $compare_query = [];
                $compare_query['from_date'] = $query['compare_from_date'];
                $compare_query['to_date'] = $query['compare_to_date'];
                $compare_data = $this->$v($compare_query, 2);
            }
            if(empty($compare_data)) {
                $result = array('base_data' => $base_data['group_value']);
            } else {
                if(empty($compare_data['group_value']) || $compare_data['group_value'] == '0.00') {
                    $trend = false;
                } else {
                    $trend = ($base_data['group_value'] - $compare_data['group_value']) / $compare_data['group_value'];
                }
                $result = array('base_data' => $base_data['group_value'], 'compare_data' => $compare_data['group_value'], 'trend' => $trend);
            }
            $return[$k] = $result;
        }
        EchoUtility::echoMsgTF(true, '获取分析总计', $return);
    }

    //新注册用户数量
    private function getNewRegisterUserCount($query, $type)
    {
        $sql = '';
        $sql .= "select date_format(date_added, '%Y-%m-%d') as group_date,count(customer_id) as group_value from ht_customer o where 1=1 ";

        $from_date = $query['from_date'];
        $to_date = $query['to_date'];
        if($from_date) {
            $sql .= " and o.date_added>='$from_date'";
        }
        if($to_date) {
            $to_date = date("Y-m-d", strtotime($to_date) + 86400);
            $sql .= " and o.date_added<='$to_date'";
        }
        if($type == 1) {
            $sql .= " group by group_date";
        }

        return $this->procUserAnalysisSql($sql, $query, $type);
    }

    //生成订单用户数量
    private function getNewOrderUserCount($query, $type)
    {
        $sql = '';
        $sql .= "select date_format(date_added, '%Y-%m-%d') as group_date,count(distinct customer_id) as group_value from ht_order o where 1=1 ";
        $from_date = $query['from_date'];
        $to_date = $query['to_date'];
        if($from_date) {
            $sql .= " and o.date_added>='$from_date'";
        }
        if($to_date) {
            $to_date = date("Y-m-d", strtotime($to_date) + 86400);
            $sql .= " and o.date_added<='$to_date'";
        }
        if($type == 1) {
            $sql .= " group by group_date";
        }

        return $this->procUserAnalysisSql($sql, $query, $type);
    }

    //成交订单用户数量
    private function getValidOrderUserCount($query, $type)
    {
        $sql = '';
        $sql .= "select date_format(date_added, '%Y-%m-%d') as group_date,count(distinct customer_id) as group_value from ht_order o ";
        $sql .= "where status_id in (21 , 4, 2, 3, 5, 17, 6, 23, 8, 10) ";
        $from_date = $query['from_date'];
        $to_date = $query['to_date'];
        if($from_date) {
            $sql .= "and o.date_added >= '$from_date'";
        }
        if($to_date) {
            $to_date = date("Y-m-d", strtotime($to_date) + 86400);
            $sql .= "and o.date_added <= '$to_date'";
        }
        if($type == 1) {
            $sql .= " group by group_date";
        }

        return $this->procUserAnalysisSql($sql, $query, $type);
    }

    //首次成交用户数量
    private function getFirstValidOrderUserCount($query, $type)
    {
        $from_date = $query['from_date'];
        $to_date = $query['to_date'];
        $sql = '';
        $sql .= "select t.od as group_date,(count(ocid) - count(o2cid)) as group_value from ";
        $sql .= "(SELECT o.order_id oid,date_format(o.date_added,'%Y-%m-%d') od,o.customer_id ocid,o2.order_id o2id,date_format(o2.date_added,'%Y-%m-%d') o2d,o2.customer_id o2cid FROM `ht_order` o ";
        $sql .= "left join ht_order o2 on o.customer_id = o2.customer_id and o.order_id > o2.order_id and date_format(o2.date_added,'%Y-%m-%d')<date_format(o.date_added,'%Y-%m-%d') ";
        $sql .= "and o2.status_id in (21 , 4, 2, 3, 5, 17, 6, 23, 8, 10) where o.status_id in (21 , 4, 2, 3, 5, 17, 6, 23, 8, 10)   ";
        if($from_date) {
            $sql .= "and o.date_added >= '$from_date'";
        }
        if($to_date) {
            $to_date = date("Y-m-d", strtotime($to_date) + 86400);
            $sql .= "and o.date_added <= '$to_date'";
        }
        $sql .= "group by o.customer_id) t ";
        if($type == 1) {
            $sql .= "group by t.od";
        }

        return $this->procUserAnalysisSql($sql, $query, $type);
    }

    //回头客数量
    private function getReturningUserCount($query, $type)
    {
        $from_date = $query['from_date'];
        $to_date = $query['to_date'];
        $sql = '';
        $sql .= "select t.od as group_date,count(o2cid) as group_value from ";
        $sql .= "(SELECT o.order_id oid,date_format(o.date_added,'%Y-%m-%d') od,o.customer_id ocid,o2.order_id o2id,date_format(o2.date_added,'%Y-%m-%d') o2d,o2.customer_id o2cid FROM `ht_order` o ";
        $sql .= "left join ht_order o2 on o.customer_id = o2.customer_id and o.order_id > o2.order_id and date_format(o2.date_added,'%Y-%m-%d')<date_format(o.date_added,'%Y-%m-%d') ";
        $sql .= "and o2.status_id in (21 , 4, 2, 3, 5, 17, 6, 23, 8, 10) where o.status_id in (21 , 4, 2, 3, 5, 17, 6, 23, 8, 10)   ";
        if($from_date) {
            $sql .= "and o.date_added >= '$from_date'";
        }
        if($to_date) {
            $to_date = date("Y-m-d", strtotime($to_date) + 86400);
            $sql .= "and o.date_added <= '$to_date'";
        }
        $sql .= "group by o.customer_id) t ";
        if($type == 1) {
            $sql .= "group by t.od";
        }

        return $this->procUserAnalysisSql($sql, $query, $type);
    }

    //首次成交用户占比
    private function getFirstValidOrderUserRate($query, $type)
    {
        $from_date = $query['from_date'];
        $to_date = $query['to_date'];
        $sql = '';
        $sql .= "select t.od as group_date,ifnull(round((count(ocid) - count(o2cid))/count(ocid),2),0) as group_value from ";
        $sql .= "(SELECT o.order_id oid,date_format(o.date_added,'%Y-%m-%d') od,o.customer_id ocid,o2.order_id o2id,date_format(o2.date_added,'%Y-%m-%d') o2d,o2.customer_id o2cid FROM `ht_order` o ";
        $sql .= "left join ht_order o2 on o.customer_id = o2.customer_id and o.order_id > o2.order_id and date_format(o2.date_added,'%Y-%m-%d')<date_format(o.date_added,'%Y-%m-%d') ";
        $sql .= "and o2.status_id in (21 , 4, 2, 3, 5, 17, 6, 23, 8, 10) where o.status_id in (21 , 4, 2, 3, 5, 17, 6, 23, 8, 10)   ";
        if($from_date) {
            $sql .= "and o.date_added >= '$from_date'";
        }
        if($to_date) {
            $to_date = date("Y-m-d", strtotime($to_date) + 86400);
            $sql .= "and o.date_added <= '$to_date'";
        }
        $sql .= "group by o.customer_id) t ";
        if($type == 1) {
            $sql .= "group by t.od";
        }

        return $this->procUserAnalysisSql($sql, $query, $type);
    }

    //订单失败用户占比
    private function getFailedOrderUserRate($query, $type)
    {
        $sql = '';
        $sql .= "select date_format(date_added, '%Y-%m-%d') as group_date, ";
        $sql .= "ifnull(round(count(distinct if(status_id in(1,22,7,25,26,9,11),customer_id,null))/count(distinct customer_id),2),0) as group_value from ht_order o ";
        $sql .= "where 1=1 ";
        $from_date = $query['from_date'];
        $to_date = $query['to_date'];
        if($from_date) {
            $sql .= " and o.date_added>='$from_date'";
        }
        if($to_date) {
            $to_date = date("Y-m-d", strtotime($to_date) + 86400);
            $sql .= " and o.date_added<='$to_date'";
        }
        if($type == 1) {
            $sql .= " group by group_date";
        }

        return $this->procUserAnalysisSql($sql, $query, $type);
    }

    //用户平均成交金额
    private function getUserAvgValidAmount($query, $type)
    {
        $sql = '';
        $sql .= "select date_format(o.date_added, '%Y-%m-%d') as group_date,ifnull(round(sum(op.total)/count(distinct o.customer_id),2),0) as group_value ";
        $sql .= "from ht_order o left join ht_order_product op on o.order_id = op.order_id ";
        $sql .= "where status_id in (21 , 4, 2, 3, 5, 17, 6, 23, 8, 10) ";
        $from_date = $query['from_date'];
        $to_date = $query['to_date'];
        if($from_date) {
            $sql .= " and o.date_added>='$from_date'";
        }
        if($to_date) {
            $to_date = date("Y-m-d", strtotime($to_date) + 86400);
            $sql .= " and o.date_added<='$to_date'";
        }
        if($type == 1) {
            $sql .= " group by group_date";
        }

        return $this->procUserAnalysisSql($sql, $query, $type);
    }

    //sql统一处理
    private function procUserAnalysisSql($sql, $query, $type = 1)
    {
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        if($type == 1) {
            $return = $command->queryAll();
            $refine_results = array();
            if(is_array($return) && count($return) > 0) {
                foreach($return as $r) {
                    $refine_results[$r['group_date']] = $r;
                }
            }
            for($d = $query['from_date']; $d <= $query['to_date'];) {
                if(!isset($refine_results[$d])) {
                    $refine_results[$d]['group_date'] = strtotime($d);
                    $refine_results[$d]['group_value'] = 0;
                } else {
                    $refine_results[$d]['group_date'] = strtotime($d);
                }
                $d = date('Y-m-d', strtotime('+1 day', strtotime($d)));
            }
            ksort($refine_results);

            return $refine_results;
        } else {
            $return = $command->queryRow();

            return $return;
        }

    }
}