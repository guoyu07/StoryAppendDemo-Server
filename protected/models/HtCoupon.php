<?php

    /**
     * This is the model class for table "ht_coupon".
     *
     * The followings are the available columns in table 'ht_coupon':
     * @property integer $coupon_id
     * @property string $name
     * @property string $description
     * @property string $code
     * @property integer $use_type
     * @property string $type
     * @property string $discount
     * @property integer $logged
     * @property integer $shipping
     * @property string $total
     * @property integer $product_min
     * @property integer $product_max
     * @property string $date_start
     * @property string $date_end
     * @property integer $uses_total
     * @property string $uses_customer
     * @property integer $customer_id
     * @property integer $status
     * @property string $date_added
     * @property integer $used_total
     * @property integer $rel_coupon_id
     */
    class HtCoupon extends CActiveRecord {
        const T_PERCENT = 'P';
        const T_FUND = 'F';

        /**
         * Returns the static model of the specified AR class.
         * Please note that you should have this exact method in all your CActiveRecord descendants!
         * @param string $className active record class name.
         * @return HtCoupon the static model class
         */
        public static function model($className = __CLASS__) {
            return parent::model($className);
        }

        public static function getCouponDiscount($discount, $type) {
            $result = sprintf('%.0f', $discount);
            if ($type == HtCoupon::T_PERCENT) {
                $result = $result . '%';
            }

            return $result;
        }

        /**
         * @return string the associated database table name
         */
        public function tableName() {
            return 'ht_coupon';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules() {
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                array('name, description,code, use_type, type, discount, total, product_min, product_max, uses_total, uses_customer, status, used_total, rel_coupon_id, is_template', 'required'),
                array('use_type, logged, shipping, product_min, product_max, uses_total, customer_id, status, used_total, rel_coupon_id', 'numerical', 'integerOnly' => true),
                array('name,description', 'length', 'max' => 128),
                array('code', 'length', 'max' => 10),
                array('type', 'length', 'max' => 1),
                array('discount, total', 'length', 'max' => 15),
                array('uses_customer', 'length', 'max' => 11),
                array('date_start, date_end, date_added', 'safe'),
                // The following rule is used by search().
                // @todo Please remove those attributes that should not be searched.
                array('coupon_id, name, description,code, use_type, type, discount, logged, shipping, total, product_min, product_max, date_start, date_end, uses_total, uses_customer, customer_id, status, date_added, used_total, rel_coupon_id', 'safe', 'on' => 'search'),
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations() {
            // NOTE: you may need to adjust the relation name and the related
            // class name for the relations automatically generated below.
            return array(
                'history' => array(self::HAS_MANY, 'HtCouponHistory', '', 'on' => 'coupon.coupon_id = coupon_history.coupon_id'),
                'use_limit' => array(self::HAS_MANY, 'HtCouponUseLimit', '', 'on' => 'coupon.coupon_id = ul.coupon_id'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels() {
            return array(
                'coupon_id' => 'Coupon',
                'name' => 'Name',
                'description' => 'Description',
                'code' => 'Code',
                'use_type' => 'Use Type',
                'type' => 'Type',
                'discount' => 'Discount',
                'logged' => 'Logged',
                'shipping' => 'Shipping',
                'total' => 'Total',
                'product_min' => '最小产品数',
                'product_max' => '最大产品数',
                'date_start' => 'Date Start',
                'date_end' => 'Date End',
                'uses_total' => 'Uses Total',
                'uses_customer' => 'Uses Customer',
                'customer_id' => 'Customer',
                'status' => 'Status',
                'date_added' => 'Date Added',
                'used_total' => 'Used Total',
                'rel_coupon_id' => 'Rel Coupon Id',
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
        public function search() {
            // @todo Please modify the following code to remove attributes that should not be searched.

            $criteria = new CDbCriteria;

            $criteria->compare('coupon_id', $this->coupon_id);
            $criteria->compare('name', $this->name, true);
            $criteria->compare('description', $this->description, true);
            $criteria->compare('code', $this->code, true);
            $criteria->compare('use_type', $this->use_type);
            $criteria->compare('type', $this->type, true);
            $criteria->compare('discount', $this->discount, true);
            $criteria->compare('logged', $this->logged);
            $criteria->compare('shipping', $this->shipping);
            $criteria->compare('total', $this->total, true);
            $criteria->compare('product_min', $this->product_min);
            $criteria->compare('product_max', $this->product_max);
            $criteria->compare('date_start', $this->date_start, true);
            $criteria->compare('date_end', $this->date_end, true);
            $criteria->compare('uses_total', $this->uses_total);
            $criteria->compare('uses_customer', $this->uses_customer, true);
            $criteria->compare('customer_id', $this->customer_id);
            $criteria->compare('status', $this->status);
            $criteria->compare('date_added', $this->date_added, true);
            $criteria->compare('used_total', $this->used_total);
            $criteria->compare('rel_coupon_id', $this->rel_coupon_id);

            return new CActiveDataProvider($this, array(
                'criteria' => $criteria,
            ));
        }

        public function defaultScope() {
            return array(
                'alias' => 'coupon',
            );
        }

        public static function generateCoupon($attributes = []) {
            $coupon = new HtCoupon();
            ModelHelper::fillItem($coupon, $attributes, ['name', 'description', 'discount', 'type', 'use_type', 'total',
                'logged', 'shipping', 'date_start', 'date_end', 'uses_total', 'uses_customer', 'customer_id', 'status']);

            $coupon['code'] = substr(md5($coupon['name'] . mt_rand()), 0, 10);

            if (!$coupon->insert()) {
                return array(0, '');
            }

            return array($coupon['coupon_id'], $coupon['code']);
        }

        public function generateWeixinScanCoupon($weixin_unionid, $discount = 10, $customer_id = 0) {
            $coupon = new HtCoupon();
            $coupon['name'] = '微信扫码送优惠券_' . $discount . '_' . $weixin_unionid;
            $coupon['description'] = '';
            $coupon['code'] = substr(md5($coupon['name']), 0, 10);
            $coupon['discount'] = (int)$discount;
            $coupon['type'] = 'F';
            $coupon['use_type'] = 1;
            $coupon['total'] = 0;
            $coupon['logged'] = 1;
            $coupon['shipping'] = 0;
            $coupon['date_start'] = date('Y-m-d', strtotime('-1Day'));
            $coupon['date_end'] = date('Y-m-d', strtotime('+2Day'));
            $coupon['uses_total'] = 1;
            $coupon['uses_customer'] = 1;
            $coupon['customer_id'] = (int)$customer_id;
            $coupon['status'] = 1;
            if (!$coupon->insert()) {
                return array(0, '');
            }

            HtDandelionPickup::model()->addNew($coupon['coupon_id'], $customer_id, HtDandelionPickup::PT_BY_WEIXIN, 0, 0);

            return array($coupon['coupon_id'], $coupon['code']);
        }

        public function getWeixinScanCoupon($customer_id) {
            // getWeixinScanCoupon
            $generated = false;
            $used = false;
            $code = '';

            $c = new CDbCriteria();
            $c->addCondition('customer_id = ' . (int)$customer_id);
            $c->addCondition('name like "微信扫码送优惠券_%"');
            $items = $this->findAll($c);
            if (!empty($items)) {
                $generated = true;
                foreach ($items as $item) {
                    if (HtCouponHistory::model()->couponUsed($item['coupon_id'], $customer_id)) {
                        $used = true;
                        break;
                    }
                }

                if (!$used) {
                    $item = $items[0];
                    $item['date_start'] = date('Y-m-d', strtotime('-1Day'));
                    $item['date_end'] = date('Y-m-d', strtotime('+2Day'));

                    $item->update();
                    $code = $item['code'];
                }
            }

            return array($generated, $used, $code);
        }

        public function generateGiftCoupon($order_id, $discount = 20) {
            $coupon = new HtCoupon();
            $coupon['name'] = '订单送优惠券_' . $discount . '_' . $order_id;
            $coupon['description'] = '';
            $coupon['code'] = substr(md5($coupon['name']), 0, 10);
            $coupon['discount'] = $discount;
            $coupon['type'] = 'F';
            $coupon['use_type'] = 1;
            $coupon['total'] = 0;
            $coupon['logged'] = 1;
            $coupon['shipping'] = 0;
            $coupon['date_start'] = date('Y-m-d', strtotime('+1Day'));
            $coupon['date_end'] = date('Y-m-d', strtotime('+6Month'));
            $coupon['uses_total'] = 1;
            $coupon['uses_customer'] = 1;
            $coupon['customer_id'] = 0;
            $coupon['status'] = 1;
            if (!$coupon->insert()) {
                return 0;
            }

            $order = HtOrder::model()->findByPk($order_id);

            HtDandelionPickup::model()->addNew($coupon['coupon_id'], $order['customer_id']);

            return $coupon['coupon_id'];
        }

        public function validateCoupon($code, $product) {
            $result = array('code' => 200, 'msg' => 'OK');

            $cp = $this->findByAttributes(['code' => $code]);
            if (!$cp) {
                $result['code'] = 404;
                $result['msg'] = '优惠券不正确！';

                return $result;
            }

            if(!empty($product['activity_id']) ){
                $ar = HtActivityRule::model()->findByPk($product['activity_id']);
                if($ar['allow_use_coupon'] ==0){
                    $result['code'] = 304;
                    $result['msg'] = '活动商品不能使用优惠券！';
                    return $result;
                }else if($ar['allow_use_coupon']==2){
                    if(!($cp['type']==self::T_PERCENT && $cp['discount'] ==100)){
                        $result['code'] = 304;
                        $result['msg'] = '活动商品不能使用优惠券！';
                        return $result;
                    }
                }
            }


            $now = date('Y-m-d');
            if ($cp['status'] == 0 || $now > $cp['date_end']) {
                $result['code'] = 304;
                $result['msg'] = '优惠券无效或者已过期！';
                return $result;
            }

            if ($now < $cp['date_start']) {
                $result['code'] = 304;
                $result['msg'] = sprintf('优惠券从%s日才可以使用！', $cp['date_start']);
                return $result;
            }

            if ($cp['customer_id'] > 0) {
                if ($cp['customer_id'] != Yii::app()->customer->getCustomerId()) {
                    $result['code'] = 204;
                    $result['msg'] = '您不能使用该优惠券！';

                    return $result;
                }
            }

//        $data = Yii::app()->cart->getProduct();
            $data = $product;
            $data['product_total'] = array_sum($product['quantities']);

            if ($cp['total'] > $data['sub_total']) {
                $result['code'] = 305;
                $result['msg'] = '未达到该优惠券使用的最小金额:￥' . $cp['total'];

                return $result;
            }

            if (($cp['product_max'] > 0 && $cp['product_max'] < $data['product_total'])) {
                $result['code'] = 305;
                $result['msg'] = '不满足该优惠券的使用限制：最多可购买' . $cp['product_max'] . '份';

                return $result;
            }

            if ($cp['product_min'] > $data['product_total']) {
                $result['code'] = 305;
                $result['msg'] = '不满足该优惠券的使用限制：最少应购买' . $cp['product_min'] . '份';

                return $result;
            }

            if ($cp['uses_total'] > 0) {
                $use_total = HtCouponHistory::model()->countByAttributes(['coupon_id' => $cp['coupon_id']]);
                if ($cp['uses_total'] <= $use_total) {
                    $result['code'] = 305;
                    $result['msg'] = '该优惠券已经达到使用上限！';

                    return $result;
                }
            }

            if ($cp['uses_customer'] > 0) {
                $use_customer_total = HtCouponHistory::model()->countByAttributes(['coupon_id' => $cp['coupon_id'], 'customer_id' => Yii::app()->customer->customerId]);
                if ($cp['uses_customer'] <= $use_customer_total) {
                    $result['code'] = 305;
                    $result['msg'] = '您对该优惠券已经达到使用上限！';

                    return $result;
                }
            }

            $limit_ids = HtCouponUseLimit::model()->findAllByAttributes(['coupon_id' => $cp['coupon_id']]);
            if ($limit_ids) {
                $valid_type = $limit_ids[0]['valid_type'];
                $limit_type = $limit_ids[0]['limit_type'];
                $is_config = 0;
                if ($valid_type == 0) { //全球券
                    $limit_type = 1;
                    $is_config = 1;
                }
//            $product_id = Yii::app()->cart->getProductId();
                $product_id = $product['product_id'];
                foreach ($limit_ids as $id) {
                    if ($valid_type == 1) { //商品券
                        if ($id['id'] == $product_id) {
                            $is_config = 1;
                            break;
                        }
                    }
                    if ($valid_type == 2) { //城市券
                        $product = HtProduct::model()->findByPk($product_id);
                        if ($id['id'] == $product['city_code']) {
                            $is_config = 1;
                            break;
                        }
                    }
                    if ($valid_type == 3) { //国家券
                        $product = HtProduct::model()->with('city')->findByPk($product_id);
                        if ($id['id'] == $product['city']['country_code']) {
                            $is_config = 1;
                            break;
                        }
                    }
                }


                if ($limit_type == $is_config) {
                    //allow use
                } else {
                    $result['code'] = 305;
                    $result['msg'] = '本次购买的商品不能使用该优惠券！';

                    return $result;
                }
            }


            // valid coupon by dandelion info
            $dandelion = HtDandelion::model()->findByAttributes(array('coupon_id' => $cp['coupon_id'], 'owner_id' => Yii::app()->customer->customerId));
            if (!empty($dandelion)) {
                if ($dandelion['use_limit'] == 2) {
                    $result['code'] = 305;
                    $result['msg'] = '该优惠券仅供分享！';
                    return $result;
                } else {
                    $dandelion_pickup = HtDandelionPickup::model()->findByAttributes(array('did' => $dandelion['did'], 'customer_id' => Yii::app()->customer->customerId));
                    if (empty($dandelion_pickup)) {
                        $result['code'] = 305;
                        $result['msg'] = '无法验证您的优惠券获取方式。';

                        return $result;
                    }
                }
            }

            $result['code'] = 200;
            $result['data'] = Converter::convertModelToArray($cp);
            $result['msg'] = 'OK！';

            return $result;

        }
    }
