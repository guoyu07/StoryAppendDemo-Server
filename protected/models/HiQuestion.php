<?php

/**
 * This is the model class for table "hi_question".
 *
 * The followings are the available columns in table 'hi_question':
 * @property string $question_id
 * @property string $content
 */
class HiQuestion extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hi_question';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('content', 'required'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('question_id, content', 'safe', 'on'=>'search'),
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
            'answers'   => array(self::HAS_MANY, 'HiQuestionAnswer', '', 'on' => 'q.question_id = qa.question_id'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'question_id' => 'Question',
			'content' => 'Content',
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

		$criteria->compare('question_id',$this->question_id,true);
		$criteria->compare('content',$this->content,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HiQuestion the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope()
    {
        return array(
            'alias' => 'q',
            'order' => 'q.question_id DESC');
    }
}
