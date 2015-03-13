<?php

/**
 * This is the model class for table "communication_info".
 *
 * The followings are the available columns in table 'communication_info':
 * @property integer $ci_id
 * @property integer $city_id
 * @property string $title
 * @property string $description
 * @property string $image
 * @property string $image_url
 * @property integer $insert_time
 * @property integer $order
 * @property integer $is_del
 * @property string $price
 * @property string $child_price
 * @property string $currency
 */
class CommunicationInfo extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'communication_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_id, title, description, image, image_url, insert_time, price, child_price', 'required'),
			array('city_id, insert_time, order, is_del', 'numerical', 'integerOnly'=>true),
			array('title, image', 'length', 'max'=>255),
			array('price, child_price', 'length', 'max'=>10),
			array('currency', 'length', 'max'=>3),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ci_id, city_id, title, description, image, image_url, insert_time, order, is_del, price, child_price, currency', 'safe', 'on'=>'search'),
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
			'ci_id' => 'Ci',
			'city_id' => 'City',
			'title' => 'Title',
			'description' => 'Description',
			'image' => 'Image',
			'image_url' => 'Image Url',
			'insert_time' => 'Insert Time',
			'order' => 'Order',
			'is_del' => 'Is Del',
			'price' => 'Price',
			'child_price' => 'Child Price',
			'currency' => 'Currency',
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

		$criteria->compare('ci_id',$this->ci_id);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('image_url',$this->image_url,true);
		$criteria->compare('insert_time',$this->insert_time);
		$criteria->compare('order',$this->order);
		$criteria->compare('is_del',$this->is_del);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('child_price',$this->child_price,true);
		$criteria->compare('currency',$this->currency,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CommunicationInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
