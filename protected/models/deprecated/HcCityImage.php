<?php

/**
 * This is the model class for table "hc_city_image".
 *
 * The followings are the available columns in table 'hc_city_image':
 * @property integer $image_id
 * @property string $city_id
 * @property string $image
 * @property string $image_url
 * @property integer $sort_order
 * @property string $description
 * @property integer $width
 * @property integer $height
 * @property integer $changed
 */
class HcCityImage extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_city_image';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_id, image_url, description, width, height, changed', 'required'),
			array('sort_order, width, height, changed', 'numerical', 'integerOnly'=>true),
			array('city_id', 'length', 'max'=>4),
			array('image, image_url, description', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('image_id, city_id, image, image_url, sort_order, description, width, height, changed', 'safe', 'on'=>'search'),
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
			'image_id' => 'Image',
			'city_id' => 'City',
			'image' => 'Image',
			'image_url' => 'Image Url',
			'sort_order' => 'Sort Order',
			'description' => 'Description',
			'width' => 'Width',
			'height' => 'Height',
			'changed' => 'Changed',
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

		$criteria->compare('image_id',$this->image_id);
		$criteria->compare('city_id',$this->city_id,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('image_url',$this->image_url,true);
		$criteria->compare('sort_order',$this->sort_order);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('width',$this->width);
		$criteria->compare('height',$this->height);
		$criteria->compare('changed',$this->changed);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcCityImage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
