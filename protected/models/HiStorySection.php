<?php

/**
 * This is the model class for table "hi_story_section".
 *
 * The followings are the available columns in table 'hi_story_section':
 * @property string $section_id
 * @property string $content
 * @property integer $story_id
 * @property integer $customer_id
 * @property integer $section_layer
 * @property string $insert_time
 */
class HiStorySection extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hi_story_section';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('story_id, customer_id, section_layer, insert_time', 'required'),
			array('story_id, customer_id, section_layer', 'numerical', 'integerOnly'=>true),
			array('content', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('section_id, content, story_id, customer_id, section_layer, insert_time', 'safe', 'on'=>'search'),
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
            'customer'   => array(self::HAS_ONE, 'HiCustomer', '', 'on' => 'ss.customer_id = cr.customer_id', 'alias' => 'cr'),
            'comments' => array(self::HAS_MANY, 'HiStoryComment', '', 'on' => 'ss.section_id = sc.section_id'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'section_id' => 'Section',
			'content' => 'Content',
			'story_id' => 'Story',
			'customer_id' => 'Owner',
			'section_layer' => 'Section Layer',
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

		$criteria->compare('section_id',$this->section_id,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('story_id',$this->story_id);
		$criteria->compare('customer_id',$this->customer_id);
		$criteria->compare('section_layer',$this->section_layer);
		$criteria->compare('insert_time',$this->insert_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function findMaxGroup($parent_id){
        $sql = 'select max(section_group) from hi_story_section where parent_id = '. $parent_id;
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);

        $result = $command->queryAll();
        return $result[0]['max(section_group)'];
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HiStorySection the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope()
    {
        return array(
            'alias' => 'ss',
            'order' => 'ss.section_layer ASC');
    }
}
