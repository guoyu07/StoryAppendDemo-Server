<?php

/**
 * This is the model class for table "gta_sightseeing".
 *
 * The followings are the available columns in table 'gta_sightseeing':
 * @property string $item_id
 * @property string $city_code
 * @property string $types
 * @property string $categories
 * @property string $duration
 * @property string $name
 * @property string $summary
 * @property string $image_url
 * @property string $language
 * @property string $description
 * @property string $includes
 * @property string $please_note
 * @property string $departure_points
 * @property string $additional_information
 * @property string $closed_dates
 * @property integer $language_id
 * @property integer $status
 */
class GtaSightseeing extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gta_sightseeing';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('item_id, city_code, name, summary, image_url, language, description, includes, please_note, departure_points, additional_information, closed_dates, language_id', 'required'),
			array('language_id, status', 'numerical', 'integerOnly'=>true),
			array('item_id', 'length', 'max'=>16),
			array('city_code', 'length', 'max'=>4),
			array('types', 'length', 'max'=>32),
			array('categories', 'length', 'max'=>256),
			array('duration', 'length', 'max'=>64),
			array('name, summary, image_url', 'length', 'max'=>255),
			array('language', 'length', 'max'=>3),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('item_id, city_code, types, categories, duration, name, summary, image_url, language, description, includes, please_note, departure_points, additional_information, closed_dates, language_id, status', 'safe', 'on'=>'search'),
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
			'types' => 'Types',
			'categories' => 'Categories',
			'duration' => 'Duration',
			'name' => 'Name',
			'summary' => 'Summary',
			'image_url' => 'Image Url',
			'language' => 'Language',
			'description' => 'Description',
			'includes' => 'Includes',
			'please_note' => 'Please Note',
			'departure_points' => 'Departure Points',
			'additional_information' => 'Additional Information',
			'closed_dates' => 'Closed Dates',
			'language_id' => 'Language',
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

		$criteria=new CDbCriteria;

		$criteria->compare('item_id',$this->item_id,true);
		$criteria->compare('city_code',$this->city_code,true);
		$criteria->compare('types',$this->types,true);
		$criteria->compare('categories',$this->categories,true);
		$criteria->compare('duration',$this->duration,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('summary',$this->summary,true);
		$criteria->compare('image_url',$this->image_url,true);
		$criteria->compare('language',$this->language,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('includes',$this->includes,true);
		$criteria->compare('please_note',$this->please_note,true);
		$criteria->compare('departure_points',$this->departure_points,true);
		$criteria->compare('additional_information',$this->additional_information,true);
		$criteria->compare('closed_dates',$this->closed_dates,true);
		$criteria->compare('language_id',$this->language_id);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GtaSightseeing the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
