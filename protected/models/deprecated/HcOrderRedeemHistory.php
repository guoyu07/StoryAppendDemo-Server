<?php

/**
 * This is the model class for table "hc_order_redeem_history".
 *
 * The followings are the available columns in table 'hc_order_redeem_history':
 * @property integer $id
 * @property integer $order_id
 * @property string $booking_ref
 * @property integer $verification_code
 * @property integer $result
 * @property string $redeem_time
 */
class HcOrderRedeemHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_order_redeem_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, booking_ref, verification_code, redeem_time', 'required'),
			array('order_id, verification_code, result', 'numerical', 'integerOnly'=>true),
			array('booking_ref', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, booking_ref, verification_code, result, redeem_time', 'safe', 'on'=>'search'),
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
			'order_id' => 'Order',
			'booking_ref' => 'Booking Ref',
			'verification_code' => 'Verification Code',
			'result' => 'Result',
			'redeem_time' => 'Redeem Time',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('booking_ref',$this->booking_ref,true);
		$criteria->compare('verification_code',$this->verification_code);
		$criteria->compare('result',$this->result);
		$criteria->compare('redeem_time',$this->redeem_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcOrderRedeemHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
