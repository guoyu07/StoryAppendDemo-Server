<?php

/**
 * This is the model class for table "ht_voucher_rule".
 *
 * The followings are the available columns in table 'ht_voucher_rule':
 * @property integer $product_id
 * @property integer $language
 * @property string $default_passenger_info
 * @property string $child_passenger_info
 * @property string $lead_passenger_info
 * @property integer $need_pay_cert
 * @property string $pay_cert
 * @property integer $need_origin_name
 * @property integer $need_signature
 */
class HtVoucherRule extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_voucher_rule';
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
			array('product_id, language, need_pay_cert, need_origin_name, need_signature', 'numerical', 'integerOnly'=>true),
			array('default_passenger_info, child_passenger_info, lead_passenger_info, pay_cert', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_id, language, default_passenger_info, child_passenger_info, lead_passenger_info, need_pay_cert, pay_cert, need_origin_name, need_signature', 'safe', 'on'=>'search'),
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
			'language' => 'Language',
			'default_passenger_info' => 'Default Passenger Info',
			'child_passenger_info' => 'Child Passenger Info',
			'lead_passenger_info' => 'Lead Passenger Info',
			'need_pay_cert' => 'Need Pay Cert',
			'pay_cert' => 'Pay Cert',
			'need_origin_name' => 'Need Origin Name',
			'need_signature' => 'Need Signature',
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
		$criteria->compare('language',$this->language);
		$criteria->compare('default_passenger_info',$this->default_passenger_info,true);
		$criteria->compare('child_passenger_info',$this->child_passenger_info,true);
		$criteria->compare('lead_passenger_info',$this->lead_passenger_info,true);
		$criteria->compare('need_pay_cert',$this->need_pay_cert);
		$criteria->compare('pay_cert',$this->pay_cert,true);
		$criteria->compare('need_origin_name',$this->need_origin_name);
		$criteria->compare('need_signature',$this->need_signature);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtVoucherRule the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
