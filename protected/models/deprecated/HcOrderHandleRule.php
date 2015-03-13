<?php

/**
 * This is the model class for table "hc_order_handle_rule".
 *
 * The followings are the available columns in table 'hc_order_handle_rule':
 * @property integer $manufacturer_id
 * @property integer $product_id
 * @property integer $need_hitour_voucher
 * @property integer $need_barcode
 * @property integer $need_op_handle
 * @property integer $need_references
 * @property integer $need_attachement
 * @property integer $need_hitour_reference
 * @property string $supplier_email
 * @property integer $need_additional_info
 */
class HcOrderHandleRule extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_order_handle_rule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('manufacturer_id, product_id', 'required'),
			array('manufacturer_id, product_id, need_hitour_voucher, need_barcode, need_op_handle, need_references, need_attachement, need_hitour_reference, need_additional_info', 'numerical', 'integerOnly'=>true),
			array('supplier_email', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('manufacturer_id, product_id, need_hitour_voucher, need_barcode, need_op_handle, need_references, need_attachement, need_hitour_reference, supplier_email, need_additional_info', 'safe', 'on'=>'search'),
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
			'manufacturer_id' => 'Manufacturer',
			'product_id' => 'Product',
			'need_hitour_voucher' => 'Need Hitour Voucher',
			'need_barcode' => 'Need Barcode',
			'need_op_handle' => 'Need Op Handle',
			'need_references' => 'Need References',
			'need_attachement' => 'Need Attachement',
			'need_hitour_reference' => 'Need Hitour Reference',
			'supplier_email' => 'Supplier Email',
			'need_additional_info' => 'Need Additional Info',
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

		$criteria->compare('manufacturer_id',$this->manufacturer_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('need_hitour_voucher',$this->need_hitour_voucher);
		$criteria->compare('need_barcode',$this->need_barcode);
		$criteria->compare('need_op_handle',$this->need_op_handle);
		$criteria->compare('need_references',$this->need_references);
		$criteria->compare('need_attachement',$this->need_attachement);
		$criteria->compare('need_hitour_reference',$this->need_hitour_reference);
		$criteria->compare('supplier_email',$this->supplier_email,true);
		$criteria->compare('need_additional_info',$this->need_additional_info);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcOrderHandleRule the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
