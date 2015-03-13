<?php

/**
 * This is the model class for table "ht_order".
 *
 * The followings are the available columns in table 'ht_order':
 * @property integer $order_id
 * @property integer $customer_id
 * @property integer $activity_id
 * @property integer $status_id
 * @property integer $total
 * @property integer $sub_total
 * @property integer $cost_total
 * @property string $contacts_name
 * @property string $contacts_address
 * @property string $contacts_telephone
 * @property string $contacts_email
 * @property string $contacts_passport
 * @property string $payment_method
 * @property string $ip
 * @property string $user_agent
 * @property string $accept_language
 * @property string $payment_time_limit
 * @property string $extract_code
 * @property string $date_added
 * @property string $date_modified
 */
class HtOrder extends HActiveRecord
{
    public $detail_url;
    public $payment_url;
    public $return_url;
    public $cancel_url;
    public $download_voucher_url;
    public $send_voucher_url;
    public $status_shortname;

    public $voucher_path;
    public $voucher_base_url;

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtOrder the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_order';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('customer_id, activity_id, status_id, total, sub_total, cost_total, contacts_name, contacts_address, contacts_telephone, contacts_email, contacts_passport, payment_method, ip, user_agent, accept_language, date_added, date_modified', 'required'),
            array('customer_id, activity_id, status_id, total, sub_total, cost_total,', 'numerical', 'integerOnly' => true),
            array('contacts_name, contacts_telephone, contacts_email, contacts_passport', 'length', 'max' => 32),
            array('contacts_address', 'length', 'max' => 128),
            array('payment_method', 'length', 'max' => 16),
            array('ip', 'length', 'max' => 40),
            array('user_agent, accept_language', 'length', 'max' => 255),
            array('extract_code', 'length', 'max' => 4),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('order_id, customer_id, activity_id, status_id, total, contacts_name, contacts_address, contacts_telephone, contacts_email, contacts_passport, payment_method, ip, user_agent, accept_language, date_added, date_modified', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'order_product'   => array(self::HAS_ONE, 'HtOrderProduct', 'order_id'),
            'order_products'  => array(self::HAS_MANY, 'HtOrderProduct', 'order_id'),
            'comments'        => array(self::HAS_MANY, 'HtOrderComment', 'order_id'),
            'payment_history' => array(self::HAS_ONE, 'HtPaymentHistory', '', 'on' => 'o.order_id = ph.order_id AND ph.pay_or_refund = 1'),
            'order_history'   => array(self::HAS_MANY, 'HtOrderHistory', 'order_id'),
            'status'          => array(self::HAS_ONE, 'HtOrderStatus', '', 'on' => 'o.status_id=status.order_status_id'),
            'passengers'      => array(self::HAS_MANY, 'HtOrderPassenger', 'order_id'),
            'insurance_codes' => array(self::HAS_MANY, 'HtInsuranceCode', 'order_id'),
            'activity'        => array(self::BELONGS_TO, 'HtActivity', 'activity_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'order_id'           => 'Order',
            'customer_id'        => 'Customer',
            'activity_id'        => 'Activity',
            'status_id'          => 'Status',
            'total'              => 'Total',
            'sub_total'          => 'Sub Total',
            'cost_total'         => 'Cost Total',
            'contacts_name'      => 'Contacts Name',
            'contacts_address'   => 'Contacts Address',
            'contacts_telephone' => 'Contacts Telephone',
            'contacts_email'     => 'Contacts Email',
            'contacts_passport'  => 'Contacts Passport',
            'payment_method'     => 'Payment Method',
            'ip'                 => 'Ip',
            'user_agent'         => 'User Agent',
            'accept_language'    => 'Accept Language',
            'payment_time_limit' => 'Payment Time Limit',
            'extract_code'       => 'Extract Code',
            'date_added'         => 'Date Added',
            'date_modified'      => 'Date Modified',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('order_id', $this->order_id);
        $criteria->compare('customer_id', $this->customer_id);
        $criteria->compare('activity_id', $this->activity_id);
        $criteria->compare('status_id', $this->status_id);
        $criteria->compare('total', $this->total);
        $criteria->compare('sub_total', $this->sub_total);
        $criteria->compare('cost_total', $this->cost_total);
        $criteria->compare('contacts_name', $this->contacts_name, true);
        $criteria->compare('contacts_address', $this->contacts_address, true);
        $criteria->compare('contacts_telephone', $this->contacts_telephone, true);
        $criteria->compare('contacts_email', $this->contacts_email, true);
        $criteria->compare('contacts_passport', $this->contacts_passport, true);
        $criteria->compare('payment_method', $this->payment_method, true);
        $criteria->compare('ip', $this->ip, true);
        $criteria->compare('user_agent', $this->user_agent, true);
        $criteria->compare('accept_language', $this->accept_language, true);
        $criteria->compare('extract_code', $this->extract_code, true);
        $criteria->compare('date_added', $this->date_added, true);
        $criteria->compare('date_modified', $this->date_modified, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'o',
            'order' => 'o.order_id DESC');
    }

    public function afterFind()
    {
        $this->detail_url = Yii::app()->request->getHostInfo() . Yii::app()->urlManager->createUrl('account/orderDetail',
                                                                                                   ['order_id' => $this->order_id]);
        if (in_array($this->status_id, [HtOrderStatus::ORDER_SHIPPED, HtOrderStatus::ORDER_OUTOF_REFUND])) {
            $this->download_voucher_url = Yii::app()->urlManager->createUrl('account/downloadVoucher',
                                                                            ['order_id' => $this->order_id]);
            $this->send_voucher_url = Yii::app()->urlManager->createUrl('account/sendVoucher',
                                                                        ['order_id' => $this->order_id]);
        }

        if (in_array($this->status_id, [HtOrderStatus::ORDER_CONFIRMED, HtOrderStatus::ORDER_PAYMENT_FAILED])) {
            $this->payment_url = Yii::app()->urlManager->createUrl('PayGate/Pay', ['order_id' => $this->order_id]);
        }

        if (in_array($this->status_id,
                     [HtOrderStatus::ORDER_CONFIRMED, HtOrderStatus::ORDER_PAYMENT_FAILED, HtOrderStatus::ORDER_NOTPAY_EXPIRED])
        ) {
            $this->cancel_url = Yii::app()->urlManager->createUrl('account/cancelOrder',
                                                                  ['order_id' => $this->order_id]);
        }

        if (in_array($this->status_id,
                     [HtOrderStatus::ORDER_PAYMENT_SUCCESS, HtOrderStatus::ORDER_PAID_EXPIRED, HtOrderStatus::ORDER_WAIT_CONFIRMATION,
                         HtOrderStatus::ORDER_TO_DELIVERY, HtORderStatus::ORDER_STOCK_FAILED, HtOrderStatus::ORDER_BOOKING_FAILED, HtOrderStatus::ORDER_SHIPPED, HtOrderStatus::ORDER_SHIPPING_FAILED])
        ) {
//            if ($this->status_id == HtOrderStatus::ORDER_SHIPPED) {
//                $order_product = HtOrderProduct::model()->findByAttributes(['order_id' => $this->order_id]);
//                if ($order_product && $order_product['return_expire_date'] >= date('Y-m-d')) {
//                    $this->return_url = Yii::app()->urlManager->createUrl('account/returnOrder', ['order_id' => $this->order_id]);
//                }
//            } else {
//                //未发货订单也检查发货规则，对于“不能退订”的商品，从下单付款后就不能退订了
//                $order_product = HtOrderProduct::model()->findByAttributes(['order_id' => $this->order_id]);
//                if (isset($order_product['product_id'])) {
//                    $return_rule = HtProductReturnRule::model()->findByPk($order_product['product_id']);
//                    if ($return_rule['return_type'] != HtProductReturnRule::DONT_RETURN){
//                        $this->return_url = Yii::app()->urlManager->createUrl('account/returnOrder', ['order_id' => $this->order_id]);
//                    }
//                }
//            }
            $order_product = HtOrderProduct::model()->findByAttributes(['order_id' => $this->order_id]);
            if ($order_product && $order_product['return_expire_date'] >= date('Y-m-d')) {
                $this->return_url = Yii::app()->urlManager->createUrl('account/returnOrder',
                                                                      ['order_id' => $this->order_id]);
            }
        }

        $voucher_root = dirname(Yii::app()->BasePath) . Yii::app()->params['VOUCHER_PATH'];
        $date = date('Ymd', strtotime($this->date_added));
        $this->voucher_path = $voucher_root . $date . DIRECTORY_SEPARATOR . $this->order_id . DIRECTORY_SEPARATOR;
        $this->voucher_base_url = Yii::app()->getBaseurl(true) . Yii::app()->params['VOUCHER_PATH'] . $date . '/' . $this->order_id . '/';

        if (!file_exists($this->voucher_path)) {
            mkdir($this->voucher_path, 0755, true);
        }

        switch ($this->status_id) {
            case HtOrderStatus::ORDER_CONFIRMED:
            case HtOrderStatus::ORDER_PAYMENT_FAILED:
                $this->status_shortname = '未支付';
                break;
            case HtOrderStatus::ORDER_SHIPPED:
                $this->status_shortname = '已发货';
                break;

            case HtOrderStatus::ORDER_REFUND_SUCCESS:
                $this->status_shortname = '已退款';
                break;

            case HtOrderStatus::ORDER_CANCELED:
                $this->status_shortname = '已取消';
                break;

            case HtOrderStatus::ORDER_RETURN_REQUEST:
            case HtOrderStatus::ORDER_WAIT_RETURN_CONFIRMATION:
            case HtOrderStatus::ORDER_REFUND_PROCESSING:
                $this->status_shortname = '退订处理中';
                break;
            default:
                $this->status_shortname = '处理中';
                break;
        }
    }

    public function limitByPage($page, $page_size = 20)
    {
        $this->getDbCriteria()->mergeWith(array('offset' => ($page - 1) * $page_size, 'limit' => $page_size));

        return $this;
    }

    public function filterByGroup($group)
    {
        $status_set = $this->getStatusSetByGroup($group);
        if ($status_set) {
            $this->getDbCriteria()->mergeWith(array('condition' => 'status_id IN (' . implode(',', $status_set) . ')'));
        }

        return $this;
    }

    private function getStatusSetByGroup($group)
    {
        $status_set = [];
        switch ($group) {
            case 'all':
                $status_set = [];
                break;
            case 'unpaid':
                $status_set = [3];
                break;
            case '':
                break;
            default:
                break;
        }

        return $status_set;
    }

    public function getMainTotalOrders($data = array())
    {

        $sql = "SELECT count(DISTINCT o.order_id) as total " .
            " FROM ht_order o WHERE 1=1 ";

        if (!empty($data['filter_supplier_id'])) {
            $sql .= " AND o.order_id in (" . $this->getOrderIDsOfSupplier((int)$data['filter_supplier_id']) . ")";
        }

        if (!empty($query_data['filterNotShipped'])) {
            $sql .= " AND o.status_id in (5,4,2)"; //5备货失败、4等待供应商确认、2备货完成待发货
        }

        if (!empty($query_data['filterNeedRefund'])) {
            $sql .= " AND o.status_id in (20,8)"; //8等待退货确认、20退款处理中
        }

        if (!empty($query_data['filterQuestion'])) {
            $sql .= " AND o.status_id in (6,9,10,12,17,19,21,22,23,26)"; //6发货失败、9退货已确认、10退货被拒绝、12退货操作失败、17预定失败、19退款失败、21支付成功、22支付失败、23退货申请、26已支付但已过期
        }

        if (!empty($data['filterToDo'])) {
            $sql .= " AND o.order_id in (" . $this->getCommentOrder(1) . ")";
        }

        $command = Yii::app()->db->createCommand($sql);

        $result = $command->queryRow();

        return $result['total'];

    }

    public function getOrderIDsOfSupplier($supplier_id)
    {
        $connection = Yii::app()->db;
        $query = $connection->createCommand("SELECT DISTINCT order_id FROM ht_order_product a,ht_product b WHERE a.product_id = b.product_id AND b.supplier_id = " . $supplier_id)->queryAll();

        $order_ids = "'0'";
        foreach ($query as $data) {
            $order_ids .= ", '" . $data['order_id'] . "'";
        }

        return $order_ids;
    }

    public function getCommentOrder($proc_status)
    {
        $connection = Yii::app()->db;
        $query = $connection->createCommand("SELECT DISTINCT order_id FROM ht_order_comment WHERE proc_status = " . $proc_status)->queryAll();

        $order_ids = "'0'";
        foreach ($query as $data) {
            $order_ids .= ", '" . $data['order_id'] . "'";
        }

        return $order_ids;
    }

    public function getTotalOrderCounts($query_data, $sort_data)
    {
        $base_sql = "SELECT s.supplier_id, s.name, IFNULL(s1.not_shipped, 0) not_shipped, IFNULL(s2.need_refund, 0) need_refund, IFNULL(s3.question, 0) question, IFNULL(s4.todo, 0) todo,
                IFNULL(s5.not_shipped_use_date, 0) not_shipped_use_date, IFNULL(s6.not_shipped_over_ship_date, 0) not_shipped_over_ship_date, IFNULL(s7.not_shipped_to_ship_date, 0) not_shipped_to_ship_date,
                IFNULL(s8.todo_complain, 0)  todo_complain, IFNULL(s9.todo_urge, 0)  todo_urge, IFNULL(s10.todo_bill, 0)  todo_bill, IFNULL(s11.todo_total, 0)  todo_total,
                IFNULL(s12.todo_edit, 0)  todo_edit, IFNULL(s13.todo_feedback, 0)  todo_feedback, IFNULL(s14.todo_record, 0)  todo_record
                FROM ht_supplier AS s ";
        $join_select_sql = "SELECT s.supplier_id AS supplier_id, count(distinct o.order_id) AS ";
        $join_sql = " FROM ht_order AS o
                    JOIN (SELECT order_id,min(order_product_id),product_id,tour_date,date_added FROM ht_order_product GROUP BY order_id) AS op
                    ON o.order_id = op.order_id
                    JOIN ht_product AS p
                    ON op.product_id = p.product_id
                    JOIN ht_product_description AS pd
                    ON p.product_id = pd.product_id
                    AND pd.language_id = 2
                    JOIN ht_supplier AS s
                    ON p.supplier_id = s.supplier_id
                    JOIN ht_product_date_rule AS pdr
                    ON pdr.product_id = op.product_id
                    left join ht_order_comment oc
                    ON op.order_id = oc.order_id
                    AND oc.proc_status = 1 ";
        $join_group_sql = "GROUP BY s.supplier_id";

        $where_sql = "";

        if (!empty($query_data['search_field']) && !empty($query_data['search_text'])) {
            if ($query_data['search_field'] == 'order_id') {
                $where_sql .= " AND o." . $query_data['search_field'] . " = '" . $query_data['search_text'] . "'";
            } else {
                $where_sql .= " AND o." . $query_data['search_field'] . " like '%" . $query_data['search_text'] . "%'";
            }
        }

        if (!empty($query_data['filter_order_status_id'])) {
            $where_sql .= " AND o.status_id = '" . $query_data['filter_order_status_id'] . "'";
        }

        if ($query_data['has_combination'] == 1) {
            if (!empty($query_data['search_product_text'])) {
                $where_sql .= " AND (pd.name LIKE '%" . $query_data['search_product_text'] . "%' ";
                $where_sql .= "or pd.product_id = '" . $query_data['search_product_text'] . "')";
            }

            if (!empty($query_data['search_added_from_date'])) {
                $where_sql .= " AND DATE(op.date_added) >= DATE('" . $query_data['search_added_from_date'] . "')";
            }

            if (!empty($query_data['search_added_to_date'])) {
                $where_sql .= " AND DATE(op.date_added) <= DATE('" . $query_data['search_added_to_date'] . "')";
            }

            if (!empty($query_data['search_tour_from_date'])) {
                $where_sql .= " AND DATE(op.tour_date) >= DATE('" . $query_data['search_tour_from_date'] . "')";
            }

            if (!empty($query_data['search_tour_to_date'])) {
                $where_sql .= " AND DATE(op.tour_date) <= DATE('" . $query_data['search_tour_to_date'] . "')";
            }
            if (!empty($query_data['search_order_id'])) {
                $where_sql .= " AND o.order_id = '" . (int)$query_data['search_order_id'] . "'";
            }
            if (!empty($query_data['search_passenger'])) {
                $passenger_where = " FROM ht_order AS o
                                        JOIN (
                                        SELECT order_id
			                            FROM ht_order_passenger
			                            AS ops JOIN (
			                            SELECT passenger_id
			                            FROM ht_passenger
			                            WHERE (
				                            zh_name LIKE '%" . $query_data['search_passenger'] . "%'
				                            OR
				                            en_name LIKE '%" . $query_data['search_passenger'] . "%'
			                            )
			                            )pax
			                            ON ops.passenger_id = pax.passenger_id
			                            GROUP BY order_id
			                            )ops
                                        ON o.order_id = ops.order_id";
                $join_sql = str_replace(" FROM ht_order AS o", $passenger_where, $join_sql);
            }
        }

        // s1
        $sql = $base_sql . " LEFT JOIN ( " . $join_select_sql . "not_shipped" . $join_sql . "WHERE o.status_id IN (5,4,2) ";
        if (strlen($where_sql) > 0) {
            $sql .= $where_sql;
        }
        $sql .= $join_group_sql . " ) s1 on s.supplier_id = s1.supplier_id ";

        // s2
        $sql .= " LEFT JOIN ( " . $join_select_sql . "need_refund" . $join_sql . "WHERE o.status_id in (20,8) ";
        if (strlen($where_sql) > 0) {
            $sql .= $where_sql;
        }
        $sql .= $join_group_sql . " ) s2 on s.supplier_id = s2.supplier_id ";

        // s3
        $sql .= " LEFT JOIN ( " . $join_select_sql . "question" . $join_sql . "WHERE o.status_id IN (6,9,10,12,17,19,21,22,23,26) ";
        if (strlen($where_sql) > 0) {
            $sql .= $where_sql;
        }
        $sql .= $join_group_sql . " ) s3 on s.supplier_id = s3.supplier_id ";

        // s4
        $sql .= " LEFT JOIN ( " . $join_select_sql . "todo" . $join_sql . "WHERE o.order_id IN (SELECT DISTINCT order_id FROM ht_order_comment WHERE proc_status = 1) ";
        if (strlen($where_sql) > 0) {
            $sql .= $where_sql;
        }
        $sql .= $join_group_sql . " ) s4 on s.supplier_id = s4.supplier_id ";

        // s5
        $sql .= " LEFT JOIN ( " . $join_select_sql . "not_shipped_use_date" . $join_sql . "WHERE o.status_id IN (5,4,2) AND DATE(op.tour_date) >= now() and tour_date <= date_add(now(),interval 2 day) ";
        if (strlen($where_sql) > 0) {
            $sql .= $where_sql;
        }
        $sql .= $join_group_sql . " ) s5 on s.supplier_id = s5.supplier_id ";

        // s6
        $sql .= " LEFT JOIN ( " . $join_select_sql . "not_shipped_over_ship_date" . $join_sql . "WHERE o.status_id IN (5,4,2) AND (date_format(now(),'%Y%m%d') - date_format(DATE(op.date_added),'%Y%m%d')) > left(pdr.lead_time,1) ";
        if (strlen($where_sql) > 0) {
            $sql .= $where_sql;
        }
        $sql .= $join_group_sql . " ) s6 on s.supplier_id = s6.supplier_id ";

        // s7
        $sql .= " LEFT JOIN ( " . $join_select_sql . "not_shipped_to_ship_date" . $join_sql . "WHERE o.status_id IN (5,4,2) AND (date_format(now(),'%Y%m%d') - date_format(DATE(op.date_added),'%Y%m%d')) <= left(pdr.lead_time,1) ";
        if (strlen($where_sql) > 0) {
            $sql .= $where_sql;
        }
        $sql .= $join_group_sql . " ) s7 on s.supplier_id = s7.supplier_id ";

        // s8
        $sql .= " LEFT JOIN ( " . $join_select_sql . "todo_complain" . $join_sql . "WHERE o.order_id in (" . $this->getCommentOrder(1) . ") AND oc.type = 7 ";
        if (strlen($where_sql) > 0) {
            $sql .= $where_sql;
        }
        $sql .= $join_group_sql . " ) s8 on s.supplier_id = s8.supplier_id ";

        // s9
        $sql .= " LEFT JOIN ( " . $join_select_sql . "todo_urge" . $join_sql . "WHERE o.order_id in (" . $this->getCommentOrder(1) . ") AND oc.type = 2 ";
        if (strlen($where_sql) > 0) {
            $sql .= $where_sql;
        }
        $sql .= $join_group_sql . " ) s9 on s.supplier_id = s9.supplier_id ";
        // s10
        $sql .= " LEFT JOIN ( " . $join_select_sql . "todo_bill" . $join_sql . "WHERE o.order_id in (" . $this->getCommentOrder(1) . ") AND oc.type = 3 ";
        if (strlen($where_sql) > 0) {
            $sql .= $where_sql;
        }
        $sql .= $join_group_sql . " ) s10 on s.supplier_id = s10.supplier_id ";
        // s11
        $sql .= " LEFT JOIN ( " . $join_select_sql . "todo_total" . $join_sql . "WHERE o.order_id in (" . $this->getCommentOrder(1) . ") AND oc.type = 4 ";
        if (strlen($where_sql) > 0) {
            $sql .= $where_sql;
        }
        $sql .= $join_group_sql . " ) s11 on s.supplier_id = s11.supplier_id ";
        // s12
        $sql .= " LEFT JOIN ( " . $join_select_sql . "todo_edit" . $join_sql . "WHERE o.order_id in (" . $this->getCommentOrder(1) . ") AND oc.type = 5 ";
        if (strlen($where_sql) > 0) {
            $sql .= $where_sql;
        }
        $sql .= $join_group_sql . " ) s12 on s.supplier_id = s12.supplier_id ";
        // s13
        $sql .= " LEFT JOIN ( " . $join_select_sql . "todo_feedback" . $join_sql . "WHERE o.order_id in (" . $this->getCommentOrder(1) . ") AND oc.type = 6 ";
        if (strlen($where_sql) > 0) {
            $sql .= $where_sql;
        }
        $sql .= $join_group_sql . " ) s13 on s.supplier_id = s13.supplier_id ";
        // s14
        $sql .= " LEFT JOIN ( " . $join_select_sql . "todo_record" . $join_sql . "WHERE o.order_id in (" . $this->getCommentOrder(1) . ") AND oc.type = 1 ";
        if (strlen($where_sql) > 0) {
            $sql .= $where_sql;
        }
        $sql .= $join_group_sql . " ) s14 on s.supplier_id = s14.supplier_id ";

        if ($query_data['filter_supplier_id'] != 0) {
            $sql .= " WHERE s.supplier_id = " . $query_data['filter_supplier_id'];
        }

        if (!empty($sort_data)) {
            $order_query = ' ';
            foreach ($sort_data as $order_field => $order_dir) {
                $order_query .= ', ' . $order_field . ' ' . ($order_dir == 1 ? 'ASC' : 'DESC');
            }
            $order_query = " ORDER BY " . substr($order_query, 2);
            $sql .= $order_query;
        }

        $command = Yii::app()->db->createCommand($sql);

        $result = $command->queryAll();

        return $result;
    }

    public function getOrders($data = array(), $totals_flag = 0)
    {
        if ($totals_flag == 1) {
            $sql = "SELECT COUNT(DISTINCT o.order_id) AS total FROM ht_order o ";
        } else {
            $sql = "SELECT t.* FROM (SELECT o.order_id,o.contacts_name,o.contacts_email,o.contacts_telephone,o.status_id,os.cn_name,s.name AS supplier_name ,pd.product_id,pd.name,o.total,op.special_code,op.date_added,o.date_modified,op.tour_date,op.cost_total,so.confirmation_ref,so.hitour_booking_ref,so.supplier_booking_ref FROM ht_order o ";
        }
        $sql .= "left join ht_order_status os on o.status_id = os.order_status_id ";
        $sql .= "left join ht_order_passenger ops on o.order_id = ops.order_id ";
        $sql .= "left join ht_passenger pax on ops.passenger_id = pax.passenger_id ";
        $sql .= "left join ht_order_product op on o.order_id = op.order_id left join ht_supplier_order so on op.supplier_order_id = so.supplier_order_id ";
        $sql .= "left join ht_product p on op.product_id = p.product_id ";
        $sql .= "left join ht_supplier s on p.supplier_id = s.supplier_id ";
        $sql .= "left join ht_product_description pd on p.product_id = pd.product_id and pd.language_id = 2 ";
        $sql .= "left join ht_product_date_rule pdr on op.product_id = pdr.product_id ";
        $sql .= "left join ht_order_comment oc on op.order_id = oc.order_id and oc.proc_status = 1 ";

        if (!empty($data['query_filter'])) {
            $query_data = $data['query_filter'];
            $where_sql = "";

            if (!empty($query_data['search_field']) || $query_data['has_combination'] == 1) {
                if (!empty($query_data['filter_supplier_id'])) {
                    $where_sql .= " AND o.order_id in (" . $this->getOrderIDsOfSupplier((int)$query_data['filter_supplier_id']) . ")";
                }

                if (!empty($query_data['search_field']) && !empty($query_data['search_text'])) {
                    if ($query_data['search_field'] == 'order_id') {
                        $where_sql .= " AND o." . $query_data['search_field'] . " = '" . $query_data['search_text'] . "'";
                    } else {
                        $where_sql .= " AND o." . $query_data['search_field'] . " like '%" . $query_data['search_text'] . "%'";
                    }
                }

                if (!empty($query_data['filter_order_status_id'])) {
                    $where_sql .= " AND o.status_id = '" . $query_data['filter_order_status_id'] . "'";
                }

                if ($query_data['has_combination'] == 1) {
                    if (!empty($query_data['search_product_text'])) {
                        $where_sql .= " AND (pd.name LIKE '%" . $query_data['search_product_text'] . "%' ";
                        $where_sql .= "or pd.product_id = '" . $query_data['search_product_text'] . "')";
                    }

                    if (!empty($query_data['search_added_from_date'])) {
                        $where_sql .= " AND DATE(op.date_added) >= DATE('" . $query_data['search_added_from_date'] . "')";
                    }

                    if (!empty($query_data['search_added_to_date'])) {
                        $where_sql .= " AND DATE(op.date_added) <= DATE('" . $query_data['search_added_to_date'] . "')";
                    }

                    if (!empty($query_data['search_tour_from_date'])) {
                        $where_sql .= " AND DATE(op.tour_date) >= DATE('" . $query_data['search_tour_from_date'] . "')";
                    }

                    if (!empty($query_data['search_tour_to_date'])) {
                        $where_sql .= " AND DATE(op.tour_date) <= DATE('" . $query_data['search_tour_to_date'] . "')";
                    }
                    if (!empty($query_data['search_order_id'])) {
                        $where_sql .= " AND o.order_id = '" . (int)$query_data['search_order_id'] . "'";
                    }
                    if (!empty($query_data['search_confirmation_ref'])) {
                        $where_sql .= " AND so.confirmation_ref like '%" . $query_data['search_confirmation_ref'] . "%'";
                        $where_sql .= " OR so.hitour_booking_ref like '%" . $query_data['search_confirmation_ref'] . "%'";
                        $where_sql .= " OR so.supplier_booking_ref like '%" . $query_data['search_confirmation_ref'] . "%'";
                    }
                    if (!empty($query_data['search_passenger'])) {
                        $where_sql .= " AND (pax.zh_name like '%" . $query_data['search_passenger'] . "%' or pax.en_name like '%" . $query_data['search_passenger'] . "%')";
                    }
                }

                if (!empty($query_data['filterNotShipped'])) {
                    $where_sql .= " AND o.status_id in (5,4,2)"; //5备货失败、4等待供应商确认、2备货完成待发货
                    if(!empty($query_data['notShipped_use_date'])){
                        $where_sql .= " AND DATE(op.tour_date) >= now() and tour_date <= date_add(now(),interval 2 day) ";
                    }
                    if(!empty($query_data['notShipped_over_ship_date'])){
                        $where_sql .= " AND (date_format(now(),'%Y%m%d') - date_format(DATE(op.date_added),'%Y%m%d')) > left(pdr.lead_time,1) ";
                    }
                    if(!empty($query_data['notShipped_to_ship_date'])){
                        $where_sql .= " AND (date_format(now(),'%Y%m%d') - date_format(DATE(op.date_added),'%Y%m%d')) <= left(pdr.lead_time,1) ";
                    }
                }

                if (!empty($query_data['filterNeedRefund'])) {
                    $where_sql .= " AND o.status_id in (20,8)"; //8等待退货确认、20退款处理中
                }

                if (!empty($query_data['filterQuestion'])) {
                    $where_sql .= " AND o.status_id in (6,9,10,12,17,19,21,22,23,26)"; //6发货失败、9退货已确认、10退货被拒绝、12退货操作失败、17预定失败、19退款失败、21支付成功、22支付失败、23退货申请、26已支付但已过期
                }

                if (!empty($query_data['filterToDo'])) {
                    $where_sql .= " AND o.order_id in (" . $this->getCommentOrder(1) . ")";
                    if(!empty($query_data['todo_record'])){
                        $where_sql .= " AND oc.type = 1 ";
                    }
                    if(!empty($query_data['todo_urge'])){
                        $where_sql .= " AND oc.type = 2 ";
                    }
                    if(!empty($query_data['todo_bill'])){
                        $where_sql .= " AND oc.type = 3 ";
                    }
                    if(!empty($query_data['todo_total'])){
                        $where_sql .= " AND oc.type = 4 ";
                    }
                    if(!empty($query_data['todo_edit'])){
                        $where_sql .= " AND oc.type = 5 ";
                    }
                    if(!empty($query_data['todo_feedback'])){
                        $where_sql .= " AND oc.type = 6 ";
                    }
                    if(!empty($query_data['todo_complain'])){
                        $where_sql .= " AND oc.type = 7 ";
                    }
                    if(!empty($query_data['todo_date_today'])){
                        $where_sql .= " AND date_format(DATE(oc.date_proc),'%Y%m%d') = date_format(now(),'%Y%m%d') ";
                    }
                    if(!empty($query_data['todo_date_tomorrow'])){
                        $where_sql .= " AND TO_DAYS(DATE(oc.date_proc)) - TO_DAYS(now()) = 1 ";
                    }
                    if(!empty($query_data['todo_date_after_tomorrow'])){
                        $where_sql .= " AND TO_DAYS(DATE(oc.date_proc)) - TO_DAYS(now()) = 2 ";
                    }
                }
            } else {
                if (!empty($query_data['filter_order_status_id'])) {
                    $where_sql .= " AND o.status_id = '" . (int)$query_data['filter_order_status_id'] . "'";
                } else {
                    $where_sql .= " AND o.status_id > '0'";
                }

                if (!empty($query_data['filter_order_id'])) {
                    $where_sql .= " AND o.order_id = '" . (int)$query_data['filter_order_id'] . "'";
                }

                if (!empty($query_data['filter_date_added'])) {
                    $where_sql .= " AND DATE(o.date_added) = DATE('" . $query_data['filter_date_added'] . "')";
                }

                if (!empty($query_data['filter_date_modified'])) {
                    $where_sql .= " AND DATE(o.date_modified) = DATE('" . $query_data['filter_date_modified'] . "')";
                }

                if (!empty($query_data['filter_total'])) {
                    $where_sql .= " AND o.total = '" . (float)$query_data['filter_total'] . "'";
                }

                if (!empty($query_data['filter_supplier_id'])) {
                    $where_sql .= " AND o.order_id in (" . $this->getOrderIDsOfSupplier((int)$query_data['filter_supplier_id']) . ")";
                }

                if (!empty($query_data['filterNotShipped'])) {
                    $where_sql .= " AND o.status_id in (5,4,2)"; //5备货失败、4等待供应商确认、2备货完成待发货
                }

                if (!empty($query_data['filterNeedRefund'])) {
                    $where_sql .= " AND o.status_id in (20,8)"; //8等待退货确认、20退款处理中
                }

                if (!empty($query_data['filterQuestion'])) {
                    $where_sql .= " AND o.status_id in (6,9,10,12,17,19,21,22,23,26)"; //6发货失败、9退货已确认、10退货被拒绝、12退货操作失败、17预定失败、19退款失败、21支付成功、22支付失败、23退货申请、26已支付但已过期
                }

                if (!empty($query_data['filterToDo'])) {
                    $where_sql .= " AND o.order_id in (" . $this->getCommentOrder(1) . ")";
                }
            }

            if (!empty($where_sql)) {
                $where_sql = substr($where_sql, 4);
                $sql .= " where " . $where_sql;
            }
        }

        if ($totals_flag != 1) {
            $sql .= 'order by o.order_id, op.order_product_id) t';
            $sql .= " group by t.order_id";
            if (isset($data['sort'])) {
                $order_query = ' ';
                foreach ($data['sort'] as $order_field => $order_dir) {
                    $order_query .= ', ' . $order_field . ' ' . ($order_dir == 1 ? 'ASC' : 'DESC');
                }
                $order_query = " ORDER BY " . substr($order_query, 2);
                $sql .= $order_query;
            }

            if (isset($data['paging']['start']) || isset($data['paging']['limit'])) {
                if ($data['paging']['start'] < 0) {
                    $data['paging']['start'] = 0;
                }

                if ($data['paging']['limit'] < 1) {
                    $data['paging']['limit'] = 20;
                }

                $sql .= " LIMIT " . (int)$data['paging']['start'] . "," . (int)$data['paging']['limit'];
            }
        }
        //echo $sql;
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);

        return $command->queryAll();

    }

