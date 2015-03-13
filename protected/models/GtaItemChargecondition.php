<?php

/**
 * This is the model class for table "gta_item_chargecondition".
 *
 * The followings are the available columns in table 'gta_item_chargecondition':
 * @property string $city_code
 * @property string $item_id
 * @property string $tour_date
 * @property integer $type
 * @property integer $allowable
 * @property integer $charge
 * @property string $from_date
 * @property string $to_date
 * @property string $currency
 * @property string $amount
 * @property string $check_time
 */
class GtaItemChargecondition extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gta_item_chargecondition';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tour_date, allowable, from_date, to_date, currency, check_time', 'required'),
			array('type, allowable, charge', 'numerical', 'integerOnly'=>true),
			array('city_code, currency', 'length', 'max'=>4),
			array('item_id', 'length', 'max'=>16),
			array('amount', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('city_code, item_id, tour_date, type, allowable, charge, from_date, to_date, currency, amount, check_time', 'safe', 'on'=>'search'),
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
			'city_code' => 'City Code',
			'item_id' => 'Item',
			'tour_date' => 'Tour Date',
			'type' => 'Type',
			'allowable' => 'Allowable',
			'charge' => 'Charge',
			'from_date' => 'From Date',
			'to_date' => 'To Date',
			'currency' => 'Currency',
			'amount' => 'Amount',
			'check_time' => 'Check Time',
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

		$criteria->compare('city_code',$this->city_code,true);
		$criteria->compare('item_id',$this->item_id,true);
		$criteria->compare('tour_date',$this->tour_date,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('allowable',$this->allowable);
		$criteria->compare('charge',$this->charge);
		$criteria->compare('from_date',$this->from_date,true);
		$criteria->compare('to_date',$this->to_date,true);
		$criteria->compare('currency',$this->currency,true);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('check_time',$this->check_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GtaItemChargecondition the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
