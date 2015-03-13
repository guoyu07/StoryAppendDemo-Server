<?php

class ProductTourPlanController extends AdminController
{
    //获取商品图文
    public function actionGetTourPlanDetail()
    {
        $return = $this->getTourPlanDetail();
        echo CJSON::encode(array('code' => 200, 'data' => $return));
    }

    //添加tourPlan
    public function actionAddTourPlan()
    {
        $product_id = $this->getProductID();
        $data = $this->getPostJsonData();
        $total_days = isset($data['total_days']) ? $data['total_days'] : 0;
        $this->deleteTourPlan($total_days);

        $result = '';
        // TODO check whether plan exist first and update existing plan if possible
        if ($total_days > 0) {
            for ($i = 1; $i <= $data['total_days']; $i++) {
                $c = new CDbCriteria();
                $c->addCondition('product_id=' . $product_id);
                if ($i == 1) {
                    $c->addInCondition('the_day', array(0, 1));
                } else {
                    $c->addCondition('the_day=' . $i);
                }
                $item = HtProductTourPlan::model()->find($c);
                if (!empty($item)) {
                    $item['is_online'] = $data['is_online'];
                    $item['total_days'] = $total_days;
                    $item['the_day'] = $i;
                    $item['title'] = $this->getTitle($data['plans'], $i);
                    $result = $item->update();
                } else {
                    $item = new HtProductTourPlan();
                    $item['is_online'] = $data['is_online'];
                    $item['product_id'] = $product_id;
                    $item['total_days'] = $total_days;
                    $item['the_day'] = $i;
                    $item['title'] = $this->getTitle($data['plans'], $i);

                    $result = $item->insert();
                }
            }
        } else {
            $item = HtProductTourPlan::model()->findByAttributes(array('product_id' => $product_id));
            if (!empty($item)) {
                $item['total_days'] = 0;
                $item['the_day'] = 0;
                $item['title'] = '';
                $item['is_online'] = $data['is_online'];
                $result = $item->update();
            } else {
                $item = new HtProductTourPlan();
                $item['is_online'] = $data['is_online'];
                $item['product_id'] = $this->getProductID();
                $result = $item->insert();
            }
        }
        HtProductDescription::model()->updateFieldValues($product_id, 'schedule',
                                                         array('cn_schedule' => $data['cn_schedule'], 'en_schedule' => $data['cn_schedule']));

        EchoUtility::echoMsgTF($result, '保存', $this->getTourPlanDetail());
    }

    private function getTitle($plans, $the_day)
    {
        if (is_array($plans)) {
            foreach ($plans as $plan) {
                if ($plan['the_day'] == $the_day) {
                    return $plan['title'];
                }
            }
        }
        return '';
    }

    //添加tourPlanGroup
    public function actionAddTourPlanGroup()
    {
        $data = $this->getPostJsonData();
        $group = new HtProductTourPlanGroup();
        $group['plan_id'] = $data['plan_id'];
        $result = $group->insert();
        if ($result) {
            $group['display_order'] = $group->getPrimaryKey();
            $group->update();
        }
        EchoUtility::echoMsgTF($result, '新增', array('group_id' => $group->getPrimaryKey()));
    }

    public function actionInsertTourPlanGroup()
    {
        $data = $this->getPostJsonData();
        $c = new CDbCriteria();
        $c->addCondition("plan_id = " . $data["plan_id"]);
        $c->order = "display_order ASC";
        $groups = HtProductTourPlanGroup::model()->findAll($c);

        $new_group_id = 0;
        $order_index = 1;
        $result = true;
        foreach ($groups as $group) {
            $group["display_order"] = $order_index;
            $result = $group->update();
            ++ $order_index;

            if ($data["group_id"] == $group["group_id"]) {
                $group = new HtProductTourPlanGroup();
                $group['plan_id'] = $data['plan_id'];
                $group['display_order'] = $order_index;
                $result = $group->insert();
                $new_group_id = $group->getPrimaryKey();
                ++ $order_index;
            }
        }

        if ($result) {
            $groups = Converter::convertModelToArray(HtProductTourPlanGroup::model()->findAll($c));
        }
        EchoUtility::echoMsgTF($result, '新增', array('groups' => $groups, 'new_group_id' => $new_group_id));
    }

