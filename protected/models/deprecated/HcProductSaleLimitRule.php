<?php

/**
 * This is the model class for table "hc_product_sale_limit_rule".
 *
 * The followings are the available columns in table 'hc_product_sale_limit_rule':
 * @property integer $product_id
 * @property integer $sale_type
 * @property integer $min_num
 * @property integer $max_num
 * @property integer $child_only
 * @property integer $min_adult_num
 * @property integer $adult_in_set
 * @property integer $child_in_set
 */
class HcProductSaleLimitRule extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_product_sale_limit_rule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id', 'required'),
			array('product_id, sale_type, min_num, max_num, child_only, min_adult_num, adult_in_set, child_in_set', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_id, sale_type, min_num, max_num, child_only, min_adult_num, adult_in_set, child_in_set', 'safe', 'on'=>'search'),
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
			'sale_type' => 'Sale Type',
			'min_num' => 'Min Num',
			'max_num' => 'Max Num',
			'child_only' => 'Child Only',
			'min_adult_num' => 'Min Adult Num',
			'adult_in_set' => 'Adult In Set',
			'child_in_set' => 'Child In Set',
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
		$criteria->compare('sale_type',$this->sale_type);
		$criteria->compare('min_num',$this->min_num);
		$criteria->compare('max_num',$this->max_num);
		$criteria->compare('child_only',$this->child_only);
		$criteria->compare('min_adult_num',$this->min_adult_num);
		$criteria->compare('adult_in_set',$this->adult_in_set);
		$criteria->compare('child_in_set',$this->child_in_set);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcProductSaleLimitRule the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
