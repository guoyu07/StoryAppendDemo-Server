<?php

/**
 * This is the model class for table "ht_sms_reply".
 *
 * The followings are the available columns in table 'ht_sms_reply':
 * @property integer $reply_id
 * @property string $mo_id
 * @property string $special_service
 * @property string $mobile
 * @property string $content
 * @property string $reply_time
 * @property string $insert_time
 */
class HtSmsReply extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return HtSmsReply the static model class
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
		return 'ht_sms_reply';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('mo_id, special_service, mobile, content, reply_time, insert_time', 'required'),
			array('special_service, mobile', 'length', 'max'=>16),
			array('content', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('reply_id, mo_id, special_service, mobile, content, reply_time, insert_time', 'safe', 'on'=>'search'),
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
			'reply_id' => 'Reply',
			'mo_id' => 'Mo',
			'special_service' => 'Special Service',
			'mobile' => 'Mobile',
			'content' => 'Content',
			'reply_time' => 'Reply Time',
			'insert_time' => 'Insert Time',
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

		$criteria->compare('reply_id',$this->reply_id);
		$criteria->compare('mo_id',$this->mo_id,true);
		$criteria->compare('special_service',$this->special_service,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('reply_time',$this->reply_time,true);
		$criteria->compare('insert_time',$this->insert_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}