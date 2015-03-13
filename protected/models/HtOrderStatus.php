<?php

/**
 * This is the model class for table "ht_order_status".
 *
 * The followings are the available columns in table 'ht_order_status':
 * @property integer $order_status_id
 * @property string $cn_name
 * @property string $en_name
 * @property string $cn_name_customer
 * @property string $en_name_customer
 * @property string $tag_name
 */
class HtOrderStatus extends CActiveRecord
{
    const ORDER_CONFIRMED = 1;
    const ORDER_TO_DELIVERY = 2;
    const ORDER_SHIPPED = 3;
    const ORDER_WAIT_CONFIRMATION = 4;
    const ORDER_STOCK_FAILED = 5;
    const ORDER_SHIPPING_FAILED = 6;
    const ORDER_CANCELED = 7;
    const ORDER_WAIT_RETURN_CONFIRMATION = 8;
    const ORDER_RETURN_CONFIRMED = 9;
    const ORDER_RETURN_REJECTED = 10;
    const ORDER_REFUND_SUCCESS = 11;
    const ORDER_RETURN_FAILED = 12;
    const ORDER_BOOKING_FAILED = 17;
    const ORDER_REFUND_FAILED = 19;
    const ORDER_REFUND_PROCESSING = 20;
    const ORDER_PAYMENT_SUCCESS = 21;
    const ORDER_PAYMENT_FAILED = 22;
    const ORDER_RETURN_REQUEST = 23;
    const ORDER_OUTOF_REFUND = 24;
    const ORDER_NOTPAY_EXPIRED = 25;
    const ORDER_PAID_EXPIRED = 26;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_order_status';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cn_name, en_name, cn_name_customer, en_name_customer, tag_name', 'required'),
			array('cn_name, en_name, cn_name_customer, en_name_customer, tag_name', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('order_status_id, cn_name, en_name, cn_name_customer, en_name_customer, tag_name', 'safe', 'on'=>'search'),
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
			'order_status_id' => 'Order Status',
			'cn_name' => 'Cn Name',
			'en_name' => 'En Name',
			'cn_name_customer' => 'Cn Name Customer',
			'en_name_customer' => 'En Name Customer',
			'tag_name' => 'Tag Name',
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

		$criteria->compare('order_status_id',$this->order_status_id);
		$criteria->compare('cn_name',$this->cn_name,true);
		$criteria->compare('en_name',$this->en_name,true);
		$criteria->compare('cn_name_customer',$this->cn_name_customer,true);
		$criteria->compare('en_name_customer',$this->en_name_customer,true);
		$criteria->compare('tag_name',$this->tag_name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtOrderStatus the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

