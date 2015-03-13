<?php

/**
 * This is the model class for table "hc_return".
 *
 * The followings are the available columns in table 'hc_return':
 * @property integer $return_id
 * @property integer $order_id
 * @property integer $order_product_id
 * @property integer $product_id
 * @property integer $customer_id
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $telephone
 * @property string $product
 * @property string $model
 * @property integer $quantity
 * @property integer $opened
 * @property integer $return_reason_id
 * @property string $charge_amount
 * @property integer $return_action_id
 * @property integer $return_status_id
 * @property string $comment
 * @property string $date_ordered
 * @property string $date_added
 * @property string $date_modified
 */
class HcReturn extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_return';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, product_id, customer_id, firstname, lastname, email, telephone, product, model, quantity, opened, return_reason_id, return_action_id, return_status_id, date_ordered, date_added, date_modified', 'required'),
			array('order_id, order_product_id, product_id, customer_id, quantity, opened, return_reason_id, return_action_id, return_status_id', 'numerical', 'integerOnly'=>true),
			array('firstname, lastname, telephone', 'length', 'max'=>32),
			array('email', 'length', 'max'=>96),
			array('product', 'length', 'max'=>255),
			array('model', 'length', 'max'=>64),
			array('charge_amount', 'length', 'max'=>10),
			array('comment', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('return_id, order_id, order_product_id, product_id, customer_id, firstname, lastname, email, telephone, product, model, quantity, opened, return_reason_id, charge_amount, return_action_id, return_status_id, comment, date_ordered, date_added, date_modified', 'safe', 'on'=>'search'),
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
			'return_id' => 'Return',
			'order_id' => 'Order',
			'order_product_id' => 'Order Product',
			'product_id' => 'Product',
			'customer_id' => 'Customer',
			'firstname' => 'Firstname',
			'lastname' => 'Lastname',
			'email' => 'Email',
			'telephone' => 'Telephone',
			'product' => 'Product',
			'model' => 'Model',
			'quantity' => 'Quantity',
			'opened' => 'Opened',
			'return_reason_id' => 'Return Reason',
			'charge_amount' => 'Charge Amount',
			'return_action_id' => 'Return Action',
			'return_status_id' => 'Return Status',
			'comment' => 'Comment',
			'date_ordered' => 'Date Ordered',
			'date_added' => 'Date Added',
			'date_modified' => 'Date Modified',
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

		$criteria->compare('return_id',$this->return_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('order_product_id',$this->order_product_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('customer_id',$this->customer_id);
		$criteria->compare('firstname',$this->firstname,true);
		$criteria->compare('lastname',$this->lastname,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('product',$this->product,true);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('opened',$this->opened);
		$criteria->compare('return_reason_id',$this->return_reason_id);
		$criteria->compare('charge_amount',$this->charge_amount,true);
		$criteria->compare('return_action_id',$this->return_action_id);
		$criteria->compare('return_status_id',$this->return_status_id);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('date_ordered',$this->date_ordered,true);
		$criteria->compare('date_added',$this->date_added,true);
		$criteria->compare('date_modified',$this->date_modified,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcReturn the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
