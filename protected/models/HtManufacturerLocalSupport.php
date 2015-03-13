<?php

/**
 * This is the model class for table "ht_manufacturer_local_support".
 *
 * The followings are the available columns in table 'ht_manufacturer_local_support':
 * @property integer $support_id
 * @property integer $manufacturer_id
 * @property integer $product_id
 * @property string $destination
 * @property string $city_code
 * @property integer $language_id
 * @property string $office_location
 * @property string $language_name
 * @property string $language_code
 * @property string $phone
 * @property string $office_hours
 * @property string $international
 * @property string $national
 * @property string $local
 * @property string $out_of_office
 */
class HtManufacturerLocalSupport extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_manufacturer_local_support';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('manufacturer_id', 'required'),
			array('manufacturer_id, product_id, language_id', 'numerical', 'integerOnly'=>true),
			array('destination', 'length', 'max'=>3),
			array('city_code, language_code', 'length', 'max'=>4),
			array('office_location, language_name, phone, office_hours, international, national, local, out_of_office', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('support_id, manufacturer_id, product_id, destination, city_code, language_id, office_location, language_name, language_code, phone, office_hours, international, national, local, out_of_office', 'safe', 'on'=>'search'),
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
			'support_id' => 'Support',
			'manufacturer_id' => 'Manufacturer',
			'product_id' => 'Product',
			'destination' => 'Destination',
			'city_code' => 'City Code',
			'language_id' => 'Language',
			'office_location' => 'Office Location',
			'language_name' => 'Language Name',
			'language_code' => 'Language Code',
			'phone' => 'Phone',
			'office_hours' => 'Office Hours',
			'international' => 'International',
			'national' => 'National',
			'local' => 'Local',
			'out_of_office' => 'Out Of Office',
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

		$criteria->compare('support_id',$this->support_id);
		$criteria->compare('manufacturer_id',$this->manufacturer_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('destination',$this->destination,true);
		$criteria->compare('city_code',$this->city_code,true);
		$criteria->compare('language_id',$this->language_id);
		$criteria->compare('office_location',$this->office_location,true);
		$criteria->compare('language_name',$this->language_name,true);
		$criteria->compare('language_code',$this->language_code,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('office_hours',$this->office_hours,true);
		$criteria->compare('international',$this->international,true);
		$criteria->compare('national',$this->national,true);
		$criteria->compare('local',$this->local,true);
		$criteria->compare('out_of_office',$this->out_of_office,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtManufacturerLocalSupport the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
