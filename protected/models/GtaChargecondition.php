<?php

/**
 * This is the model class for table "gta_chargecondition".
 *
 * The followings are the available columns in table 'gta_chargecondition':
 * @property integer $id
 * @property string $booking_reference
 * @property integer $type
 * @property integer $maximum_possible_charges_shown
 * @property integer $charge
 * @property integer $allowable
 * @property string $from_date
 * @property string $to_date
 * @property string $currency
 * @property string $amount
 * @property string $effective_from_date
 * @property string $effective_to_date
 * @property string $msg
 */
class GtaChargecondition extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GtaChargecondition the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gta_chargecondition';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, maximum_possible_charges_shown, charge, allowable, from_date, to_date, currency, effective_from_date, effective_to_date', 'required'),
			array('type, maximum_possible_charges_shown, charge, allowable', 'numerical', 'integerOnly'=>true),
			array('booking_reference', 'length', 'max'=>30),
			array('currency', 'length', 'max'=>4),
			array('amount', 'length', 'max'=>10),
			array('msg', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, booking_reference, type, maximum_possible_charges_shown, charge, allowable, from_date, to_date, currency, amount, effective_from_date, effective_to_date, msg', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'booking_reference' => 'Booking Reference',
			'type' => 'Type',
			'maximum_possible_charges_shown' => 'Maximum Possible Charges Shown',
			'charge' => 'Charge',
			'allowable' => 'Allowable',
			'from_date' => 'From Date',
			'to_date' => 'To Date',
			'currency' => 'Currency',
			'amount' => 'Amount',
			'effective_from_date' => 'Effective From Date',
			'effective_to_date' => 'Effective To Date',
			'msg' => 'Msg',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('booking_reference',$this->booking_reference,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('maximum_possible_charges_shown',$this->maximum_possible_charges_shown);
		$criteria->compare('charge',$this->charge);
		$criteria->compare('allowable',$this->allowable);
		$criteria->compare('from_date',$this->from_date,true);
		$criteria->compare('to_date',$this->to_date,true);
		$criteria->compare('currency',$this->currency,true);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('effective_from_date',$this->effective_from_date,true);
		$criteria->compare('effective_to_date',$this->effective_to_date,true);
		$criteria->compare('msg',$this->msg,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}