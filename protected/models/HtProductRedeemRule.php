<?php
/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/15/14
 * Time: 6:12 PM
 */

/**
 * This is the model class for table "ht_product_redeem_rule".
 *
 * The followings are the available columns in table 'ht_product_redeem_rule':
 * @property integer $product_id
 * @property integer $redeem_type
 * @property string $expire_date
 * @property string $duration
 * @property string $usage_limit
 */
class HtProductRedeemRule extends CActiveRecord
{
    const  TOUR_DATE_ONLY = 1;
    const  ISSUED_DYNAMIC = 2;
    const  ABSOLUTE_EXPIRED_DATE = 3;
    const  TOUR_DATE_DURATION = 4;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtProductRedeemRule the static model class
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
        return 'ht_product_redeem_rule';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, redeem_type', 'required'),
            array('product_id, redeem_type', 'numerical', 'integerOnly' => true),
            array('duration', 'length', 'max' => 32),
            array('usage_limit', 'length', 'max' => 1024),
            array('expire_date', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('product_id, redeem_type, expire_date, duration, usage_limit', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'product_id' => 'Product',
            'redeem_type' => 'Redeem Type',
            'expire_date' => 'Expire Date',
            'duration' => 'Duration',
            'usage_limit' => 'Usage Limit',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('redeem_type', $this->redeem_type);
        $criteria->compare('expire_date', $this->expire_date, true);
        $criteria->compare('duration', $this->duration, true);
        $criteria->compare('usage_limit', $this->usage_limit, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getRuleDesc()
    {
        switch ($this->redeem_type) {
            case self::TOUR_DATE_ONLY:
                $desc = '您选择的日期当天兑换';//在您选择的使用日期当天兑换有效
                break;
            case self::TOUR_DATE_DURATION:
                $desc = sprintf('您选择的日期起%s内兑换',
                                str_ireplace(['day', 'month', 'year'], ['天', '个月', '年'], $this->duration));
                break;
            case self::ABSOLUTE_EXPIRED_DATE:
                $desc = sprintf('兑换截止到:%s', $this->expire_date);
                break;
            case self::ISSUED_DYNAMIC:
                $desc = sprintf('下单后%s兑换', str_ireplace(['day', 'month', 'year'], ['天', '个月', '年'], $this->duration));
                break;
            default:
                $desc = '您选择的日期当天兑换';
        }

        if ($this->usage_limit) {
            $desc .= '<br>' . sprintf('兑换后%s使用有效', $this->usage_limit);
        }

        return $desc;
    }

    protected function beforeSave()
    {
        HtProduct::clearCachedRuleDesc($this->product_id);

        return parent::beforeSave();
    }
}