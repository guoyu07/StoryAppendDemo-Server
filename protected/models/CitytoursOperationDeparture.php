<?php

/**
 * This is the model class for table "citytours_operation_departure".
 *
 * The followings are the available columns in table 'citytours_operation_departure':
 * @property integer $operation_id
 * @property string $departure_id
 * @property string $time
 * @property string $first_service
 * @property string $last_service
 * @property integer $intervals
 * @property string $departure_point
 * @property string $address_lines
 * @property string $telephone
 * @property string $description
 * @property integer $language_id
 */
class CitytoursOperationDeparture extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'citytours_operation_departure';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('operation_id, departure_id, time, first_service, last_service, departure_point, address_lines, telephone, description, language_id', 'required'),
			array('operation_id, intervals, language_id', 'numerical', 'integerOnly'=>true),
			array('departure_id', 'length', 'max'=>16),
			array('departure_point, address_lines', 'length', 'max'=>128),
			array('telephone', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('operation_id, departure_id, time, first_service, last_service, intervals, departure_point, address_lines, telephone, description, language_id', 'safe', 'on'=>'search'),
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
			'operation_id' => 'Operation',
			'departure_id' => 'Departure',
			'time' => 'Time',
			'first_service' => 'First Service',
			'last_service' => 'Last Service',
			'intervals' => 'Intervals',
			'departure_point' => 'Departure Point',
			'address_lines' => 'Address Lines',
			'telephone' => 'Telephone',
			'description' => 'Description',
			'language_id' => 'Language',
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

		$criteria->compare('operation_id',$this->operation_id);
		$criteria->compare('departure_id',$this->departure_id,true);
		$criteria->compare('time',$this->time,true);
		$criteria->compare('first_service',$this->first_service,true);
		$criteria->compare('last_service',$this->last_service,true);
		$criteria->compare('intervals',$this->intervals);
		$criteria->compare('departure_point',$this->departure_point,true);
		$criteria->compare('address_lines',$this->address_lines,true);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('language_id',$this->language_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CitytoursOperationDeparture the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