    //更新tourPlanGroup
    public function actionUpdateTourPlanGroup()
    {
        $data = $this->getPostJsonData();
        if (empty($data['time']) && empty($data['title'])) {
            EchoUtility::echoCommonFailed('时间和标题至少填写一项！');

            return;
        }
        $group = HtProductTourPlanGroup::model()->findByPk($data['group_id']);
        $group['time'] = $data['time'];
        $group['title'] = $data['title'];
        $result = $group->update();
        EchoUtility::echoMsgTF($result, '更新');
    }

    //添加图文项
    public function actionAddTourPlanItem()
    {
        $data = $this->getPostJsonData();
        $item = new HtProductTourPlanItem();
        if (isset($data['group_id'])) {
            $item['group_id'] = $data['group_id'];
            $result = $item->insert();
        } else {
            $group = HtProductTourPlanGroup::model()->find('plan_id = ' . $data['plan_id']);
            if ($group['group_id']) {
                $group_id = $group['group_id'];
            } else {
                $group = new HtProductTourPlanGroup();
                $group['plan_id'] = $data['plan_id'];
                $group->insert();
                $group_id = $group->getPrimaryKey();
            }

            $item['group_id'] = $group_id;
            $result = $item->insert();
        }
        if ($result) {
            $item['display_order'] = $item->getPrimaryKey();
            $item->update();
        }
        EchoUtility::echoMsgTF($result, '新增', array('item_id' => $item->getPrimaryKey()));
    }

