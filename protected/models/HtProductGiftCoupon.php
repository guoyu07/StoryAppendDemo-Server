<?php

/**
 * This is the model class for table "ht_product_gift_coupon".
 *
 * The followings are the available columns in table 'ht_product_gift_coupon':
 * @property integer $id
 * @property integer $product_id
 * @property integer $coupon_id
 * @property integer $quantity
 * @property integer $date_type
 * @property string $date_start
 * @property string $date_end
 * @property string $start_offset
 * @property string $end_range
 * @property integer $customer_limit
 * @property integer $status
 */
class HtProductGiftCoupon extends CActiveRecord
{
    const STATUS_INVALID = 0;
    const STATUS_VALID = 1;

    const DATE_TYPE_ABSOLUTE = 0;
    const DATE_TYPE_RELATIVE = 1;

    const CUSTOMER_EVERYONE = 0;
    const CUSTOMER_SELFONLY = 1;

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductGiftCoupon the static model class
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
        return 'ht_product_gift_coupon';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, coupon_id, date_start, date_end', 'required'),
            array('product_id, coupon_id, quantity, date_type, customer_limit, status', 'numerical', 'integerOnly' => true),
            array('start_offset, end_range', 'length', 'max' => 8),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, product_id, coupon_id, quantity, date_type, date_start, date_end, start_offset, end_range, customer_limit, status', 'safe', 'on' => 'search'),
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
            'template_coupon' => array(self::HAS_ONE, 'HtCoupon', '','on'=>'pgc.coupon_id = coupon.coupon_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'product_id' => 'Product',
            'coupon_id' => 'Coupon',
            'quantity' => 'Quantity',
            'date_type' => 'Date Type',
            'date_start' => 'Date Start',
            'date_end' => 'Date End',
            'start_offset' => 'Start Offset',
            'end_range' => 'End Range',
            'customer_limit' => 'Customer Limit',
            'status' => 'Status',
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

        $criteria->compare('id', $this->id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('coupon_id', $this->coupon_id);
        $criteria->compare('quantity', $this->quantity);
        $criteria->compare('date_type', $this->date_type);
        $criteria->compare('date_start', $this->date_start, true);
        $criteria->compare('date_end', $this->date_end, true);
        $criteria->compare('start_offset', $this->start_offset, true);
        $criteria->compare('end_range', $this->end_range, true);
        $criteria->compare('customer_limit', $this->customer_limit);
        $criteria->compare('status', $this->status);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'pgc',
        );
    }

}
