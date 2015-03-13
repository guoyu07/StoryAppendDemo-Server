<?php

/**
 * This is the model class for table "ht_dandelion".
 *
 * The followings are the available columns in table 'ht_dandelion':
 * @property integer $did
 * @property integer $coupon_id
 * @property integer $owner_id
 * @property integer $use_limit
 * @property integer $return_or_not
 * @property integer $max_return_count
 * @property integer $return_amount
 * @property string $fund_expire_date
 * @property integer $share_max_time
 * @property string $share_date_limit
 * @property string $insert_time
 */
class HtDandelion extends CActiveRecord
{
    const USE_LIMIT_NONE = 0;
    const USE_LIMIT_SELF = 1;
    const USE_LIMIT_OTHER = 2;

    const RETURN_TYPE_ONCE = 1;
    const RETURN_TYPE_MULTIPLE = 2;


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_dandelion';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('coupon_id, owner_id, use_limit, return_or_not, max_return_count, return_amount, share_max_time, share_date_limit, insert_time', 'required'),
            array('coupon_id, owner_id, use_limit, return_or_not, max_return_count, return_amount, share_max_time', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('did, coupon_id, owner_id, use_limit, return_or_not, max_return_count, return_amount, share_max_time, share_date_limit, insert_time', 'safe', 'on' => 'search'),
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
            'dandelion_pickup' => array(self::HAS_MANY, 'HtDandelionPickup', '', 'on'=>'dandelion_pickup.did = dandelion.did'),
            'coupon' => array(self::HAS_ONE, 'HtCoupon', '', 'on'=>'coupon.coupon_id = dandelion.coupon_id'),
            'fund_history' => array(self::HAS_MANY, 'HtCustomerFundHistory', '', 'on'=>'fund_history.did = dandelion.did'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'did' => 'Did',
            'coupon_id' => 'Coupon',
            'owner_id' => 'Owner',
            'use_limit' => '0：不限制；1：只自己；2：只别人',
            'return_or_not' => '0：不返利；1：返利',
            'max_return_count' => '1：一次；2：2次',
            'return_amount' => '每次返利多少',
            'fund_expire_date' => '基金过期时间',
            'share_max_time' => '最大分享次数；0：不限制；',
            'share_date_limit' => '分享截止日期',
            'insert_time' => '插入时间',
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

        $criteria->compare('did', $this->did);
        $criteria->compare('coupon_id', $this->coupon_id);
        $criteria->compare('owner_id', $this->owner_id);
        $criteria->compare('use_limit', $this->use_limit);
        $criteria->compare('return_or_not', $this->return_or_not);
        $criteria->compare('max_return_count', $this->max_return_count);
        $criteria->compare('return_amount', $this->return_amount);
        $criteria->compare('fund_expire_date', $this->fund_expire_date);
        $criteria->compare('share_max_time', $this->share_max_time);
        $criteria->compare('share_date_limit', $this->share_date_limit, true);
        $criteria->compare('insert_time', $this->insert_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtDandelion the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'dandelion',
        );
    }
}
