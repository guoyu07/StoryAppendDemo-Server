<?php

/**
 * This is the model class for table "ht_order_invoice_status".
 *
 * The followings are the available columns in table 'ht_order_invoice_status':
 * @property integer $order_id
 * @property integer $invoice_status
 */
class HtOrderInvoiceStatus extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_order_invoice_status';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, invoice_status', 'required'),
			array('order_id, invoice_status', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('order_id, invoice_status', 'safe', 'on'=>'search'),
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
			'order_id' => 'Order',
			'invoice_status' => 'Invoice Status',
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

		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('invoice_status',$this->invoice_status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtOrderInvoiceStatus the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function updateRefundOrderStatus($order_id)
    {
        $item = $this->findByPk($order_id);
        if($item){
            if($item['invoice_status'] == 1){
                $item['invoice_status'] = 2;
                $result = $item->update();
                if($result){
                    //更新
                    $c = new CDbCriteria();
                    $c->addCondition('order_id = '.$order_id);
                    $c->order = 'insert_time DESC';
                    //取该订单上一条对账操作历史
                    $invoiceHistory = HtOrderInvoiceHistory::model()->find($c);
                    if($invoiceHistory){
                        $invoice_history = new HtOrderInvoiceHistory();
                        $invoice_history['invoice_id'] = $invoiceHistory['invoice_id'];
                        $invoice_history['insert_id'] = Yii::app()->user->id;
                        $invoice_history['insert_time'] = date('Y-m-d H:i:s');
                        $invoice_history['order_id'] = $order_id;
                        $invoice_history['status'] = 2;//有问题
                        $invoice_history['reason'] = 2;//已退款
                        $invoice_history['remark'] = '退款自动变更对账状态';//已退款
                        $invoice_history->insert();
                    }

                }
            }
        }
    }
}
