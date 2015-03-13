<?php

/**
 * This is the model class for table "hc_order_passenger".
 *
 * The followings are the available columns in table 'hc_order_passenger':
 * @property integer $order_passenger_id
 * @property integer $order_id
 * @property integer $order_product_id
 * @property string $zh_name
 * @property string $en_name
 * @property integer $gender
 * @property string $birth_date
 * @property integer $age
 * @property string $nationality
 * @property string $passport_number
 * @property string $passport_type
 * @property string $passport_issue_date
 * @property string $passport_expire_date
 * @property string $passport_issue_place
 * @property string $taiwan_pass
 * @property string $taiwan_pass_expire_date
 * @property string $hk_pass
 * @property string $hk_pass_expire_date
 * @property string $mobile
 * @property string $email
 * @property integer $is_child
 * @property integer $child_age
 * @property string $arrival_date
 * @property string $arrival_flight_no
 * @property string $arrival_airport
 * @property string $arrival_flight_time
 * @property string $leave_date
 * @property string $leave_flight_no
 * @property string $leave_airport
 * @property string $leave_flight_time
 * @property string $food_preferences
 * @property string $entree
 * @property string $main_course
 * @property string $dessert
 * @property string $home_address
 * @property string $departure_hotel
 * @property string $departure_hotel_address
 * @property string $departure_hotel_time
 * @property string $setoff_hotel
 * @property string $return_hotel
 * @property string $hotle_booking_ref
 * @property string $dress_size
 * @property string $vehicle_model
 * @property integer $height
 * @property string $shipping_recipent
 * @property string $shipping_address
 * @property string $shipping_postcode
 * @property string $shipping_mobile
 * @property string $tour_time
 */
