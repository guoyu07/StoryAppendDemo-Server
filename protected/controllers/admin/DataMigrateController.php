<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 4/11/14
 * Time: 5:38 PM
 */
class DataMigrateController extends AdminController
{

    public function actionMigrateProductPassengerRule()
    {
        // NOTE: migrate sale rule first since the product pasenger rule item depends on it\
        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';
        $c = new CDbCriteria();
        $c->addCondition('product_id>100');
        $passenger_rules = HcProductPassengerRule::model()->findAll($c);
        foreach ($passenger_rules as $passenger_rule) {
            $product_id = $passenger_rule['product_id'];
            $item = HtProductPassengerRule::model()->findByPk($passenger_rule['product_id']);
            if (empty($item)) {
                $item = new HtProductPassengerRule();
                $item['product_id'] = $product_id;
                $item['need_passenger_num'] = $passenger_rule['need_passenger_num'];
                $item['need_lead'] = $passenger_rule['need_lead'];
                $item['lead_fields'] = $passenger_rule['lead_fields'];
                $item->insert();
            } else {
                $item['need_passenger_num'] = $passenger_rule['need_passenger_num'];
                $item['need_lead'] = $passenger_rule['need_lead'];
                $item['lead_fields'] = $passenger_rule['lead_fields'];
                $item->update();
            }

            $sale_limit_rule = HcProductSaleLimitRule::model()->findByPk($product_id);
            $sale_type = $sale_limit_rule['sale_type'];
            $result = $this->handlePassengerRuleItem($product_id, $sale_type, $passenger_rule);
            echo '<h3>product_id: ' . $product_id . ', result: ' . $result . ', time: ' . date('Y/m/d H:i:s',
                                                                                               time()) . '</h3>';
        }
        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';
    }

    private function handlePassengerRuleItem($product_id, $sale_type, $passenger_rule)
    {
        HtProductPassengerRuleItem::model()->deleteAllByAttributes(array('product_id' => $product_id));
        $data = array();
        switch ($sale_type) {
            case 1: {
                $data[] = array('product_id' => $product_id, 'ticket_id' => 2, 'fields' => $passenger_rule['default_fields']);
                $data[] = array('product_id' => $product_id, 'ticket_id' => 3, 'fields' => $passenger_rule['child_fields']);
            }
                break;
            case 2: {
                if ($passenger_rule['need_lead']) {
                    $fields = $passenger_rule['lead_fields'];
                } else {
                    $fields = $passenger_rule['default_fields'];
                }
                $data[] = array('product_id' => $product_id, 'ticket_id' => 1, 'fields' => $fields);
            }
                break;
            case 3: {
                $data[] = array('product_id' => $product_id, 'ticket_id' => 2, 'fields' => $passenger_rule['default_fields']);
                $data[] = array('product_id' => $product_id, 'ticket_id' => 3, 'fields' => $passenger_rule['child_fields']);
            }
                break;

        }

        $result = true;
        foreach ($data as $item_data) {
            $item = new HtProductPassengerRuleItem();
            ModelHelper::fillItem($item, $item_data);
            $result = $result && $item->insert();
        }

        return $result;
    }

    public function actionMigrateTourOperation()
    {
        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';
        $data = HcProductTourOperation::model()->findAll('language_id=2');
        foreach ($data as $orig_item) {
            $operation_id = $orig_item['operation_id'];
            $item = HtProductTourOperation::model()->findByPk($operation_id);
            if (!empty($item)) {
                ModelHelper::fillItem($item, $orig_item, array('from_date', 'to_date', 'frequency', 'languages'));
                $item['confirmation_type'] = $orig_item['confirmation'] == 'IM' ? 1 : 2;
                if ($orig_item['from_date'] == '0000-00-00') {
                    $item['from_date'] = '2014-01-01';
                }

                $result = $item->update();
                echo '<h3>Update operation id: ' . $operation_id . ', result: ' . $result . ', time: ' . date('Y/m/d H:i:s',
                                                                                                              time()) . '</h3>';
            } else {
                $item = new HtProductTourOperation();

                ModelHelper::fillItem($item, $orig_item,
                                      array('operation_id', 'product_id', 'from_date', 'to_date', 'frequency', 'languages'));
                $item['confirmation_type'] = $orig_item['confirmation'] == 'IM' ? 1 : 2;
                if ($orig_item['from_date'] == '0000-00-00') {
                    $item['from_date'] = '2014-01-01';
                }

                $result = $item->insert();
                echo '<h3>Insert operation id: ' . $operation_id . ', result: ' . $result . ', time: ' . date('Y/m/d H:i:s',
                                                                                                              time()) . '</h3>';
            }
        }

        $connection = Yii::app()->db;

        $sql = 'DELETE FROM ht_product_tour_operation WHERE from_date > to_date';
        $command = $connection->createCommand($sql);
        $command->execute();

        echo '<h2>End: ' . date('Y/m/d H:i:s', time()) . '</h2>';
    }

    public function actionUpdateProductDateRule()
    {
        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';
        $connection = Yii::app()->db;

//        $sql = 'DELETE FROM ht_product_date_rule;';
//        $command = $connection->createCommand($sql);
//        $command->execute();
//
        $sql = 'INSERT IGNORE INTO ht_product_date_rule SELECT * FROM `hc_product_sale_date_rule`';
        $command = $connection->createCommand($sql);
        $command->execute();

        $sql = "SELECT * FROM `ht_product_date_rule` WHERE sale_range_type = 0";
//        $sql .= " AND product_id < 800";

        $command = $connection->createCommand($sql);
        $date_rules = $command->queryAll();

        foreach ($date_rules as $date_rule) {
            $result = false;
            $product_id = $date_rule['product_id'];
            $from_to = HtProductTourOperation::model()->getFromTo($product_id);
            if (!empty($from_to)) {
                $item = HtProductDateRule::model()->findByPk($product_id);
                $item['from_date'] = $from_to['from_date'];
                $item['to_date'] = $from_to['to_date'];
                $result = $item->update();
            }
            echo '<h3>product_id: ' . $product_id . ', result: ' . $result . ', time: ' . date('Y/m/d H:i:s',
                                                                                               time()) . '</h3>';
        }
        echo '<h2>End: ' . date('Y/m/d H:i:s', time()) . '</h2>';
    }

