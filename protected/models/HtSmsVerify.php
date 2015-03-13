<?php

/**
 * This is the model class for table "ht_sms_verify".
 *
 * The followings are the available columns in table 'ht_sms_verify':
 * @property integer $id
 * @property string $session_id
 * @property string $phone_number
 * @property string $sms_code
 * @property string $insert_time
 * @property string $verify_time
 */
class HtSmsVerify extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_sms_verify';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('session_id, sms_code', 'required'),
			array('session_id', 'length', 'max'=>64),
			array('phone_number', 'length', 'max'=>16),
			array('sms_code', 'length', 'max'=>6),
			array('verify_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, session_id, phone_number,sms_code, insert_time, verify_time', 'safe', 'on'=>'search'),
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
			'session_id' => 'Session',
			'phone_number' => 'Phone Number',
			'sms_code' => 'Sms Code',
			'insert_time' => 'Insert Time',
			'verify_time' => 'Verify Time',
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
		$criteria->compare('session_id',$this->session_id,true);
		$criteria->compare('phone_number',$this->session_id,true);
		$criteria->compare('sms_code',$this->sms_code,true);
		$criteria->compare('insert_time',$this->insert_time,true);
		$criteria->compare('verify_time',$this->verify_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtSmsVerify the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope(){
        return array(
            'order'=>'id DESC',
        );
    }
}
