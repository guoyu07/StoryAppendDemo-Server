<?php

/**
 * This is the model class for table "feedback".
 *
 * The followings are the available columns in table 'feedback':
 * @property integer $id
 * @property integer $app_id
 * @property string $ver
 * @property string $ctime
 * @property string $issue
 * @property integer $terminal_type
 * @property string $terminal_os
 * @property string $sn
 * @property string $uid
 * @property integer $status
 * @property string $comments
 */
class Feedback extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'feedback';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('app_id, terminal_type, status', 'numerical', 'integerOnly'=>true),
			array('ver', 'length', 'max'=>64),
			array('issue', 'length', 'max'=>512),
			array('terminal_os, sn, uid', 'length', 'max'=>128),
			array('ctime, comments', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, app_id, ver, ctime, issue, terminal_type, terminal_os, sn, uid, status, comments', 'safe', 'on'=>'search'),
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
			'app_id' => 'App',
			'ver' => 'Ver',
			'ctime' => 'Ctime',
			'issue' => 'Issue',
			'terminal_type' => 'Terminal Type',
			'terminal_os' => 'Terminal Os',
			'sn' => 'Sn',
			'uid' => 'Uid',
			'status' => 'Status',
			'comments' => 'Comments',
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
		$criteria->compare('app_id',$this->app_id);
		$criteria->compare('ver',$this->ver,true);
		$criteria->compare('ctime',$this->ctime,true);
		$criteria->compare('issue',$this->issue,true);
		$criteria->compare('terminal_type',$this->terminal_type);
		$criteria->compare('terminal_os',$this->terminal_os,true);
		$criteria->compare('sn',$this->sn,true);
		$criteria->compare('uid',$this->uid,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('comments',$this->comments,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Feedback the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
