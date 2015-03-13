<?php

/**
 * This is the model class for table "ht_sms_history".
 *
 * The followings are the available columns in table 'ht_sms_history':
 * @property integer $history_id
 * @property integer $batch_id
 * @property string $content
 * @property string $mobile
 * @property string $rrid
 * @property string $insert_time
 */
class HtSmsHistory extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return HtSmsHistory the static model class
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
		return 'ht_sms_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('content, mobile, rrid, insert_time', 'required'),
			array('batch_id', 'numerical', 'integerOnly'=>true),
			array('content', 'length', 'max'=>255),
			array('rrid', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('history_id, batch_id, content, mobile, rrid, insert_time', 'safe', 'on'=>'search'),
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
			'history_id' => 'History',
			'batch_id' => 'Batch',
			'content' => 'Content',
			'mobile' => 'Mobile',
			'rrid' => 'Rrid',
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

		$criteria->compare('history_id',$this->history_id);
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('rrid',$this->rrid,true);
		$criteria->compare('insert_time',$this->insert_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}