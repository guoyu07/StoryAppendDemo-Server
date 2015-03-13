<?php

/**
 * This is the model class for table "citytours_tour_operation".
 *
 * The followings are the available columns in table 'citytours_tour_operation':
 * @property integer $operation_id
 * @property string $city_code
 * @property string $item_id
 * @property string $sale_range
 * @property string $from_date
 * @property string $to_date
 */
class CitytoursTourOperation extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'citytours_tour_operation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('from_date, to_date', 'required'),
			array('city_code', 'length', 'max'=>4),
			array('item_id', 'length', 'max'=>16),
			array('sale_range', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('operation_id, city_code, item_id, sale_range, from_date, to_date', 'safe', 'on'=>'search'),
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
			'operation_id' => 'Operation',
			'city_code' => 'City Code',
			'item_id' => 'Item',
			'sale_range' => 'Sale Range',
			'from_date' => 'From Date',
			'to_date' => 'To Date',
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

		$criteria->compare('operation_id',$this->operation_id);
		$criteria->compare('city_code',$this->city_code,true);
		$criteria->compare('item_id',$this->item_id,true);
		$criteria->compare('sale_range',$this->sale_range,true);
		$criteria->compare('from_date',$this->from_date,true);
		$criteria->compare('to_date',$this->to_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CitytoursTourOperation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