    public function actionMigrateDeparture()
    {
        if (!Yii::app()->user->checkAccess('HT_ProductEdit')) {
            $this->redirect($this->createUrl('/'));
        }

        $product_id = 0;
        if (isset($_GET['product_id'])) {
            $product_id = (int)$_GET['product_id'];
            HtProductDeparture::model()->deleteAllByAttributes(array('product_id' => $product_id));
            HtProductDeparturePlan::model()->deleteAllByAttributes(array('product_id' => $product_id));
        } else {
//            HtProductDeparture::model()->deleteAll();
//            HtProductDeparturePlan::model()->deleteAll();
        }

        $sql = "SELECT od.*, pto.product_id, pto.from_date, pto.to_date FROM `hc_product_operation_departure` od
                LEFT JOIN ht_product_tour_operation pto ON pto.operation_id = od.operation_id
                WHERE od.operation_id> 0 and od.departure_id is not null and od.departure_id<>''
                AND od.operation_id in (SELECT pto.operation_id FROM hc_product_tour_operation)";
        if ($product_id > 0) {
            $sql .= " AND pto.product_id=" . $product_id;
        } else {
//            $sql .= " AND pto.product_id >= 500 AND pto.product_id<= 1000";
        }
        $sql .= " ORDER BY pto.product_id, pto.operation_id, od.departure_id, od.time, od.language_id";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $data = $command->queryAll();

        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';
        foreach ($data as $item) {
            $departure_code = $item['departure_id'];
            $product_id = $item['product_id'];
            $language_id = $item['language_id'];
            $result = true;

            if (HtProductDeparture::model()->findByPk(array('product_id' => $product_id, 'departure_code' => $departure_code, 'language_id' => $language_id)) == null) {
                $result = HtProductDeparture::model()->addDeparture(
                    array(
                        'product_id'      => $product_id,
                        'departure_code'  => $departure_code,
                        'language_id'     => $language_id,
                        'departure_point' => $item['departure_point'] ? $item['departure_point'] : '',
                        'address_lines'   => $item['address_lines'],
                        'telephone'       => $item['telephone'],
                        'description'     => $item['description'],
                        'first_service'   => $item['first_service'],
                        'last_service'    => $item['last_service'],
                        'intervals'       => $item['intervals'],
                    ));
            }

            if ($result) {
                $from_date = $item['from_date'];
                if ($from_date == '0000-00-00') {
                    $from_date = '2014-01-01';
                }
                $to_date = $item['to_date'];
                $time = $item['time'];
                $additional_limit = $item['additional_limit'];
                if (!HtProductDeparturePlan::model()->alreadyExists($product_id, $departure_code, 1, $from_date,
                                                                    $to_date, $time)
                ) {

                    HtProductDeparturePlan::model()->addPlan($product_id, $departure_code, 1, $from_date, $to_date,
                                                             $time, $additional_limit);
                }
            }
        }

        // TODO handle those have only one language

        $sql = "SELECT distinct product_id, departure_code, count(*) the_count FROM `ht_product_departure`
                GROUP BY product_id, departure_code
                HAVING the_count <2";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $data = $command->queryAll();
        foreach ($data as $item) {
            $hsp = HtProductDeparture::model()->findByAttributes(array('product_id' => $item['product_id'], 'departure_code' => $item['departure_code']));
            $new_item = new HtProductDeparture();
            ModelHelper::fillItem($new_item, $hsp);
            $new_item['language_id'] = $hsp['language_id'] == 1 ? 2 : 1;
            $new_item->insert();
        }

        $sql = 'DELETE FROM ht_product_departure_plan WHERE to_date < "2014-07-01" OR from_date > to_date';
        $command = $connection->createCommand($sql);
        $command->execute();

        echo '<h2>End: ' . date('Y/m/d H:i:s', time()) . '</h2>';
    }

    public function actionMigrateSpecialCode()
    {
        // updated by SQL.
        // TODO update ht_order_product
    }

    public function actionMigrateSaleRule()
    {
        // read rule from hc_product_sale_limit_rule,
        // update ht_product_ticket_rule, ht_product_sale_rule, ht_product_package_rule
        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';

        $c = new CDbCriteria();
        $c->addCondition('product_id>100');
        $limit_rules = HcProductSaleLimitRule::model()->findAll($c); //'product_id= 1328'
        foreach ($limit_rules as $limit_rule) {
            $product_id = $limit_rule['product_id'];
            echo '<h3>product_id: ' . $product_id . '</h2>';
            $product = HcProduct::model()->findByPk($product_id);
            if ($product['manufacturer_id'] == 38) {
                continue;
            }

            HtProductTicketRule::model()->deleteAllByAttributes(array('product_id' => $product_id));
            HtProductPackageRule::model()->deleteAllByAttributes(array('product_id' => $product_id));

            $this->handleTicketRule($product_id, $limit_rule['sale_type'], $limit_rule['child_only'],
                                    $limit_rule['min_adult_num'], $limit_rule['adult_in_set'],
                                    $limit_rule['child_in_set']);

            HtProductSaleRule::model()->deleteByPk($product_id);
            $this->handleProductSaleRule($product_id, $limit_rule['sale_type'], $limit_rule['min_num'],
                                         $limit_rule['max_num']);
        }
        echo '<h2>End: ' . date('Y/m/d H:i:s', time()) . '</h2>';
    }

