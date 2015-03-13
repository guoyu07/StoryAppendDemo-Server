<?php
/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/15/14
 * Time: 6:11 PM
 */


/**
 * This is the model class for table "ht_product_date_rule".
 *
 * The followings are the available columns in table 'ht_product_date_rule':
 * @property integer $product_id
 * @property integer $need_tour_date
 * @property string $close_dates
 * @property string $lead_time
 * @property string $buy_in_advance
 * @property integer $sale_range_type
 * @property string $sale_range
 * @property string $from_date
 * @property string $to_date
 * @property integer $day_type
 * @property integer $shipping_day_type
 */
class HtProductDateRule extends HActiveRecord
{
    const TYPE_TO_DATE = 0;
    const TYPE_RANGE = 1;

    const CANLENDAR_DAY = 0;
    const WORKING_DAY = 1;

    public $shipping_desc;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtProductDateRule the static model class
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
        return 'ht_product_date_rule';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, sale_range_type, from_date, to_date', 'required'),
            array('product_id, need_tour_date, sale_range_type, day_type, shipping_day_type', 'numerical', 'integerOnly' => true),
            array('close_dates', 'length', 'max' => 1024),
            array('lead_time, buy_in_advance, sale_range', 'length', 'max' => 10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('product_id, need_tour_date, close_dates, lead_time, buy_in_advance, sale_range_type, sale_range, from_date, to_date, day_type, shipping_day_type', 'safe', 'on' => 'search'),
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
            'need_tour_date' => 'Need Tour Date',
            'close_dates' => 'Close Dates',
            'lead_time' => 'Lead Time',
            'buy_in_advance' => 'Buy In Advance',
            'sale_range_type' => 'Sale Range Type',
            'sale_range' => 'Sale Range',
            'from_date' => 'From Date',
            'to_date' => 'To Date',
            'day_type' => 'Day type',
            'shipping_day_type' => 'Shipping Day Type',
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
        $criteria->compare('need_tour_date', $this->need_tour_date);
        $criteria->compare('close_dates', $this->close_dates, true);
        $criteria->compare('lead_time', $this->lead_time, true);
        $criteria->compare('buy_in_advance', $this->buy_in_advance, true);
        $criteria->compare('sale_range_type', $this->sale_range_type);
        $criteria->compare('sale_range', $this->sale_range, true);
        $criteria->compare('from_date', $this->from_date, true);
        $criteria->compare('to_date', $this->to_date, true);
        $criteria->compare('day_type', $this->day_type);
        $criteria->compare('shipping_day_type', $this->shipping_day_type);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'pdr',
        );
    }

    public function afterFind()
    {
        $this->shipping_desc = $this->getShippingDesc();
    }

    public function getShippingDesc()
    {
        $day_type = $this->shipping_day_type == self::CANLENDAR_DAY ? '天' : '个工作日';
        $lead_time = $this->lead_time;
        if (strtolower($lead_time) == '0day') {
            $lead_time = '2day';
        }
        $desc = str_ireplace(['day', 'month', 'year'], [$day_type, '个月', '年'], $lead_time);

        return sprintf('%s内发送确认单', $desc);
    }

    public function getBuyDesc()
    {
        if ($this->buy_in_advance == '0day' || $this->buy_in_advance == '0Day') {
            $desc = '';
        } else {
            $desc = sprintf('需提前%s购买', str_ireplace(['day', 'month'],
                                                    [($this->day_type == self::WORKING_DAY) ? '个工作日' : '天', '个月'],
                                                    $this->buy_in_advance));
        }

        return $desc;
    }

    protected function beforeSave()
    {
        HtProduct::clearCachedRuleDesc($this->product_id);
        HtProductPricePlan::clearCache($this->product_id);

        return parent::beforeSave();
    }
}