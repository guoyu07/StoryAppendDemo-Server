<?php

/**
 * This is the model class for table "hi_question_answer".
 *
 * The followings are the available columns in table 'hi_question_answer':
 * @property string $answer_id
 * @property string $content
 * @property integer $customer_id
 * @property integer $question_id
 * @property string $insert_date
 */
class HiQuestionAnswer extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hi_question_answer';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('content, customer_id, question_id, insert_date', 'required'),
			array('customer_id, question_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('answer_id, content, customer_id, question_id, insert_date', 'safe', 'on'=>'search'),
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
            'customer'   => array(self::HAS_ONE, 'HiCustomer', '', 'on' => 'qa.customer_id = ccs.customer_id', 'alias' => 'ccs'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'answer_id' => 'Answer',
			'content' => 'Content',
			'customer_id' => 'Customer',
			'question_id' => 'Question',
			'insert_date' => 'Insert Date',
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

		$criteria->compare('answer_id',$this->answer_id,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('customer_id',$this->customer_id);
		$criteria->compare('question_id',$this->question_id);
		$criteria->compare('insert_date',$this->insert_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HiQuestionAnswer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope()
    {
        return array(
            'alias' => 'qa',
            'order' => 'qa.answer_id DESC');
    }
}
