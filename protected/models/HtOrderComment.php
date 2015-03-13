<?php

/**
 * This is the model class for table "ht_order_comment".
 *
 * The followings are the available columns in table 'ht_order_comment':
 * @property integer $comment_id
 * @property integer $order_id
 * @property integer $user_id
 * @property string $comment
 * @property integer $proc_status
 * @property string $date_added
 * @property string $date_modified
 * @property string $date_proc
 * @property integer $type
 */
class HtOrderComment extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_order_comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, user_id, comment, date_added, date_modified, date_proc', 'required'),
			array('order_id, user_id, proc_status, type', 'numerical', 'integerOnly'=>true),
			array('comment', 'length', 'max'=>1000),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('comment_id, order_id, user_id, comment, proc_status, date_added, type, date_proc date_modified', 'safe', 'on'=>'search'),
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
			'complaint' => array(self::HAS_MANY, 'HtOrderCommentComplaint', '', 'on' => 'oc.comment_id = cc.comment_id'),
		);
	}

    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'comment_id' => 'Comment',
			'order_id' => 'Order',
			'user_id' => 'User',
			'comment' => 'Comment',
			'proc_status' => 'Proc Status',
			'date_added' => 'Date Added',
			'date_modified' => 'Date Modified',
			'type' => 'Type',
			'date_proc' => 'Date Proc'
		);
	}

    public function defaultScope()
    {
        return array(
            'alias' => 'oc',
            'order' => 'oc.date_added',
        );
    }

    public function scopes()
    {
        return array(
            'todo' => array('proc_status = 1'),
            'done' => array('proc_status = 2'),
            'common' => array('proc_status = 0'),
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

		$criteria->compare('comment_id',$this->comment_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('proc_status',$this->proc_status);
		$criteria->compare('date_added',$this->date_added,true);
		$criteria->compare('date_modified',$this->data_modified,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('date_proc',$this->date_proc,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtOrderComment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
