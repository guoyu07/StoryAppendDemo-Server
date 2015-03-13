<?php

/**
 * This is the model class for table "hc_insurance_code".
 *
 * The followings are the available columns in table 'hc_insurance_code':
 * @property integer $id
 * @property integer $company_id
 * @property string $partner_code
 * @property string $product_code
 * @property string $redeem_code
 * @property integer $redeem_status
 * @property string $redeem_start_date
 * @property string $redeem_expire_date
 * @property integer $order_id
 * @property integer $refunded
 */
class HcInsuranceCode extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_insurance_code';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('redeem_start_date, redeem_expire_date', 'required'),
			array('company_id, redeem_status, order_id, refunded', 'numerical', 'integerOnly'=>true),
			array('partner_code, redeem_code', 'length', 'max'=>16),
			array('product_code', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, company_id, partner_code, product_code, redeem_code, redeem_status, redeem_start_date, redeem_expire_date, order_id, refunded', 'safe', 'on'=>'search'),
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
			'company_id' => 'Company',
			'partner_code' => 'Partner Code',
			'product_code' => 'Product Code',
			'redeem_code' => 'Redeem Code',
			'redeem_status' => 'Redeem Status',
			'redeem_start_date' => 'Redeem Start Date',
			'redeem_expire_date' => 'Redeem Expire Date',
			'order_id' => 'Order',
			'refunded' => 'Refunded',
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
		$criteria->compare('company_id',$this->company_id);
		$criteria->compare('partner_code',$this->partner_code,true);
		$criteria->compare('product_code',$this->product_code,true);
		$criteria->compare('redeem_code',$this->redeem_code,true);
		$criteria->compare('redeem_status',$this->redeem_status);
		$criteria->compare('redeem_start_date',$this->redeem_start_date,true);
		$criteria->compare('redeem_expire_date',$this->redeem_expire_date,true);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('refunded',$this->refunded);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcInsuranceCode the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
