<?php

/**
 * This is the model class for table "hc_supplier_order".
 *
 * The followings are the available columns in table 'hc_supplier_order':
 * @property integer $id
 * @property integer $product_id
 * @property integer $manufacturer_id
 * @property string $booking_reference
 * @property string $tour_date
 * @property string $city_code
 * @property string $item_id
 * @property string $special_code
 * @property string $confirmation_ref
 * @property string $additional_info
 * @property integer $current_status
 */
class HcSupplierOrder extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_supplier_order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, manufacturer_id, tour_date, additional_info', 'required'),
			array('product_id, manufacturer_id, current_status', 'numerical', 'integerOnly'=>true),
			array('booking_reference, special_code', 'length', 'max'=>32),
			array('city_code', 'length', 'max'=>4),
			array('item_id', 'length', 'max'=>128),
			array('confirmation_ref', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, product_id, manufacturer_id, booking_reference, tour_date, city_code, item_id, special_code, confirmation_ref, additional_info, current_status', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'product_id' => 'Product',
			'manufacturer_id' => 'Manufacturer',
			'booking_reference' => 'Booking Reference',
			'tour_date' => 'Tour Date',
			'city_code' => 'City Code',
			'item_id' => 'Item',
			'special_code' => 'Special Code',
			'confirmation_ref' => 'Confirmation Ref',
			'additional_info' => 'Additional Info',
			'current_status' => 'Current Status',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('manufacturer_id',$this->manufacturer_id);
		$criteria->compare('booking_reference',$this->booking_reference,true);
		$criteria->compare('tour_date',$this->tour_date,true);
		$criteria->compare('city_code',$this->city_code,true);
		$criteria->compare('item_id',$this->item_id,true);
		$criteria->compare('special_code',$this->special_code,true);
		$criteria->compare('confirmation_ref',$this->confirmation_ref,true);
		$criteria->compare('additional_info',$this->additional_info,true);
		$criteria->compare('current_status',$this->current_status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcSupplierOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
