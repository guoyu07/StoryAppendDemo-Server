<?php

/**
 * This is the model class for table "ht_ip_filter".
 *
 * The followings are the available columns in table 'ht_ip_filter':
 * @property integer $filter_id
 * @property string $filter_type
 * @property string $ips
 * @property string $channel
 * @property integer $valid
 */
class HtIpFilter extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return HtIpFilter the static model class
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
		return 'ht_ip_filter';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('filter_type, ips, channel, valid', 'required'),
			array('valid', 'numerical', 'integerOnly'=>true),
			array('filter_type', 'length', 'max'=>16),
			array('ips', 'length', 'max'=>255),
			array('channel', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('filter_id, filter_type, ips, channel, valid', 'safe', 'on'=>'search'),
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
			'filter_id' => 'Filter',
			'filter_type' => 'Filter Type',
			'ips' => 'Ips',
			'channel' => 'Channel',
			'valid' => 'Valid',
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

		$criteria->compare('filter_id',$this->filter_id);
		$criteria->compare('filter_type',$this->filter_type,true);
		$criteria->compare('ips',$this->ips,true);
		$criteria->compare('channel',$this->channel,true);
		$criteria->compare('valid',$this->valid);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}