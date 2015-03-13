<?php

/**
 * This is the model class for table "hc_address".
 *
 * The followings are the available columns in table 'hc_address':
 * @property integer $address_id
 * @property integer $customer_id
 * @property string $firstname
 * @property string $lastname
 * @property string $company
 * @property string $company_id
 * @property string $tax_id
 * @property string $address_1
 * @property string $address_2
 * @property string $city
 * @property string $postcode
 * @property integer $country_id
 * @property integer $zone_id
 * @property string $passport_number
 * @property string $telephone
 * @property string $email
 */
class HcAddress extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_address';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('customer_id, firstname, lastname, company, company_id, tax_id, address_1, address_2, city, postcode', 'required'),
			array('customer_id, country_id, zone_id', 'numerical', 'integerOnly'=>true),
			array('firstname, lastname, company, company_id, tax_id, passport_number, telephone', 'length', 'max'=>32),
			array('address_1, address_2, city', 'length', 'max'=>128),
			array('postcode', 'length', 'max'=>10),
			array('email', 'length', 'max'=>96),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('address_id, customer_id, firstname, lastname, company, company_id, tax_id, address_1, address_2, city, postcode, country_id, zone_id, passport_number, telephone, email', 'safe', 'on'=>'search'),
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
			'address_id' => 'Address',
			'customer_id' => 'Customer',
			'firstname' => 'Firstname',
			'lastname' => 'Lastname',
			'company' => 'Company',
			'company_id' => 'Company',
			'tax_id' => 'Tax',
			'address_1' => 'Address 1',
			'address_2' => 'Address 2',
			'city' => 'City',
			'postcode' => 'Postcode',
			'country_id' => 'Country',
			'zone_id' => 'Zone',
			'passport_number' => 'Passport Number',
			'telephone' => 'Telephone',
			'email' => 'Email',
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

		$criteria->compare('address_id',$this->address_id);
		$criteria->compare('customer_id',$this->customer_id);
		$criteria->compare('firstname',$this->firstname,true);
		$criteria->compare('lastname',$this->lastname,true);
		$criteria->compare('company',$this->company,true);
		$criteria->compare('company_id',$this->company_id,true);
		$criteria->compare('tax_id',$this->tax_id,true);
		$criteria->compare('address_1',$this->address_1,true);
		$criteria->compare('address_2',$this->address_2,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('postcode',$this->postcode,true);
		$criteria->compare('country_id',$this->country_id);
		$criteria->compare('zone_id',$this->zone_id);
		$criteria->compare('passport_number',$this->passport_number,true);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('email',$this->email,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcAddress the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
