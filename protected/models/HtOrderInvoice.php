<?php

/**
 * This is the model class for table "ht_order_invoice".
 *
 * The followings are the available columns in table 'ht_order_invoice':
 * @property integer $invoice_id
 * @property string $invoice_sn
 * @property integer $supplier_id
 * @property string $invoice_date
 * @property string $invoice_doc
 * @property string $remark
 */
class HtOrderInvoice extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_order_invoice';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('invoice_sn, supplier_id, invoice_date, invoice_doc', 'required'),
			array('supplier_id', 'numerical', 'integerOnly'=>true),
			array('invoice_sn', 'length', 'max'=>50),
			array('invoice_doc, remark', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('invoice_id, invoice_sn, supplier_id, invoice_date, invoice_doc, remark', 'safe', 'on'=>'search'),
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
			'invoice_id' => 'Invoice',
			'invoice_sn' => 'Invoice Sn',
			'supplier_id' => 'Supplier',
			'invoice_date' => 'Invoice Date',
			'invoice_doc' => 'Invoice Doc',
			'remark' => 'Remark',
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

		$criteria->compare('invoice_id',$this->invoice_id);
		$criteria->compare('invoice_sn',$this->invoice_sn,true);
		$criteria->compare('supplier_id',$this->supplier_id);
		$criteria->compare('invoice_date',$this->invoice_date,true);
		$criteria->compare('invoice_doc',$this->invoice_doc,true);
		$criteria->compare('remark',$this->remark,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtOrderInvoice the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    //获取对账单对应状态数量
    public function getTotalsByInvoiceStatus($invoice_id,$invoice_status)
    {
        $connection = Yii::app()->db;
        $sql = "select  count(distinct b.order_id) as total from ht_order_invoice_history a , ht_order_invoice_status b where a.invoice_id = $invoice_id and a.order_id = b.order_id and b.invoice_status = $invoice_status";
        $query = $connection->createCommand($sql)->queryRow();
        return $query['total'];
    }
}
