<?php

/**
 * This is the model class for table "ht_product_special_combo".
 *
 * The followings are the available columns in table 'ht_product_special_combo':
 * @property integer $product_id
 * @property string $special_id
 * @property integer $group_id
 * @property string $special_code
 * @property string $group_info
 */
class HtProductSpecialCombo extends CActiveRecord
{
    public $group_info_expanded = [];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_special_combo';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, special_id, group_id, special_code, group_info', 'required'),
            array('product_id, group_id', 'numerical', 'integerOnly' => true),
            array('special_id, special_code', 'length', 'max' => 8),
            array('group_info', 'length', 'max' => 256),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('product_id, special_id, group_id, special_code, group_info', 'safe', 'on' => 'search'),
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
            'special_id' => 'Special',
            'group_id' => 'Group',
            'special_code' => 'Special Code',
            'group_info' => 'Group Info',
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

        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('special_id', $this->special_id, true);
        $criteria->compare('group_id', $this->group_id);
        $criteria->compare('special_code', $this->special_code, true);
        $criteria->compare('group_info', $this->group_info, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductSpecialCombo the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'psc',
            'order' => 'psc.special_combo_id ASC',
        );
    }

    protected function afterFind()
    {
        $group_info = $this->group_info;
        $groups = explode('|', $group_info);
        foreach($groups as $group) {
            $parts = explode(':', $group);
            $this->group_info_expanded[] = ['group_id' => $parts[0], 'special_code' => $parts[1]];
        }
    }

    public static function updateSpecialCombo($product_id)
    {
        $special_groups = HtProductSpecialGroup::getAllGroups($product_id, true);

        $special_combos = HtProductSpecialCombo::model()->findAllByAttributes(['product_id' => $product_id, 'status' => 1]);

        $combination_items = HtProductSpecialCombo::generateCombinationItems($special_groups);

        if(empty($special_combos)) {
            foreach($combination_items as $combination_item) {
                HtProductSpecialCombo::addItem(['product_id' => $product_id, 'group_info' => $combination_item]);
            }
        } else {
            $existing_items = ModelHelper::getList($special_combos, 'group_info');
            $check_result = HtProductSpecialCombo::checkCombinationItem($combination_items[0], $existing_items[0]);
            if($check_result == 0) {
                // group count, order not changed. may be items added or delete
                if(count($combination_items) > count($existing_items)) {
                    foreach($combination_items as $combination_item) {
                        if(!in_array($combination_item, $existing_items)) {
                            HtProductSpecialCombo::addItem(['product_id' => $product_id, 'group_info' => $combination_item]);
                        }
                    }
                } else if(count($combination_items) < count($special_combos)) {
                    foreach($special_combos as $special_combo) {
                        if(!in_array($special_combo['group_info'], $combination_items)) {
                            HtProductSpecialCombo::deleteItem($product_id, $special_combo);
                        }
                    }
                }

            } elseif($check_result == 1) {
                // group count not changed, order changed
                $combination_item = $combination_items[0];
                $ci_parts = explode('|', $combination_item);
                $ci_group_ids = [];
                foreach($ci_parts as $ci_part) {
                    $ci_part_group_special = explode(':', $ci_part);
                    $ci_group_ids[] = $ci_part_group_special[0];
                }
                $new_existing_items = [];
                foreach($special_combos as $special_combo) {
                    $new_ei = [];

                    foreach($ci_group_ids as $ci_group_id) {
                        foreach($special_combo['group_info_expanded'] as $group_special) {
                            if($ci_group_id == $group_special['group_id']) {
                                $new_ei[] = $ci_group_id . ':' . $group_special['special_code'];
                            }
                        }
                    }

                    $new_existing_item = implode('|', $new_ei);
                    $new_existing_items[] = $new_existing_item;

                    HtProductSpecialCombo::updateItem($special_combo['special_combo_id'], ['group_info' => $new_existing_item]);
                }
                if(count($combination_items) > count($existing_items)) {
                    foreach($combination_items as $combination_item) {
                        if(!in_array($combination_item, $new_existing_items)) {
                            HtProductSpecialCombo::addItem(['product_id' => $product_id, 'group_info' => $combination_item]);
                        }
                    }
                } else if(count($combination_items) < count($special_combos)) {
                    foreach($special_combos as $special_combo) {
                        if(!in_array($special_combo['group_info'], $combination_items)) {
                            HtProductSpecialCombo::deleteItem($product_id, $special_combo);
                        }
                    }
                }
            } else {
                // delete existing, add new
                foreach($special_combos as $special_combo) {
                    HtProductSpecialCombo::deleteItem($product_id, $special_combo);
                }
                foreach($combination_items as $combination_item) {
                    HtProductSpecialCombo::addItem(['product_id' => $product_id, 'group_info' => $combination_item]);
                }
            }
        }
    }

    public static function generateCombinationItems($special_groups)
    {
        $combination_items = [];
        foreach($special_groups as $special_group) {
            if(empty($combination_items)) {
                if(is_array($special_group['special_items']) && count($special_group['special_items']) > 0 && $special_group['status'] == 1) {
                    foreach($special_group['special_items'] as $special_item) {
                        if($special_item['status'] == 1) {
                            array_push($combination_items,
                                       $special_group['group_id'] . ':' . $special_item['special_code']);
                        }
                    }
                }
            } else {
                if(is_array($special_group['special_items']) && count($special_group['special_items']) > 0 && $special_group['status'] == 1) {
                    $existing_items = $combination_items;
                    $combination_items = [];
                    foreach($existing_items as $prefix_item) {
                        foreach($special_group['special_items'] as $special_item) {
                            if($special_item['status'] == 1) {
                                array_push($combination_items,
                                           $prefix_item . '|' . $special_group['group_id'] . ':' . $special_item['special_code']);
                            }
                        }
                    }
                }
            }
        }

        return $combination_items;
    }

    public static function checkCombinationItem($combination_item, $existing_item)
    {
        if($combination_item == $existing_item) {
            return 0;
        } else {
            $ci_parts = explode('|', $combination_item);
            $ei_parts = explode('|', $existing_item);
            if(count($ci_parts) != count($ei_parts)) {
                //group数量不一样
                return 2;
            } else {
                $order_changed = false;
                foreach($ci_parts as $ci_part) {
                    $ci_group_id = explode(':', $ci_part)[0];
                    $ei_group_id = explode(':', $ei_parts[0])[0];
                    if($ci_group_id != $ei_group_id) {
                        $order_changed = true;
                    }
                }

                return $order_changed ? 1 : 0;
            }
        }
    }

    public static function addItem($data)
    {
        $item = new HtProductSpecialCombo();
        ModelHelper::fillItem($item, $data);
        $item['special_id'] = substr(md5(microtime() + mt_rand(1, 32768)), 0, 8);

        return $item->insert();
    }

    public static function updateItem($special_combo_id, $data)
    {
        $item = HtProductSpecialCombo::model()->findByPk($special_combo_id);

        return ModelHelper::updateItem($item, $data);
    }

    public static function deleteItem($product_id, $special_combo)
    {
        $order = HtOrderProduct::model()->findByAttributes(array('product_id' => $product_id, 'special_code' => $special_combo['special_id']));
        if($order) {
            HtProductSpecialCombo::updateItem($special_combo['special_combo_id'], ['status' => 0]);
        } else {
            HtProductSpecialCombo::model()->deleteByPk($special_combo['special_combo_id']);
        }
        //清除该special对应价格计划
        $price_plans = Converter::convertModelToArray(HtProductPricePlan::model()->findAllByAttributes(array('product_id' => $product_id)));
        $price_plans_special = Converter::convertModelToArray(HtProductPricePlanSpecial::model()->findAllByAttributes(array('product_id' => $product_id)));
        HtProductPricePlan::model()->removeSpecialCodePricePlan($price_plans, $special_combo['special_id']);
        HtProductPricePlan::model()->removeSpecialCodePricePlan($price_plans_special, $special_combo['special_id'], 1);
        HtProductPricePlan::clearCache($product_id);
    }

    public static function getAllComboSpecialDetail($product_id,$special_id = '')
    {
        global $ordered_special_codes;
        $result = [];
        $groups = [];
        $special = [];
        $specials = [];
        $special_items = [];

        //group信息
        $special_group = HtProductSpecialGroup::model()->with('special_items_valid.item_limit')->findAllByAttributes(array('product_id' => $product_id, 'status' => 1));
        $special_group = Converter::convertModelToArray($special_group);
        if($special_group) {
            foreach($special_group as $group) {
                if(count($group['special_items_valid']) > 0) {
                    $one_group = array('group_id' => $group['group_id'], 'title' => $group['cn_title']);
                    $one_group['codes'] = array_map(array("HtProductSpecialCombo", "onlyCode"), $group['special_items_valid']);

                    $special_items = array_merge($special_items, $group['special_items_valid']);

                    array_push($groups, $one_group);
                }
            }
            $result['groups'] = $groups;
            $ordered_special_codes = $result['special_codes'] = $special_items;
        }

        $c = new CDbCriteria();
        $c->addCondition('product_id = '.$product_id);

        if(!empty($special_id)){
            $c->addCondition("special_id = '$special_id'");
        }else{
            $c->addCondition('status = 1');
        }
        $special_combos = HtProductSpecialCombo::model()->findAll($c);
        if($special_combos) {
            foreach($special_combos as $combo) {
                $items = [];
                $g_parts = explode('|', $combo['group_info']);
                foreach($g_parts as $g) {
                    $s_parts = explode(':', $g);
                    $group_id = $s_parts[0];
                    $special_code = $s_parts[1];
                    $special_item = HtProductSpecialItem::model()->findByPk(array('group_id' => $group_id, 'special_code' => $special_code));
                    $special_item = Converter::convertModelToArray($special_item);
                    array_push($items, array('group_id' => $special_item['group_id'], 'special_code' => $special_item['special_code'], 'name' => $special_item['cn_name']));
                }
                $special['special_id'] = $combo['special_id'];
                $special['group_info'] = $combo['group_info'];
                $special['items'] = $items;
                array_push($specials, $special);
            }
            usort($specials, array("HtProductSpecialCombo", "sortByGroup"));
            $result['specials'] = $specials;
        }

        return $result;
    }

    public static function getSpecialDetail($product_id,$special_id = '')
    {
        global $ordered_special_codes;
        $special = [];
        $specials = [];
        $special_items = [];

        $special_group = HtProductSpecialGroup::model()->with('special_items_valid')->findAllByAttributes(array('product_id' => $product_id, 'status' => 1));
        $special_group = Converter::convertModelToArray($special_group);
        if($special_group) {
            foreach($special_group as $group) {
                if(count($group['special_items_valid']) > 0) {
                    $special_items = array_merge($special_items, $group['special_items_valid']);
                }
            }
            $ordered_special_codes = $special_items;
        }

        $c = new CDbCriteria();
        $c->addCondition('product_id = '.$product_id);

        if(!empty($special_id)){
            $c->addCondition("special_id = '$special_id'");
        }else{
            $c->addCondition("status = 1");
        }

        $special_combos = HtProductSpecialCombo::model()->findAll($c);
        if($special_combos) {
            foreach($special_combos as $combo) {
                $items = [];
                $g_parts = explode('|', $combo['group_info']);
                foreach($g_parts as $g) {
                    $s_parts = explode(':', $g);
                    $group_id = $s_parts[0];
                    $special_code = $s_parts[1];
                    $special_item = HtProductSpecialItem::model()->findByPk(array('group_id' => $group_id, 'special_code' => $special_code));
                    $special_item = Converter::convertModelToArray($special_item);
                    $group = HtProductSpecialGroup::model()->findByPk($group_id);
                    $special_item['group_title'] = $group['cn_title'];
                    $special_item['group_title_en'] = $group['en_title'];
                    array_push($items, $special_item);
                }
                $special['special_id'] = $combo['special_id'];
                $special['group_info'] = $combo['group_info'];
                $special['items'] = $items;
                array_push($specials, $special);
            }
            usort($specials, array("HtProductSpecialCombo", "sortByGroup"));
        }

        return $specials;
    }

    public function getSpecialGroupByCode($product_id, $special_code) {
        $all_groups = HtProductSpecialGroup::model()->with('special_items_valid')->findAllByAttributes(['product_id' => $product_id]);
        foreach($all_groups as $i => $group) {
            foreach($group['special_items_valid'] as $one_special) {
                if($one_special['special_code'] == $special_code) {
                    return $group;
                }
            }
        }

        return false;
    }

    public function needSpecialCode($product_id)
    {
        $special_codes = $this->findAll('product_id = ' . $product_id);
        if(!empty($special_codes)) {
            foreach($special_codes as $special_code){
                if($special_code['status'] == 1){
                    return true;
                }
            }
        }

        return false;
    }


    private static function onlyCode($special_item)
    {
        return $special_item['special_code'];
    }
    private static function findIndex($code, $set) {
        for($i = 0, $len = count($set); $i < $len; $i++) {
            if($set[$i]['special_code'] == $code) {
                return $i;
            }
        }

        return -1;
    }
    private static function sortByGroup($a, $b)
    {
        global $ordered_special_codes;

        foreach($a['items'] as $index => $item) {
            if($a['items'][$index]['special_code'] == $b['items'][$index]['special_code']) {
                continue;
            } else {
                $a_sindex = HtProductSpecialCombo::findIndex($a['items'][$index]['special_code'], $ordered_special_codes);
                $b_sindex = HtProductSpecialCombo::findIndex($b['items'][$index]['special_code'], $ordered_special_codes);
                return $a_sindex > $b_sindex ? 1 : -1;
            }
        }

        return 0;
    }

    public function getOneSpecialInfo($product_id,$special_id)
    {
        $return = array();
        $combo = $this->findByAttributes(array('product_id'=>$product_id,'special_id'=>$special_id));
        if($combo){
            $special_item = HtProductSpecialItem::model()->findByPk(array('group_id'=>$combo['group_info_expanded'][0]['group_id'],'special_code'=>$combo['group_info_expanded'][0]['special_code']));
            $return = Converter::convertModelToArray($special_item);
        }

        return $return;
    }

    public static function copySpecialInfo($product_id, $new_product_id)
    {
        $result = false;

        //插入group及item信息
        $group_id_map = array();
        $special_groups = HtProductSpecialGroup::model()->findAllByAttributes(array('product_id'=>$product_id,'status'=>1));
        $special_groups = Converter::convertModelToArray($special_groups);
        if($special_groups){
            foreach($special_groups as $special_group){
                $group = new HtProductSpecialGroup();
                $fill_data = $special_group;
                unset($fill_data['group_id']);
                ModelHelper::fillItem($group, $fill_data);
                $group['product_id'] = $new_product_id;
                $result = $group->insert();
                $group_id_map[$special_group['group_id']] = $group->getPrimaryKey();
                $special_items = HtProductSpecialItem::model()->findAllByAttributes(array('group_id'=>$special_group['group_id'],'status'=>1));
                $special_items = Converter::convertModelToArray($special_items);
                if($special_items){
                    foreach($special_items as $special_item){
                        $item = new HtProductSpecialItem();
                        ModelHelper::fillItem($item, $special_item);
                        $item['group_id'] = $group->getPrimaryKey();
                        $result = $item->insert();
                        //插入limit
                        $limit = HtProductSpecialItemLimit::model()->findByAttributes(array('group_id'=>$special_item['group_id'],'special_code'=>$special_item['special_code']));
                        if($limit){
                            $limit = Converter::convertModelToArray($limit);
                            $new_limit = new HtProductSpecialItemLimit();
                            ModelHelper::fillItem($new_limit, $limit);
                            $new_limit['group_id'] = $group->getPrimaryKey();
                            $result = $new_limit->insert();
                        }
                    }
                }
            }
        }

        //插入新combo信息
        $special_combos = HtProductSpecialCombo::model()->findAllByAttributes(array('product_id'=>$product_id,'status'=>1));
        $special_combos = Converter::convertModelToArray($special_combos);
        foreach($special_combos as $special_combo){
            $combo = new HtProductSpecialCombo();
            unset($special_combo['special_combo_id']);
            ModelHelper::fillItem($combo, $special_combo);
            $combo['product_id'] = $new_product_id;
            $result = $combo->insert();
            if($result){
                $groups = explode('|', $combo['group_info']);
                $group_info_expanded = array();
                foreach($groups as $group) {
                    $parts = explode(':', $group);
                    $group_info_expanded[] = ['group_id' => $group_id_map[$parts[0]], 'special_code' => $parts[1]];
                }
                $new_group_info = array();
                foreach($group_info_expanded as $group_info){
                    array_push($new_group_info, $group_info['group_id'].':'.$group_info['special_code']);
                }
                $new_group_info = implode('|',$new_group_info);
                $combo['group_info'] = $new_group_info;
                $result = $combo->update();
            }
        }

        return $result;
    }
}
