<?php

/**
 * This is the model class for table "ht_product_price_plan".
 *
 * The followings are the available columns in table 'ht_product_price_plan':
 * @property integer $price_plan_id
 * @property integer $product_id
 * @property integer $valid_region
 * @property string $from_date
 * @property string $to_date
 * @property string $currency
 * @property integer $need_tier_pricing
 * @property string $special_codes
 */
class HtProductPricePlan extends CActiveRecord
{
    const ALL_REGION = 0;
    const DATE_RANGE = 1;

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
            array('from_date, to_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('price_plan_id, product_id, valid_region, from_date, to_date, currency, need_tier_pricing, special_codes', 'safe', 'on' => 'search'),
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
            'items' => array(self::HAS_MANY, 'HtProductPricePlanItem', '', 'on' => 'price_plan_item.price_plan_id = price_plan.price_plan_id AND price_plan_item.is_special=0'),
            'check' => array(self::HAS_ONE, 'HtProduct', '', 'on' => 'p.product_id = price_plan.product_id'),
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
            'special_codes' => 'Special Codes'
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
        $criteria->compare('special_codes', $this->special_codes);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'price_plan',
            'order' => 'from_date ASC,to_date ASC'
        );
    }

    public static function copyPricePlan($product_id, $new_product_id)
    {
        $result = false;
        $price_plans = HtProductPricePlan::model()->findAllByAttributes(array('product_id' => $product_id));
        foreach ($price_plans as $price_plan) {
            $items = HtProductPricePlanItem::model()->findAllByAttributes(array('price_plan_id' => $price_plan['price_plan_id']));
            $new_price_plan = new HtProductPricePlan();
            ModelHelper::fillItem($new_price_plan, $price_plan);
            $new_price_plan->price_plan_id = null;
            $new_price_plan->product_id = $new_product_id;
            $result = $new_price_plan->insert();
            if ($result) {
                foreach ($items as $item) {
                    $new_item = new HtProductPricePlanItem();
                    ModelHelper::fillItem($new_item, $item);
                    $new_item->price_plan_id = $new_price_plan->price_plan_id;
                    $new_item->item_id = null;
                    $result = $new_item->insert();
                }
            }
        }

        return $result;
    }

    public function getPricePlanFromTo($product_id)
    {
        $sql = 'SELECT min(from_date) AS min_date, max(to_date) AS max_date FROM `' . $this->tableName() . '` WHERE product_id=' . $product_id;
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $result = $command->queryRow();

        return array('min_date' => $result['min_date'], 'max_date' => $result['max_date']);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_price_plan';
    }

    public function getPricePlanWithMap($product_id, $tour_date = '')
    {
        $today = date('Y-m-d');
        $can_cache = empty($tour_date);
        $key = 'HtProductPricePlan_price_plan_with_map_' . $product_id . '_' . $today;
        if ($can_cache) {
            $price_plan = Yii::app()->cache->get($key);
        }

        if (empty($price_plan)) {
            $price_plans = $this->getPricePlan($product_id, $tour_date, $today, true);
            $price_plan = $price_plans;
            foreach ($price_plan as &$p) {
                if (!isset($p['price_map'])) {
                    $p['price_map'] = array();
                }

                foreach ($p['items'] as $i) {
                    $sk = $i['special_code'] ? $i['special_code'] : 0;
                    $tk = $i['ticket_id'];
                    $nk = $i['quantity'];

                    if (!isset($p['price_map'][$sk])) {
                        $p['price_map'][$sk] = array();
                    }

                    if (!isset($p['price_map'][$sk][$tk])) {
                        $p['price_map'][$sk][$tk] = array();
                    }

                    $p['price_map'][$sk][$tk][$nk] = $i;
                    unset($p['items']);
                }
            }
            if ($can_cache) {
                Yii::app()->cache->set($key, $price_plan, 30 * 60);
            }
        }

        return $price_plan;
    }

    public function getPricePlan($product_id, $tour_date = '', $sale_date = '', $all = false)
    {
        $price_plan_raw = array();

        if (empty($tour_date)) {
            $tour_date = date('Y-m-d');
        }

        if (empty($sale_date)) {
            $sale_date = date('Y-m-d');
        }

        //special plan
        $price_plan_special = HtProductPricePlanSpecial::model()->getPricePlanSpecial($product_id, $sale_date);
        if ($price_plan_special) {
            $price_plan_raw[] = Converter::convertModelToArray($price_plan_special);
        }

        //normal plan
        $criteria = new CDbCriteria();
        if (!$all) {
            $criteria->addCondition('"' . $tour_date . '" BETWEEN from_date AND to_date');
            $criteria->addCondition('valid_region=0', 'OR');
        }
        $price_plan_org = HtProductPricePlan::model()->with('items')->findAllByAttributes(['product_id' => $product_id],
                                                                                          $criteria);
        if (empty($price_plan_org)) {
            $criteria = new CDbCriteria();
            if ($all) {
                $criteria->addCondition('"' . $tour_date . '" < to_date');
                $criteria->addCondition('valid_region=0', 'OR');
            }
            $price_plan_org = HtProductPricePlan::model()->with('items')->findAllByAttributes(['product_id' => $product_id],
                                                                                              $criteria);
        }
        $price_plan_org = Converter::convertModelToArray($price_plan_org);
        $price_plan_raw = array_merge($price_plan_raw, $price_plan_org);

        //  filter price plan item has special code disabled

        global $special_code_status;
        $special_codes = HtProductSpecialCombo::model()->findAllByAttributes(['product_id' => $product_id]);
        if (!empty($special_codes)) {
            foreach ($special_codes as $special_code) {
                $special_code_status[$special_code['special_id']] = $special_code['status'];
            }
        }
        if (!function_exists('filterItem')) {
            function filterItem($item)
            {
                global $special_code_status;

                if (!empty($item['special_code']) && isset($special_code_status[$item['special_code']]) && ($special_code_status[$item['special_code']] == 0)) {
                    return false;
                }

                return true;
            }
        }

        if (!empty($special_codes)) {
            foreach ($price_plan_raw as &$price_plan) {
                $price_plan['items'] = array_filter($price_plan['items'], 'filterItem');
            }
        }

        return $price_plan_raw;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductPricePlan the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function clearCache($product_id)
    {
        $today = date('Y-m-d');
        $key = 'HtProductPricePlan_show_price_' . $today . '_' . $product_id;
        Yii::app()->cache->delete($key);

        $key = 'HtProductPricePlan_price_plan_with_map_' . $product_id . '_' . $today;
        Yii::app()->cache->delete($key);
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

    public function getShowPrices($product_id, $spc = '', $sale_date = '')
    {
        $can_cache = empty($spc) && empty($sale_date);

        if (empty($sale_date)) {
            $sale_date = date('Y-m-d');
        }

        $key = 'HtProductPricePlan_show_price_' . $sale_date . '_' . $product_id;
        if ($can_cache) {
            $show_prices = Yii::app()->cache->get($key);
        }

        if (empty($show_prices)) {
            $tour_date = $sale_date;
            $date_rule = HtProductDateRule::model()->findByPk($product_id);
            if ($date_rule && $date_rule['need_tour_date']) {
                $tour_date = date('Y-m-d', strtotime($tour_date . $date_rule['buy_in_advance']));
                $tour_date = max($tour_date, $date_rule['from_date']);
            }

            $price_plans = $this->getPricePlan($product_id, $tour_date, $sale_date);
            if (count($price_plans) == 1) {
                $show_prices = $this->convertShowPrices($price_plans[0], $spc);
            } else if (count($price_plans) > 1) {
                $special_show = $this->convertShowPrices($price_plans[0], $spc);
                $special_show['discount'] = $special_show['orig_price'] - $special_show['price'];

                $show_prices = $special_show;
            } else {
                $show_prices['orig_price'] = 9999;
                $show_prices['price'] = 9999;
            }
            if ($can_cache) {
                Yii::app()->cache->set($key, $show_prices, 4 * 60);
            }
        }

        return $show_prices;
    }

    private function convertShowPrices($price_plan, $spc = '')
    {
        $items = isset($price_plan['items']) ? $price_plan['items'] : array();
        if (!$items) {
            $show_prices['orig_price'] = 9999;
            $show_prices['price'] = 9999;
            $show_prices['discount'] = $show_prices['orig_price'] - $show_prices['price'];

            //$show_prices['special_info'] = [];

            return $show_prices;
        }
        unset($price_plan['items']);

        $item = null;
        foreach ($items as $i) {
            if ($i['special_code'] != $spc && !empty($spc))
                continue;
            if ($i['ticket_id'] == HtTicketType::TYPE_UNIFIED || $i['ticket_id'] == HtTicketType::TYPE_ADULT || $i['ticket_id'] == HtTicketType::TYPE_PACKAGE) {
                if (!$item || $i['price'] < $item['price']) $item = $i;
            }
        }
        if (!$item) $item = array_shift($items);
        $title = HtTicketType::model()->getTicketTitle($item['ticket_id'],$price_plan['product_id']);
        $show_prices = ['price' => $item['price'], 'orig_price' => $item['orig_price'], 'title' => $title];
        if (isset($price_plan['slogan'])) {
            $show_prices['special_info'] = $price_plan;
        }

        if (empty($show_prices)) {
            $show_prices['orig_price'] = 9999;
            $show_prices['price'] = 9999;
        }

        return $show_prices;
    }

    public function ProductPricePlanCheck()
    {
        $price_plan_check = HtProductPricePlan::model()->with('check')->findAll(array(
            'condition' => 'valid_region = 1 AND to_date < "2015-01-15" AND p.status = 3 ',
            'order' => 'to_date',
        ));
        return $price_plan_check;
    }

    public  function removeSpecialCodePricePlan($data, $special_code, $is_special = 0)
    {
        $result = 1;
        foreach ($data as $item) {
            if ($this->removeSpecialCodePricePlanItem($item["price_plan_id"], $special_code)) {
                $items = HtProductPricePlanItem::model()->findAll($item["price_plan_id"]);
                if(!$items){
                    if ($is_special == 0) {
                        $result = HtProductPricePlan::model()->deleteAll("price_plan_id=" . $item["price_plan_id"]);
                    } else {
                        $result = HtProductPricePlanSpecial::model()->deleteAll("price_plan_id=" . $item["price_plan_id"]);
                    }
                    if ($result == 0) {
                        break;
                    }
                }
            }
        }

        return $result;
    }

    public function removeSpecialCodePricePlanItem($price_plan_id, $special_code, $is_special = 0)
    {
        $c = new CDbCriteria();
        $c->addCondition('price_plan_id = ' . $price_plan_id);
        $c->addCondition("special_code = '$special_code'");
        $c->addCondition("is_special = " . $is_special);
        $result = HtProductPricePlanItem::model()->deleteAll($c);

        return $result;
    }

    public function removePricePlan($product_id,$is_special)
    {
        $result = 1;
        if($is_special){
            $price_plans = HtProductPricePlanSpecial::model()->findAll('product_id = '.$product_id);
        }else{
            $price_plans = HtProductPricePlan::model()->findAll('product_id = '.$product_id);
        }
        if($price_plans){
            foreach($price_plans as $item){
                HtProductPricePlanItem::model()->deleteAll("price_plan_id=" .$item["price_plan_id"]);
                if ($is_special == 0) {
                    $result = HtProductPricePlan::model()->deleteAll("price_plan_id=" . $item["price_plan_id"]);
                } else {
                    $result = HtProductPricePlanSpecial::model()->deleteAll("price_plan_id=" . $item["price_plan_id"]);
                }
            }
        }
        return $result;
    }
}
