<?php

/**
 * This is the model class for table "hc_affiliate_transaction".
 *
 * The followings are the available columns in table 'hc_affiliate_transaction':
 * @property integer $affiliate_transaction_id
 * @property integer $affiliate_id
 * @property integer $order_id
 * @property string $description
 * @property string $amount
 * @property string $date_added
 */
class HcAffiliateTransaction extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_affiliate_transaction';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('affiliate_id, order_id, description, amount, date_added', 'required'),
			array('affiliate_id, order_id', 'numerical', 'integerOnly'=>true),
			array('amount', 'length', 'max'=>15),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('affiliate_transaction_id, affiliate_id, order_id, description, amount, date_added', 'safe', 'on'=>'search'),
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
			'affiliate_transaction_id' => 'Affiliate Transaction',
			'affiliate_id' => 'Affiliate',
			'order_id' => 'Order',
			'description' => 'Description',
			'amount' => 'Amount',
			'date_added' => 'Date Added',
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

		$criteria->compare('affiliate_transaction_id',$this->affiliate_transaction_id);
		$criteria->compare('affiliate_id',$this->affiliate_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('date_added',$this->date_added,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcAffiliateTransaction the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
