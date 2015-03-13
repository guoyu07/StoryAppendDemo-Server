<?php

/**
 * This is the model class for table "ht_order_product_history".
 *
 * The followings are the available columns in table 'ht_order_product_history':
 * @property integer $order_product_history_id
 * @property integer $order_id
 * @property integer $order_product_id
 * @property integer $status_id
 * @property string $comment
 * @property string $date_added
 */
class HtOrderProductHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_order_product_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, order_product_id, status_id, comment', 'required'),
			array('order_id, order_product_id, status_id', 'numerical', 'integerOnly'=>true),
			array('date_added', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('order_product_history_id, order_id, order_product_id, status_id, comment, date_added', 'safe', 'on'=>'search'),
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
			'order_product_history_id' => 'Order Product History',
			'order_id' => 'Order',
			'order_product_id' => 'Order Product',
			'status_id' => 'Status',
			'comment' => 'Comment',
			'date_added' => 'Date Added',
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

		$criteria->compare('order_product_history_id',$this->order_product_history_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('order_product_id',$this->order_product_id);
		$criteria->compare('status_id',$this->status_id);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('date_added',$this->date_added,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtOrderProductHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
