<?php

/**
 * This is the model class for table "iventure_pass".
 *
 * The followings are the available columns in table 'iventure_pass':
 * @property string $item_id
 * @property string $city_code
 * @property string $name
 * @property string $summary
 * @property string $image_url
 * @property string $description
 * @property string $includes
 * @property string $additional_information
 * @property string $closed_dates
 * @property string $exchange_points
 * @property string $exchange_duration
 * @property string $use_duration
 * @property integer $language_id
 */
class IventurePass extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'iventure_pass';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('item_id, description, includes, additional_information, exchange_points', 'required'),
			array('language_id', 'numerical', 'integerOnly'=>true),
			array('item_id', 'length', 'max'=>16),
			array('city_code', 'length', 'max'=>4),
			array('name, summary, image_url, closed_dates, exchange_duration, use_duration', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('item_id, city_code, name, summary, image_url, description, includes, additional_information, closed_dates, exchange_points, exchange_duration, use_duration, language_id', 'safe', 'on'=>'search'),
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
			'item_id' => 'Item',
			'city_code' => 'City Code',
			'name' => 'Name',
			'summary' => 'Summary',
			'image_url' => 'Image Url',
			'description' => 'Description',
			'includes' => 'Includes',
			'additional_information' => 'Additional Information',
			'closed_dates' => 'Closed Dates',
			'exchange_points' => 'Exchange Points',
			'exchange_duration' => 'Exchange Duration',
			'use_duration' => 'Use Duration',
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

		$criteria->compare('item_id',$this->item_id,true);
		$criteria->compare('city_code',$this->city_code,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('summary',$this->summary,true);
		$criteria->compare('image_url',$this->image_url,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('includes',$this->includes,true);
		$criteria->compare('additional_information',$this->additional_information,true);
		$criteria->compare('closed_dates',$this->closed_dates,true);
		$criteria->compare('exchange_points',$this->exchange_points,true);
		$criteria->compare('exchange_duration',$this->exchange_duration,true);
		$criteria->compare('use_duration',$this->use_duration,true);
		$criteria->compare('language_id',$this->language_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return IventurePass the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
