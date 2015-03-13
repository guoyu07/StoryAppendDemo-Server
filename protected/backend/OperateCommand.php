<?php
/**
 * @project hitour.server
 * @file OperateCommand.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 14-7-18 下午2:32
 **/

class OperateCommand extends CConsoleCommand
{
    private $headerMap = array(
        array(
            '账户' => 'account',
            '城市id' => 'city_code',
            '产品id' => 'product_id',
            '推广计划名称' => 'spread_plan',
            '推广单元名称' => 'spread_unit',
            '关键词名称' => 'keywords',
            '匹配模式' => 'match_mode',
            '出价' => 'price',
            '访问URL' => 'pc_url',
            '移动访问URL' => 'mobile_url',
            '启用/暂停' => 'start_using',
        ),
        array(
            '账户' => 'account',
            '产品id-1' => 'product_id_1',
            '产品id-2' => 'product_id_2',
            '产品id-3' => 'product_id_3',
            '产品id-4' => 'product_id_4',
            '产品id-5' => 'product_id_5',
            '推广计划名称' => 'spread_plan',
            '推广单元名称' => 'spread_unit',
            '子链一名称' => 'keywords_1',
            '子链一URL' => 'pc_url_1',
            '子链二名称' => 'keywords_2',
            '子链二URL' => 'pc_url_2',
            '子链三名称' => 'keywords_3',
            '子链三URL' => 'pc_url_3',
            '子链四名称' => 'keywords_4',
            '子链四URL' => 'pc_url_4',
            '子链五名称' => 'keywords_5',
            '子链五URL' => 'pc_url_5',
            '启用/暂停' => 'start_using',
            '投放设备' => 'put_device',
            '蹊径子链状态' => 'xijin_status',
        ),
        array(
            '账户' => 'account',
            '关键词id-1' => 'product_id_1',
            '关键词id-2' => 'product_id_2',
            '关键词id-3' => 'product_id_3',
            '关键词id-4' => 'product_id_4',
            '推广计划名称' => 'spread_plan',
            '推广单元名称' => 'spread_unit',
            '关键词1' => 'keywords_1',
            '跳转URL1' => 'pc_url_1',
            '关键词2' => 'keywords_2',
            '跳转URL2' => 'pc_url_2',
            '关键词3' => 'keywords_3',
            '跳转URL3' => 'pc_url_3',
            '关键词4' => 'keywords_4',
            '跳转URL4' => 'pc_url_4',
        ),
    );
    private $work_type = 0;

    public function usageError($message)
    {
        echo("Error: $message\n\n".$this->getHelp()."\n");
        exit(1);
    }

    public function runError($message)
    {
        echo("Error: $message\n\n");
        exit(1);
    }

    public function init()
    {
        FileUtility::loadClassWithoutYii('phpexcel/PHPExcel.php');
        return true;
    }

    public function actionIndex()
    {
        echo 'Usage: '."\n\n";
        echo '    php entry.php operate baidukw [--file]' . "\n";
        echo '    php entry.php operate baidukw [--file]' . "\n";
        echo "\n";
    }

    public function actionBaiduKw($source, $target, $type)
    {
        if (!file_exists($source)) {
            echo 'File['.$source.'] not exist, please check.'."\n";
            return false;
        }

        if ($type > count($this->headerMap)) {
            echo 'Type['.$type.'] not supported.'."\n";
            return false;
        }
        $this->work_type = $type;

        $data = $this->loadData($source);
        if (!empty($data)) {
            $this->writeData($target, $data);
        }
    }

