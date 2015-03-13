<?php

/**
 * This is the model class for table "gta_sightseeing_hotel".
 *
 * The followings are the available columns in table 'gta_sightseeing_hotel':
 * @property integer $hotel_id
 * @property string $city_code
 * @property string $hotel_code
 * @property string $hotel_name
 * @property string $locations
 * @property integer $language_id
 */
class GtaSightseeingHotel extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gta_sightseeing_hotel';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_code, hotel_code, hotel_name, locations, language_id', 'required'),
			array('language_id', 'numerical', 'integerOnly'=>true),
			array('city_code, hotel_code', 'length', 'max'=>8),
			array('hotel_name', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('hotel_id, city_code, hotel_code, hotel_name, locations, language_id', 'safe', 'on'=>'search'),
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
			'hotel_id' => 'Hotel',
			'city_code' => 'City Code',
			'hotel_code' => 'Hotel Code',
			'hotel_name' => 'Hotel Name',
			'locations' => 'Locations',
			'language_id' => 'Language',
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

		$criteria->compare('hotel_id',$this->hotel_id);
		$criteria->compare('city_code',$this->city_code,true);
		$criteria->compare('hotel_code',$this->hotel_code,true);
		$criteria->compare('hotel_name',$this->hotel_name,true);
		$criteria->compare('locations',$this->locations,true);
		$criteria->compare('language_id',$this->language_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GtaSightseeingHotel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
