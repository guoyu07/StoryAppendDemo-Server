<?php

/**
 * This is the model class for table "ht_dandelion_pickup".
 *
 * The followings are the available columns in table 'ht_dandelion_pickup':
 * @property integer $pick_type
 * @property integer $did
 * @property integer $from_order_id
 * @property integer $coupon_id
 * @property integer $customer_id
 * @property string $pick_date
 * @property integer $used_order_id
 * @property string $used_date
 */
class HtDandelionPickup extends CActiveRecord
{
    const PT_DEFAULT = 0;
    const PT_BY_ORDER = 1;
    const PT_BY_PICKUP = 2;
    const PT_BY_WEIXIN = 3;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_dandelion_pickup';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('did, customer_id, pick_date, used_order_id, used_date', 'required'),
            array('did, customer_id, used_order_id, pick_type, from_order_id, coupon_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('did, customer_id, pick_date, used_order_id, used_date, pick_type, from_order_id, coupon_id', 'safe', 'on' => 'search'),
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
            'dandelion' => array(self::HAS_ONE, 'HtDandelion', '', 'on' => 'dandelion.did = dandelion_pickup.did'),
            'coupon' => array(self::HAS_ONE, 'HtCoupon', '', 'on' => 'coupon.coupon_id = dandelion_pickup.coupon_id'),
            'customer' => array(self::HAS_ONE, 'HtCustomer', '', 'on' => 'customer.customer_id = dandelion_pickup.customer_id'),
            'customer_third_weixin' => array(self::HAS_ONE, 'HtCustomerThird', '', 'on' => 'customer_third.customer_id = dandelion_pickup.customer_id and customer_third.otype=3'),
            'use_limit' => array(self::HAS_MANY, 'HtCouponUseLimit', '', 'on' => 'coupon.coupon_id = ul.coupon_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'pick_type' => 'Pick Type',
            'did' => 'Did',
            'from_order_id' => 'From Order ID',
            'coupon_id' => 'Coupon ID',
            'customer_id' => 'Customer',
            'pick_date' => '领取时间',
            'used_order_id' => '使用于订单',
            'used_date' => '使用时间',
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

        $criteria->compare('pick_type', $this->pick_type);
        $criteria->compare('did', $this->did);
        $criteria->compare('from_order_id', $this->from_order_id);
        $criteria->compare('coupon_id', $this->coupon_id);
        $criteria->compare('customer_id', $this->customer_id);
        $criteria->compare('pick_date', $this->pick_date, true);
        $criteria->compare('used_order_id', $this->used_order_id);
        $criteria->compare('used_date', $this->used_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtDandelionPickup the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'dandelion_pickup',
        );
    }

    public function addNew($coupon_id, $customer_id, $pick_type = 0, $did = 0, $from_order_id = 0)
    {
        $item = new HtDandelionPickup();
        $item['coupon_id'] = $coupon_id;
        $item['customer_id'] = $customer_id;
        $item['pick_type'] = $pick_type;
        $item['did'] = $did;
        $item['from_order_id'] = $from_order_id;
        $item['pick_date'] = date('Y-m-d H:i:s');
        $item['used_order_id'] = 0;
        $item['used_date'] = '0000-00-00';

        return $item->insert();
    }
}
