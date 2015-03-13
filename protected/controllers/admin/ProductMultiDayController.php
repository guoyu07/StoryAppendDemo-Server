<?php

/**
 * Created by hitour.server.
 * User: yangzehua
 * Date: 10/15/14
 * Time: 2:24 PM
 */
class ProductMultiDayController extends AdminController
{
    public function actionMultiDayIntroduce()
    {
        $product_id = $this->getProductId();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') {
            $recommendation = HtProductTripIntroduction::model()->getTripIntroductionByProductId($product_id);
            echo CJSON::encode(array('code' => 200, 'msg' => '多日行程简介', 'data' => $recommendation));
        } else {
            if ($request_method == 'post') {
                $introduce = HtProductTripIntroduction::model()->findByPk($product_id);
                $data = $this->getPostJsonData();
                if (empty($introduce)) {
                    $new_introduce = new HtProductTripIntroduction();
                    $new_introduce['product_id'] = $product_id;
                    ModelHelper::fillItem($new_introduce, $data, ['brief_author', 'brief_avatar', 'brief_title',
                        'brief_description', 'brief_image', 'brief_image_mobile', 'trip_intro_image', 'status']);

                    $result = $new_introduce->insert();
                    EchoUtility::echoMsgTF($result, '添加');
                } else {
                    $result = ModelHelper::updateItem($introduce, $data,
                                                      ['brief_author', 'brief_avatar', 'brief_title', 'brief_description', 'status']);
                    EchoUtility::echoMsgTF($result, '更新', Converter::convertModelToArray($introduce));
                }
            }
        }
    }

    public function actionMultiDayHighLight()
    {
        $product_id = $this->getProductId();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') {
            $trip_highlight = HtTripHighlight::model()->getProductTripHighlights($product_id);
            echo CJSON::encode(array('code' => 200, 'msg' => '多日行程亮点', 'data' => $trip_highlight));
        } else {
            if ($request_method == 'post') {
                $data = $this->getPostJsonData();
                if (empty($data['id'])) {
                    $new_highlight = new HtTripHighlight();
                    $new_highlight['product_id'] = $product_id;
                    //途径城市
                    $tour_cities = '';
                    if(is_array($data['tour_cities']) && count($data['tour_cities']) > 0){
                        $cities = array();
                        foreach($data['tour_cities'] as $city)
                        {
                            array_push($cities,$city['city_code']);
                        }
                        $tour_cities = implode(';',$cities);
                    }
                    $data['tour_cities'] = $tour_cities;

                    ModelHelper::fillItem($new_highlight, $data, ['total_days', 'distance', 'highlight_summary',
                        'start_location', 'finish_location', 'tour_cities', 'suitable_time']);

                    $result = $new_highlight->insert();
                    if ($result) {
                        $result = ($result && $this->updateHighlightRefs($data['highlight_refs'],
                                                                         $new_highlight->getPrimaryKey()));
                        if ($result) {
                            $response_data = HtTripHighlight::model()->getProductTripHighlights($product_id);
                            EchoUtility::echoMsgTF($result, '添加', $response_data);
                        } else {
                            EchoUtility::echoMsgTF(false, '添加', []);
                        }
                    } else {
                        EchoUtility::echoMsgTF(false, '添加', []);
                    }
                } else {
                    $highlight = HtTripHighlight::model()->findByPk($data['id']);
                    //途径城市
                    $tour_cities = '';
                    if(is_array($data['tour_cities']) && count($data['tour_cities']) > 0){
                        $cities = array();
                        foreach($data['tour_cities'] as $city)
                        {
                            array_push($cities,$city['city_code']);
                        }
                        $tour_cities = implode(';',$cities);
                    }
                    $data['tour_cities'] = $tour_cities;

                    $result = ModelHelper::updateItem($highlight, $data,
                                                      ['total_days', 'distance', 'highlight_summary', 'start_location', 'finish_location', 'tour_cities', 'suitable_time']);
                    if ($result) {
                        $result = ($result && $this->updateHighlightRefs($data['highlight_refs'], $data['id']));
                        if ($result) {
                            $response_data = HtTripHighlight::model()->getProductTripHighlights($product_id);
                            EchoUtility::echoMsgTF($result, '更新', $response_data);
                        } else {
                            EchoUtility::echoMsgTF(false, '更新', []);
                        }
                    } else {
                        EchoUtility::echoMsgTF(false, '更新', []);
                    }
                }
            }
        }
    }

    public function actionUpdateAvatar()
    {
        $product_id = $this->getProductId();
        $to_dir = 'image/upload/multi_day/avatars/' . $product_id . '/';
        $result = FileUtility::uploadFile($to_dir);

        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            EchoUtility::echoMsgTF(true, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionUpdateBriefImage()
    {
        $product_id = $this->getProductId();
        $this->updateImage($product_id, 'brief_image');
    }

    public function actionUpdateBriefImageMobile()
    {
        $product_id = $this->getProductId();
        $this->updateImage($product_id, 'brief_image_mobile');
    }

    public function actionUpdateTripIntroImage()
    {
        $product_id = $this->getProductId();
        $this->updateImage($product_id, 'trip_intro_image');
    }

    public function actionUpdateTripImageMobile()
    {
        $product_id = $this->getProductId();
        $this->updateImage($product_id, 'brief_image_mobile');
    }

    public function actionUpdateTripLineImage()
    {
        $product_id = $this->getProductId();
        $this->updateImage($product_id, 'line_image');
    }

    private function updateImage($product_id, $field)
    {
        $to_dir = 'image/upload/multi_day/' . $field . '/' . $product_id . '/';
        $result = FileUtility::uploadFile($to_dir);

        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }
            $introduce = HtProductTripIntroduction::model()->findByPk($product_id);
            if (!$introduce) {
                $new_introduce = new HtProductTripIntroduction();
                $new_introduce['product_id'] = $product_id;
                $new_introduce[$field] = $image_url;
                $result = $new_introduce->insert();
                EchoUtility::echoMsgTF($result, '添加', array($field => $image_url));
            } else {
                $data[$field] = $image_url;
                $result = ModelHelper::updateItem($introduce, $data, [$field]);
                EchoUtility::echoMsgTF($result, '更新', Converter::convertModelToArray($introduce));
            }
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    private function updateHighlightRefs($highlight_ref_arr, $highlight_id)
    {
        $result = true;

        HtTripHighlightRef::model()->deleteAllByAttributes(array('highlight_id' => $highlight_id));

        foreach ($highlight_ref_arr as $ref) {
            $new_highlight_ref = new HtTripHighlightRef();
            $new_highlight_ref['highlight_id'] = $highlight_id;
            ModelHelper::fillItem($new_highlight_ref, $ref, ['date', 'location', 'local_highlight', 'lodging']);

            $result = $new_highlight_ref->insert();

            if (!$result) {
                return false;
            }
        }

        return $result;
    }

    private function getProductId()
    {
        return (int)$this->getParam('product_id');
    }

    private function getRefId()
    {
        return (int)$this->getParam('ref_id');
    }
}