    //更新（上传）图片
    public function actionUploadImage()
    {
        $item_id = (int)$_POST['item_id'];
        $hptp = new HtProductTourPlanItem();
        $item = $hptp->findByPk($item_id);
        if (empty($item)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $item_id . '的图片。');

            return;
        }
        $to_dir = Yii::app()->params['TOUR_IMAGE_ROOT'] . $item_id . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . $to_dir . $file;
            }
            $item['image_url'] = $image_url;
            $result = $item->update();

            EchoUtility::echoMsgTF($result, '保存', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    //更新图文描述内容
    public function actionUpdateTourPlanItem()
    {
        $data = $this->getPostJsonData();
        $check_failed = true;
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $k => $v) {
                if ($k == 'title' || $k == 'description' || $k == 'image_url') {
                    $check_failed = $check_failed && empty($v);
                }
            }
        }
        if ($check_failed) {
            EchoUtility::echoCommonFailed("图文内容不能为空！");

            return;
        }
        $result = true;
        $hptp = new HtProductTourPlanItem();
        $item = $hptp->findByPk($data['item_id']);
        if (empty($item)) {
            $result = false;
        } else {
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $k => $v) {
                    if ($k != 'editing') {
                        $item[$k] = $v;
                    }
                }
                $r = $item->update();
                $result = $result && $r;
            }
        }
        EchoUtility::echoMsgTF($result, '保存');
    }

    //更新图文项顺序
    public function actionUpdateImageDescOrders()
    {
        $data = $this->getPostJsonData();
        $result = true;
        foreach ($data as $item_order) {
            $hpid = new HtProductImageDescription();
            $item = $hpid->findByPk($item_order['id']);
            if (empty($item)) {
                $result = false;
            } else {
                $item['sort_order'] = $item_order['sort_order'];
                $r = $item->update();
                $result = $result && $r;
            }

            if (!$result) {
                break;
            }
        }
        EchoUtility::echoMsgTF($result, '更新');
    }

    //删除图片
    public function actionDeleteImage()
    {
        $item_id = (int)Yii::app()->request->getParam('item_id');
        $dir = Yii::app()->params['DIR_UPLOAD_ROOT'] . Yii::app()->params['TOUR_IMAGE_ROOT'] . $item_id;
        @FileUtility::deleteDir($dir);
        $item = HtProductTourPlanItem::model()->findByPk($item_id);
        $item['image_url'] = '';
        $result = $item->update();
        EchoUtility::echoMsgTF($result, '删除');
    }

    //删除图文项
    public function actionDeleteItem()
    {
        $item_id = (int)Yii::app()->request->getParam('item_id');
        $dir = Yii::app()->params['DIR_UPLOAD_ROOT'] . Yii::app()->params['TOUR_IMAGE_ROOT'] . $item_id;
        @FileUtility::deleteDir($dir);
        $hptp = new HtProductTourPlanItem();
        $result = $hptp->deleteByPk($item_id) > 0;
        EchoUtility::echoMsgTF($result, '删除');
    }

    //删除图文分组
    public function actionDeleteGroup()
    {
        $group_id = (int)Yii::app()->request->getParam('group_id');
        HtProductTourPlanItem::model()->deleteAll('group_id = ' . $group_id);
        $result = HtProductTourPlanGroup::model()->deleteByPk($group_id);
        EchoUtility::echoMsgTF($result, '删除');
    }

    // adjust item order
    public function actionUpdateItemsOrder()
    {
        $data = $this->getPostJsonData();
        $result = true;
        foreach ($data as $order_data) {
            $item = HtProductTourPlanItem::model()->findByPk($order_data['item_id']);
            $result = ModelHelper::updateItem($item, $order_data, array('display_order', 'group_id')) == 1;
            if (!$result) {
                break;
            }
        }
        EchoUtility::echoMsgTF($result, '更新产品图文条目顺序');
    }

    private function getTourPlanDetail()
    {
        $product_id = $this->getProductID();
        $tourPlan = HtProductTourPlan::model()->with('groups.items')->findAll('product_id = ' . $product_id);
        $tourPlan = Converter::convertModelToArray($tourPlan);
        $return = array();
        $display_type = 0;
        $total_days = 0;
        $is_online = 1;
        if (is_array($tourPlan) && count($tourPlan) > 0) {
            $is_online = $tourPlan[0]['is_online'];
            if ($tourPlan[0]['total_days'] > 0) {
                $display_type = 1;
                $total_days = $tourPlan[0]['total_days'];
            };
        }
        $schedule_names = HtProductDescription::model()->getFieldValues($product_id, 'schedule');
        $return['is_online'] = $is_online;
        $return['display_type'] = $display_type;
        $return['total_days'] = $total_days;
        $return['plans'] = $tourPlan;
        $return['cn_schedule'] = $schedule_names['cn_schedule'];

        return $return;
    }

    private function deleteTourPlan($total_days)
    {
        // TODO delete plan or update plan according to total_days of exists plans

        $tourPlan = HtProductTourPlan::model()->with('groups')->findAll('product_id = ' . $this->getProductID());
        if (!empty($tourPlan)) {
            $tourPlan = Converter::convertModelToArray($tourPlan);
            $plan_total_days = $tourPlan[0]['total_days'];
            if ($plan_total_days <= $total_days) {
                return true;
            }

            foreach ($tourPlan as $plan) {
                if ($total_days == 0 && $plan['the_day'] == 0) {
                    continue;
                }
                if ($plan['the_day'] <= $total_days) {
                    continue;
                }

                if (is_array($plan['groups']) && count($plan['groups']) > 0) {
                    foreach ($plan['groups'] as $group) {
                        HtProductTourPlanItem::model()->deleteAll('group_id = ' . $group['group_id']);
                    }
                }
                HtProductTourPlanGroup::model()->deleteAll('plan_id = ' . $plan['plan_id']);
                HtProductTourPlan::model()->deleteByPk($plan['plan_id']);
            }
        }

        return true;
    }

    private function getProductID()
    {
        return (int)Yii::app()->request->getParam('product_id');
    }
}