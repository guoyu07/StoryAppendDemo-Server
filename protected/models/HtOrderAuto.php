<?php

/**
 * This is the model class for table "ht_order_auto".
 *
 * The followings are the available columns in table 'ht_order_auto':
 * @property integer $auto_id
 * @property integer $order_id
 * @property integer $status
 * @property integer $need_time
 * @property string $action
 * @property string $comment
 * @property string $date_added
 * @property string $date_modified
 */
class HtOrderAuto extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return HtOrderAuto the static model class
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
		return 'ht_order_auto';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, status, need_time, action, comment, date_added', 'required'),
			array('order_id, status, need_time', 'numerical', 'integerOnly'=>true),
			array('action', 'length', 'max'=>16),
			array('comment', 'length', 'max'=>128),
			array('date_modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('auto_id, order_id, status, need_time, action, comment, date_added, date_modified', 'safe', 'on'=>'search'),
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
			'auto_id' => 'Auto',
			'order_id' => 'Order',
			'status' => 'Status',
			'need_time' => 'Need Time',
			'action' => 'Action',
			'comment' => 'Comment',
			'date_added' => 'Date Added',
			'date_modified' => 'Date Modified',
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

		$criteria->compare('auto_id',$this->auto_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('need_time',$this->need_time);
		$criteria->compare('action',$this->action,true);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('date_added',$this->date_added,true);
		$criteria->compare('date_modified',$this->date_modified,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}