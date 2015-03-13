<?php
FileUtility::loadClassWithoutYii('phpexcel/PHPExcel.php');

/**
 * Created by PhpStorm.
 * User: hotblue
 * Date: 5/16/14
 * Time: 4:34 PM
 */
class ProductDepartureController extends AdminController
{
    /**
     *
     */
    public function actionEdit()
    {
    }

    /**
     *获取departurePlans信息
     */
    public function actionDeparturePlans()
    {
        $product_id = $this->getProductID();

        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') {
            $data = $this->getDeparturePlans($product_id);

            echo CJSON::encode(array('code' => 200, 'msg' => '获取产品departurePlan成功！', 'data' => $data));
        } else {
            if ($request_method == 'post') {
                $data = $this->getPostJsonData();
                $this->saveDeparturePlans($product_id, $data);
            }
        }
    }

    public function actionDeparturePoint()
    {
        $product_id = $this->getProductID();
        $departure_code = $this->getDepartureCode();

        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') {

        } else {
            if ($request_method == 'post') {
                $departure_code = substr(md5($product_id . microtime()), 8, 16); //生成departure_code
                $data = array(
                    'product_id'      => $product_id,
                    'departure_point' => '地点',
                    'language_id'     => 2,
                    'departure_code'  => $departure_code,
                );
                $result = $this->addDeparture($data);

                $data['departure_point'] = 'Point';
                $data['language_id'] = 1;
                $result = $result && $this->addDeparture($data);
                EchoUtility::echoMsgTF($result, '添加', HtProductDeparture::model()->getDepartures($product_id));

            } else {
                if ($request_method == 'delete') { // 删除
                    HtProductDeparturePlan::model()->deleteAllByAttributes(array('product_id' => $product_id, 'departure_code' => $departure_code));
                    $result = HtProductDeparture::model()->deleteAllByAttributes(array('product_id' => $product_id, 'departure_code' => $departure_code));
                    EchoUtility::echoMsgTF($result, '删除', HtProductDeparture::model()->getDepartures($product_id));
                }
            }
        }
    }

    public function actionDeparturePlan()
    {
        $product_id = $this->getProductID();
        $departure_plan_id = $this->getDeparturePlanID();

        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') {

        } else {
            if ($request_method == 'post') {
                $departures = HtProductDeparture::model()->getDepartures($product_id);
                if (count($departures) == 0) {
                    EchoUtility::echoCommonFailed('请先添加地点。');

                    return;
                }
                $departure_code = '';
                foreach ($departures as $key => $value) {
                    $departure_code = $key;
                    break;
                }

                $item = new HtProductDeparturePlan();
                $item['product_id'] = $product_id;

                $data = $this->getPostJsonData();
                $vaid_region = $data['valid_region'];
                $item['valid_region'] = $vaid_region;

                if ($vaid_region == 1) {
                    $plan_info = $data['plan_info'];
                    ModelHelper::fixDateValue($plan_info, array('from_date', 'to_date'));
                    $item['from_date'] = $plan_info['from_date'];
                    $item['to_date'] = $plan_info['to_date'];
                }

                $item['departure_code'] = $departure_code;
                $item['time'] = '9:00';
                $result = $item->insert();
                EchoUtility::echoMsgTF($result, '添加', Converter::convertModelToArray($item));
            } else {
                if ($request_method == 'delete') {
                    $result = HtProductDeparturePlan::model()->deleteByPk($departure_plan_id);
                    EchoUtility::echoMsgTF($result, '删除');
                }
            }
        }
    }

    public function actionUploadDeparturePoints()
    {
        $src_filename = html_entity_decode($_FILES["file"]['name'], ENT_QUOTES, 'UTF-8');

        $file = '';
        $to_dir = '';
        $path = '';
        if (!isset($json['error'])) {
            if (is_uploaded_file($_FILES["file"]['tmp_name']) && file_exists($_FILES["file"]['tmp_name'])) {
                $file = basename($src_filename);

                $json['file'] = $file;
                $to_dir = date('Ymd_His', time()) . '/';
                $path = Yii::app()->basePath . '/../' . Yii::app()->params['DEPARTURES_FILE_ROOT'] . $to_dir;
                if (!file_exists($path)) {
                    Yii::log('excel path:' . $path);
                    mkdir($path, 0755, true);
                }
                move_uploaded_file($_FILES["file"]['tmp_name'], $path . $file);
            }
        }

        $result = $this->importDepartures($path . $file);

        echo CJSON::encode(array('code' => $result["code"], 'msg' => $result["msg"], 'data' => $result["data"]));
    }

    private function importDepartures($filePath)
    {
        $result = array();

        /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
        $reader = PHPExcel_IOFactory::createReader('Excel2007');
        if (!$reader->canRead($filePath)) {
            $reader = PHPExcel_IOFactory::createReader('Excel5');
            if (!$reader->canRead($filePath)) {
                $result = ['code' => 400, 'msg' => '无法读取Excel，请检查文件格式！'];

                return $result;
            }
        }

        $PHPExcel = $reader->load($filePath);

        $currentSheet = $PHPExcel->getSheet(0);
        $allColumn = $currentSheet->getHighestColumn();
        $allRow = $currentSheet->getHighestRow();

        $header_map = $this->headerMap();
        $header = array();
        for ($rowIdx = 1; $rowIdx <= 1; $rowIdx++) {
            for ($colIdx = 'A'; $colIdx <= $allColumn; $colIdx++) {

                $val = $currentSheet->getCellByColumnAndRow(ord($colIdx) - 65, $rowIdx)->getValue();
                if ($val instanceof PHPExcel_RichText) {
                    $val = $val->getPlainText();
                }

                $header[$colIdx] = $header_map[$val];
            }
        }

        $raw_data = array();
        for ($rowIdx = 2; $rowIdx <= $allRow; $rowIdx++) {
            $raw_item = array();
            for ($colIdx = 'A'; $colIdx <= $allColumn; $colIdx++) {
                $val = $currentSheet->getCellByColumnAndRow(ord($colIdx) - 65, $rowIdx)->getValue();
                if ($val instanceof PHPExcel_RichText) {
                    $val = $val->getPlainText();
                }

                $raw_item[$header[$colIdx]] = $val;
            }
            $raw_data[] = $raw_item;
        }

        if (count($raw_data) > 0) {
            $result = ['code' => 200, 'msg' => '导入成功', 'data' => $raw_data];
        }

        return $result;
    }

    private function headerMap()
    {
        return array(
            'Name' => 'en_name',
            'Time' => 'departure_time'
        );
    }


    /**
     *保存departur_plan信息
     */
    private function saveDeparturePlans($product_id, $data)
    {
        $result = true;

        //remove deleted records.
        $old_plans = $this->getDeparturePlans($product_id);
        foreach ($old_plans["plan_list"] as $duration_plan) {
            foreach ($duration_plan["plans"] as $plan) {
                $res = HtProductDeparturePlan::model()->deleteByPk($plan["departure_plan_id"]);
                if (!$res) {
                    $result = false;
                    break;
                }
            }
        }

        if (!$result) {
            EchoUtility::echoMsgTF($result, '保存', $data);

            return;
        }

        if ($data["has_departure"] == 0) {
            EchoUtility::echoMsgTF($result, '保存', $this->getDeparturePlans($product_id));

            return;
        }

        // Update Departure Point title.
        $result = HtProductDescription::model()->updateFieldValues($product_id, 'departure_title', $data);

        //update and insert records.
        foreach ($data['plan_list'] as $item) {
            if (is_array($item['plans']) && count($item['plans']) > 0) {
                foreach ($item['plans'] as $plan) {
                    $new_plan = new HtProductDeparturePlan();
                    $new_plan['product_id'] = $product_id;
                    $vaid_region = $plan['valid_region'];
                    $new_plan['valid_region'] = $vaid_region;

                    ModelHelper::fixDateValue($plan, array('from_date', 'to_date'));
                    $new_plan["from_date"] = $plan["from_date"];
                    $new_plan["to_date"] = $plan["to_date"];
                    $new_plan["time"] = $plan["time"];

                    $departure_points = $plan["departures"];
                    $code = $this->updateDeparturePoint($product_id, $departure_points[0],
                                                        $departure_points[1]);
                    if ($code == "0") {
                        $result = false;

                        return;
                    }
                    $new_plan['departure_code'] = $code;

                    $new_plan['time'] = $plan["time"];
                    $new_plan['additional_limit'] = $plan["additional_limit"];
                    $result = $new_plan->insert();
                }
            }
        }
        EchoUtility::echoMsgTF($result, '保存', $this->getDeparturePlans($product_id));
    }

    private function updateDeparturePoint($product_id, $departure_point_cn, $departure_point_en)
    {
        if (!empty($departure_point_en["departure_code"]) && !empty($departure_point_cn["departure_code"])) {
            //更新departure英文信息
            $pd = HtProductDeparture::model()->findByAttributes(array('product_id' => $product_id, 'departure_code' => $departure_point_en['departure_code'], 'language_id' => 1));
            $pd['departure_point'] = $departure_point_en['departure_point'];
            $result = $pd->update();

            //更新departure中文信息
            $pd = HtProductDeparture::model()->findByAttributes(array('product_id' => $product_id, 'departure_code' => $departure_point_cn['departure_code'], 'language_id' => 2));
            $pd['departure_point'] = $departure_point_cn['departure_point'];
            $result = $pd->update();

            if ($result) {
                return $pd['departure_code'];
            } else {
                return "0";
            }
        } else {
            return $this->insertDeparturePoint($product_id, $departure_point_en, $departure_point_cn);
        }
    }

    private function insertDeparturePoint($product_id, $departure_point_en, $departure_point_cn)
    {
        $departure_code = substr(md5($product_id . microtime()), 8, 16);

        $data = array(
            'product_id'      => $product_id,
            'departure_point' => $departure_point_en["departure_point"],
            'language_id'     => 1,
            'departure_code'  => $departure_code
        );
        $result = $this->addDeparture($data);

        $data['departure_point'] = $departure_point_cn["departure_point"];
        $data['language_id'] = 2;
        $result = $result && $this->addDeparture($data);
        if ($result) {
            return $departure_code;
        } else {
            return "0";
        }
    }

    private function getDeparturePlans($product_id)
    {
        $departure_titles = HtProductDescription::model()->getFieldValues($product_id, 'departure_title');

        $plans = HtProductDeparturePlan::model()->with(['departures' => ['order' => 'pdep.language_id DESC']])->findAllByAttributes(array('product_id' => $product_id));

        $plans = Converter::convertModelToArray($plans);

        $has_departure = 0;
        if (count($plans) > 0) {
            $has_departure = 1;
        }

        $valid_region = 0;
        $duration_group = array();
        foreach ($plans as $plan) {
            $valid_region = $plan['valid_region'];
            if (!empty($plan["additional_limit"])) {
                $plan["additional_limit"] = explode(";", $plan["additional_limit"]);
            } else {
                $plan["additional_limit"] = [];
            }

            if (strlen($plan["time"]) > 0) {
                $plan["time"] = substr($plan["time"], 0, strlen($plan["time"]) - 3);
            }

            $is_new_duration = true;
            foreach ($duration_group as &$duration) {
                if($duration['from_date'] == $plan['from_date'] && $duration['to_date'] == $duration['to_date']) {
                    array_push($duration['plans'], $plan);
                    $is_new_duration = false;
                    break;
                }
            }

            if ($is_new_duration) {
                $new_duration['from_date'] = $plan['from_date'];
                $new_duration['to_date'] = $plan['to_date'];
                $new_duration['plans'] = [];
                if (count($duration_group) == 0) {
                    array_push($new_duration['plans'], $plan);
                }
                array_push($duration_group, $new_duration);
            }
        }



        return array('has_departure' => $has_departure, 'cn_departure_title' => $departure_titles["cn_departure_title"], 'en_departure_title' => $departure_titles["en_departure_title"], 'valid_region' => $valid_region, 'plan_list' => $duration_group);
    }

    private function addDeparture($data)
    {
        $pd = new HtProductDeparture();
        ModelHelper::fillItem($pd, $data);

        return $pd->insert();
    }

    private function getProductID()
    {
        return (int)Yii::app()->request->getParam('product_id');
    }

    private function getDepartureCode()
    {
        return Yii::app()->request->getParam('departure_code');
    }

    private function getDeparturePlanID()
    {
        return (int)Yii::app()->request->getParam('departure_plan_id');
    }
}