<?php

/**
 * This is the model class for table "ht_order_trace".
 *
 * The followings are the available columns in table 'ht_order_trace':
 * @property integer $order_id
 * @property string $first_uri
 * @property string $url_referer
 * @property string $ip
 * @property string $user_agent
 * @property string $accept_language
 * @property string $channel
 * @property string $cookies
 * @property string $insert_time
 */
class HtOrderTrace extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_order_trace';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, first_uri, url_referer, ip, user_agent, accept_language, cookies', 'required'),
			array('order_id', 'numerical', 'integerOnly'=>true),
			array('first_uri, user_agent, accept_language', 'length', 'max'=>255),
			array('ip', 'length', 'max'=>40),
            array('channel', 'max'=>16),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('order_id, first_uri, url_referer, ip, user_agent, accept_language', 'safe', 'on'=>'search'),
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
			'order_id' => 'Order',
			'first_uri' => 'First Uri',
			'url_referer' => 'Url Referer',
			'ip' => 'Ip',
			'user_agent' => 'User Agent',
			'accept_language' => 'Accept Language',
            'channel' => 'Channel',
            'cookies' => 'Cookie',
            'insert_time' => 'Insert Time',
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

		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('first_uri',$this->first_uri,true);
		$criteria->compare('url_referer',$this->url_referer,true);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('user_agent',$this->user_agent,true);
		$criteria->compare('accept_language',$this->accept_language,true);
        $criteria->compare('channel',$this->channel,true);
        $criteria->compare('cookies',$this->cookies,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtOrderTrace the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
