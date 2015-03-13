<?php

/**
 * This is the model class for table "gta_order_status_history".
 *
 * The followings are the available columns in table 'gta_order_status_history':
 * @property integer $id
 * @property string $booking_reference
 * @property string $api_reference
 * @property string $response_reference
 * @property string $time
 * @property string $status_code
 * @property string $description
 * @property string $remarks
 */
class GtaOrderStatusHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gta_order_status_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('booking_reference, time, status_code, description, remarks', 'required'),
			array('booking_reference', 'length', 'max'=>30),
			array('api_reference', 'length', 'max'=>32),
			array('response_reference', 'length', 'max'=>128),
			array('status_code', 'length', 'max'=>3),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, booking_reference, api_reference, response_reference, time, status_code, description, remarks', 'safe', 'on'=>'search'),
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
			'booking_reference' => 'Booking Reference',
			'api_reference' => 'Api Reference',
			'response_reference' => 'Response Reference',
			'time' => 'Time',
			'status_code' => 'Status Code',
			'description' => 'Description',
			'remarks' => 'Remarks',
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
		$criteria->compare('booking_reference',$this->booking_reference,true);
		$criteria->compare('api_reference',$this->api_reference,true);
		$criteria->compare('response_reference',$this->response_reference,true);
		$criteria->compare('time',$this->time,true);
		$criteria->compare('status_code',$this->status_code,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('remarks',$this->remarks,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GtaOrderStatusHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
