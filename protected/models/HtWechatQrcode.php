<?php

/**
 * This is the model class for table "ht_wechat_qrcode".
 *
 * The followings are the available columns in table 'ht_wechat_qrcode':
 * @property integer $scene_id
 * @property string $ticket
 * @property integer $expire_seconds
 * @property integer $create_time
 * @property string $action_name
 */
class HtWechatQrcode extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_wechat_qrcode';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('scene_id, ticket, expire_seconds, create_time, action_name', 'required'),
			array('scene_id, expire_seconds, create_time', 'numerical', 'integerOnly'=>true),
			array('ticket', 'length', 'max'=>256),
			array('action_name', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('scene_id, ticket, expire_seconds, create_time, action_name', 'safe', 'on'=>'search'),
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
			'scene_id' => 'Scene',
			'ticket' => 'Ticket',
			'expire_seconds' => 'Expire Seconds',
			'create_time' => 'Create Time',
			'action_name' => 'Action Name',
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

		$criteria->compare('scene_id',$this->scene_id);
		$criteria->compare('ticket',$this->ticket,true);
		$criteria->compare('expire_seconds',$this->expire_seconds);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('action_name',$this->action_name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtWechatQrcode the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
