<?php

/**
 * This is the model class for table "hc_payment_history".
 *
 * The followings are the available columns in table 'hc_payment_history':
 * @property integer $id
 * @property integer $pay_or_refund
 * @property integer $order_id
 * @property integer $order_product_id
 * @property string $out_order_id
 * @property string $trade_no
 * @property string $notify_id
 * @property string $total
 * @property string $charge
 * @property string $total_fee
 * @property string $buyer_id
 * @property string $buyer_email
 * @property string $trade_time
 * @property string $raw_data
 */
class HcPaymentHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_payment_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('trade_time, raw_data', 'required'),
			array('pay_or_refund, order_id, order_product_id', 'numerical', 'integerOnly'=>true),
			array('out_order_id', 'length', 'max'=>64),
			array('trade_no', 'length', 'max'=>32),
			array('notify_id, buyer_id, buyer_email', 'length', 'max'=>128),
			array('total, charge, total_fee', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pay_or_refund, order_id, order_product_id, out_order_id, trade_no, notify_id, total, charge, total_fee, buyer_id, buyer_email, trade_time, raw_data', 'safe', 'on'=>'search'),
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
			'pay_or_refund' => 'Pay Or Refund',
			'order_id' => 'Order',
			'order_product_id' => 'Order Product',
			'out_order_id' => 'Out Order',
			'trade_no' => 'Trade No',
			'notify_id' => 'Notify',
			'total' => 'Total',
			'charge' => 'Charge',
			'total_fee' => 'Total Fee',
			'buyer_id' => 'Buyer',
			'buyer_email' => 'Buyer Email',
			'trade_time' => 'Trade Time',
			'raw_data' => 'Raw Data',
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
		$criteria->compare('pay_or_refund',$this->pay_or_refund);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('order_product_id',$this->order_product_id);
		$criteria->compare('out_order_id',$this->out_order_id,true);
		$criteria->compare('trade_no',$this->trade_no,true);
		$criteria->compare('notify_id',$this->notify_id,true);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('charge',$this->charge,true);
		$criteria->compare('total_fee',$this->total_fee,true);
		$criteria->compare('buyer_id',$this->buyer_id,true);
		$criteria->compare('buyer_email',$this->buyer_email,true);
		$criteria->compare('trade_time',$this->trade_time,true);
		$criteria->compare('raw_data',$this->raw_data,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcPaymentHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
