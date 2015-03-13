<?php

/**
 * Created by hitour.server.
 * User: xingminglister
 * Date: 10/17/14
 * Time: 3:27 PM
 */
class TripPlanController extends AdminController
{
    /*
     * for hotels and products in product, use product/getBundleList, please.
     *
     */

    public function actionGetTripPlan()
    {
        $product_id = $this->getProductId();

        $plan = HtTripPlan::model()->with(['points', 'traffic'])->findAllByAttributes(array('product_id' => $product_id));

        $online = 0;
        if (!empty($plan)) {
            $online = $plan[0]['online'];
        }

        EchoUtility::echoCommonMsg(200, '', ['is_online' => $online, 'data' => Converter::convertModelToArray($plan)]);
    }

    public function actionChangeToOnline()
    {
        $product_id = $this->getProductId();
        $online = $this->getParam('online', 0);
        HtTripPlan::model()->updateAll(['online' => $online], 'product_id = ' . $product_id);


        EchoUtility::echoCommonMsg(200, '更新完毕。');
    }

    public function actionPlanInfo()
    {
        $plan_id = $this->getPlanId();

        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') {
            $this->getPlanInfo($plan_id);
        } else if ($request_method == 'post') {
            $this->saveOrUpdatePlanInfo($plan_id);
        } else if ($request_method == 'delete') {
            $this->deletePlanInfo($plan_id);
        }
    }

    public function actionChangePlanOrder()
    {
        //  change plan order
        $data = $this->getPostJsonData();
        foreach ($data as $item) {
            $plan_id = $item['plan_id'];
            $plan = HtTripPlan::model()->findByPk($plan_id);
            $plan['day'] = $item['day'];
            $plan['display_order'] = $item['display_order'];

            $plan->update();
        }

        EchoUtility::echoCommonMsg(200, '顺序调整完毕。');

    }

    public function actionSavePlanPoints()
    {
        // save points to plan
        $plan_id = $this->getPlanId();
        $data = $this->getPostJsonData();

        $result = true;
        foreach ($data['points'] as $point) {
            if (!empty($point['point_id'])) {
                $point_id = $point['point_id'];
                $item = HtTripPlanPoint::model()->findByPk($point_id);
                $result = ModelHelper::updateItem($item, $point, ['the_id', 'the_alias', 'latlng']);
                if (false == $result) {
                    break;
                }
            } else {
                $new_point = new HtTripPlanPoint();
                $new_point['plan_id'] = $plan_id;
                ModelHelper::fillItem($new_point, $point,
                                      ['plan_id', 'type', 'the_id', 'the_alias', 'display_order', 'latlng']);
                $result = $new_point->insert();
            }
        }

        EchoUtility::echoMsgTF($result);
    }

    public function actionSavePlanTraffic()
    {
        // save plan traffic info
        $plan_id = $this->getPlanId();
        $data = $this->getPostJsonData();

        $result = true;
        foreach ($data as $traffic) {
            $from_point = $traffic['from_point'];
            $to_point = $traffic['to_point'];

            if($traffic['trans_type'] == HtTripPlanTraffic::TRANS_TYPE_NOT_KNOWN) {
                HtTripPlanTraffic::model()->deleteByPk(['plan_id' => $plan_id, 'from_point' => $from_point, 'to_point' => $to_point]);
                continue;
            }
            
            $item = HtTripPlanTraffic::model()->findByPk(['plan_id' => $plan_id, 'from_point' => $from_point, 'to_point' => $to_point]);
            if (empty($item)) {
                $item = new HtTripPlanTraffic();
                $item['plan_id'] = $plan_id;
                ModelHelper::fillItem($item, $traffic, ['from_point', 'to_point', 'trans_type', 'description']);
                $result = $item->insert();
            } else {
                $result = ModelHelper::updateItem($item, $traffic, ['trans_type', 'description']);
            }

            if (false == $result) {
                break;
            }
        }

        EchoUtility::echoMsgTF($result);
    }

    public function actionSavePlanPointOfLand()
    {
        // TODO save plan point of type land

    }

    public function actionChangePlanPointOrder()
    {
        $plan_id = $this->getPlanId();
        $data = $this->getPostJsonData();

        foreach ($data as $point) {
            $item = HtTripPlanPoint::model()->findByPk($point['point_id']);
            $item['display_order'] = $point['display_order'];
            $item->update();
        }

        // update traffic info
        $items = HtTripPlanPoint::model()->findAllByAttributes(['plan_id' => $plan_id]);
        $from_to_group = [];
        $point_id_list = ModelHelper::getList($items, 'point_id');
        for ($index = 0; $index < count($point_id_list) - 1; $index++) {
            $s = "(from_point = " . $point_id_list[$index] . " AND to_point = " . $point_id_list[$index + 1] . ")";
            array_push($from_to_group, $s);
        }

        HtTripPlanTraffic::model()->deleteAll('NOT (' . implode(' OR ', $from_to_group) . ')');

        EchoUtility::echoCommonMsg(200, '更新成功！');
    }

    public function actionAddPlanPoint()
    {
        // add plan point
        $plan_id = $this->getPlanId();
        $data = $this->getPostJsonData();

        $point = new HtTripPlanPoint();
        $point['plan_id'] = $plan_id;
        ModelHelper::fillItem($point, $data,
                              ['plan_id', 'type', 'the_id', 'the_alias', 'display_order', 'description', 'latlng']);
        $result = $point->insert();
        if ($result) {
            EchoUtility::echoCommonMsg(200, '添加成功！', $point);
        } else {
            EchoUtility::echoCommonMsg(200, '添加失败。');
        }
    }

    public function actionUpdatePlanPoint()
    {
        $point_id = (int)$this->getParam('point_id');

        $data = $this->getPostJsonData();

        $point = HtTripPlanPoint::model()->findByPk($point_id);
        ModelHelper::fillItem($point, $data,
                              ['type', 'the_id', 'the_alias', 'display_order', 'description', 'latlng']);
        $result = $point->update();

        EchoUtility::echoMsgTF($result, '更新', $point);
    }

    public function actionDeletePlanPoint()
    {
        //  delete plan point
        $point_id = (int)$this->getParam('point_id');

        HtTripPlanPoint::model()->deleteByPk($point_id);
        HtTripPlanPointImage::model()->deleteAllByAttributes(['point_id' => $point_id]);
        HtTripPlanTraffic::model()->deleteAllByAttributes(['from_point' => $point_id]);
        HtTripPlanTraffic::model()->deleteAllByAttributes(['to_point' => $point_id]);

        EchoUtility::echoCommonMsg(200, '删除完毕。');
    }

    public function actionPlanPointImages()
    {
        $point_id = (int)$this->getParam('point_id');

        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') {
            $images = HtTripPlanPointImage::model()->findAllByAttributes(['point_id' => $point_id]);

            EchoUtility::echoCommonMsg(200, '', $images);
        } else if ($request_method == 'post') {
            $data = $this->getPostJsonData();

            $result = true;
            foreach ($data as $point_image) {
                $item = HtTripPlanPointImage::model()->findByPk($point_image['image_id']);
                $result = ModelHelper::updateItem($item, $point_image, ['title', 'description', 'display_order']);
            }
            EchoUtility::echoByResult($result);
        }
    }

    public function actionPlanPointImage()
    {
        $point_id = (int)$this->getParam('point_id', 0);
        $image_id = (int)$this->getParam('image_id', 0);
        $display_order = (int)$this->getParam('display_order', 1);

        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') {
        } else if ($request_method == 'post') {
            $to_dir = 'image/upload/trip_point/' . $point_id . '/';
            $result = FileUtility::uploadFile($to_dir);
            if ($result['code'] == 200) {
                $file = $result['file'];
                $image_url = FileUtility::uploadToQiniu($to_dir . $file);
                if ($image_url == '') {
                    EchoUtility::echoCommonFailed('上传图片文件到七牛失败。');

                    return;
                }
            } else {
                EchoUtility::echoCommonFailed('上传图片文件失败。');

                return;
            }

            if ($image_id > 0) {
                $point_image = HtTripPlanPointImage::model()->findByPk($image_id);
                $point_image['image_url'] = $image_url;
                $result = $point_image->update();
            } else {
                $point_image = new HtTripPlanPointImage();
                $point_image['point_id'] = $point_id;
                $point_image['image_url'] = $image_url;
                $point_image['display_order'] = $display_order;
                $result = $point_image->insert();
            }

            EchoUtility::echoMsgTF($result, '图片保存',Converter::convertModelToArray($point_image));
        } else if ($request_method == 'delete') {
            HtTripPlanPointImage::model()->deleteByPk($image_id);
            EchoUtility::echoCommonMsg(200, '删除成功。');
        }
    }

    private function getPlanInfo($plan_id)
    {
        // get plan info

        $plan = HtTripPlan::model()->with(['points', 'traffic'])->findByPk($plan_id);

        EchoUtility::echoCommonMsg(200, '', Converter::convertModelToArray($plan));
    }

    private function saveOrUpdatePlanInfo($plan_id)
    {
        //  save or update plan info
        $product_id = $this->getProductId();
        $data = $this->getPostJsonData();
        if ($plan_id > 0) {
            $plan = HtTripPlan::model()->findByPk($plan_id);
            $result = ModelHelper::updateItem($plan, $data, ['day', 'title', 'description', 'online', 'display_order']);
            EchoUtility::echoMsg($result, '更新行程', $plan);
        } else {
            $plan = new HtTripPlan();
            $plan['product_id'] = $product_id;
            ModelHelper::fillItem($plan, $data, ['day', 'title', 'description', 'online', 'display_order']);
            $result = $plan->insert();

            EchoUtility::echoMsgTF($result, '添加行程', $plan);
        }
    }

    private function deletePlanInfo($plan_id)
    {
        //  delete plan info
        HtTripPlan::model()->deleteByPk($plan_id);

        $points = HtTripPlanPoint::model()->findAllByAttributes(['plan_id' => $plan_id]);
        if (!empty($points)) {
            $point_ids = ModelHelper::getList($points, 'point_id');
            HtTripPlanPointImage::model()->deleteAll('point_id in (' . implode(",", $point_ids) . ')');
        }

        HtTripPlanPoint::model()->deleteAllByAttributes(['plan_id' => $plan_id]);
        HtTripPlanTraffic::model()->deleteAllByAttributes(['plan_id' => $plan_id]);

        EchoUtility::echoCommonMsg(200, '删除完毕。');
    }

    private function getProductId()
    {
        return (int)$this->getParam('product_id');
    }

    private function getPlanId()
    {
        return (int)$this->getParam('plan_id');
    }

} 