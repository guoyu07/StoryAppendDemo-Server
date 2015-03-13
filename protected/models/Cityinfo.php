<?php

/**
 * This is the model class for table "cityinfo".
 *
 * The followings are the available columns in table 'cityinfo':
 * @property integer $cityinfo_id
 * @property integer $city_id
 * @property integer $type
 * @property integer $category_id
 * @property string $title
 * @property string $image_url
 * @property string $real_url
 * @property string $description
 * @property string $url
 * @property string $source
 * @property string $source_url
 * @property string $source_icon
 * @property string $insert_time
 */
class Cityinfo extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cityinfo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('category_id, real_url, description, source_icon, insert_time', 'required'),
			array('city_id, type, category_id', 'numerical', 'integerOnly'=>true),
			array('title, image_url, url, source, source_url, source_icon', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('cityinfo_id, city_id, type, category_id, title, image_url, real_url, description, url, source, source_url, source_icon, insert_time', 'safe', 'on'=>'search'),
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
			'cityinfo_id' => 'Cityinfo',
			'city_id' => 'City',
			'type' => 'Type',
			'category_id' => 'Category',
			'title' => 'Title',
			'image_url' => 'Image Url',
			'real_url' => 'Real Url',
			'description' => 'Description',
			'url' => 'Url',
			'source' => 'Source',
			'source_url' => 'Source Url',
			'source_icon' => 'Source Icon',
			'insert_time' => 'Insert Time',
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

		$criteria->compare('cityinfo_id',$this->cityinfo_id);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('image_url',$this->image_url,true);
		$criteria->compare('real_url',$this->real_url,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('source',$this->source,true);
		$criteria->compare('source_url',$this->source_url,true);
		$criteria->compare('source_icon',$this->source_icon,true);
		$criteria->compare('insert_time',$this->insert_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Cityinfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
