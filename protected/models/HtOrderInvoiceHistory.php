<?php

/**
 * This is the model class for table "ht_order_invoice_history".
 *
 * The followings are the available columns in table 'ht_order_invoice_history':
 * @property integer $invoice_history_id
 * @property integer $invoice_id
 * @property integer $order_id
 * @property integer $status
 * @property integer $reason
 * @property string $remark
 * @property integer $insert_id
 * @property string $insert_time
 */
class HtOrderInvoiceHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_order_invoice_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('invoice_id, order_id, status, reason, remark, insert_id, insert_time', 'required'),
			array('invoice_id, order_id, status, reason, insert_id', 'numerical', 'integerOnly'=>true),
			array('remark', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array( 'invoice_history_id, invoice_id, order_id, status, reason, remark, insert_id, insert_time', 'safe', 'on'=>'search'),
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
            'invoice_history_id' => 'Invoice History',
			'invoice_id' => 'Invoice',
			'order_id' => 'Order',
			'status' => 'Status',
			'reason' => 'Reason',
			'remark' => 'Remark',
			'insert_id' => 'Insert',
			'insert_time' => 'Insert Time',
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

        $criteria->compare('invoice_history_id',$this->invoice_history_id);
		$criteria->compare('invoice_id',$this->invoice_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('reason',$this->reason);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('insert_id',$this->insert_id);
		$criteria->compare('insert_time',$this->insert_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtOrderInvoiceHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
