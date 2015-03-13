<?php

/**
 * This is the model class for table "supplier_aot".
 *
 * The followings are the available columns in table 'supplier_aot':
 * @property integer $aot_id
 * @property integer $supplier_id
 * @property integer $language_id
 * @property string $destination
 * @property string $city_code
 * @property string $office_location
 * @property string $language_name
 * @property string $language_code
 * @property string $office_hours
 * @property string $international
 * @property string $national
 * @property string $local
 * @property string $out_of_office
 */
class SupplierAot extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'supplier_aot';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('supplier_id, language_id, destination, city_code, office_location, language_name, language_code, office_hours, international, national, local, out_of_office', 'required'),
			array('supplier_id, language_id', 'numerical', 'integerOnly'=>true),
			array('destination, language_code', 'length', 'max'=>3),
			array('city_code', 'length', 'max'=>4),
			array('office_location, language_name, office_hours, international, national, local, out_of_office', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('aot_id, supplier_id, language_id, destination, city_code, office_location, language_name, language_code, office_hours, international, national, local, out_of_office', 'safe', 'on'=>'search'),
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
			'aot_id' => 'Aot',
			'supplier_id' => 'Supplier',
			'language_id' => 'Language',
			'destination' => 'Destination',
			'city_code' => 'City Code',
			'office_location' => 'Office Location',
			'language_name' => 'Language Name',
			'language_code' => 'Language Code',
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

		$criteria->compare('aot_id',$this->aot_id);
		$criteria->compare('supplier_id',$this->supplier_id);
		$criteria->compare('language_id',$this->language_id);
		$criteria->compare('destination',$this->destination,true);
		$criteria->compare('city_code',$this->city_code,true);
		$criteria->compare('office_location',$this->office_location,true);
		$criteria->compare('language_name',$this->language_name,true);
		$criteria->compare('language_code',$this->language_code,true);
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
	 * @return SupplierAot the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
