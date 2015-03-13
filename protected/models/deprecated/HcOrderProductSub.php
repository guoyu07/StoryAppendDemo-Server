<?php

/**
 * This is the model class for table "hc_order_product_sub".
 *
 * The followings are the available columns in table 'hc_order_product_sub':
 * @property integer $order_product_id
 * @property integer $sub_product_id
 * @property integer $sub_manufacturer_id
 * @property string $supplier_order_id
 * @property string $attachment
 */
class HcOrderProductSub extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_order_product_sub';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sub_product_id, sub_manufacturer_id', 'numerical', 'integerOnly'=>true),
			array('supplier_order_id', 'length', 'max'=>128),
			array('attachment', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('order_product_id, sub_product_id, sub_manufacturer_id, supplier_order_id, attachment', 'safe', 'on'=>'search'),
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
			'order_product_id' => 'Order Product',
			'sub_product_id' => 'Sub Product',
			'sub_manufacturer_id' => 'Sub Manufacturer',
			'supplier_order_id' => 'Supplier Order',
			'attachment' => 'Attachment',
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

		$criteria->compare('order_product_id',$this->order_product_id);
		$criteria->compare('sub_product_id',$this->sub_product_id);
		$criteria->compare('sub_manufacturer_id',$this->sub_manufacturer_id);
		$criteria->compare('supplier_order_id',$this->supplier_order_id,true);
		$criteria->compare('attachment',$this->attachment,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcOrderProductSub the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
