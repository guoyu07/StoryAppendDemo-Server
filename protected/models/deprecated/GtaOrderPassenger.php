<?php

/**
 * This is the model class for table "gta_order_passenger".
 *
 * The followings are the available columns in table 'gta_order_passenger':
 * @property string $booking_reference
 * @property integer $passenger_id
 * @property string $zh_name
 * @property string $en_name
 * @property string $passport_number
 * @property integer $is_child
 * @property integer $child_age
 */
class GtaOrderPassenger extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gta_order_passenger';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('booking_reference, passenger_id, en_name', 'required'),
			array('passenger_id, is_child, child_age', 'numerical', 'integerOnly'=>true),
			array('booking_reference', 'length', 'max'=>30),
			array('zh_name', 'length', 'max'=>16),
			array('en_name', 'length', 'max'=>255),
			array('passport_number', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('booking_reference, passenger_id, zh_name, en_name, passport_number, is_child, child_age', 'safe', 'on'=>'search'),
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
			'passenger_id' => 'Passenger',
			'zh_name' => 'Zh Name',
			'en_name' => 'En Name',
			'passport_number' => 'Passport Number',
			'is_child' => 'Is Child',
			'child_age' => 'Child Age',
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
		$criteria->compare('passenger_id',$this->passenger_id);
		$criteria->compare('zh_name',$this->zh_name,true);
		$criteria->compare('en_name',$this->en_name,true);
		$criteria->compare('passport_number',$this->passport_number,true);
		$criteria->compare('is_child',$this->is_child);
		$criteria->compare('child_age',$this->child_age);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GtaOrderPassenger the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
