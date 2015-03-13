<?php

/**
 * This is the model class for table "ht_insurance_order".
 *
 * The followings are the available columns in table 'ht_insurance_order':
 * @property string $cpic_order_id
 * @property string $pol_number
 * @property integer $order_id
 * @property integer $order_passenger_id
 * @property integer $status
 * @property string $date_added
 * @property string $date_modified
 */
class HtInsuranceOrder extends CActiveRecord
{
    const PENDING = 1;
    const CONFIRMED = 2;
    const CANCELED = 3;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return HtInsuranceOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_insurance_order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cpic_order_id, pol_number, order_id, date_added, date_modified', 'required'),
			array('order_id, status', 'numerical', 'integerOnly'=>true),
			array('cpic_order_id, pol_number', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('cpic_order_id, pol_number, order_id, order_passenger_id, status, date_added, date_modified', 'safe', 'on'=>'search'),
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
			'cpic_order_id' => 'Cpic Order',
			'pol_number' => 'Pol Number',
			'order_id' => 'Order',
            'order_passenger_id' => 'Order Passenger ID',
			'status' => 'Status',
			'date_added' => 'Date Added',
			'date_modified' => 'Date Modified',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('cpic_order_id',$this->cpic_order_id,true);
		$criteria->compare('pol_number',$this->pol_number,true);
		$criteria->compare('order_id',$this->order_id);
        $criteria->compare('order_passenger_id',$this->order_passenger_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('date_added',$this->date_added,true);
		$criteria->compare('date_modified',$this->date_modified,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}