    private function handleTicketRule($product_id, $sale_type, $child_only, $min_adult_num, $adult_in_set, $child_in_set)
    {
        $age_ranges = $this->getAgeRange($product_id);

        switch ($sale_type) {
            case 1: {
                $data = array('product_id'     => $product_id,
                              'ticket_id'      => 2,
                              'age_range'      => $age_ranges['adult_age_range'],
                              'is_independent' => 1,
                              'min_num'        => $child_only ? 0 : $min_adult_num);
                $this->addProductTicketRule($data);

                $data['ticket_id'] = 3;
                $data['age_range'] = $age_ranges['child_age_range'];
                $data['is_independent'] = $child_only ? 1 : 0;
                $data['min_num'] = 0;

                $this->addProductTicketRule($data);
            }
                break;
            case 2: {
                $data = array('product_id'     => $product_id,
                              'ticket_id'      => 1,
                              'age_range'      => '',
                              'is_independent' => 1,
                              'min_num'        => 0);
                $this->addProductTicketRule($data);
            }
                break;
            case 3: {
                $data = array('product_id'     => $product_id,
                              'ticket_id'      => 2,
                              'age_range'      => $age_ranges['adult_age_range'],
                              'is_independent' => 1,
                              'min_num'        => 0);
                $this->addProductTicketRule($data);

                $data['ticket_id'] = 3;
                $data['age_range'] = $age_ranges['child_age_range'];
                $data['is_independent'] = 0;
                $data['min_num'] = 0;

                $this->addProductTicketRule($data);

                $data['ticket_id'] = 99;
                $data['age_range'] = '';
                $this->addProductTicketRule($data);

                $data = array('product_id'      => $product_id,
                              'base_product_id' => $product_id,
                              'ticket_id'       => 2,
                              'quantity'        => $adult_in_set);
                $this->addProductPackageRule($data);

                $data['ticket_id'] = 3;
                $data['quantity'] = $child_in_set;
                $this->addProductPackageRule($data);
            }
                break;
        }
    }

    private function getAgeRange($product_id)
    {
        $product = HcProduct::model()->findByPk($product_id);

        $adult_age_range = $product['age_range'];
        if (substr($adult_age_range, -1, 1) == '+') {
            $adult_age_range = substr($adult_age_range, 0, strlen($adult_age_range) - 1) . '-100';
        }
        $child_age_range = $product['child_age_range'];

        return array(
            'adult_age_range' => $adult_age_range,
            'child_age_range' => $child_age_range);
    }

    private function addProductTicketRule($data)
    {
        $item = new HtProductTicketRule();
        ModelHelper::fillItem($item, $data);

        return $item->insert();
    }

    private function addProductPackageRule($data)
    {
        $item = new HtProductPackageRule();
        ModelHelper::fillItem($item, $data);

        return $item->insert();
    }

    private function handleProductSaleRule($product_id, $sale_type, $min_num, $max_num)
    {
        $item = new HtProductSaleRule();
        $item['product_id'] = $product_id;
        $item['sale_in_package'] = $sale_type == 3 ? 1 : 0;
        $item['min_num'] = $min_num;
        $item['max_num'] = $max_num;
        $result = $item->insert();

        return $result;

//            return $item->insert();
    }

    public function actionMigrateProductPricePlan()
    {
        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';

        $sql = "SELECT * FROM `ht_product_date_rule` WHERE (sale_range_type = 0 AND to_date <> '0000-00-00' OR sale_range_type=1 AND sale_range is not null)";
        $sql .= " AND product_id > 1100";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);

        $date_rules = $command->queryAll();

        foreach ($date_rules as $date_rule) {
            $product_id = $date_rule['product_id'];
            $sale_range_type = $date_rule['sale_range_type'];
            if ($sale_range_type == 0) {
                $from_date = $date_rule['from_date'];
                $to_date = $date_rule['to_date'];
            } else {
                $from_date = date('Y-m-d', time());
                $to_date = date('Y-m-d', time() + 365 * 24 * 60 * 60);
            }

            $special_code_list = array();
//            $special_codes = HtProductSpecialCode::model()->findAll('product_id = ' . $product_id);
//            if (!empty($special_codes)) {
//                $special_code_list = ModelHelper::getList($special_codes, 'special_code');
//            }

            $special_codes = HtProductSpecialCombo::model()->findAll('product_id = ' . $product_id);
            if (!empty($special_codes)) {
                $special_code_list = ModelHelper::getList($special_codes, 'special_id');
            }

            $ticket_rules = HtProductTicketRule::model()->findAll('product_id = ' . $product_id);
            $ticket_id_list = ModelHelper::getList($ticket_rules, 'ticket_id');
            if (in_array('99', $ticket_id_list)) {
                $ticket_id_list = array('99');
            }

            $price_info = HcProduct::model()->findByPk($product_id);
            if ($price_info['manufacturer_id'] == 11) {
                echo "<h3>skip products of GTA -- product_id: $product_id </h3>";
                continue;
            }
            $result = $this->handleProductPricePlan($product_id, $price_info, $from_date, $to_date, $special_code_list,
                                                    $ticket_id_list);

            echo '<h3>product_id: ' . $product_id . ', result: ' . $result . ', time: ' . date('Y/m/d H:i:s',
                                                                                               time()) . '</h3>';
        }

