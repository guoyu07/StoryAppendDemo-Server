<?php

/**
 * This is the model class for table "hc_customer_field".
 *
 * The followings are the available columns in table 'hc_customer_field':
 * @property integer $customer_id
 * @property integer $custom_field_id
 * @property integer $custom_field_value_id
 * @property integer $name
 * @property string $value
 * @property integer $sort_order
 */
class HcCustomerField extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_customer_field';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('customer_id, custom_field_id, custom_field_value_id, name, value, sort_order', 'required'),
			array('customer_id, custom_field_id, custom_field_value_id, name, sort_order', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('customer_id, custom_field_id, custom_field_value_id, name, value, sort_order', 'safe', 'on'=>'search'),
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
			'customer_id' => 'Customer',
			'custom_field_id' => 'Custom Field',
			'custom_field_value_id' => 'Custom Field Value',
			'name' => 'Name',
			'value' => 'Value',
			'sort_order' => 'Sort Order',
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

		$criteria->compare('customer_id',$this->customer_id);
		$criteria->compare('custom_field_id',$this->custom_field_id);
		$criteria->compare('custom_field_value_id',$this->custom_field_value_id);
		$criteria->compare('name',$this->name);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('sort_order',$this->sort_order);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcCustomerField the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
