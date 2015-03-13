<?php

/**
 * This is the model class for table "hc_product_sale_date_rule".
 *
 * The followings are the available columns in table 'hc_product_sale_date_rule':
 * @property integer $product_id
 * @property integer $need_tour_date
 * @property string $close_dates
 * @property string $lead_time
 * @property string $buy_in_advance
 * @property integer $sale_range_type
 * @property string $sale_range
 * @property string $from_date
 * @property string $to_date
 */
class HcProductSaleDateRule extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_product_sale_date_rule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id', 'required'),
			array('product_id, need_tour_date, sale_range_type', 'numerical', 'integerOnly'=>true),
			array('close_dates', 'length', 'max'=>1024),
			array('lead_time, buy_in_advance, sale_range', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_id, need_tour_date, close_dates, lead_time, buy_in_advance, sale_range_type, sale_range', 'safe', 'on'=>'search'),
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
			'need_tour_date' => '订单中是否需要填写出行日期，1：需要；0：不需要',
			'close_dates' => '关闭日期',
			'lead_time' => '发货时间--0Day：立刻；1Day：1个工作日内',
			'buy_in_advance' => '提前购买；0Day：不需要；1Day：提前1天',
			'sale_range_type' => '0: to_date, 1: sale_range',
			'sale_range' => '30Day, Month, 1Year',
			'from_date' => '售卖开始时间',
			'to_date' => '售卖截止日期',
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
		$criteria->compare('need_tour_date',$this->need_tour_date);
		$criteria->compare('close_dates',$this->close_dates,true);
		$criteria->compare('lead_time',$this->lead_time,true);
		$criteria->compare('buy_in_advance',$this->buy_in_advance,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcProductSaleDateRule the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