    public function getUnShippedOrderCostAmount($supplier_id)
    {
        $sql = "SELECT sum(op.total) AS total FROM ht_order o LEFT JOIN ht_order_product op ON o.order_id = op.order_id LEFT JOIN ht_product AS p ON op.product_id = p.product_id WHERE o.status_id IN (21,4,2) AND p.supplier_id = ";
        $sql .= $supplier_id;

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);

        return $command->query();
    }

    public function getActionUrls()
    {
        $urls = array();
        if (in_array($this->status_id, [HtOrderStatus::ORDER_SHIPPED, HtOrderStatus::ORDER_OUTOF_REFUND])) {
            $urls['download_voucher_url'] = Yii::app()->urlManager->createUrl('account/downloadVoucher',
                                                                              ['order_id' => $this->order_id]);
        } else {
            $urls['download_voucher_url'] = '';
        }

        if (in_array($this->status_id, [HtOrderStatus::ORDER_CONFIRMED, HtOrderStatus::ORDER_PAYMENT_FAILED])) {
            $urls['payment_url'] = Yii::app()->urlManager->createUrl('PayGate/Pay', ['order_id' => $this->order_id]);
        } else {
            $urls['payment_url'] = '';
        }

        if (in_array($this->status_id,
                     [HtOrderStatus::ORDER_CONFIRMED, HtOrderStatus::ORDER_PAYMENT_FAILED, HtOrderStatus::ORDER_NOTPAY_EXPIRED])
        ) {
            $urls['cancel_url'] = Yii::app()->urlManager->createUrl('account/cancelOrder',
                                                                    ['order_id' => $this->order_id]);
        } else {
            $urls['cancel_url'] = '';
        }

        if (in_array($this->status_id,
                     [HtOrderStatus::ORDER_PAYMENT_SUCCESS, HtOrderStatus::ORDER_PAID_EXPIRED, HtOrderStatus::ORDER_WAIT_CONFIRMATION,
                         HtOrderStatus::ORDER_TO_DELIVERY, HtORderStatus::ORDER_STOCK_FAILED, HtOrderStatus::ORDER_BOOKING_FAILED, HtOrderStatus::ORDER_SHIPPED, HtOrderStatus::ORDER_SHIPPING_FAILED])
        ) {
            $urls['return_url'] = Yii::app()->urlManager->createUrl('account/returnOrder',
                                                                    ['order_id' => $this->order_id]);
        } else {
            $urls['return_url'] = '';
        }

        return $urls;
    }

    public function updateExtractCode($order_id)
    {
        $sql = "UPDATE `ht_order` SET `extract_code` = substr(md5(concat(`customer_id`,'_',`order_id`,'_',now())),1,4) WHERE `order_id` = " . (int)$order_id;
        $update_num = Yii::app()->db->createCommand($sql)->execute();

        return $update_num == 1;
    }

    public static function getSalesVolume($product_id)
    {
        $key = 'CACHE_KEY_SALES_VOLUME_' . $product_id;
        $result = (int)Yii::app()->cache->get($key);
        if(!empty($result)) {
            return $result;
        }

        $result = 0;
        $sql = 'SELECT count(*) the_count FROM ht_order o LEFT JOIN ht_order_product op ON op.order_id = o.order_id ' .
            'WHERE op.product_id = ' . $product_id . ' AND op.bundle_product_id = 0 AND o.status_id in (3)';
        $connection = Yii::app()->db;
        $query = $connection->createCommand($sql)->queryRow();
        if ($query) {
            $result = (int)$query['the_count'];
        }

        if($result == 0) {
            $result = (int)(((($product_id +3721) % 103) * 97 ) % 71) + 1;
        }

        if($result > 300) {
            $result += 688; // add magic number
        }  else {
            $result += 88;
        }

        Yii::app()->cache->set($key, $result, 3*60);

        return $result;
    }

}
