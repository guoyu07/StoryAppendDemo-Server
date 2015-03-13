<?php
FileUtility::loadClassWithoutYii('phpexcel/PHPExcel.php');

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 6/9/14
 * Time: 11:52 AM
 */
class Insurance
{

    public function importCodes($filePath)
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

        $save_result = $this->saveInsuranceCodes($raw_data);

        if ($save_result) {
            $result['code'] = 200;
            $result['msg'] = '导入保险码成功，共导入' . count($raw_data) . '个保险码!';
            $result['data'] = count($raw_data);
        } else {
            $result['code'] = 400;
            $result['msg'] = '导入保险码失败!';
            $result['data'] = "0";
        }

        return $result;
    }

    private function headerMap()
    {
        return array(
            '商户代码' => 'partner_code',
            '产品代码' => 'product_code',
            '兑换码' => 'redeem_code',
            '兑换状态(0或1)' => 'redeem_status',
            '兑换起始时间' => 'redeem_start_date',
            '兑换截止时间' => 'redeem_expire_date',
        );
    }

    private function saveInsuranceCodes($raw_data)
    {
        $transaction = HtProductStockPdf::model()->dbConnection->beginTransaction();
        try {
            foreach ($raw_data as $item) {
                $m = new HtInsuranceCode();
                ModelHelper::fillItem($m, $item);
                $m['redeem_status'] = 0;
                $m['order_id'] = 0;
                $m['refunded'] = 0;
                $m['company_id'] = 1;
                $m->save();
            }
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollback();
            Yii::log('saveInsuranceCodes Failed.', CLogger::LEVEL_ERROR, 'hitour.biz.insurance');
            return false;
        }
    }
}