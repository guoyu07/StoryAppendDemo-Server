<?php

/**
 * This is the model class for table "ht_activity_rule".
 *
 * The followings are the available columns in table 'ht_activity_rule':
 * @property integer $activity_id
 * @property integer $logged
 * @property string $payment_types
 * @property integer $allow_use_coupon
 * @property integer $display_only_in_activity
 * @property integer $terminal
 * @property integer $sale_only_in_activity
 * @property integer $max_order_num
 * @property string $payment_reservation
 * @property integer $activity_coupon_id
 * @property string $activity_coupon_title
 */
class HtActivityRule extends CActiveRecord
{
    const DIS_ANYWAY = 0;
    const DIS_ONLY_ACTIVITY = 1;
    const DIS_ONGOING_ACTIVITY = 2;

    const T_MOBILE = 1;
    const T_PC = 2;
    const T_ALL = 3;

    const CACHE_ONE = 'HtActivity_rule_';
    const CACHE_ONE_WITH_COUPON = 'HtActivity_rule_with_coupon_';

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtActivityRule the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_activity_rule';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('activity_id, payment_types, allow_use_coupon, activity_coupon_id, activity_coupon_title', 'required'),
            array('activity_id, logged, allow_use_coupon, display_only_in_activity, terminal, sale_only_in_activity, max_order_num, activity_coupon_id', 'numerical', 'integerOnly'=>true),
            array('payment_reservation', 'length', 'max'=>16),
            array('activity_coupon_title', 'length', 'max'=>64),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('activity_id, logged, payment_types, allow_use_coupon, display_only_in_activity, terminal, sale_only_in_activity, max_order_num, payment_reservation, activity_coupon_id, activity_coupon_title', 'safe', 'on'=>'search'),
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
            'coupon' => array(self::HAS_ONE, 'HtCoupon', '', 'on' => 'activity_r.activity_coupon_id=coupon.coupon_id'),
        );
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'activity_r',
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'activity_id' => 'Activity',
            'logged' => 'Logged',
            'payment_types' => 'Payment Types',
            'allow_use_coupon' => 'Allow Use Coupon',
            'display_only_in_activity' => 'Display Only In Activity',
            'terminal' => 'Terminal',
            'sale_only_in_activity' => 'Sale Only In Activity',
            'max_order_num' => 'Max Order Num',
            'payment_reservation' => 'Payment Reservation',
            'activity_coupon_id' => 'Activity Coupon',
            'activity_coupon_title' => 'Activity Coupon Title',
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

        $criteria->compare('activity_id',$this->activity_id);
        $criteria->compare('logged',$this->logged);
        $criteria->compare('payment_types',$this->payment_types,true);
        $criteria->compare('allow_use_coupon',$this->allow_use_coupon);
        $criteria->compare('display_only_in_activity',$this->display_only_in_activity);
        $criteria->compare('terminal',$this->terminal);
        $criteria->compare('sale_only_in_activity',$this->sale_only_in_activity);
        $criteria->compare('max_order_num',$this->max_order_num);
        $criteria->compare('payment_reservation',$this->payment_reservation,true);
        $criteria->compare('activity_coupon_id',$this->activity_coupon_id);
        $criteria->compare('activity_coupon_title',$this->activity_coupon_title,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    protected function beforeSave()
    {
        Yii::app()->cache->delete(HtActivity::CACHE_ALL_WITH_ACTIVITY_PRODUCT_ACTIVITY_RULE_PREFIX . $this->activity_id);
        Yii::app()->cache->delete(HtActivitys::CACHE_ALL_WITH_ACTIVITY_PRODUCT_ACTIVITY_RULE_PREFIX . $this->activity_id . '_1');
        Yii::app()->cache->delete(HtActivitys::CACHE_ALL_WITH_ACTIVITY_PRODUCT_ACTIVITY_RULE_PREFIX . 0 . '_1');
        Yii::app()->cache->delete(HtActivitys::CACHE_ONE_WITH_ACTIVITY_PRODUCT_ACTIVITY_RULE_PREFIX . $this->activity_id);
        Yii::app()->cache->delete(HtActivity::CACHE_ONE_WITH_ACTIVITY_RULE_PREFIX . $this->activity_id);
        Yii::app()->cache->delete(HtActivityRule::CACHE_ONE_WITH_COUPON . $this->activity_id);
        Yii::app()->cache->delete(HtActivityRule::CACHE_ONE . $this->activity_id);

        return parent::beforeSave();
    }


    public function findOneByPk($activity_id)
    {
        $key = HtActivityRule::CACHE_ONE . $activity_id;
        $result = Yii::app()->cache->get($key);
        if (empty($result)) {
            $result = $this->findByPk($activity_id);
            $result = Converter::convertModelToArray($result);
            Yii::app()->cache->set($key, $result, 10 * 60);
        }

        return $result;
    }

    public function findOneWithCouponByPk($activity_id)
    {
        $key = HtActivityRule::CACHE_ONE_WITH_COUPON . $activity_id;
        $result = Yii::app()->cache->get($key);
        if (empty($result)) {
            $result = $this->with('coupon')->findByPk($activity_id);
            $result = Converter::convertModelToArray($result);
            Yii::app()->cache->set($key, $result,  10 * 60);
        }

        return $result;
    }
}
