<?php

/**
 * This is the model class for table "hc_product_special".
 *
 * The followings are the available columns in table 'hc_product_special':
 * @property integer $product_special_id
 * @property integer $product_id
 * @property integer $customer_group_id
 * @property string $reseller
 * @property string $topic
 * @property integer $priority
 * @property string $price
 * @property string $child_price
 * @property string $date_start
 * @property string $date_end
 */
class HcProductSpecial extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_product_special';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, customer_group_id, reseller, topic, child_price', 'required'),
			array('product_id, customer_group_id, priority', 'numerical', 'integerOnly'=>true),
			array('reseller', 'length', 'max'=>64),
			array('topic', 'length', 'max'=>128),
			array('price', 'length', 'max'=>15),
			array('child_price', 'length', 'max'=>11),
			array('date_start, date_end', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_special_id, product_id, customer_group_id, reseller, topic, priority, price, child_price, date_start, date_end', 'safe', 'on'=>'search'),
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
			'product_special_id' => 'Product Special',
			'product_id' => 'Product',
			'customer_group_id' => 'Customer Group',
			'reseller' => 'Reseller',
			'topic' => 'Topic',
			'priority' => 'Priority',
			'price' => 'Price',
			'child_price' => 'Child Price',
			'date_start' => 'Date Start',
			'date_end' => 'Date End',
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

		$criteria->compare('product_special_id',$this->product_special_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('customer_group_id',$this->customer_group_id);
		$criteria->compare('reseller',$this->reseller,true);
		$criteria->compare('topic',$this->topic,true);
		$criteria->compare('priority',$this->priority);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('child_price',$this->child_price,true);
		$criteria->compare('date_start',$this->date_start,true);
		$criteria->compare('date_end',$this->date_end,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcProductSpecial the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
