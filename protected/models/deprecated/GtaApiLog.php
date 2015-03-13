<?php

/**
 * This is the model class for table "gta_api_log".
 *
 * The followings are the available columns in table 'gta_api_log':
 * @property integer $log_id
 * @property string $request_type
 * @property string $request_detail
 * @property string $response_status
 * @property string $response_detail
 * @property string $request_time
 */
class GtaApiLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gta_api_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('request_type, request_detail, response_status, response_detail, request_time', 'required'),
			array('request_type, response_status', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('log_id, request_type, request_detail, response_status, response_detail, request_time', 'safe', 'on'=>'search'),
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
			'log_id' => 'Log',
			'request_type' => 'Request Type',
			'request_detail' => 'Request Detail',
			'response_status' => 'Response Status',
			'response_detail' => 'Response Detail',
			'request_time' => 'Request Time',
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

		$criteria->compare('log_id',$this->log_id);
		$criteria->compare('request_type',$this->request_type,true);
		$criteria->compare('request_detail',$this->request_detail,true);
		$criteria->compare('response_status',$this->response_status,true);
		$criteria->compare('response_detail',$this->response_detail,true);
		$criteria->compare('request_time',$this->request_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GtaApiLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
