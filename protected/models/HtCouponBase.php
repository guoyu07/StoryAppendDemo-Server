<?php

/**
 * This is the model class for table "ht_coupon_base".
 *
 * The followings are the available columns in table 'ht_coupon_base':
 * @property integer $coupon_base_id
 * @property string $name
 * @property integer $use_type
 * @property string $type
 * @property string $discount
 * @property integer $available_count
 * @property integer $logged
 * @property integer $shipping
 * @property string $total
 * @property string $date_start
 * @property string $date_end
 * @property string $city_name
 * @property string $avail_date
 * @property string $special_price
 * @property integer $product_id
 * @property string $product_apply_to
 * @property string $product_not_apply_to
 * @property string $image
 * @property integer $uses_total
 * @property string $uses_customer
 * @property integer $status
 * @property string $date_added
 */
class HtCouponBase extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_coupon_base';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, use_type, type, discount, available_count, logged, shipping, total, city_name, avail_date, special_price, product_id, image, uses_total, uses_customer, status', 'required'),
            array('use_type, available_count, logged, shipping, product_id, uses_total, status', 'numerical', 'integerOnly' => true),
            array('name, image', 'length', 'max' => 128),
            array('type', 'length', 'max' => 1),
            array('discount, total', 'length', 'max' => 15),
            array('city_name, avail_date', 'length', 'max' => 30),
            array('special_price, uses_customer', 'length', 'max' => 11),
            array('product_apply_to, product_not_apply_to', 'length', 'max' => 2048),
            array('date_start, date_end, date_added', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('coupon_base_id, name, use_type, type, discount, available_count, logged, shipping, total, date_start, date_end, city_name, avail_date, special_price, product_id, product_apply_to, product_not_apply_to, image, uses_total, uses_customer, status, date_added', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'coupon_base_id' => 'Coupon Base',
            'name' => 'Name',
            'use_type' => 'Use Type',
            'type' => 'Type',
            'discount' => 'Discount',
            'available_count' => 'Available Count',
            'logged' => 'Logged',
            'shipping' => 'Shipping',
            'total' => 'Total',
            'date_start' => 'Date Start',
            'date_end' => 'Date End',
            'city_name' => 'City Name',
            'avail_date' => 'Avail Date',
            'special_price' => 'Special Price',
            'product_id' => 'Product',
            'product_apply_to' => 'Product Apply To',
            'product_not_apply_to' => 'Product Not Apply To',
            'image' => 'Image',
            'uses_total' => 'Uses Total',
            'uses_customer' => 'Uses Customer',
            'status' => 'Status',
            'date_added' => 'Date Added',
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

        $criteria->compare('coupon_base_id', $this->coupon_base_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('use_type', $this->use_type);
        $criteria->compare('type', $this->type, true);
        $criteria->compare('discount', $this->discount, true);
        $criteria->compare('available_count', $this->available_count);
        $criteria->compare('logged', $this->logged);
        $criteria->compare('shipping', $this->shipping);
        $criteria->compare('total', $this->total, true);
        $criteria->compare('date_start', $this->date_start, true);
        $criteria->compare('date_end', $this->date_end, true);
        $criteria->compare('city_name', $this->city_name, true);
        $criteria->compare('avail_date', $this->avail_date, true);
        $criteria->compare('special_price', $this->special_price, true);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('product_apply_to', $this->product_apply_to, true);
        $criteria->compare('product_not_apply_to', $this->product_not_apply_to, true);
        $criteria->compare('image', $this->image, true);
        $criteria->compare('uses_total', $this->uses_total);
        $criteria->compare('uses_customer', $this->uses_customer, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('date_added', $this->date_added, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtCouponBase the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function generateCouponByBase($coupon_base_id)
    {
        $coupon_base = $this->findByPk($coupon_base_id);
        if (empty($coupon_base)) {
            return array(false, 0, '');
        } else {
            //  check available_count first
            $available_count = $coupon_base['available_count'];
            if ($available_count <= 0) {
                return array(false, 0, '');
            }

            $coupon = new HtCoupon();
            ModelHelper::fillItem($coupon, $coupon_base, array('name', 'use_type', 'type', 'discount', 'logged', 'shipping',
                'total', 'date_start', 'date_end', 'use_total', 'uses_customer', 'status'));
            $coupon['code'] = substr(md5($coupon['name'] . date('Y-m-d') . mt_rand()), 0, 10);
            $coupon['date_added'] = date('Y-m-d H:i:s');

            $result = $coupon->insert();

            if ($result) {
                $coupon_id = $coupon['coupon_id'];
                //  handle coupon product
                $product_apply_to = explode(',', $coupon_base['product_apply_to']);
                if (!empty($product_apply_to)) {
                    foreach ($product_apply_to as $product_id) {
                        if(empty($product_id)) continue;

                        HtCouponUseLimit::addNew( array('coupon_id' => $coupon_id, 'id' => $product_id,'limit_type'=>1,'valid_type'=>1));
                    }
                }

                $product_not_apply_to = explode(',', $coupon_base['product_not_apply_to']);
                if (!empty($product_not_apply_to)) {
                    foreach ($product_not_apply_to as $product_id) {
                        if(empty($product_id)) continue;
                        HtCouponUseLimit::addNew( array('coupon_id' => $coupon_id, 'id' => $product_id,'limit_type'=>0,'valid_type'=>1));
                    }
                }
            }

            return array($result, $coupon['coupon_id'], $coupon['code']);
        }
    }
}
