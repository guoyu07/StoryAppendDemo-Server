<?php

/**
 * This is the model class for table "ht_order_comment_complaint".
 *
 * The followings are the available columns in table 'ht_order_comment_complaint':
 * @property integer $complaint_id
 * @property integer $complaint_type
 * @property string $complaint_md
 * @property integer $detail_type
 * @property string $detail_md
 */
class HtOrderCommentComplaint extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_order_comment_complaint';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('complaint_type, detail_type', 'numerical', 'integerOnly'=>true),
			array('complaint_md, detail_md', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('complaint_id, complaint_type, complaint_md, detail_type, detail_md', 'safe', 'on'=>'search'),
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
			'complaint_id' => 'Complaint',
			'complaint_type' => 'Complaint Type',
			'complaint_md' => 'Complaint Md',
			'detail_type' => 'Detail Type',
			'detail_md' => 'Detail Md',
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

		$criteria->compare('complaint_id',$this->complaint_id);
		$criteria->compare('complaint_type',$this->complaint_type);
		$criteria->compare('complaint_md',$this->complaint_md,true);
		$criteria->compare('detail_type',$this->detail_type);
		$criteria->compare('detail_md',$this->detail_md,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtOrderCommentComplaint the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function defaultScope()
	{
		return array(
			'alias' => 'cc',
		);
	}
}
