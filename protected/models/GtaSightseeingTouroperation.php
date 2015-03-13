<?php

/**
 * This is the model class for table "gta_sightseeing_touroperation".
 *
 * The followings are the available columns in table 'gta_sightseeing_touroperation':
 * @property integer $operation_id
 * @property string $city_code
 * @property string $item_id
 * @property string $languages
 * @property string $commentary
 * @property string $from_date
 * @property string $to_date
 * @property string $frequency
 * @property string $override_text
 * @property integer $language_id
 * @property integer $available
 * @property string $update_time
 */
class GtaSightseeingTouroperation extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gta_sightseeing_touroperation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_code, item_id, languages, commentary, from_date, to_date, frequency, override_text, language_id, update_time', 'required'),
			array('language_id, available', 'numerical', 'integerOnly'=>true),
			array('city_code', 'length', 'max'=>4),
			array('item_id', 'length', 'max'=>64),
			array('commentary, frequency', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('operation_id, city_code, item_id, languages, commentary, from_date, to_date, frequency, override_text, language_id, available, update_time', 'safe', 'on'=>'search'),
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
			'city_code' => 'City Code',
			'item_id' => 'Item',
			'languages' => 'Languages',
			'commentary' => 'Commentary',
			'from_date' => 'From Date',
			'to_date' => 'To Date',
			'frequency' => 'Frequency',
			'override_text' => 'Override Text',
			'language_id' => 'Language',
			'available' => 'Available',
			'update_time' => 'Update Time',
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
		$criteria->compare('city_code',$this->city_code,true);
		$criteria->compare('item_id',$this->item_id,true);
		$criteria->compare('languages',$this->languages,true);
		$criteria->compare('commentary',$this->commentary,true);
		$criteria->compare('from_date',$this->from_date,true);
		$criteria->compare('to_date',$this->to_date,true);
		$criteria->compare('frequency',$this->frequency,true);
		$criteria->compare('override_text',$this->override_text,true);
		$criteria->compare('language_id',$this->language_id);
		$criteria->compare('available',$this->available);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GtaSightseeingTouroperation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
