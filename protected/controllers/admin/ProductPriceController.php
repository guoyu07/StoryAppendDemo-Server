<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 5/16/14
 * Time: 4:32 PM
 */
class ProductPriceController extends AdminController
{
    public function actionIndex()
    {
        echo 'Price editing';
    }

    public function actionSpecialGroup()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $product_id = $this->getProductID();
        $product = HtProduct::model()->findByPk($product_id);
        if ($request_method == 'get') {
            $data = HtProductSpecialGroup::getAllGroups($product_id);

            EchoUtility::echoCommonMsg(200, '', ['has_special' => empty($data) ? 0 : 1, 'groups' => $data]);
        } elseif ($request_method == 'post') {
            $data = $this->getPostJsonData();
            if (!empty($data)) {
                foreach ($data as $group) {
                    if (isset($group['group_id']) && !empty($group['group_id'])) {
                        $special_group = HtProductSpecialGroup::model()->findByPk($group['group_id']);
                        ModelHelper::updateItem($special_group, $group,
                                                ['cn_title', 'en_title', 'display_order', 'status']);
                    } else {
                        $group['product_id'] = $product_id;
                        $special_group = new HtProductSpecialGroup();
                        ModelHelper::fillItem($special_group, $group,
                                              ['product_id', 'cn_title', 'en_title', 'display_order', 'status']);
                        $special_group->insert();
                        $group['group_id'] = $special_group['group_id'];
                    }

                    foreach ($group['special_items'] as $item) {
                        if($product['type'] = HtProduct::T_CHARTER_BUS){
                            $item['product_origin_name'] = '';//包车商品不需要供应商原始名称
                        }
                        if(empty($item['group_id'])){
                            $item['group_id'] = $group['group_id'];
                        }
                        $special_code = $item['special_code'];
                        if (empty($special_code)) {
                            $special_code = substr(md5(microtime() + mt_rand(1, 32768)), 0, 8);
                            $item['special_code'] = $special_code;
                        }

                        $special_item = HtProductSpecialItem::model()->findByPk(['group_id' => $group['group_id'], 'special_code' => $special_code]);

                        if (empty($special_item)) {
                            $special_item = new HtProductSpecialItem();
                            ModelHelper::fillItem($special_item, $item,
                                                  ['group_id', 'special_code', 'cn_name', 'en_name',
                                                      'description', 'product_origin_name', 'mapping_product_id', 'mapping_special_code', 'status', 'display_order']);
                            $special_item->insert();
                        } else {
                            ModelHelper::updateItem($special_item, $item, ['cn_name', 'en_name', 'description',
                                'product_origin_name', 'mapping_product_id', 'mapping_special_code', 'status', 'display_order']);
                        }
                        if ($item['item_limit']) {
                            $item_limit = HtProductSpecialItemLimit::model()->findByPk(['group_id' => $group['group_id'], 'special_code' => $special_code]);
                            if (empty($item_limit)) {
                                $item_limit = new HtProductSpecialItemLimit();
                                $item['item_limit']['group_id'] = $group['group_id'];
                                $item['item_limit']['special_code'] = $special_code;
                                ModelHelper::fillItem($item_limit, $item['item_limit'], ['group_id',  'special_code', 'limit_pax_num', 'max_pax_num', 'min_pax_num']);
                                $item_limit->insert();
                            } else {
                                ModelHelper::updateItem($item_limit, $item['item_limit'], ['limit_pax_num', 'max_pax_num', 'min_pax_num']);
                            }
                        } else {
                            HtProductSpecialItemLimit::model()->deleteByPk(['group_id' => $group['group_id'], 'special_code' => $special_code]);
                        }
                    }
                }
            }
            $result = HtProductSpecialGroup::getAllGroups($product_id);

            HtProductSpecialCombo::updateSpecialCombo($product_id);
            //TODO:clearPricePlan

            EchoUtility::echoCommonMsg(200, '保存完毕。', $result);
        } elseif ($request_method == 'delete') {
            // do nothing. delete group not supported
        }
    }

    public function actionSpecialItem()
    {
        // add/update/delete special code
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $group_id = $this->getSpecialGroupID();
        $special_code = $this->getProductSpecialCode();
        $pk = array('group_id' => $group_id, 'special_code' => $special_code);
        $item = HtProductSpecialItem::model()->findByPk($pk);
        if ($request_method == 'get') {

        } else {
            if ($request_method == 'post') {
                $data = $this->getPostJsonData();
                //special_item
                if (empty($item)) {
                    $item = new HtProductSpecialItem();
                    //  generate special_code
                    $item['group_id'] = $group_id;
                    $special_code = substr(md5(date('Ymd_H:i:s', time())), 0, 8);
                    $item['special_code'] = $special_code;
                    $item['cn_name'] = $data['cn_name'];
                    $item['en_name'] = $data['en_name'];
                    $item['description'] = $data['description'];
                    $item['product_origin_name'] = $data['product_origin_name'];
                    $item['status'] = $data['status'];
                    $item['display_order'] = $data['display_order'];
                    $result = $item->insert();
                    if($result){
                        $pk = array('group_id' => $group_id, 'special_code' => $special_code);
                    }
                } else {
                    //  specify the fields to be updated
                    $result = ModelHelper::updateItem($item, $data,
                                                      array('cn_name', 'en_name', 'description', 'product_origin_name', 'mapping_product_id', 'mapping_special_code', 'status'));
                }

                //item_limit
                if ($data['item_limit']) {
                    $item_limit = HtProductSpecialItemLimit::model()->findByPk($pk);
                    if (empty($item_limit)) {
                        $item_limit = new HtProductSpecialItemLimit();
                        $data['item_limit']['special_code'] = $special_code;
                        ModelHelper::fillItem($item_limit, $data['item_limit'], ['group_id',  'special_code', 'limit_pax_num', 'max_pax_num', 'min_pax_num']);
                        $item_limit->insert();
                    } else {
                        ModelHelper::updateItem($item_limit, $data['item_limit'], ['limit_pax_num', 'max_pax_num', 'min_pax_num']);
                    }
                }
                $info = HtProductSpecialItem::model()->with('item_limit')->findByPk($pk);
                EchoUtility::echoMsgTF($result, '更新', Converter::convertModelToArray($info));

            } else {
                if ($request_method == 'delete') {
                    $result = HtProductSpecialItem::model()->deleteByPk($pk);
                    $result = HtProductSpecialItemLimit::model()->deleteByPk($pk);
                    EchoUtility::echoMsgTF($result > 0, '删除');
                }
            }
        }
    }

    public function actionSpecialGroupOrder()
    {
        // TODO update display order of special groups
        $data = $this->getPostJsonData();
        $result = true;
        foreach ($data as $group) {
            $special_group = HtProductSpecialGroup::model()->findByPk($group['group_id']);
            $part_result = ModelHelper::updateItem($special_group,
                                                   array('display_order' => $group['display_order']));
            if ($part_result != 1) {
                $result = false;
                break;
            }
        }
        $product_id = $this->getProductID();
        HtProductSpecialCombo::updateSpecialCombo($product_id);
        EchoUtility::echoMsgTF($result, '更新special group排序');
    }

    public function actionSpecialItemOrder()
    {
        // TODO update display order of special item
        $data = $this->getPostJsonData();
        $result = true;
        foreach ($data as $item) {
            $special_item = HtProductSpecialGroup::model()->findByPk(array('group_id'=>$item['group_id'],'special_code'=>$item['special_code']));
            $part_result = ModelHelper::updateItem($special_item,
                                                   array('display_order' => $item['display_order']));
            if ($part_result != 1) {
                $result = false;
                break;
            }
        }

        EchoUtility::echoMsgTF($result, '更新special排序');
    }

    public function actionUpdateSpecialItemStatus()
    {
        $data = $this->getPostJsonData();
        $item = HtProductSpecialItem::model()->findByPk(array('group_id'=>$data['group_id'],'special_code'=>$data['special_code']));
        if (empty($item)) {
            EchoUtility::echoCommonFailed('没有找到该special');

            return;
        }
        $result = ModelHelper::updateItem($item, $data, array('status'));
        EchoUtility::echoMsg($result, '产品分组状态', '', Converter::convertModelToArray($item));
    }


    public function actionGetProductSpecialCodes()
    {
        $product_id = $this->getProductID();
        $special_codes = HtProductSpecialCode::model()->findAll('product_id=' . $product_id);
        $data['special_codes'] = Converter::convertModelToArray($special_codes);

        $special_titles = HtProductDescription::model()->getFieldValues($product_id, 'special_title');
        $data = array_merge($data, $special_titles);

        EchoUtility::echoMsgTF(true, '', $data);
    }

    public function actionSaveProductSpecialCodes()
    {
        $product_id = $this->getProductID();
        $data = $this->getPostJsonData();
        $result = true;

        if ($data["need_special_code"] == 2) {
            $data["cn_special_title"] = "";
            $data["en_special_title"] = "";
        }

        $result = HtProductDescription::model()->updateFieldValues($product_id, 'special_title', $data);
        if (!$result) {
            EchoUtility::echoCommonFailed('更新Special Code 名称失败。');

            return;
        }

        if ($data["need_special_code"] == 2) {
            $deleted = HtProductSpecialCode::model()->deleteAll('product_id=' . $product_id);
            if ($deleted > 0) {
                $this->clearPricePlan($product_id);
                $this->clearPricePlan($product_id, 1);
            }
        } else {
            // remove special codes which have already deleted by user.
            $c = new CDbCriteria();
            $c->addCondition('product_id=' . $product_id);
            $price_plans = HtProductPricePlan::model()->findAll($c);
            $price_plans = Converter::convertModelToArray($price_plans);
            $special_codes = HtProductSpecialCode::model()->findAll($c);
            $special_codes = Converter::convertModelToArray($special_codes);
            foreach ($special_codes as $origin_code) {
                $need_remove = true;
                foreach ($data['special_codes'] as $new_code) {
                    if (!empty($new_code["special_code"]) && $new_code["special_code"] == $origin_code["special_code"]) {
                        $need_remove = false;
                        break;
                    }
                }
                if ($need_remove) {
                    $pk = array('product_id' => $product_id, 'special_code' => $origin_code["special_code"]);
                    $res = $this->removeSpecialCodePricePlan($price_plans, $origin_code["special_code"]);

                    $price_plans_special = Converter::convertModelToArray(HtProductPricePlan::model()->findAllByAttributes(array('product_id' => $product_id)));

                    $res = $this->removeSpecialCodePricePlan($price_plans_special, $origin_code['special_code'], 1);

                    HtProductPricePlan::clearCache($product_id);

                    $res = HtProductSpecialCode::model()->deleteByPk($pk);
                    if ($res = false) {
                        break;
                    }
                }
            }

            // insert and update logic.
            foreach ($data['special_codes'] as $special_code) {
                if (empty($special_code["special_code"])) {
                    // Insert new special code.
                    $item = new HtProductSpecialCode();
                    $item['product_id'] = $product_id;
                    $item['special_code'] = substr(md5(microtime()), 0, 8);
                    ModelHelper::fillItem($item, $special_code,
                                          ['cn_name', 'en_name', 'description', 'product_origin_name', 'mapping_product_id', 'mapping_special_code', 'status']);

                    $ret = $item->insert();
                } else {
                    // update special code.
                    $sc = HtProductSpecialCode::model()->findByPk(array('product_id' => $product_id, 'special_code' => $special_code['special_code']));
                    $ret = ModelHelper::updateItem($sc, $special_code,
                                                   array('cn_name', 'en_name', 'description', 'product_origin_name', 'mapping_product_id', 'mapping_special_code', 'status'));
                }

                if ($ret != 1) {
                    $result = false;
                    break;
                }
            }

        }

        EchoUtility::echoMsgTF($result, '保存');
    }

    public function actionDeleteProductSpecialCodes()
    {
        // if product do not need special code, delete all special code
        HtProductSpecialCode::model()->deleteAll('product_id=' . $this->getProductID());
        EchoUtility::echoMsgTF(true, '删除');
    }

    public function actionProductSpecialCode()
    {
        // add/update/delete special code
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $product_id = $this->getProductID();
        $special_code = $this->getProductSpecialCode();
        $pk = array('product_id' => $product_id, 'special_code' => $special_code);

        if ($request_method == 'get') {

        } else {
            if ($request_method == 'post') {
                $data = $this->getPostJsonData();
                $item = HtProductSpecialCode::model()->findByPk($pk);

                if (empty($item)) {
                    $item = new HtProductSpecialCode();
                    //  generate special_code
                    $item['product_id'] = $product_id;
                    $item['special_code'] = substr(md5(date('Ymd_H:i:s', time())), 0, 8);
                    $item['cn_name'] = '中文名';
                    $item['en_name'] = '英文名';
                    $item['description'] = '';
                    $item['product_origin_name'] = '';
                    $item['status'] = '1';

                    $result = $item->insert();
                    EchoUtility::echoMsgTF($result, '添加', $item);
                } else {
                    //  specify the fields to be updated
                    $result = ModelHelper::updateItem($item, $data,
                                                      array('cn_name', 'en_name', 'description', 'product_origin_name', 'mapping_product_id', 'mapping_special_code', 'status'));
                    EchoUtility::echoMsgTF($result, '更新', $item);
                }
            } else {
                if ($request_method == 'delete') {
                    $result = HtProductSpecialCode::model()->deleteByPk($pk);

                    EchoUtility::echoMsgTF($result > 0, '删除');
                }
            }
        }
    }

    public function actionTicketRules()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $product_id = $this->getProductID();

        if ($request_method == 'get') {
            $data = $this->getProductTicketRules($product_id);
            EchoUtility::echoMsgTF(true, '', $data);
        } else {
            if ($request_method == 'post') {
                HtProductTicketRule::model()->deleteAll('product_id=' . $product_id . ' AND ticket_id<>99');
                $data = $this->getPostJsonData();
                $result = true;
                foreach ($data['ticket_rules'] as $ticket_rule) {
                    $ticket_id = $ticket_rule['ticket_id'];
                    $item = HtProductTicketRule::model()->findByPk(array('product_id' => $product_id, 'ticket_id' => $ticket_id));
                    if (empty($item)) {
                        $item = new HtProductTicketRule();
                        $item['product_id'] = $product_id;
                        $item['ticket_id'] = $ticket_id;
                        $item['age_range'] = $ticket_rule['age_range'];
                        $item['description'] = $ticket_rule['description'];

                        $result = $item->insert();
                        if (!$result) {
                            break;
                        }
                    } else {
                        $item['age_range'] = $ticket_rule['age_range'];
                        $item['description'] = $ticket_rule['description'];

                        $result = $item->update();
                        if (!$result) {
                            break;
                        }
                    }
                }

                if (!empty($data['reset_ticket_type']) && $data['reset_ticket_type'] == '1') {
                    HtProductTicketRule::model()->deleteAll('product_id=' . $product_id . ' AND ticket_id=99');
                    HtProductPackageRule::model()->deleteAll('product_id=' . $product_id);
                    $this->clearPricePlan($product_id);

                    HtProductSaleRule::model()->deleteAll('product_id=' . $product_id);
                    $sale_rule = new HtProductSaleRule();
                    $sale_rule["product_id"] = $product_id;
                    $sale_rule->insert();
                }

                $ticket_ids = ModelHelper::getList($data['ticket_rules'], 'ticket_id');
                HtProductPassengerRuleItem::model()->updateByTicketRule($product_id, $ticket_ids);

                EchoUtility::echoMsgTF($result, '', $this->getProductTicketRules($product_id));
            }
        }
    }

    public function actionTicketTypes()
    {
        $data = HtTicketType::model()->findAll();
        EchoUtility::echoMsgTF(true, '', Converter::convertModelToArray($data));
    }

    public function actionGetDateRule()
    {
        $product_id = $this->getProductID();

        $data = array();

        $tour_date_titles = HtProductDescription::model()->getFieldValues($product_id, 'tour_date_title');
        $data = array_merge($data, $tour_date_titles);

        HtProductDescription::model()->findAll('product_id=' . $product_id);

        $product_date_rule = HtProductDateRule::model()->findByPk($product_id);
        $data['product_date_rule'] = Converter::convertModelToArray($product_date_rule);
        $product_tour_operation = HtProductTourOperation::model()->findAll('product_id=' . $product_id);
        $data['product_tour_operation'] = Converter::convertModelToArray($product_tour_operation);

        EchoUtility::echoMsgTF(true, '', $data);
    }

    public function actionSaveDateRule()
    {
        $product_id = $this->getProductID();
        $data = $this->getPostJsonData();

        $result = HtProductDescription::model()->updateFieldValues($product_id, 'tour_date_title', $data);
        if (!$result) {
            EchoUtility::echoCommonFailed('更新使用日期标题失败。');

            return;
        }

        $product_date_rule = $data['product_date_rule'];
        $sale_date_item = HtProductDateRule::model()->findByPk($product_id);
        ModelHelper::fixDateValue($product_date_rule, array('from_date', 'to_date'));
        $result = ModelHelper::updateItem($sale_date_item, $product_date_rule,
                                          array('need_tour_date', 'from_date', 'to_date'));

        $product_tour_operations = $data['product_tour_operation'];
        //  valid from_date to_date to make sure no intersection between them
        foreach ($product_tour_operations as $product_tour_operation) {
            ModelHelper::fixDateValue($product_tour_operation, array('from_date', 'to_date'));
            $item = HtProductTourOperation::model()->findByPk($product_tour_operation['operation_id']);
            $ret = ModelHelper::updateItem($item, $product_tour_operation,
                                           array('from_date', 'to_date', 'close_dates'));
            if ($ret != 1) {
                EchoUtility::echoCommonFailed('保存售卖区间失败.');
                break;
            }
        }
        //  update from_date to_date of product date rule
        $from_to = HtProductTourOperation::model()->getFromTo($product_id);
        if (!empty($from_to)) {
            ModelHelper::updateItem($sale_date_item, $from_to, ['from_date', 'to_date']);

            $price_plan = HtProductPricePlan::model()->findByAttributes(['valid_region' => 0, 'product_id' => $product_id]);
            ModelHelper::updateItem($price_plan, $from_to, ['from_date', 'to_date']);
            $departure_plans = HtProductDeparturePlan::model()->findAllByAttributes(array('product_id'=>$product_id));
            if($departure_plans){
                foreach($departure_plans as $plan)
                {
                    ModelHelper::updateItem($plan, $from_to, ['from_date', 'to_date']);
                }
            }
        }

        EchoUtility::echoMsgTF(true, '数据保存成功。', $data);
    }

    public function actionProductTourOperation()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $product_id = $this->getProductID();
        $operation_id = (int)Yii::app()->request->getParam('operation_id');

        if ($request_method == 'post') {
            $item = new HtProductTourOperation();
            $item['product_id'] = $product_id;
            $item['from_date'] = date('Y-m-d', time());
            $item['to_date'] = date('Y-m-d', time());
            $item['frequency'] = '';
            $item['confirmation_type'] = 1;
            $item['languages'] = '';

            $result = $item->insert();
            EchoUtility::echoMsgTF($result, '添加', $item);
        } else {
            if ($request_method == 'delete') {
                $result = HtProductTourOperation::model()->deleteByPk($operation_id);
                EchoUtility::echoMsgTF($result, '删除');
            }
        }
    }

    public function actionProductPricePlanBasicInfo()
    {
        $product_id = $this->getProductID();
        $data = array();

        $date_rule = HtProductDateRule::model()->findByPk($product_id);
        $data['from_date'] = $date_rule['from_date'];
        $data['to_date'] = $date_rule['to_date'];
        $data['has_special_code'] = HtProductSpecialCombo::model()->needSpecialCode($product_id);

        $data['special_info'] = HtProductSpecialCombo::getAllComboSpecialDetail($product_id);

        $ticket_types = HtTicketType::model()->getTicketTypesOfProduct($product_id);
        $data['ticket_types'] = $ticket_types;
        $sale_rule = HtProductSaleRule::model()->findByPk($product_id);
        $data['min_num'] = $sale_rule['min_num'];
        $data['max_num'] = $sale_rule['max_num'];

        EchoUtility::echoMsgTF(true, '', $data);
    }

    public function actionProductPricePlans()
    {
        $product_id = $this->getProductID();

        $data = HtProductPricePlan::model()->with('items')->findAll('product_id=' . $product_id);
        $data = Converter::convertModelToArray($data);

        $data = $this->filterPricePlan($product_id, $data);

        EchoUtility::echoMsgTF(true, '', $data);
    }

    public function actionProductPricePlan()
    {
        $product_id = $this->getProductID();
        $price_plan_id = (int)Yii::app()->request->getParam('price_plan_id');

        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($request_method == 'get') {
            $data = HtProductPricePlan::model()->with('items')->findByPk($price_plan_id);
            $data = Converter::convertModelToArray($data);
            $data = $this->filterPricePlan($product_id, $data);
            EchoUtility::echoMsgTF(true, '', $data);
        } else {
            if ($request_method == 'post') {
                //  add or update product price plan
                $data = $this->getPostJsonData();
                ModelHelper::fixDateValue($data, array('from_date', 'to_date'));

                if (!empty($price_plan_id)) {
                    $price_plan = HtProductPricePlan::model()->findByPk($price_plan_id);
                    $columns = array('valid_region', 'need_tier_pricing');
                    if ($data['valid_region'] == 1) {
                        $columns = array_merge($columns, array('from_date', 'to_date'));
                    }
                    $result = ModelHelper::updateItem($price_plan, $data, $columns);

                    //  add/update/delete price plan items
                    if ($result) {
                        $price_plan_items = $data['items'];
                        $result = $this->updateProductPricePlanItems($price_plan_items, $price_plan_id);
                        HtProductPricePlan::clearCache($product_id);
                    }
                } else {
                    $price_plan = new HtProductPricePlan();
                    $price_plan['product_id'] = $product_id;
                    $price_plan['valid_region'] = $data['valid_region'];
                    if ($data['valid_region'] == 1) {
                        ModelHelper::fixDateValue($data, array('from_date', 'to_date'));
                        $price_plan['from_date'] = $data['from_date'];
                        $price_plan['to_date'] = $data['to_date'];
                    }
                    $price_plan['need_tier_pricing'] = $data['need_tier_pricing'];

                    $result = $price_plan->insert();

                    //  add price plan items
                    if ($result) {
                        $price_plan_id = $price_plan['price_plan_id'];

                        $price_plan_items = $data['items'];
                        $result = $this->addProductPricePlanItem($price_plan_id, 0, $price_plan_items);
                    }
                }
                $this->updatePricePlanSpecialCodes($product_id);//更新special_codes
                $data = array();
                if (!empty($price_plan_id)) {
                    $data = Converter::convertModelToArray(HtProductPricePlan::model()->with('items')->findByPk($price_plan_id));
                }

                EchoUtility::echoMsgTF($result, '添加', $data);
            } else {
                if ($request_method == 'delete') {
                    // delete price plan
                    HtProductPricePlanItem::model()->deleteAllByAttributes(array('price_plan_id' => $price_plan_id, 'is_special' => 0));
                    HtProductPricePlan::model()->deleteAll('price_plan_id =' . $price_plan_id);

                    HtProductPricePlan::clearCache($product_id);

                    EchoUtility::echoMsgTF(true, '删除');
                }
            }
        }
    }

    public function actionProductPricePlanSpecials()
    {
        $product_id = $this->getProductID();

        $data = HtProductPricePlanSpecial::model()->with('items')->findAll('product_id=' . $product_id);
        $data = Converter::convertModelToArray($data);

        $data = $this->filterPricePlan($product_id, $data);

        EchoUtility::echoMsgTF(true, '', $data);
    }

    public function actionProductPricePlanSpecial()
    {
        $product_id = $this->getProductID();
        $price_plan_id = (int)Yii::app()->request->getParam('price_plan_id');

        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($request_method == 'get') {
            $data = HtProductPricePlanSpecial::model()->with('items')->findByPk($price_plan_id);
            $data = Converter::convertModelToArray($data);
            EchoUtility::echoMsgTF(true, '', $data);
        } else {
            if ($request_method == 'post') {
                //  add or update product price plan
                $data = $this->getPostJsonData();

                if (!empty($price_plan_id)) {
                    $price_plan = HtProductPricePlanSpecial::model()->findByPk($price_plan_id);
                    $columns = array('valid_region', 'need_tier_pricing', 'reseller', 'slogan');
                    if ($data['valid_region'] == 1) {
                        $columns = array_merge($columns, array('from_date', 'to_date'));
                    }
                    $result = ModelHelper::updateItem($price_plan, $data, $columns);

                    //  add/update/delete price plan items
                    if ($result) {
                        $price_plan_items = $data['items'];
                        $result = $this->updateProductPricePlanItems($price_plan_items, $price_plan_id, 1);
                    }
                } else {
                    $price_plan = new HtProductPricePlanSpecial();
                    $price_plan['product_id'] = $product_id;
                    $columns = array('valid_region', 'need_tier_pricing', 'reseller', 'slogan');

                    ModelHelper::fillItem($price_plan, $data, $columns);
                    $price_plan['valid_region'] = $data['valid_region'];
                    if ($data['valid_region'] == 1) {
                        ModelHelper::fixDateValue($data, array('from_date', 'to_date'));
                        $price_plan['from_date'] = $data['from_date'];
                        $price_plan['to_date'] = $data['to_date'];
                    }
                    $result = $price_plan->insert();

                    //  add price plan items
                    if ($result) {
                        $price_plan_id = $price_plan['price_plan_id'];

                        $result = $this->addProductPricePlanItem($price_plan_id, 1, $data['items']);
                    }
                }
                $this->updatePricePlanSpecialCodes($product_id,1);//更新special_codes
                $data = array();
                if (!empty($price_plan_id)) {
                    $data = Converter::convertModelToArray(HtProductPricePlanSpecial::model()->with('items')->findByPk($price_plan_id));
                }

                HtProductGroupRef::updateProductGroupOfType6($product_id);

                EchoUtility::echoMsgTF($result, '添加', $data);
            } else {
                if ($request_method == 'delete') {
                    // delete price plan
                    HtProductPricePlanItem::model()->deleteAllByAttributes(array('price_plan_id' => $price_plan_id, 'is_special' => 1));
                    HtProductPricePlanSpecial::model()->deleteAll('price_plan_id =' . $price_plan_id);

                    HtProductPricePlan::clearCache($product_id);

                    HtProductGroupRef::updateProductGroupOfType6($product_id);

                    EchoUtility::echoMsgTF(true, '删除');
                }
            }
        }
    }

    private function addProductPricePlanItem($price_plan_id, $is_special, $postData)
    {
        foreach ($postData as $data) {
            $item = new HtProductPricePlanItem();
            ModelHelper::fillItem($item, $data);
            $item['price_plan_id'] = $price_plan_id;
            $item['is_special'] = $is_special;
            $result = $item->insert();
            if (!$result) {
                return $result;
            }
        }

        return true;
    }

    private function getProductTicketRules($product_id)
    {
        $data = array();
        $ticket_rules = HtProductTicketRule::model()->findAll('product_id = ' . $product_id . ' AND ticket_id<>99');
        switch (count($ticket_rules)) {
            case 1:
                $data['ticket_type'] = 1;
                break;
            case 2:
                $data['ticket_type'] = 2;
                break;
            case 3:
                $data['ticket_type'] = 3;
                break;
            default:
                $data['ticket_type'] = 3;
                break;
        }
        $data['ticket_rules'] = Converter::convertModelToArray($ticket_rules);

        return $data;
    }

    private function getProductSpecialCode()
    {
        return Yii::app()->request->getParam('special_code');
    }

    private function getProductID()
    {
        return (int)Yii::app()->request->getParam('product_id');
    }

    private function getSpecialGroupID()
    {
        return (int)Yii::app()->request->getParam('group_id');
    }

    private function clearPricePlan($product_id, $is_special = 0)
    {
        if ($is_special == 0) {
            $price_plans = HtProductPricePlan::model()->findAll('product_id =' . $product_id);
        } else {
            $price_plans = HtProductPricePlanSpecial::model()->findAllByAttributes(array('product_id' => $product_id));
        }
        if (count($price_plans) > 0) {
            foreach ($price_plans as $plan) {
                $price_plan_id = $plan["price_plan_id"];
                HtProductPricePlanItem::model()->deleteAllByAttributes(array('price_plan_id' => $price_plan_id, 'is_special' => $is_special));

                if ($is_special == 0) {
                    HtProductPricePlan::model()->deleteByPk($price_plan_id);
                } else {
                    HtProductPricePlanSpecial::model()->deleteByPk($price_plan_id);
                }
            }
        }

        HtProductPricePlan::clearCache($product_id);
    }

    private function removeSpecialCodePricePlan($data, $special_code, $is_special = 0)
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

    private function removeSpecialCodePricePlanItem($price_plan_id, $special_code, $is_special = 0)
    {
        $c = new CDbCriteria();
        $c->addCondition('price_plan_id = ' . $price_plan_id);
        $c->addCondition("special_code = '$special_code'");
        $c->addCondition("is_special = " . $is_special);
        $result = HtProductPricePlanItem::model()->deleteAll($c);

        return $result;
    }

    /**
     * @param $price_plan_items
     * @param $price_plan_id
     * @return bool
     */
    private function updateProductPricePlanItems($price_plan_items, $price_plan_id, $is_special = 0)
    {
        $result = true;
        $item_ids = ModelHelper::getList($price_plan_items, 'item_id');
        $c = new CDbCriteria();
        $c->addCondition('price_plan_id=' . $price_plan_id);
        $c->addNotInCondition('item_id', $item_ids);
        $c->addCondition('is_special = ' . $is_special);
        HtProductPricePlanItem::model()->deleteAll($c);

        foreach ($price_plan_items as $price_plan_item) {
            if (!empty($price_plan_item['item_id'])) {
                $item = HtProductPricePlanItem::model()->findByPk($price_plan_item['item_id']);

                $tmp_ret = ModelHelper::updateItem($item, $price_plan_item,
                                                   array('ticket_id', 'special_code', 'quantity', 'cost_price', 'orig_price', 'price', 'frequency'));
                $ret = $tmp_ret == 1;
            } else {
                $item = new HtProductPricePlanItem();
                ModelHelper::fillItem($item, $price_plan_item, array('ticket_id',
                    'special_code', 'is_special', 'quantity', 'cost_price', 'orig_price', 'price', 'frequency'));
                $item['price_plan_id'] = $price_plan_id;

                $ret = $item->insert();
            }
            if (!$ret) {
                $result = false;

                break;
            }
        }

        return $result;
    }

    /**
     * @param $product_id
     * @param $data
     * @return $data
     */
    private function filterPricePlan($product_id, $data)
    {
        global $special_code_list;

        $special_codes = HtProductSpecialCombo::model()->findAllByAttributes(['product_id' => $product_id, 'status' => 1]);
        $special_code_list = ModelHelper::getList($special_codes, 'special_id');

        if (isset($data['items'])) {
            $data['items'] = $this->filterItems($data);
        } else {
            foreach ($data as &$price_plan) {
                $price_plan['items'] = $this->filterItems($price_plan);
            }
        }

        return $data;
    }

    private function filterItems($price_plan)
    {
        global $special_code_list;

        $filtered_items = array();
        foreach ($price_plan['items'] as $value) {
            if (empty($value['special_code']) || in_array($value['special_code'], $special_code_list)) {
                array_push($filtered_items, $value);
            }
        }

        return $filtered_items;
    }

    private function updatePricePlanSpecialCodes($product_id,$is_special = 0)
    {
        $result = true;
        if($is_special == 1){
            $data = HtProductPricePlanSpecial::model()->with('items')->findAll('product_id = '.$product_id);
        }else{
            $data = HtProductPricePlan::model()->with('items')->findAll('product_id = '.$product_id);
        }

        $data = Converter::convertModelToArray($data);

        foreach($data as $price_plan){
            if($price_plan['items']){
                $special_codes = array();
                foreach($price_plan['items'] as $item){
                    if(!empty($item['frequency'])){
                        array_push($special_codes,$item['special_code']);
                    }
                }
                $special_codes = array_unique($special_codes);
                $special_codes = implode(';',$special_codes);
                if($is_special == 1){
                    $plan = HtProductPricePlanSpecial::model()->findByPk($price_plan['price_plan_id']);
                }else{
                    $plan = HtProductPricePlan::model()->findByPk($price_plan['price_plan_id']);
                }

                $plan['special_codes'] = $special_codes;
                $result = $plan->update();
            }
        }
        return $result;
    }

}
