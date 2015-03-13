<?php

/**
 * This is the model class for table "hc_product_stock".
 *
 * The followings are the available columns in table 'hc_product_stock':
 * @property integer $product_id
 * @property string $sale_date
 * @property integer $all_stock_num
 * @property integer $current_stock_num
 * @property integer $payment_reservation_duration
 * @property integer $is_stock_limited
 */
class HcProductStock extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_product_stock';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, sale_date, all_stock_num, current_stock_num', 'required'),
			array('product_id, all_stock_num, current_stock_num, payment_reservation_duration, is_stock_limited', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_id, sale_date, all_stock_num, current_stock_num, payment_reservation_duration, is_stock_limited', 'safe', 'on'=>'search'),
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
			'product_id' => 'Product',
			'sale_date' => 'Sale Date',
			'all_stock_num' => 'All Stock Num',
			'current_stock_num' => 'Current Stock Num',
			'payment_reservation_duration' => 'Payment Reservation Duration',
			'is_stock_limited' => 'Is Stock Limited',
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

		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('sale_date',$this->sale_date,true);
		$criteria->compare('all_stock_num',$this->all_stock_num);
		$criteria->compare('current_stock_num',$this->current_stock_num);
		$criteria->compare('payment_reservation_duration',$this->payment_reservation_duration);
		$criteria->compare('is_stock_limited',$this->is_stock_limited);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcProductStock the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
