<?php

/**
 * This is the model class for table "ht_cps_history".
 *
 * The followings are the available columns in table 'ht_cps_history':
 * @property integer $cps_history_id
 * @property string $channel
 * @property integer $order_id
 * @property integer $status_id
 * @property string $content
 * @property string $insert_time
 */
class HtCpsHistory extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return HtCpsHistory the static model class
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
		return 'ht_cps_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('channel, order_id, status_id, content, insert_time', 'required'),
			array('order_id, status_id', 'numerical', 'integerOnly'=>true),
			array('channel', 'length', 'max'=>16),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('cps_history_id, channel, order_id, status_id, content, insert_time', 'safe', 'on'=>'search'),
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
			'cps_history_id' => 'Cps History',
			'channel' => 'Channel',
			'order_id' => 'Order',
			'status_id' => 'Status',
			'content' => 'Content',
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

		$criteria->compare('cps_history_id',$this->cps_history_id);
		$criteria->compare('channel',$this->channel,true);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('status_id',$this->status_id);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('insert_time',$this->insert_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}