class HcOrderPassenger extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_order_passenger';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, order_product_id', 'required'),
			array('order_id, order_product_id, gender, age, is_child, child_age, height', 'numerical', 'integerOnly'=>true),
			array('zh_name, arrival_flight_no, leave_flight_no, food_preferences, departure_hotel_time, shipping_recipent, shipping_postcode', 'length', 'max'=>16),
			array('en_name, entree, main_course, dessert, departure_hotel_address', 'length', 'max'=>255),
			array('nationality', 'length', 'max'=>4),
			array('passport_number, passport_issue_place, taiwan_pass, hk_pass, mobile, arrival_airport, arrival_flight_time, leave_airport, leave_flight_time, hotle_booking_ref, vehicle_model, shipping_mobile, tour_time', 'length', 'max'=>32),
			array('passport_type', 'length', 'max'=>2),
			array('email, home_address', 'length', 'max'=>64),
			array('departure_hotel, setoff_hotel, return_hotel, shipping_address', 'length', 'max'=>128),
			array('dress_size', 'length', 'max'=>8),
			array('birth_date, passport_issue_date, passport_expire_date, taiwan_pass_expire_date, hk_pass_expire_date, arrival_date, leave_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('order_passenger_id, order_id, order_product_id, zh_name, en_name, gender, birth_date, age, nationality, passport_number, passport_type, passport_issue_date, passport_expire_date, passport_issue_place, taiwan_pass, taiwan_pass_expire_date, hk_pass, hk_pass_expire_date, mobile, email, is_child, child_age, arrival_date, arrival_flight_no, arrival_airport, arrival_flight_time, leave_date, leave_flight_no, leave_airport, leave_flight_time, food_preferences, entree, main_course, dessert, home_address, departure_hotel, departure_hotel_address, departure_hotel_time, setoff_hotel, return_hotel, hotle_booking_ref, dress_size, vehicle_model, height, shipping_recipent, shipping_address, shipping_postcode, shipping_mobile, tour_time', 'safe', 'on'=>'search'),
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
			'order_passenger_id' => 'Order Passenger',
			'order_id' => 'Order',
			'order_product_id' => 'Order Product',
			'zh_name' => 'Zh Name',
			'en_name' => '名字拼音，需要和护照名保持一致',
			'gender' => 'Gender',
			'birth_date' => 'Birth Date',
			'age' => 'Age',
			'nationality' => 'Nationality',
			'passport_number' => 'Passport Number',
			'passport_type' => 'Passport Type',
			'passport_issue_date' => 'Passport Issue Date',
			'passport_expire_date' => 'Passport Expire Date',
			'passport_issue_place' => 'Passport Issue Place',
			'taiwan_pass' => 'Taiwan Pass',
			'taiwan_pass_expire_date' => 'Taiwan Pass Expire Date',
			'hk_pass' => 'Hk Pass',
			'hk_pass_expire_date' => 'Hk Pass Expire Date',
			'mobile' => 'Mobile',
			'email' => 'Email',
			'is_child' => 'Is Child',
			'child_age' => 'Child Age',
			'arrival_date' => 'Arrival Date',
			'arrival_flight_no' => 'Arrival Flight No',
			'arrival_airport' => 'Arrival Airport',
			'arrival_flight_time' => 'Arrival Flight Time',
			'leave_date' => 'Leave Date',
			'leave_flight_no' => 'Leave Flight No',
			'leave_airport' => 'Leave Airport',
			'leave_flight_time' => 'Leave Flight Time',
			'food_preferences' => 'Food Preferences',
			'entree' => 'Entree',
			'main_course' => 'Main Course',
			'dessert' => 'Dessert',
			'home_address' => 'Home Address',
			'departure_hotel' => 'Departure Hotel',
			'departure_hotel_address' => 'Departure Hotel Address',
			'departure_hotel_time' => 'Departure Hotel Time',
			'setoff_hotel' => 'Setoff Hotel',
			'return_hotel' => 'Return Hotel',
			'hotle_booking_ref' => 'Hotle Booking Ref',
			'dress_size' => 'Dress Size',
			'vehicle_model' => 'Vehicle Model',
			'height' => '单位cm',
			'shipping_recipent' => 'Shipping Recipent',
			'shipping_address' => 'Shipping Address',
			'shipping_postcode' => 'Shipping Postcode',
			'shipping_mobile' => 'Shipping Mobile',
			'tour_time' => 'Tour Time',
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

		$criteria->compare('order_passenger_id',$this->order_passenger_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('order_product_id',$this->order_product_id);
		$criteria->compare('zh_name',$this->zh_name,true);
		$criteria->compare('en_name',$this->en_name,true);
		$criteria->compare('gender',$this->gender);
		$criteria->compare('birth_date',$this->birth_date,true);
		$criteria->compare('age',$this->age);
		$criteria->compare('nationality',$this->nationality,true);
		$criteria->compare('passport_number',$this->passport_number,true);
		$criteria->compare('passport_type',$this->passport_type,true);
		$criteria->compare('passport_issue_date',$this->passport_issue_date,true);
		$criteria->compare('passport_expire_date',$this->passport_expire_date,true);
		$criteria->compare('passport_issue_place',$this->passport_issue_place,true);
		$criteria->compare('taiwan_pass',$this->taiwan_pass,true);
		$criteria->compare('taiwan_pass_expire_date',$this->taiwan_pass_expire_date,true);
		$criteria->compare('hk_pass',$this->hk_pass,true);
		$criteria->compare('hk_pass_expire_date',$this->hk_pass_expire_date,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('is_child',$this->is_child);
		$criteria->compare('child_age',$this->child_age);
		$criteria->compare('arrival_date',$this->arrival_date,true);
		$criteria->compare('arrival_flight_no',$this->arrival_flight_no,true);
		$criteria->compare('arrival_airport',$this->arrival_airport,true);
		$criteria->compare('arrival_flight_time',$this->arrival_flight_time,true);
		$criteria->compare('leave_date',$this->leave_date,true);
		$criteria->compare('leave_flight_no',$this->leave_flight_no,true);
		$criteria->compare('leave_airport',$this->leave_airport,true);
		$criteria->compare('leave_flight_time',$this->leave_flight_time,true);
		$criteria->compare('food_preferences',$this->food_preferences,true);
		$criteria->compare('entree',$this->entree,true);
		$criteria->compare('main_course',$this->main_course,true);
		$criteria->compare('dessert',$this->dessert,true);
		$criteria->compare('home_address',$this->home_address,true);
		$criteria->compare('departure_hotel',$this->departure_hotel,true);
		$criteria->compare('departure_hotel_address',$this->departure_hotel_address,true);
		$criteria->compare('departure_hotel_time',$this->departure_hotel_time,true);
		$criteria->compare('setoff_hotel',$this->setoff_hotel,true);
		$criteria->compare('return_hotel',$this->return_hotel,true);
		$criteria->compare('hotle_booking_ref',$this->hotle_booking_ref,true);
		$criteria->compare('dress_size',$this->dress_size,true);
		$criteria->compare('vehicle_model',$this->vehicle_model,true);
		$criteria->compare('height',$this->height);
		$criteria->compare('shipping_recipent',$this->shipping_recipent,true);
		$criteria->compare('shipping_address',$this->shipping_address,true);
		$criteria->compare('shipping_postcode',$this->shipping_postcode,true);
		$criteria->compare('shipping_mobile',$this->shipping_mobile,true);
		$criteria->compare('tour_time',$this->tour_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcOrderPassenger the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
