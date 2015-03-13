<?php

/**
 * This is the model class for table "hc_product_option_value".
 *
 * The followings are the available columns in table 'hc_product_option_value':
 * @property integer $product_option_value_id
 * @property integer $product_option_id
 * @property integer $product_id
 * @property integer $option_id
 * @property integer $option_value_id
 * @property integer $quantity
 * @property integer $subtract
 * @property string $price
 * @property string $child_price
 * @property string $price_prefix
 * @property integer $points
 * @property string $points_prefix
 * @property string $weight
 * @property string $weight_prefix
 * @property string $languages
 * @property string $language_list_code
 * @property integer $is_delete
 */
class HcProductOptionValue extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_product_option_value';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_option_id, product_id, option_id, option_value_id, quantity, subtract, price, price_prefix, points, points_prefix, weight, weight_prefix', 'required'),
			array('product_option_id, product_id, option_id, option_value_id, quantity, subtract, points, is_delete', 'numerical', 'integerOnly'=>true),
			array('price, weight', 'length', 'max'=>15),
			array('child_price', 'length', 'max'=>10),
			array('price_prefix, points_prefix, weight_prefix', 'length', 'max'=>1),
			array('languages, language_list_code', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_option_value_id, product_option_id, product_id, option_id, option_value_id, quantity, subtract, price, child_price, price_prefix, points, points_prefix, weight, weight_prefix, languages, language_list_code, is_delete', 'safe', 'on'=>'search'),
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
			'product_option_value_id' => 'Product Option Value',
			'product_option_id' => 'Product Option',
			'product_id' => 'Product',
			'option_id' => 'Option',
			'option_value_id' => 'Option Value',
			'quantity' => 'Quantity',
			'subtract' => 'Subtract',
			'price' => 'Price',
			'child_price' => 'Child Price',
			'price_prefix' => 'Price Prefix',
			'points' => 'Points',
			'points_prefix' => 'Points Prefix',
			'weight' => 'Weight',
			'weight_prefix' => 'Weight Prefix',
			'languages' => 'Languages',
			'language_list_code' => 'Language List Code',
			'is_delete' => 'Is Delete',
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

		$criteria->compare('product_option_value_id',$this->product_option_value_id);
		$criteria->compare('product_option_id',$this->product_option_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('option_id',$this->option_id);
		$criteria->compare('option_value_id',$this->option_value_id);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('subtract',$this->subtract);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('child_price',$this->child_price,true);
		$criteria->compare('price_prefix',$this->price_prefix,true);
		$criteria->compare('points',$this->points);
		$criteria->compare('points_prefix',$this->points_prefix,true);
		$criteria->compare('weight',$this->weight,true);
		$criteria->compare('weight_prefix',$this->weight_prefix,true);
		$criteria->compare('languages',$this->languages,true);
		$criteria->compare('language_list_code',$this->language_list_code,true);
		$criteria->compare('is_delete',$this->is_delete);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcProductOptionValue the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
