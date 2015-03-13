<?php

/**
 * This is the model class for table "ht_order_gift_coupon".
 *
 * The followings are the available columns in table 'ht_order_gift_coupon':
 * @property integer $order_id
 * @property integer $product_id
 * @property integer $coupon_id
 */
class HtOrderGiftCoupon extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_order_gift_coupon';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_id, product_id, coupon_id', 'required'),
            array('order_id, product_id, coupon_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('order_id, product_id, coupon_id', 'safe', 'on' => 'search'),
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
            'coupon' => array(self::HAS_ONE, 'HtCoupon', '', 'on' => 'gift_coupon.coupon_id = coupon.coupon_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'order_id' => 'Order',
            'product_id' => 'Product ID',
            'coupon_id' => 'Coupon',
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
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('coupon_id', $this->coupon_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'gift_coupon',
        );
    }

    public function grantGiftCoupon($order_id)
    {
        $result = ['code' => 200, 'msg' => 'OK'];
        $order_products = HtOrderProduct::model()->findAllByAttributes(['order_id' => $order_id]);
        foreach ($order_products as $order_product) {
            $product_id = $order_product['product_id'];

            $product_gift_coupon = Converter::convertModelToArray(HtProductGiftCoupon::model()->with('template_coupon')->findAllByAttributes(['product_id' => $product_id, 'status' => HtProductGiftCoupon::STATUS_VALID]));
            $granted_count = HtOrderGiftCoupon::model()->countByAttributes(['order_id' => $order_id]);

            $all_gift_number = 0;
            foreach ($product_gift_coupon as $pgc) {
                $all_gift_number += $pgc['quantity'];
            }

            if ($granted_count < $all_gift_number) {
                if ($granted_count > 0) {
                    HtOrderGiftCoupon::model()->deleteAllByAttributes(['order_id' => $order_id]);
                }

                $order = Converter::convertModelToArray(HtOrder::model()->findByPk($order_id));
                foreach ($product_gift_coupon as $gt) {
                    for ($i = 0; $i < $gt['quantity']; $i++) {
                        $isok = $this->instanceGiftCoupon($order, $product_id, $gt, $i);
                        if (!$isok) {
                            $result['code'] = 400;
                            $result['msg'] = 'Coupon配货失败！';
                            Yii::log('Gift coupon grant Failed!Order_id:' . $order_id, CLogger::LEVEL_INFO,
                                     'hitour.model.giftcoupon');
                            break;
                        }
                    }
                }
            } else {
                Yii::log('Gift coupon has been granted.Order_id:' . $order_id, CLogger::LEVEL_INFO,
                         'hitour.model.giftcoupon');
            }
            if($result['code'] == 400) {
                break;
            }
        }

        return $result;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtOrderGiftCoupon the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function instanceGiftCoupon($order, $product_id, $product_gift, $idx = 0)
    {
        $order_id = $order['order_id'];
        $customer_id = $order['customer_id'];
        $template_coupon = $product_gift['template_coupon'];

        //insert htcoupon
        $coupon = new HtCoupon();
        $coupon['name'] = '订单送优惠券_' . (int)$template_coupon['discount'] . '_' . $order_id . '_' . $idx . '_' . $product_gift['product_id'];
        $coupon['code'] = strtoupper(substr(md5($coupon['name'] . $template_coupon['code']), 0, 10));
        $coupon['discount'] = $template_coupon['discount'];
        $coupon['description'] = $template_coupon['description'];
        $coupon['type'] = $template_coupon['type'];
        $coupon['use_type'] = $template_coupon['use_type'];
        $coupon['product_min'] = $template_coupon['product_min'];
        $coupon['product_max'] = $template_coupon['product_max'];
        $coupon['total'] = $template_coupon['total'];
        $coupon['logged'] = $template_coupon['logged'];
        $coupon['shipping'] = $template_coupon['shipping'];
        if ($product_gift['date_type'] == HtProductGiftCoupon::DATE_TYPE_ABSOLUTE) {
            $coupon['date_start'] = $product_gift['date_start'];
            $coupon['date_end'] = $product_gift['date_end'];
        } else {
            $coupon['date_start'] = date('Y-m-d', strtotime($product_gift['start_offset']));
            $coupon['date_end'] = date('Y-m-d', strtotime($product_gift['end_range'] . $product_gift['start_offset'].'-1day'));
        }

        $coupon['uses_total'] = $template_coupon['uses_total'];
        $coupon['uses_customer'] = $template_coupon['uses_customer'];
        $coupon['date_added'] = $template_coupon['uses_customer'];
        if ($product_gift['customer_limit'] == HtProductGiftCoupon::CUSTOMER_SELFONLY) {
            $coupon['customer_id'] = $customer_id;
        } else {
            $coupon['customer_id'] = 0;
        }
        $coupon['status'] = 1;
        $coupon['date_added'] = date('Y-m-d H:i:s');

        $coupon['rel_coupon_id'] = $template_coupon['coupon_id'];

        if (!$coupon->insert()) {
            return false;
        }

        HtDandelionPickup::model()->addNew($coupon['coupon_id'], $customer_id, HtDandelionPickup::PT_BY_ORDER, 0,
                                           $order_id);

        //insert coupon product
        //$sql = 'INSERT INTO ht_coupon_product(coupon_id, product_id, could_use) SELECT ' . (int)$coupon['coupon_id'] . ',product_id,could_use FROM ht_coupon_product WHERE coupon_id =' . (int)$template_coupon['coupon_id'];
        $sql = 'INSERT INTO ht_coupon_use_limit(coupon_id, id,limit_type,valid_type) SELECT ' . (int)$coupon['coupon_id'] . ',id,limit_type,valid_type FROM ht_coupon_use_limit WHERE coupon_id =' . (int)$template_coupon['coupon_id'];
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->execute();

        //insert order coupon history
        $order_gift_coupon = new HtOrderGiftCoupon();
        $order_gift_coupon->order_id = $order_id;
        $order_gift_coupon->product_id = $product_id;
        $order_gift_coupon->coupon_id = $coupon['coupon_id'];
        if (!$order_gift_coupon->insert()) {
            return false;
        }

        return true;
    }

    public function getCouponsByOrderId($order_id) {
        return Converter::convertModelToArray(HtOrderGiftCoupon::model()->with('coupon')->findAllByAttributes(['order_id' => $order_id]));
    }


//    public function generateGiftCoupon($order_id, $discount = 20)
//    {
//        $coupon = new HtCoupon();
//        $coupon['name'] = '订单送优惠券_' . $discount . '_' . $order_id;
//        $coupon['code'] = substr(md5($coupon['name']), 0, 10);
//        $coupon['discount'] = $discount;
//        $coupon['type'] = 'F';
//        $coupon['use_type'] = 1;
//        $coupon['total'] = 0;
//        $coupon['logged'] = 1;
//        $coupon['shipping'] = 0;
//        $coupon['date_start'] = date('Y-m-d', strtotime('+1Day'));
//        $coupon['date_end'] = date('Y-m-d', strtotime('+6Month'));
//        $coupon['uses_total'] = 1;
//        $coupon['uses_customer'] = 1;
//        $coupon['customer_id'] = 0;
//        $coupon['status'] = 1;
//        if (!$coupon->insert()) {
//            return 0;
//        }
//
//        return $coupon['coupon_id'];
//    }

//    private function generateCoupon($order_id, $discount = 20)
//    {
//        //insert coupon
//        $coupon_id = HtCoupon::model()->generateGiftCoupon($order_id, $discount);
//        if($coupon_id == 0) {
//            return false;
//        }
//
//        //insert coupon product //TODO:wenzi
//
//        $gift = new HtOrderGiftCoupon();
//        $gift['order_id'] = $order_id;
//        $gift['coupon_id'] = $coupon_id;
//        if (!$gift->insert()) {
//            return false;
//        }
//
//        return true;
//    }
//
//    public function needGiftCouponNum($product_id)
//    {
//        $discount_map = array(
//            '1328' => 20,
//            '1329' => 20,
//            '1486' => 30,
//            '1484' => 30,
//            '1490' => 30,
//            '1549' => 30,
//            '1550' => 30,
//            '1551' => 30,
//            '1552' => 50,
//            '1553' => 50,
//            '1554' => 50,
//            '1578' => 50,
//            '1579' => 50,
//            '1580' => 50,
//            '1581' => 50,
//            '1604' => 50,
//            '1605' => 50,
//            '1607' => 50,
//            '2470' => 20,
//            '2471' => 20,
//            '2466' => 20,
//
//        );
//        if (isset($discount_map[$product_id])) {
//            return $discount_map[$product_id];
//        } else {
//            return false;
//        }
//    }


}