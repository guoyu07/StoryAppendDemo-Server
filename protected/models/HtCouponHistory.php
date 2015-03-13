<?php

/**
 * This is the model class for table "ht_coupon_history".
 *
 * The followings are the available columns in table 'ht_coupon_history':
 * @property integer $coupon_history_id
 * @property integer $coupon_id
 * @property integer $order_id
 * @property integer $customer_id
 * @property string $amount
 * @property string $date_added
 */
class HtCouponHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_coupon_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('coupon_id, order_id, customer_id, amount, date_added', 'required'),
			array('coupon_id, order_id, customer_id', 'numerical', 'integerOnly'=>true),
			array('amount', 'length', 'max'=>15),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('coupon_history_id, coupon_id, order_id, customer_id, amount, date_added', 'safe', 'on'=>'search'),
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
            'coupon' => array(self::HAS_ONE, 'HtCoupon', '', 'on'=>'coupon_history.coupon_id = coupon.coupon_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'coupon_history_id' => 'Coupon History',
			'coupon_id' => 'Coupon',
			'order_id' => 'Order',
			'customer_id' => 'Customer',
			'amount' => 'Amount',
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

		$criteria->compare('coupon_history_id',$this->coupon_history_id);
		$criteria->compare('coupon_id',$this->coupon_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('customer_id',$this->customer_id);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('date_added',$this->date_added,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtCouponHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope()
    {
        return array(
            'alias' => 'coupon_history',
        );
    }

    public function couponUsed($coupon_id, $customer_id = 0) {
        $c = new CDbCriteria();
        $c->addCondition('coupon_id=' . (int)$coupon_id);
        if($customer_id > 0) {
            $c->addCondition('customer_id=' . (int)$customer_id);
        }
        $item = $this->find($c);
        return !empty($item);
    }

    public function addCouponHistory($coupon_id, $order_id, $customer_id, $amount) {
        $ch = new HtCouponHistory();
        $ch->coupon_id = $coupon_id;
        $ch->order_id = $order_id;
        $ch->customer_id = $customer_id;
        $ch->amount = $amount;
        $ch->date_added = date('Y-m-d H:i:s');
        $result = $ch->save();
    }
}