    private function loadData($file)
    {
        /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
        $reader = PHPExcel_IOFactory::createReader('Excel2007');
        if (!$reader->canRead($file)) {
            $reader = PHPExcel_IOFactory::createReader('Excel5');
            if (!$reader->canRead($file)) {
                $result = array('code' => 400, 'msg' => '无法读取Excel，请检查文件格式！');
                return $result;
            }
        }

        $PHPExcel = $reader->load($file);

        $currentSheet = $PHPExcel->getSheet(0);
        $allColumn = $currentSheet->getHighestColumn();
        $allRow = $currentSheet->getHighestRow();

        $header_map = $this->headerMap[$this->work_type];
        $header = array();
        for ($rowIdx = 1; $rowIdx <= 1; $rowIdx++) {
            for ($colIdx = 'A'; $colIdx <= $allColumn; $colIdx++) {

                $val = $currentSheet->getCellByColumnAndRow(ord($colIdx) - 65, $rowIdx)->getValue();
                if($val instanceof PHPExcel_RichText){
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
                if($val instanceof PHPExcel_RichText){
                    $val = $val->getPlainText();
                }

                $raw_item[$header[$colIdx]] = $val;
            }
            $raw_data[] = $raw_item;
        }

        return $raw_data;
    }

    private function writeData($file, $data)
    {
        $objExcel = new PHPExcel();
        $objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
        $objExcel->createSheet();
        $objActSheet = $objExcel->getSheet(0);
        $objActSheet->setTitle(date('Ymd'));

        $i = 0;
        foreach ($this->headerMap[$this->work_type] as $key => $title) {
            $cell_pos = chr(ord('A') + $i++) . '1';
            $objActSheet->setCellValue($cell_pos, $key);
        }

        switch($this->work_type) {
            case 0:
                $objActSheet->getColumnDimension("A")->setWidth(5);
                $objActSheet->getColumnDimension("B")->setWidth(8);
                $objActSheet->getColumnDimension("C")->setWidth(8);
                $objActSheet->getColumnDimension("D")->setWidth(20);
                $objActSheet->getColumnDimension("E")->setWidth(30);
                $objActSheet->getColumnDimension("F")->setWidth(30);
                $objActSheet->getColumnDimension("G")->setWidth(15);
                $objActSheet->getColumnDimension("H")->setWidth(8);
                $objActSheet->getColumnDimension("I")->setWidth(30);
                $objActSheet->getColumnDimension("J")->setWidth(30);
                $objActSheet->getColumnDimension("K")->setWidth(8);

                $row_idx = 2;
                foreach ($data as $row) {
                    $id = empty($row['city_code']) ? $row['product_id'] : $row['city_code'];
                    $objActSheet->setCellValue('A' . $row_idx, $row['account']);
                    $objActSheet->setCellValue('B' . $row_idx, $row['city_code']);
                    $objActSheet->setCellValue('C' . $row_idx, $row['product_id']);
                    $objActSheet->setCellValue('D' . $row_idx, $row['spread_plan']);
                    $objActSheet->setCellValue('E' . $row_idx, $row['spread_unit']);
                    $objActSheet->setCellValue('F' . $row_idx, $row['keywords']);
                    $objActSheet->setCellValue('G' . $row_idx, $row['match_mode']);
                    $objActSheet->setCellValue('H' . $row_idx, $row['price']);
                    $objActSheet->setCellValue('I' . $row_idx, $this->createUrl($id, $row['account'], $row['spread_plan'], $row['spread_unit'], $row['keywords'], 1));
                    $objActSheet->setCellValue('J' . $row_idx, $this->createUrl($id, $row['account'], $row['spread_plan'], $row['spread_unit'], $row['keywords'], 2));
                    $objActSheet->setCellValue('K' . $row_idx, $row['start_using']);
                    $row_idx++;
                }
                break;
            case 1:
                for($i = 'A'; $i <='U'; $i++) {
                    $objActSheet->getColumnDimension($i)->setWidth(10);
                }
                $row_idx = 2;
                foreach ($data as $row) {
                    $objActSheet->setCellValue('A' . $row_idx, $row['account']);
                    $objActSheet->setCellValue('B' . $row_idx, $row['product_id_1']);
                    $objActSheet->setCellValue('C' . $row_idx, $row['product_id_2']);
                    $objActSheet->setCellValue('D' . $row_idx, $row['product_id_3']);
                    $objActSheet->setCellValue('E' . $row_idx, $row['product_id_4']);
                    $objActSheet->setCellValue('F' . $row_idx, $row['product_id_5']);
                    $objActSheet->setCellValue('G' . $row_idx, $row['spread_plan']);
                    $objActSheet->setCellValue('H' . $row_idx, $row['spread_unit']);
                    $objActSheet->setCellValue('I' . $row_idx, $row['keywords_1']);
                    $objActSheet->setCellValue('J' . $row_idx, $this->createUrl($row['product_id_1'], $row['account'], $row['spread_plan'], $row['spread_unit'], $row['keywords_1'], 1));
                    $objActSheet->setCellValue('K' . $row_idx, $row['keywords_2']);
                    $objActSheet->setCellValue('L' . $row_idx, $this->createUrl($row['product_id_2'], $row['account'], $row['spread_plan'], $row['spread_unit'], $row['keywords_2'], 1));
                    $objActSheet->setCellValue('M' . $row_idx, $row['keywords_3']);
                    $objActSheet->setCellValue('N' . $row_idx, $this->createUrl($row['product_id_3'], $row['account'], $row['spread_plan'], $row['spread_unit'], $row['keywords_3'], 1));
                    $objActSheet->setCellValue('O' . $row_idx, $row['keywords_4']);
                    $objActSheet->setCellValue('P' . $row_idx, $this->createUrl($row['product_id_4'], $row['account'], $row['spread_plan'], $row['spread_unit'], $row['keywords_4'], 1));
                    $objActSheet->setCellValue('Q' . $row_idx, $row['keywords_5']);
                    $objActSheet->setCellValue('R' . $row_idx, $this->createUrl($row['product_id_5'], $row['account'], $row['spread_plan'], $row['spread_unit'], $row['keywords_5'], 1));
                    $objActSheet->setCellValue('S' . $row_idx, $row['start_using']);
                    $objActSheet->setCellValue('T' . $row_idx, $row['put_device']);
                    $objActSheet->setCellValue('U' . $row_idx, $row['xijin_status']);
                    $row_idx++;
                }
                break;
            case 2:
                for($i = 'A'; $i <= 'O'; $i++) {
                    $objActSheet->getColumnDimension($i)->setWidth(10);
                }
                $row_idx = 2;
                foreach ($data as $row) {
                    $objActSheet->setCellValue('A' . $row_idx, $row['account']);
                    $objActSheet->setCellValue('B' . $row_idx, $row['product_id_1']);
                    $objActSheet->setCellValue('C' . $row_idx, $row['product_id_2']);
                    $objActSheet->setCellValue('D' . $row_idx, $row['product_id_3']);
                    $objActSheet->setCellValue('E' . $row_idx, $row['product_id_4']);
                    $objActSheet->setCellValue('F' . $row_idx, $row['spread_plan']);
                    $objActSheet->setCellValue('G' . $row_idx, $row['spread_unit']);
                    $objActSheet->setCellValue('H' . $row_idx, $row['keywords_1']);
                    $objActSheet->setCellValue('I' . $row_idx, $this->createUrl($row['product_id_1'], $row['account'], $row['spread_plan'], $row['spread_unit'], $row['keywords_1'], 1));
                    $objActSheet->setCellValue('J' . $row_idx, $row['keywords_2']);
                    $objActSheet->setCellValue('K' . $row_idx, $this->createUrl($row['product_id_2'], $row['account'], $row['spread_plan'], $row['spread_unit'], $row['keywords_2'], 1));
                    $objActSheet->setCellValue('L' . $row_idx, $row['keywords_3']);
                    $objActSheet->setCellValue('M' . $row_idx, $this->createUrl($row['product_id_3'], $row['account'], $row['spread_plan'], $row['spread_unit'], $row['keywords_3'], 1));
                    $objActSheet->setCellValue('N' . $row_idx, $row['keywords_4']);
                    $objActSheet->setCellValue('O' . $row_idx, $this->createUrl($row['product_id_4'], $row['account'], $row['spread_plan'], $row['spread_unit'], $row['keywords_4'], 1));
                    $row_idx++;
                }
                break;
            default:
                break;
        }

        $objWriter->save($file);
    }

    private function createUrl($id, $account, $spread_plan, $spread_unit, $keywords, $type)
    {
        $base_url = 'http://www.hitour.cc';
        if (!is_numeric($id)) {
            $city = $this->getCityInfo($id);
            if (!empty($city)) {
                if ($type == 1) {
                    $base_url .= '/'.(str_replace(' ','_',$city['country_en'])).'/'.(str_replace(' ','_',$city['city_en']));
                }else if ($type == 2) {
                    $base_url .= '/mobile#/city/'.$city['code'];
                }
            }
        }else if ($this->productExist($id)) {
            if ($type == 1) {
                $base_url .= '/sightseeing/'.$id;
            }else if ($type == 2) {
                $base_url .= '/mobile#/product/'.$id;
            }
        }
        if ($base_url == 'http://www.hitour.cc' && $id != '首页' && $id != '专题活动') {
            return '';
        }
        if ($id == '专题活动') {
            $base_url = 'http://www.hitour.cc/activity/disney';
        }
        $param['utm_source'] = 'baidu';
        $param['utm_medium'] = 'sem';
        $param['utm_keyword'] = md5('hitour.'.$account.'.account') . '+';
        $param['utm_keyword'] .= md5('hitour.'.$spread_plan.'.spread_plan') . '+';
        $param['utm_keyword'] .= md5('hitour.'.$spread_unit.'.spread_unit') . '+';
        $param['utm_keyword'] .= md5('hitour.'.$keywords.'.keywords');

        $url = $base_url . '?' . http_build_query($param);
        return $url;
    }

    private function getCityInfo($city_code)
    {
        $city = array();
        $sql = 'SELECT ci.city_code,ci.cn_name city_cn,ci.en_name city_en,';
        $sql .= 'co.cn_name country_cn,co.en_name country_en,ct.cn_name continent_cn ';
        $sql .= 'FROM ht_city ci ';
        $sql .= 'LEFT JOIN ht_country co on ci.country_code=co.country_code ';
        $sql .= 'LEFT JOIN ht_continent ct on co.continent_id=ct.continent_id ';
        $sql .= 'WHERE ci.city_code="'.$city_code.'"';
        $result = Yii::app()->db->createCommand($sql)->queryRow();
        if (!empty($result) && count($result) > 0) {
            $city['code'] = $result['city_code'];
            $city['city_cn'] = $result['city_cn'];
            $city['city_en'] = $result['city_en'];
            $city['country_cn'] = $result['country_cn'];
            $city['country_en'] = $result['country_en'];
            $city['continent_cn'] = $result['continent_cn'];
        }
        return $city;
    }

    private function productExist($product_id)
    {
        $product = HtProduct::model()->findByPk($product_id);
        if (!empty($product)) {
            return true;
        }else{
            return false;
        }
    }

}