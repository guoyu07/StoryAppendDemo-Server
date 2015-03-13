<?php

/**
 * This is the model class for table "hc_product_image".
 *
 * The followings are the available columns in table 'hc_product_image':
 * @property integer $product_image_id
 * @property integer $product_id
 * @property string $image
 * @property string $image_url
 * @property integer $sort_order
 * @property string $name
 * @property string $short_desc
 * @property string $web_link
 * @property integer $width
 * @property integer $height
 * @property integer $changed
 */
class HcProductImage extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_product_image';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, image_url, web_link, width, height, changed', 'required'),
			array('product_id, sort_order, width, height, changed', 'numerical', 'integerOnly'=>true),
			array('image, image_url, web_link', 'length', 'max'=>255),
			array('name', 'length', 'max'=>64),
			array('short_desc', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_image_id, product_id, image, image_url, sort_order, name, short_desc, web_link, width, height, changed', 'safe', 'on'=>'search'),
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
			'product_image_id' => 'Product Image',
			'product_id' => 'Product',
			'image' => 'Image',
			'image_url' => 'Image Url',
			'sort_order' => 'Sort Order',
			'name' => 'Name',
			'short_desc' => 'Short Desc',
			'web_link' => 'Web Link',
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

		$criteria->compare('product_image_id',$this->product_image_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('image_url',$this->image_url,true);
		$criteria->compare('sort_order',$this->sort_order);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('short_desc',$this->short_desc,true);
		$criteria->compare('web_link',$this->web_link,true);
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
	 * @return HcProductImage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
