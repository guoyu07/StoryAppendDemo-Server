<?php

/**
 * This is the model class for table "ht_payment_history".
 *
 * The followings are the available columns in table 'ht_payment_history':
 * @property integer $id
 * @property integer $pay_or_refund
 * @property integer $refund_reason
 * @property integer $payment_really
 * @property string $payment_type
 * @property integer $supplier_id
 * @property integer $order_id
 * @property string $product_id
 * @property string $refund_order_id
 * @property string $trade_id
 * @property string $notify_id
 * @property string $trade_total
 * @property string $comment
 * @property string $card_number
 * @property string $buyer_id
 * @property string $buyer_email
 * @property string $trade_time
 * @property string $insert_time
 * @property string $raw_data
 */
class HtPaymentHistory extends CActiveRecord
{
    const PAYMENT = 1;
    const REFUND = 0;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtPaymentHistory the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_payment_history';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('payment_really, payment_type, trade_id, notify_id, buyer_id, buyer_email, trade_time, insert_time, raw_data', 'required'),
            array('pay_or_refund, payment_really, supplier_id, order_id', 'numerical', 'integerOnly' => true),
            array('payment_type', 'length', 'max' => 16),
            array('order_product_id', 'length', 'max' => 64),
            array('trade_id', 'length', 'max' => 32),
            array('notify_id, buyer_id, buyer_email', 'length', 'max' => 128),
            array('trade_total', 'length', 'max' => 10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, pay_or_refund, payment_really, payment_type, supplier_id, order_id, order_product_id, trade_id, notify_id, trade_total, buyer_id, buyer_email, trade_time, insert_time, raw_data', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'pay_or_refund' => 'Pay Or Refund',
            'refund_reason' => 'Refund Reason',
            'payment_really' => 'Payment Really',
            'payment_type' => 'Payment Type',
            'supplier_id' => 'Supplier',
            'order_id' => 'Order',
            'product_id' => 'Product',
            'refund_order_id' => 'Refund Order',
            'trade_id' => 'Trade',
            'notify_id' => 'Notify',
            'trade_total' => 'Trade Total',
            'comment' => 'Comment',
            'card_number' => 'Card Number',
            'buyer_id' => 'Buyer',
            'buyer_email' => 'Buyer Email',
            'trade_time' => 'Trade Time',
            'insert_time' => 'Insert Time',
            'raw_data' => 'Raw Data',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('pay_or_refund', $this->pay_or_refund);
        $criteria->compare('payment_really', $this->payment_really);
        $criteria->compare('payment_type', $this->payment_type, true);
        $criteria->compare('supplier_id', $this->supplier_id);
        $criteria->compare('order_id', $this->order_id);
        $criteria->compare('product_id', $this->product_id, true);
        $criteria->compare('refund_order_id', $this->refund_order_id);
        $criteria->compare('trade_id', $this->trade_id, true);
        $criteria->compare('notify_id', $this->notify_id, true);
        $criteria->compare('trade_total', $this->trade_total, true);
        $criteria->compare('comment', $this->comment, true);
        $criteria->compare('card_number', $this->card_number, true);
        $criteria->compare('buyer_id', $this->buyer_id, true);
        $criteria->compare('buyer_email', $this->buyer_email, true);
        $criteria->compare('trade_time', $this->trade_time, true);
        $criteria->compare('insert_time', $this->insert_time, true);
        $criteria->compare('raw_data', $this->raw_data, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array('alias' => 'ph');
    }
}
