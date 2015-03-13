<?php

/**
 * This is the model class for table "ht_product_price_plan_special".
 *
 * The followings are the available columns in table 'ht_product_price_plan_special':
 * @property integer $price_plan_id
 * @property integer $product_id
 * @property integer $valid_region
 * @property string $from_date
 * @property string $to_date
 * @property string $currency
 * @property integer $need_tier_pricing
 * @property string $special_codes
 * @property string $reseller
 * @property string $slogan
 */
class HtProductPricePlanSpecial extends CActiveRecord
{
    const ALL_REGION = 0;
    const DATE_RANGE = 1;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_price_plan_special';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id', 'required'),
            array('product_id, valid_region, need_tier_pricing', 'numerical', 'integerOnly' => true),
            array('currency', 'length', 'max' => 4),
            array('special_codes', 'length', 'max' => 1024),
            array('reseller', 'length', 'max' => 100),
            array('slogan', 'length', 'max' => 200),
            array('from_date, to_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('price_plan_id, product_id, valid_region, from_date, to_date, currency, need_tier_pricing, special_codes, reseller, slogan', 'safe', 'on' => 'search'),
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
            'items' => array(self::HAS_MANY, 'HtProductPricePlanItem', '', 'on' => 'price_plan_item.price_plan_id = price_plan_special.price_plan_id AND price_plan_item.is_special=1'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'price_plan_id' => 'Price Plan',
            'product_id' => 'Product',
            'valid_region' => '0：整个区间；1：自定义区间',
            'from_date' => 'From Date',
            'to_date' => 'To Date',
            'currency' => 'Currency',
            'need_tier_pricing' => '0：不需要；1：需要',
            'special_codes' => 'Special Codes',
            'reseller' => '经销商',
            'slogan' => '口号',
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

        $criteria->compare('price_plan_id', $this->price_plan_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('valid_region', $this->valid_region);
        $criteria->compare('from_date', $this->from_date, true);
        $criteria->compare('to_date', $this->to_date, true);
        $criteria->compare('currency', $this->currency, true);
        $criteria->compare('need_tier_pricing', $this->need_tier_pricing);
        $criteria->compare('special_codes', $this->special_codes, true);
        $criteria->compare('reseller', $this->reseller, true);
        $criteria->compare('slogan', $this->slogan, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductPricePlanSpecial the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'price_plan_special',
            'order' => 'from_date ASC,to_date ASC'
        );
    }

    public function getPricePlanSpecial($product_id, $date = '')
    {
        if (empty($date)) {
            $date = date('Y-m-d');
        }
        $criteria = new CDbCriteria;
        $criteria->addCondition('"' . $date . '" BETWEEN from_date AND to_date');
        $criteria->addCondition('valid_region=0', 'OR');
        $plan_special = $this->with('items')->findByAttributes(['product_id' => $product_id], $criteria);
        if ($plan_special) {
            $plan_special['valid_region'] = self::ALL_REGION; //DON'T DELETE IT!
            return $plan_special;
        }

        return null;
    }

    protected function beforeSave()
    {
        HtProductPricePlan::clearCache($this->product_id);

        return parent::beforeSave();
    }

    protected function beforeDelete()
    {
        HtProductPricePlan::clearCache($this->product_id);

        return parent::beforeDelete();
    }
}
