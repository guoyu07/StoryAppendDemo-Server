<?php

/**
 * This is the model class for table "gta_order".
 *
 * The followings are the available columns in table 'gta_order':
 * @property string $booking_reference
 * @property string $api_reference
 * @property string $tour_date
 * @property string $city_code
 * @property string $item_id
 * @property string $special_code
 * @property string $departure_point
 * @property string $departure_time
 * @property string $language
 * @property string $language_list_code
 * @property string $gross
 * @property string $commission
 * @property string $nett
 * @property string $current_status
 * @property string $response_reference
 * @property string $confirmation_ref
 * @property string $payable_by
 * @property string $supplier_code
 * @property string $supplier_title
 */
class GtaOrder extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gta_order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('booking_reference, tour_date, city_code, item_id, current_status', 'required'),
			array('booking_reference', 'length', 'max'=>30),
			array('api_reference, departure_point, confirmation_ref', 'length', 'max'=>64),
			array('city_code, language', 'length', 'max'=>4),
			array('item_id, response_reference, supplier_code', 'length', 'max'=>128),
			array('special_code, language_list_code', 'length', 'max'=>32),
			array('departure_time', 'length', 'max'=>5),
			array('gross, commission, nett', 'length', 'max'=>10),
			array('current_status', 'length', 'max'=>3),
			array('payable_by, supplier_title', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('booking_reference, api_reference, tour_date, city_code, item_id, special_code, departure_point, departure_time, language, language_list_code, gross, commission, nett, current_status, response_reference, confirmation_ref, payable_by, supplier_code, supplier_title', 'safe', 'on'=>'search'),
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
			'booking_reference' => 'Booking Reference',
			'api_reference' => 'Api Reference',
			'tour_date' => 'Tour Date',
			'city_code' => 'City Code',
			'item_id' => 'Item',
			'special_code' => 'Special Code',
			'departure_point' => 'Departure Point',
			'departure_time' => 'Departure Time',
			'language' => 'Language',
			'language_list_code' => 'Language List Code',
			'gross' => 'Gross',
			'commission' => 'Commission',
			'nett' => 'Nett',
			'current_status' => 'Current Status',
			'response_reference' => 'Response Reference',
			'confirmation_ref' => 'Confirmation Ref',
			'payable_by' => 'Payable By',
			'supplier_code' => 'Supplier Code',
			'supplier_title' => 'Supplier Title',
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

		$criteria->compare('booking_reference',$this->booking_reference,true);
		$criteria->compare('api_reference',$this->api_reference,true);
		$criteria->compare('tour_date',$this->tour_date,true);
		$criteria->compare('city_code',$this->city_code,true);
		$criteria->compare('item_id',$this->item_id,true);
		$criteria->compare('special_code',$this->special_code,true);
		$criteria->compare('departure_point',$this->departure_point,true);
		$criteria->compare('departure_time',$this->departure_time,true);
		$criteria->compare('language',$this->language,true);
		$criteria->compare('language_list_code',$this->language_list_code,true);
		$criteria->compare('gross',$this->gross,true);
		$criteria->compare('commission',$this->commission,true);
		$criteria->compare('nett',$this->nett,true);
		$criteria->compare('current_status',$this->current_status,true);
		$criteria->compare('response_reference',$this->response_reference,true);
		$criteria->compare('confirmation_ref',$this->confirmation_ref,true);
		$criteria->compare('payable_by',$this->payable_by,true);
		$criteria->compare('supplier_code',$this->supplier_code,true);
		$criteria->compare('supplier_title',$this->supplier_title,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GtaOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