        echo '<h2>End: ' . date('Y/m/d H:i:s', time()) . '</h2>';
    }

    private function handleProductPricePlan($product_id, $price_info, $from_date, $to_date, $special_code_list, $ticket_id_list, $is_special = 0, $reseller = '', $slogan = '')
    {

        $adult_price = $price_info['price'];
        $child_price = $price_info['child_price'];

        $supplier_adult_price = $price_info['supplier_price'];
        $supplier_child_price = $price_info['supplier_child_price'];

        $orig_adult_price = $price_info['orig_price'];
        $orig_child_price = $price_info['orig_child_price'];

        $stock_adult_price = $price_info['stock_price'];
        $stock_child_price = $price_info['stock_child_price'];

        if ($is_special == 0) {
            $price_plans = HtProductPricePlan::model()->findAllByAttributes(array('product_id' => $product_id));
            $price_plan_ids = ModelHelper::getList($price_plans, 'price_plan_id');
            foreach ($price_plan_ids as $price_plan_id) {
                HtProductPricePlan::model()->deleteByPk($price_plan_id);
                HtProductPricePlanItem::model()->deleteAllByAttributes(array('price_plan_id' => $price_plan_id, 'is_special' => 0));
            }

            $item = new HtProductPricePlan();
            $data = array('product_id'        => $product_id, 'valid_region' => 0, 'from_date' => $from_date, 'to_date' => $to_date,
                          'need_tier_pricing' => 0, 'special_codes' => join(';', $special_code_list));
            ModelHelper::fillItem($item, $data);
            $result = $item->insert();
            if (!$result) {
                return false;
            }
        } else {
            $price_plans = HtProductPricePlanSpecial::model()->findAllByAttributes(array('product_id' => $product_id));
            $price_plan_ids = ModelHelper::getList($price_plans, 'price_plan_id');
            foreach ($price_plan_ids as $price_plan_id) {
                HtProductPricePlanSpecial::model()->deleteByPk($price_plan_id);
                HtProductPricePlanItem::model()->deleteAllByAttributes(array('price_plan_id' => $price_plan_id, 'is_special' => 1));
            }

            $item = new HtProductPricePlanSpecial();
            $data = array('product_id'        => $product_id, 'valid_region' => 1, 'from_date' => $from_date, 'to_date' => $to_date,
                          'need_tier_pricing' => 0, 'special_codes' => join(';', $special_code_list),
                          'reseller'          => $reseller, 'slogan' => $slogan);
            ModelHelper::fillItem($item, $data);
            $result = $item->insert();
            if (!$result) {
                return false;
            }
        }

        $price_plan_id = $item['price_plan_id'];

        foreach ($ticket_id_list as $ticket_id) {
            if (!empty($special_code_list)) {
                $sql = 'SELECT pov.*, ov.* FROM `hc_product_option_value` pov LEFT JOIN hc_option_value ov ON ov.option_value_id = pov.option_value_id';
                $sql .= ' WHERE product_id = ' . $product_id;
                $connection = Yii::app()->db;
                $command = $connection->createCommand($sql);
                $special_prices = $command->queryAll();

                $special_price_list = array();
                foreach ($special_prices as $special_price) {
                    $short_special_code = substr($special_price['special_code'], 0, 8);
                    $special_price_list[$short_special_code] = array(
                        'price_prefix' => $special_price['price_prefix'],
                        'price'        => $special_price['price'],
                        'child_price'  => $special_price['child_price'],
                    );
                }

                foreach ($special_code_list as $special_code) {
                    $special_price_info = $special_price_list[$special_code];

                    $price_prefix = $special_price_info['price_prefix'];
                    $special_adult_price = $special_price_info['price'];
                    $special_child_price = $special_price_info['child_price'];

                    $adjust_adult_price = $price_prefix == '+' ? $special_adult_price : 0 - $special_adult_price;
                    $adjust_child_price = $price_prefix == '+' ? $special_child_price : 0 - $special_child_price;

                    $data = array(
                        'is_special'     => $is_special,
                        'price_plan_id'  => $price_plan_id,
                        'ticket_id'      => $ticket_id,
                        'special_code'   => $special_code, // TODO  get price that special code related
                        'quantity'       => 1,
                        'supplier_price' => $ticket_id == 3 ? $supplier_child_price + $adjust_child_price : $supplier_adult_price + $adjust_adult_price,
                        'cost_price'     => $ticket_id == 3 ? $stock_child_price + $adjust_child_price : $stock_adult_price + $adjust_adult_price,
                        'orig_price'     => $ticket_id == 3 ? $orig_child_price + $adjust_child_price : $orig_adult_price + $adjust_adult_price,
                        'price'          => $ticket_id == 3 ? $child_price + $adjust_child_price : $adult_price + $adjust_adult_price
                    );
                    $result = $this->addPricePlanItem($data);
                    if (!$result) {
                        return false;
                    }
                }
            } else {
                $data = array(
                    'is_special'     => $is_special,
                    'price_plan_id'  => $price_plan_id,
                    'ticket_id'      => $ticket_id,
                    'special_code'   => '',
                    'quantity'       => 1,
                    'supplier_price' => $ticket_id == 3 ? $supplier_child_price : $supplier_adult_price,
                    'cost_price'     => $ticket_id == 3 ? $stock_child_price : $stock_adult_price,
                    'orig_price'     => $ticket_id == 3 ? $orig_child_price : $orig_adult_price,
                    'price'          => $ticket_id == 3 ? $child_price : $adult_price
                );
                $result = $this->addPricePlanItem($data);
                if (!$result) {
                    return false;
                }
            }
        }

        return true;
    }

    private function addPricePlanItem($data)
    {
        $item = new HtProductPricePlanItem();
        ModelHelper::fillItem($item, $data);

        return $item->insert();
    }

    public function actionMigrateProductPriceSpecial()
    {
        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';

        $specials = HcProductSpecial::model()->findAll();
        foreach ($specials as $special) {
            $product_id = $special['product_id'];

//            $special_code_list = array();
//            $special_codes = HtProductSpecialCode::model()->findAll('product_id = ' . $product_id);
//            if (!empty($special_codes)) {
//                $special_code_list = ModelHelper::getList($special_codes, 'special_code');
//            }

            $special_codes = HtProductSpecialCombo::model()->findAll('product_id = ' . $product_id);
            if (!empty($special_codes)) {
                $special_code_list = ModelHelper::getList($special_codes, 'special_id');
            }

            $ticket_rules = HtProductTicketRule::model()->findAll('product_id = ' . $product_id);
            $ticket_id_list = ModelHelper::getList($ticket_rules, 'ticket_id');
            if (in_array('99', $ticket_id_list)) {
                $ticket_id_list = array('99');
            }

            $price_info = Converter::convertModelToArray(HcProduct::model()->findByPk($product_id));
            $price_info['price'] = $special['price'];
            $price_info['child_price'] = $special['child_price'];

            $result = $this->handleProductPricePlan($product_id, $price_info, $special['date_start'],
                                                    $special['date_end'], $special_code_list,
                                                    $ticket_id_list, 1, $special['reseller'], $special['topic']);

            echo '<h3>product_id: ' . $product_id . ', result: ' . $result . ', time: ' . date('Y/m/d H:i:s',
                                                                                               time()) . '</h3>';
        }

        echo '<h2>End: ' . date('Y/m/d H:i:s', time()) . '</h2>';
    }

    public function actionMigrateOrderPassenger()
    {
        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';

        $passenger_meta = HtPassengerMetaData::model()->findAll();
        $normal_fields = array();
        $merged_fields = array();
        foreach ($passenger_meta as $item) {
            if ($item['storage_merge'] == 0) {
                array_push($normal_fields, $item['storage_field']);
            } else {
                array_push($merged_fields, $item['storage_field']);
            }
        }

        $c = new CDbCriteria();
        $c->addCondition('order_passenger_id < 1000');
        $c->addCondition('order_passenger_id > 0');

        $data = HcOrderPassenger::model()->findAll($c);
//        HtOrderPassenger::model()->deleteAll();

        foreach ($data as $from_item) {
            $order_passenger_id = $from_item['order_passenger_id'];
            HtOrderPassengerBak::model()->deleteByPk($order_passenger_id);
            $to_item = new HtOrderPassengerBak();
            foreach ($normal_fields as $field) {
                $to_item[$field] = $from_item[$field];
            }

            $merged_value = array();
            foreach ($merged_fields as $field) {
                $from_field = $field;
                if ($field == 'hotel_booking_ref') {
                    $from_field = 'hotle_booking_ref';
                }

                if (!empty($from_item[$from_field])) {
                    $merged_value[$field] = $from_item[$from_field];
                }
            }
            $to_item['merged_fields'] = CJSON::encode($merged_value);
            $to_item['order_passenger_id'] = $order_passenger_id;
            $result = $to_item->insert();
            echo '<h3>order_passenger_id: ' . $order_passenger_id . ', result: ' . $result . ', time: ' . date('Y/m/d H:i:s',
                                                                                                               time()) . '</h3>';
        }

        echo '<h2>End: ' . date('Y/m/d H:i:s', time()) . '</h2>';
    }

    public function actionMigrateProductGroup()
    {

    }

    public function actionMigrateProductShippingRule()
    {
        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';

        $orderHandleRules = HcOrderHandleRule::model()->findAll();

        $pid_mid_rule_list = array();
        foreach ($orderHandleRules as $orderHandleRule) {
            $key = $orderHandleRule['product_id'] . '-' . $orderHandleRule['manufacturer_id'];
            $pid_mid_rule_list[$key] = $orderHandleRule;
        }

        $c = new CDbCriteria();
        $c->addCondition('product_id>100');
        $products = HtProduct::model()->findAll($c);
        foreach ($products as $product) {
            $result = false;
            $product_id = $product['product_id'];
            $manufacturer_id = $product['supplier_id'];
            $key = $product_id . '-' . $manufacturer_id;

            $rule = null;
            if (isset($pid_mid_rule_list[$key])) {
                $rule = $pid_mid_rule_list[$key];
            } else {
                $key = '0-' . $manufacturer_id;
                if (isset($pid_mid_rule_list[$key])) {
                    $rule = $pid_mid_rule_list[$key];
                }
            }

            if ($rule == null) {
                echo '<h3>product_id: ' . $product_id . ', no order handle rule found.';
            } else {
                $confirmation_type = 0;
                $supplier_feedback_type = 3;
                if ($rule['need_confirmation_code'] > 0) {
                    $supplier_feedback_type = 1;
                    $confirmation_type = $rule['need_confirmation_code'];
                } else {
                    if ($rule['need_attachement'] == 1) {
                        $supplier_feedback_type = 2;
                        $confirmation_type = 1;
                    } else {
                        if ($rule['need_references'] > 0) {
                            $supplier_feedback_type = 1;
                        }
                    }
                }

                $need_notify_supplier = $rule['supplier_need_hitour_voucher'] == 1 ? 1 : 0;

                $item = HtProductShippingRule::model()->findByPk($product_id);
                if (empty($item)) {
                    $item = new HtProductShippingRule();
                    //  fill data
                    $item['product_id'] = $product_id;
                    ModelHelper::fillItem($item, $rule, array('booking_type', 'supplier_email'));
                    $item['language_id'] = $rule['email_language_id'];
                    $item['need_supplier_booking_ref'] = $rule['need_references'];
                    $item['supplier_feedback_type'] = $supplier_feedback_type;
                    $item['confirmation_type'] = $confirmation_type;
                    $item['need_hitour_booking_ref'] = $rule['need_hitour_reference'];
                    $item['confirmation_display_type'] = $rule['need_barcode'] == '1' ? '2' : '1';
                    $item['display_additional_info'] = $rule['need_additional_info'];
                    $item['need_notify_supplier'] = $need_notify_supplier;

                    $result = $item->insert();
                } else {
                    //  update fields
                    ModelHelper::fillItem($item, $rule, array('booking_type', 'supplier_email'));
                    $item['language_id'] = $rule['email_language_id'];
                    $item['need_supplier_booking_ref'] = $rule['need_references'];
                    $item['supplier_feedback_type'] = $supplier_feedback_type;
                    $item['confirmation_type'] = $confirmation_type;
                    $item['need_hitour_booking_ref'] = $rule['need_hitour_reference'];
                    $item['confirmation_display_type'] = $rule['need_barcode'] == '1' ? '2' : '1';
                    $item['display_additional_info'] = $rule['need_additional_info'];
                    $item['need_notify_supplier'] = $need_notify_supplier;

                    $result = $item->update();
                }
            }

            echo '<h3>product_id: ' . $product_id . ', result: ' . $result . ', time: ' . date('Y/m/d H:i:s',
                                                                                               time()) . '</h3>';
        }

        echo '<h2>End: ' . date('Y/m/d H:i:s', time()) . '</h2>';
    }

    public function actionCopyProductTourPlan()
    {
        $from_product_id = (int)Yii::app()->request->getParam('from_product_id');
        $to_product_id = (int)Yii::app()->request->getParam('to_product_id');
        $this->copyTourPlan($from_product_id, $to_product_id);

        echo '<h2>From ' . $from_product_id . ' to ' . $to_product_id . '. Done on ' . date('Y/m/d H:i:s',
                                                                                            time()) . '</h2>';
    }

    public function actionUpdateCloseDatesOfTourOperation()
    {
        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';

        $c = new CDbCriteria();
        $c->addCondition('frequency<>""');
        $c->addNotInCondition('frequency', array('Daily', '每天'));
        $c->addCondition('product_id > 100');
        $items = HtProductTourOperation::model()->findAll($c);
        foreach ($items as $item) {
            $frequency = $item['frequency'];
            $close_dates = $this->getCloseDates($frequency);
            $item['close_dates'] = $close_dates;
            $result = $item->update();

            echo "<h3>Operation_id: " . $item['operation_id'] . ", product_id: " . $item['product_id'] . ", result: $result, frequency: $frequency, close_dates: $close_dates </h3>";
        }

        $c = new CDbCriteria();
        $c->addCondition('close_dates<>""');
        $c->addCondition('product_id > 100');
        $date_rules = HcProductSaleDateRule::model()->findAll($c);
        foreach ($date_rules as $rule) {
            $close_dates = $rule['close_dates'];
            $new_close_dates = $this->cleanCloseDates($close_dates);
            if (!$new_close_dates == '') {
                $items = HtProductTourOperation::model()->findAll('product_id = ' . $rule['product_id']);
                foreach ($items as $item) {
                    $exists_close_dates = $item['close_dates'];
                    if ($exists_close_dates == '') {
                        $exists_close_dates = $new_close_dates;
                    } else {
                        $exists_close_dates .= ';' . $new_close_dates;
                    }
                    $item['close_dates'] = $exists_close_dates;
                    $result = $item->update();

                    echo "<h3>Operation_id: " . $item['operation_id'] . ", product_id: " . $item['product_id'] . ", result: $result, frequency: " . $item['frequency'] . ", close_dates: $exists_close_dates </h3>";
                }
            }
        }

        echo '<h2>End: ' . date('Y/m/d H:i:s', time()) . '</h2>';

    }

    public function actionUpdateHowItWorks()
    {
        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';

//        require_once 'Michelf/Markdown.inc.php';

        $items = HtProductDescription::model()->findAll("length(how_it_works)>20");
        foreach ($items as $item) {
            $how_it_works = $item['how_it_works'];
            $s = rawurldecode(html_entity_decode($how_it_works));
            $json = json_decode($s, true);
            $md_text = $json['md_text'];
            $md_html = $json['md_html'];

//            echo '<h2>original md_html</h2>';
//            echo $md_html;

            $md_text = $md_text . "\n1. 请务必用A4纸打印兑换单，如不打印出现无法兑换的情况，后果由客人自己承担。";

//            $md_html = \Michelf\Markdown::defaultTransform($md_text);

//            echo '<h2>new md_html:</h2>';
//            echo $md_html;


            $json = array('md_text' => $md_text, 'md_html' => $md_html);
            $new_how_it_works = rawurlencode(json_encode($json));

            $item['how_it_works'] = $new_how_it_works;
            $result = $item->update();

            echo '<h3>product_id: ' . $item['product_id'] . ', result: ' . $result . '</h3>';
//            break;
        }

        echo '<h2>End: ' . date('Y/m/d H:i:s', time()) . '</h2>';
    }

    public function actionUpdateVoucherRef()
    {
        $c = new CDbCriteria();
        $c->addInCondition('o.status_id', array('3'));
        $c->addCondition('o.order_id > 0');
        $c->addCondition('o.order_id <= 1000');
        $orders = HtOrder::model()->with('order_product')->findAll($c);
        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';
        foreach ($orders as $order) {
            $date = date('Ymd', strtotime($order['date_added']));

            $voucher_path = Yii::app()->params['DIR_UPLOAD_ROOT'] . Yii::app()->params['VOUCHER_PATH'] . $date . '/' . $order['order_id'] . '/';
            $files = FileUtility::collectFiles($voucher_path, 'pdf');

            if (!empty($files)) {
                $supplier_order_id = $order['order_product']['supplier_order_id'];
                $item = HtSupplierOrder::model()->findByPk($supplier_order_id);
                if (!empty($item)) {
                    $item['voucher_ref'] = json_encode($files);
                    $result = $item->update();
                    echo '<h3>order_id: ' . $order['order_id'] . ', supplier_order_id: ' . $supplier_order_id . ', result: ' . $result . '</h3>';
                } else {
                    echo '<h3>supplier_order_id ' . $supplier_order_id . ' not found.</h3>';
                }
            } else {
                echo '<h3>order_id: ' . $order['order_id'] . ',No pdf found.</h3>';
            }

            break;
        }


        echo '<h2>End: ' . date('Y/m/d H:i:s', time()) . '</h2>';
    }

    private function cleanCloseDates($close_dates)
    {
        $list = explode(';', $close_dates);
        $valid_list = array();
        foreach ($list as $item) {
            if (strpos($item, '-') > 0) {
                array_push($valid_list, $item);
            }
        }

        return implode(';', $valid_list);
    }

    private function getCloseDates($frequency)
    {
        $to_be_replaced = array('/', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期天',
            '周一', '周二', '周三', '周四', '周五', '周六', '周日',
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $replace_to = array(';', '周1', '周2', '周3', '周4', '周5', '周6', '周7',
            '周1', '周2', '周3', '周4', '周5', '周6', '周7',
            '周1', '周2', '周3', '周4', '周5', '周6', '周7');

        $result = str_replace($to_be_replaced, $replace_to, $frequency);

        $list = explode(";", $result);

        $close_dates = array();
        for ($i = 1; $i < 8; $i++) {
            $str = '周' . $i;
            if (!in_array($str, $list)) {
                array_push($close_dates, $str);
            }
        }

        return implode(';', $close_dates);
    }

    private function copyTourPlan($product_id, $new_product_id)
    {
        //  copy tour plan
        $data = HtProductTourPlan::model()->with('groups.items')->findAll('product_id=' . $product_id);
        HtProductTourPlan::model()->deleteAll('product_id=' . $new_product_id); // delete old
        foreach ($data as $tour_plan) {
            $new_tour_plan = new HtProductTourPlan();
            ModelHelper::fillItem($new_tour_plan, $tour_plan, array('total_days', 'the_day', 'title'));
            $new_tour_plan['product_id'] = $new_product_id;
            $result = $new_tour_plan->insert();
            if ($result) {
                $new_tour_plan_id = $new_tour_plan['plan_id'];
                //  copy groups
                $groups = $tour_plan['groups'];
                foreach ($groups as $group) {
                    $new_tour_plan_group = new HtProductTourPlanGroup();
                    ModelHelper::fillItem($new_tour_plan_group, $group,
                                          array('title', 'time', 'display_order')); // TODO
                    $new_tour_plan_group['plan_id'] = $new_tour_plan_id;
                    $result = $new_tour_plan_group->insert();
                    if ($result) {
                        //  copy items
                        $new_group_id = $new_tour_plan_group['group_id'];
                        $items = $group['items'];
                        foreach ($items as $item) {
                            $new_tour_plan_item = new HtProductTourPlanItem();
                            ModelHelper::fillItem($new_tour_plan_item, $item,
                                                  array('image_url', 'title', 'description', 'display_order'));
                            $new_tour_plan_item['group_id'] = $new_group_id;
                            $new_tour_plan_item->insert();
                        }
                    }
                }
            }
        }
    }

    public function actionUpdatePickTicketMap()
    {
        return;

        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';
        $c = new CDbCriteria();
        $c->addCondition('need_pick_ticket_album=1');
        $c->addCondition('pick_ticket_album_id>0');
        $c->addCondition('pick_ticket_map=""');
        $c->addCondition('pt_group_info IS NOT NULL');
        $c->limit = 30;
        $c->order = 'product_id ASC';

        $data = HtProductAlbum::model()->findAll($c);
        foreach ($data as $item) {
            $album_id = $item['pick_ticket_album_id'];
            if ($album_id > 0) {
                // TODO get album landinfos, pg_group_info
                $points = array();
                $landinfos = Landinfo::model()->getLandinfos($album_id);
                $pt_group_info = CJSON::decode($item['pt_group_info']);
                if (empty($pt_group_info) || !is_array($pt_group_info)) {
                    continue;
                }
                foreach ($pt_group_info as $group) {
                    foreach ($group['items'] as $landinfo_id) {
                        foreach ($landinfos as $landinfo) {
                            if ($landinfo['landinfo_id'] == $landinfo_id) {
                                $point = explode(',', $landinfo['location']);
                                if (count($point) == 2) {
                                    array_push($points, array($point[1], $point[0]));
                                }
                            }
                        }
                    }
                }

                // TODO construct markers, calculate center
                $center = array(0, 0);
                $markers = '';
                if (count($points) > 0) {
                    $index = 0;
                    foreach ($points as $point) {
                        $center[0] += $point[0];
                        $center[1] += $point[1];

                        $loc = $point[0] . ',' . $point[1];
                        if (!empty($markers)) {
                            $markers .= ',';
                        }

                        $markers .= 'pin-s-' . chr(97 + $index) . '+f00(' . $loc . ')';

                        $index++;
                    }

                    $center[0] = $center[0] / count($points);
                    $center[1] = $center[1] / count($points);
                }

                // TODO get image by mapbox static map service update $item

                $zoom = 12;
                $url = 'http://api.tiles.mapbox.com/v3/natecui.ig5adgfm/' . $markers . '/' . $center[0] . ',' . $center[1] . ',' . $zoom . '/1280x310.png';

                $image = FileUtility::downloadToFile($url);
                if (!empty($image)) {
                    $pick_ticket_map = FileUtility::uploadToQiniu($image, true);

                    $item['pick_ticket_map'] = $pick_ticket_map;
                    $item->update();

                    echo '<h3>Updated product: ' . $item['product_id'] . '</h3>';
                }
            }
        }

        echo '<h2>End: ' . date('Y/m/d H:i:s', time()) . '</h2>';
    }

    public function actionUpdateAlbumMap()
    {
        return;

        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';
        $c = new CDbCriteria();
        $c->addCondition('need_album=1');
        $c->addCondition('album_id>0');
        $c->addCondition('album_map=""');
        $c->limit = 30;
        $c->order = 'product_id ASC';

        $data = HtProductAlbum::model()->findAll($c);
        foreach ($data as $item) {
            $album_id = $item['album_id'];
            if ($album_id > 0) {
                // TODO get album landinfos
                $points = array();
                $landinfos = Landinfo::model()->getLandinfos($album_id);
                if (empty($landinfos)) {
                    continue;
                }
                foreach ($landinfos as $landinfo) {
                    $point = explode(',', $landinfo['location']);
                    if (count($point) == 2) {
                        array_push($points, array($point[1], $point[0]));
                    }
                }

                // TODO construct markers, calculate center
                $center = array(0, 0);
                $markers = '';
                if (count($points) > 0) {
                    $index = 0;
                    foreach ($points as $point) {
                        $center[0] += $point[0];
                        $center[1] += $point[1];

                        $loc = $point[0] . ',' . $point[1];
                        if (!empty($markers)) {
                            $markers .= ',';
                        }

                        $markers .= 'pin-s-' . chr(97 + $index) . '+f00(' . $loc . ')';

                        $index++;
                    }

                    $center[0] = $center[0] / count($points);
                    $center[1] = $center[1] / count($points);
                }

                // TODO get image by mapbox static map service update $item

                $zoom = 12;
                $url = 'http://api.tiles.mapbox.com/v3/natecui.ig5adgfm/' . $markers . '/' . $center[0] . ',' . $center[1] . ',' . $zoom . '/1280x310.png';

                $image = FileUtility::downloadToFile($url);
                if (!empty($image)) {
                    $album_map = FileUtility::uploadToQiniu($image, true);

                    $item['album_map'] = $album_map;
                    $item->update();

                    echo '<h3>Updated product: ' . $item['product_id'] . '</h3>';
                }
            }
        }

        echo '<h2>End: ' . date('Y/m/d H:i:s', time()) . '</h2>';
    }

    public function actionServiceIncludePart1()
    {
        $max_length = 50;
        if (isset($_GET['max'])) {
            $max_length = (int)$_GET['max'];
        }
        $items = HtProduct::model()->with('description')->findAll('status=3');
        foreach ($items as $item) {
            $service_include = $item['description']['service_include'];
            $service_include = rawurldecode($service_include);
            $parts = json_decode($service_include, true);
            $md_text = isset($parts['md_text']) ? $parts['md_text'] : '';

//            $service_include = html_entity_decode($service_include);
            if (strlen($md_text) < 8) {
                continue;
            }

            $pos2 = strpos($md_text, '##', 4);

            $len = mb_strlen($md_text, 'utf-8');
            if ($pos2 !== false) {
                $s = substr($md_text, 0, $len);

                $len = mb_strlen($s, 'utf-8');
            }
            if ($len > $max_length) {
//                echo $service_include;
                echo '<h2>product id: ' . $item['product_id'] . ', service include part 1 length: ' . $len;
            }
        }
    }

    public function actionCleanCloseDates()
    {
        echo '<h2>Start: ' . date('Y/m/d H:i:s', time()) . '</h2>';
        $c = new CDbCriteria();
        $c->addCondition('close_dates<>""');
        $c->addCondition('product_id > 200');
        $c->order = 'product_id ASC';
        $c->limit = 50;

        $items = HtProductTourOperation::model()->findAll($c);
        foreach ($items as $item) {
            $close_dates = $item['close_dates'];
            $duplicated = false;
            $close_date_list_orig = explode(';', $close_dates);

            $close_date_list_clean = array();
            foreach ($close_date_list_orig as $date) {
                if (in_array($date, $close_date_list_clean)) {
                    $duplicated = true;
                } else {
                    if (!empty($date)) {
                        array_push($close_date_list_clean, $date);
                    }
                }
            }
            if ($duplicated) {
                $item['close_dates'] = implode(";", $close_date_list_clean);
                $item->update();

                echo '<h3>Product ' . $item['product_id'] . ' has duplicated close_dates. original: ' . implode(";",
                                                                                                                $close_date_list_orig) .
                    ', clean: ' . implode(";", $close_date_list_clean) . '</h3>';
            } else {
                echo '<h3>Porduct ' . $item['product_id'] . ' is clean.</h3>';
            }
        }

        echo '<h2>End: ' . date('Y/m/d H:i:s', time()) . '</h2>';
    }

    public function actionPGUpdate()
    {
        $cities = HtCity::model()->getAllCitiesHaveProductsOnline();

        foreach ($cities as $city) {
            $has_special = false;
            echo '<h2>Update city ' . $city['cn_name'] . ' -- code ' . $city['city_code'] . '...</h2>';
            $city_code = $city['city_code'];
            $pg = HtProductGroup::model()->findByAttributes(['city_code' => $city_code, 'type' => 6]);
            if (empty($pg)) {
                EchoUtility::echoCommonFailed('City ' . $city['cn_name'] . ' has no product group "特价商品".');

                return;
            }

            HtProductGroupRef::model()->deleteAllByAttributes(['group_id' => $pg['group_id']]);

            $products = HtProduct::model()->findAllByAttributes(['status' => 3, 'city_code' => $city_code]);
            $product_ids = ModelHelper::getList($products, 'product_id');

            $other_products = HtProductCity::model()->with('product')->findAllByAttributes(['city_code' => $city_code]);
            foreach ($other_products as $product) {
                if ($product['product']['status'] == 3) {
                    array_push($product_ids, $product['product_id']);
                }
            }

            foreach ($product_ids as $product_id) {
                $price_special = HtProductPricePlanSpecial::model()->getPricePlanSpecial($product_id);
                if (!empty($price_special)) {
                    // add an record
                    $item = new HtProductGroupRef();
                    $item['group_id'] = $pg['group_id'];
                    $item['product_id'] = $product_id;
                    $item['product_image_url'] = '';
                    $item['display_order'] = 1;
                    $item->insert();
                    $has_special = true;
                }
            }

            if ($has_special) {
                echo "<h3>has special</h3>";
            }
        }

        echo '<h2>finished.</h2>';
    }

    public function actionOperationList()
    {
        echo '<h2>数据迁移</h2>';
//        echo '<h3><a href="' . $this->createUrl('dataMigrate/migrateProductPassengerRule') . '" target="_blank">Product Passenger Rule</a></h3>';
//        echo '<h3><a href="' . $this->createUrl('dataMigrate/updateCloseDatesOfTourOperation') . '" target="_blank">Update Product Tour Operation Close Dates</a></h3>';
//        echo '<h3><a href="' . $this->createUrl('dataMigrate/migrateTourOperation') . '" target="_blank">Migrate Tour Operation</a></h3>';
//        echo '<h3><a href="' . $this->createUrl('dataMigrate/updateProductDateRule') . '" target="_blank">Update Product DateRule</a></h3>';
//        echo '<h3><a href="' . $this->createUrl('dataMigrate/migrateDeparture') . '" target="_blank">Migrate Departure</a></h3>';
//        echo '<h3><a href="' . $this->createUrl('dataMigrate/migrateSpecialCode') .'" target="_blank">Product Special Code</a></h3>';
//        echo '<h3><a href="' . $this->createUrl('dataMigrate/migrateSaleRule') . '" target="_blank">Product Sale Rule</a></h3>';
//        echo '<h3><a href="' . $this->createUrl('dataMigrate/migrateProductPricePlan') . '" target="_blank">Product Price Plan</a></h3>';
//        echo '<h3><a href="' . $this->createUrl('dataMigrate/migrateProductPriceSpecial') . '" target="_blank">Product Price Plan Special</a></h3>';
//        echo '<h3><a href="' . $this->createUrl('dataMigrate/migrateProductShippingRule') . '" target="_blank">Product Shipping Rule</a></h3>';
//        echo '<h3><a href="' . $this->createUrl('dataMigrate/migrateOrderPassenger') . '" target="_blank">Order Passenger</a></h3>';


    }
}
