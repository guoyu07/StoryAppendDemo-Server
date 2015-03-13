<?php

/**
 * This is the model class for table "ht_customer_fund_history".
 *
 * The followings are the available columns in table 'ht_customer_fund_history':
 * @property integer $history_id
 * @property integer $did
 * @property integer $order_id
 * @property integer $customer_id
 * @property integer $add_or_sub
 * @property integer $amount
 * @property string add_date
 * @property string sub_date
 * @property string expire_date
 * @property string $comment
 */
class HtCustomerFundHistory extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_customer_fund_history';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('customer_id, amount', 'required'),
            array('did, order_id, customer_id, add_or_sub, amount', 'numerical', 'integerOnly' => true),
            array('comment', 'length', 'max' => 200),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('history_id, did, order_id, customer_id, add_or_sub, amount, comment', 'safe', 'on' => 'search'),
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
            'customer' => array(self::HAS_ONE, 'HtCustomer', '', 'on'=>'customer.customer_id = fund_history.customer_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'history_id' => 'History',
            'did' => 'Dandelion ID',
            'order_id' => 'Order ID',
            'customer_id' => 'Customer',
            'add_or_sub' => '0：减少；1：增加；',
            'amount' => '金额',
            'add_date' => 'Add Date',
            'sub_date' => 'Subtract Date',
            'expire_date' => 'Expire Date',
            'comment' => '备注',
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

        $criteria = new CDbCriteria;

        $criteria->compare('history_id', $this->history_id);
        $criteria->compare('did', $this->did);
        $criteria->compare('order_id', $this->order_id);
        $criteria->compare('customer_id', $this->customer_id);
        $criteria->compare('add_or_sub', $this->add_or_sub);
        $criteria->compare('amount', $this->amount);
        $criteria->compare('comment', $this->comment, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtCustomerFundHistory the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'fund_history',
        );
    }
}
