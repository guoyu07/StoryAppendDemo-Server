<?php

/**
 * This is the model class for table "hc_product_price_plan".
 *
 * The followings are the available columns in table 'hc_product_price_plan':
 * @property integer $product_id
 * @property string $from_date
 * @property string $to_date
 * @property string $price
 * @property string $age_range
 * @property string $child_price
 * @property string $child_age_range
 * @property string $currency
 */
class HcProductPricePlan extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_product_price_plan';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, price, age_range, child_price, child_age_range, currency', 'required'),
			array('product_id', 'numerical', 'integerOnly'=>true),
			array('price, child_price', 'length', 'max'=>10),
			array('age_range, child_age_range', 'length', 'max'=>64),
			array('currency', 'length', 'max'=>3),
			array('from_date, to_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_id, from_date, to_date, price, age_range, child_price, child_age_range, currency', 'safe', 'on'=>'search'),
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
			'product_id' => 'Product',
			'from_date' => 'From Date',
			'to_date' => 'To Date',
			'price' => 'Price',
			'age_range' => 'Age Range',
			'child_price' => 'Child Price',
			'child_age_range' => 'Child Age Range',
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

		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('from_date',$this->from_date,true);
		$criteria->compare('to_date',$this->to_date,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('age_range',$this->age_range,true);
		$criteria->compare('child_price',$this->child_price,true);
		$criteria->compare('child_age_range',$this->child_age_range,true);
		$criteria->compare('currency',$this->currency,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcProductPricePlan the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
