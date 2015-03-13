<?php

/**
 * This is the model class for table "hc_product_redeem_rule".
 *
 * The followings are the available columns in table 'hc_product_redeem_rule':
 * @property integer $product_id
 * @property integer $redeem_type
 * @property string $expire_date
 * @property string $duration
 * @property string $usage_limit
 */
class HcProductRedeemRule extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_product_redeem_rule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, redeem_type', 'required'),
			array('product_id, redeem_type', 'numerical', 'integerOnly'=>true),
			array('duration', 'length', 'max'=>32),
			array('usage_limit', 'length', 'max'=>1024),
			array('expire_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_id, redeem_type, expire_date, duration, usage_limit', 'safe', 'on'=>'search'),
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
			'redeem_type' => 'Redeem Type',
			'expire_date' => 'Expire Date',
			'duration' => 'Duration',
			'usage_limit' => 'Usage Limit',
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
		$criteria->compare('redeem_type',$this->redeem_type);
		$criteria->compare('expire_date',$this->expire_date,true);
		$criteria->compare('duration',$this->duration,true);
		$criteria->compare('usage_limit',$this->usage_limit,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcProductRedeemRule the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
