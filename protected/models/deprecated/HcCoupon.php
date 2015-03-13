<?php

/**
 * This is the model class for table "hc_coupon".
 *
 * The followings are the available columns in table 'hc_coupon':
 * @property integer $coupon_id
 * @property string $name
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
 */
class HcCoupon extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_coupon';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, code, use_type, type, discount, logged, shipping, total, product_min, product_max, uses_total, uses_customer, status, used_total', 'required'),
			array('use_type, logged, shipping, product_min, product_max, uses_total, customer_id, status, used_total', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>128),
			array('code', 'length', 'max'=>10),
			array('type', 'length', 'max'=>1),
			array('discount, total', 'length', 'max'=>15),
			array('uses_customer', 'length', 'max'=>11),
			array('date_start, date_end, date_added', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('coupon_id, name, code, use_type, type, discount, logged, shipping, total, product_min, product_max, date_start, date_end, uses_total, uses_customer, customer_id, status, date_added, used_total', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'coupon_id' => 'Coupon',
			'name' => 'Name',
			'code' => 'Code',
			'use_type' => 'Use Type',
			'type' => 'Type',
			'discount' => 'Discount',
			'logged' => 'Logged',
			'shipping' => 'Shipping',
			'total' => 'Total',
			'product_min' => 'Product Min',
			'product_max' => 'Product Max',
			'date_start' => 'Date Start',
			'date_end' => 'Date End',
			'uses_total' => 'Uses Total',
			'uses_customer' => 'Uses Customer',
			'customer_id' => 'Customer',
			'status' => 'Status',
			'date_added' => 'Date Added',
			'used_total' => 'Used Total',
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

		$criteria=new CDbCriteria;

		$criteria->compare('coupon_id',$this->coupon_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('use_type',$this->use_type);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('discount',$this->discount,true);
		$criteria->compare('logged',$this->logged);
		$criteria->compare('shipping',$this->shipping);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('product_min',$this->product_min);
		$criteria->compare('product_max',$this->product_max);
		$criteria->compare('date_start',$this->date_start,true);
		$criteria->compare('date_end',$this->date_end,true);
		$criteria->compare('uses_total',$this->uses_total);
		$criteria->compare('uses_customer',$this->uses_customer,true);
		$criteria->compare('customer_id',$this->customer_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('date_added',$this->date_added,true);
		$criteria->compare('used_total',$this->used_total);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcCoupon the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
