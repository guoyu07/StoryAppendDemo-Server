<?php

/**
 * Created by PhpStorm.
 * User: hotblue
 * Date: 5/28/14
 * Time: 10:47 PM
 */
class ProductStockController extends AdminController
{

    public $layout = '//layouts/center';

    public function actionStockHistory()
    {
        $data = HtProductStockPdfHistory::model()->with('ticket')->findAll(array('condition' => 't.product_id = ' . $this->getProductID(), 'order' => 'upload_time DESC'));
        $stockPdfHistory = Converter::convertModelToArray($data);
        $data = HtProduct::model()->with('city', 'description',
                                         'supplier')->find('p.product_id = ' . $this->getProductID());
        $productInfo = Converter::convertModelToArray($data);

        $return = array();
        $return['product_info']['city_name'] = $productInfo['city']['cn_name'];
        $return['product_info']['supplier'] = $productInfo['supplier']['name'] . '/' . $productInfo['supplier']['cn_name'];
        $return['product_info']['product_name'] = $productInfo['description']['name'];
        $return['product_info']['product_id'] = $this->getProductID();
        foreach ($stockPdfHistory as $item) {
            $history = array();
            $history['batch_id'] = $item['batch_id'];
            $history['ticket_name'] = $item['ticket']['cn_name'];
            $history['source_filename'] = $item['source_filename'];
            $history['upload_time'] = $item['upload_time'];
            $history['confirmed_count'] = $item['confirmed_count'];
            $history['status'] = $item['status'];
            $return['history_list'][] = $history;
        }

        echo CJSON::encode(array('code' => 200, 'msg' => '获取库存上传历史成功！', 'data' => $return));
    }

    public function actionFileDuplicated()
    {
        $data = HtProductStockPdfHistory::model()->with('ticket')->find('batch_id = ' . $this->getBatchID());
        $stockPdfHistory = Converter::convertModelToArray($data);
        $data = HtProduct::model()->with('city', 'description',
                                         'supplier')->find('p.product_id = ' . $stockPdfHistory['product_id']);
        $productInfo = Converter::convertModelToArray($data);

        $return = array();
        $stockPdfHistory['city_name'] = $productInfo['city']['cn_name'];
        $stockPdfHistory['supplier'] = $productInfo['supplier']['name'] . '/' . $productInfo['supplier']['cn_name'];
        $stockPdfHistory['product_name'] = $productInfo['description']['name'];
        $return['duplicated'] = $stockPdfHistory;

        $duplication_info = $stockPdfHistory['duplication_info'];
        $duplicated_files = explode(',', $duplication_info);
        $detailed_duplication_info = array();
        foreach ($duplicated_files as $duplicated_file) {
            $md5_file = explode(':', $duplicated_file);
            if (count($md5_file) == 2) {
                $existed = Converter::convertModelToArray(HtProductStockPdf::model()->with('pdfHistory')->findAll("file_md5 = '$md5_file[0]'"));
                if (count($existed) == 1) {
                    $detailed_duplication_info[] = array(
                        'file' => $md5_file[1],
                        'existed_file' => $existed[0]['filename'],
                        'upload_time' => $existed[0]['upload_time'],
                        'source_comment' => $existed[0]['pdfHistory']['source_comment']
                    );
                } else {
                    $detailed_duplication_info[] = array(
                        'file' => $md5_file[1],
                        'existed_file' => 'N/A',
                        'upload_time' => 'N/A',
                        'source_comment' => 'N/A');
                }
            }
        }
        $return['detailed_duplication_info'] = $detailed_duplication_info;
        echo CJSON::encode(array('code' => 200, 'msg' => '获取重复文件成功！', 'data' => $return));
    }

    public function actionGetUnconfirmed()
    {
        $Criteria = new CDbCriteria();
        $Criteria->addCondition("batch_id = " . $this->getBatchID());
        $Criteria->addCondition("status = 0");
        $data = Converter::convertModelToArray(HtProductStockPdf::model()->findAll($Criteria));
        foreach ($data as &$item) {
            $item['pdf_url'] = Yii::app()->request->hostInfo . Yii::app()->params['STOCK_PDF_ROOT'] . $item['directory'] . $item['filename'];
        }

        $batch_info = Converter::convertModelToArray(HtProductStockPdfHistory::model()->findByPk($this->getBatchID()));
        $productInfo = Converter::convertModelToArray(HtProduct::model()->with('city', 'description',
                                                                               'supplier')->find('p.product_id = ' . $batch_info['product_id']));
        $batch_info['city_name'] = $productInfo['city']['cn_name'];
        $batch_info['supplier'] = $productInfo['supplier']['name'] . '/' . $productInfo['supplier']['cn_name'];
        $batch_info['product_name'] = $productInfo['description']['name'];
        echo CJSON::encode(array('code' => 200, 'msg' => '获取待抽检PDF成功！', 'data' => array('file_info' => $data, 'batch_info' => $batch_info)));
    }

    public function actionConfirm()
    {
        $count = HtProductStockPdf::model()->updateAll(array('status' => 1), 'batch_id=:batch_id',
                                                       array(':batch_id' => $this->getBatchID()));
        HtProductStockPdfHistory::model()->updateByPk($this->getBatchID(),
                                                      array('status' => 3, 'confirmed_count' => $count));
        echo CJSON::encode(array('code' => 200, 'msg' => '确认成功！'));
    }

    public function actionDelete()
    {
        HtProductStockPdf::model()->deleteAll('batch_id = ' . $this->getBatchID());
        HtProductStockPdfHistory::model()->deleteByPk($this->getBatchID());
        echo CJSON::encode(array('code' => 200, 'msg' => '删除成功！'));
    }

    public function actionUploadFile()
    {
        $product_id = $this->getProductID();
        $ticket_id = Yii::app()->request->getParam('ticket_id');
        $comment = Yii::app()->request->getParam('comment');
        //$product_id = 1329;
        //$ticket_id = 2;

        $src_filename = html_entity_decode($_FILES["file"]['name'], ENT_QUOTES, 'UTF-8');

        $json = array();
        if (strtolower(substr(strrchr($src_filename, '.'), 1)) != "zip") {
            $json['error'] = '无效的文件类型！';
        }

        if ($_FILES["file"]['error'] != UPLOAD_ERR_OK) {
            switch ($_FILES["file"]['error']) {
                case 1:
                    $json['error'] = '警告： 上传的文件超过了在php.ini配置中的上传文件大小上限！';
                    break;
                case 2:
                    $json['error'] = '警告： 上传的文件超过了在HTML表单内指定的上传文件大小上限！';
                    break;
                case 3:
                    $json['error'] = '警告： 只上传了部份文件！';
                    break;
                case 4:
                    $json['error'] = '警告： 没有上传文件！';
                    break;
                case 6:
                    $json['error'] = '警告： 缺少临时文件夹！';
                    break;
                case 7:
                    $json['error'] = '警告： 无法写入文件！';
                    break;
                case 8:
                    $json['error'] = '警告： 文件上传终止！';
                    break;
                case 999:
                    $json['error'] = '警告： 没有可提供的错误代码！';
                    break;
            }
        }
        $file = '';
        $to_dir = '';
        if (!isset($json['error'])) {
            if (is_uploaded_file($_FILES["file"]['tmp_name']) && file_exists($_FILES["file"]['tmp_name'])) {
                $file = date('Ymd_His', time()) . '_' . basename($src_filename);

                $json['file'] = $file;
                $to_dir = $product_id . '/' . $ticket_id . '/';
                $path = Yii::app()->basePath . '/../' . Yii::app()->params['STOCK_PDF_ROOT'] . $to_dir;
                if (!file_exists($path)) {
                    Yii::log('pdf path:' . $path);
                    mkdir($path, 0755, true);
                }
                move_uploaded_file($_FILES["file"]['tmp_name'], $path . $file);
            }
        }
        if (isset($json['error'])) {
            echo CJSON::encode(array('code' => 402, 'msg' => '上传失败：' . $json['error']));
        } else {
            // add a record of the uploaded file
            $prductStockPdfHistory = new HtProductStockPdfHistory();
            $prductStockPdfHistory['product_id'] = $product_id;
            $prductStockPdfHistory['ticket_id'] = $ticket_id;
            $prductStockPdfHistory['source_filename'] = $src_filename;
            $prductStockPdfHistory['source_comment'] = $comment;
            $prductStockPdfHistory['target_filename'] = $file;
            $prductStockPdfHistory['target_dir'] = $to_dir;
            $prductStockPdfHistory['upload_time'] = date("Y-m-d H:i:s", time());
            $prductStockPdfHistory['status'] = 0;
            $batch_id = '';
            if ($prductStockPdfHistory->insert()) {
                $batch_id = $prductStockPdfHistory->primaryKey;
            }

            // TODO call zip handling service
            $result = $this->processZipFile($batch_id);
            echo CJSON::encode(array('code' => 200, 'msg' => '上传成功，正在处理...', 'data' => $result));
        }

    }

    public function actionUploadInsurance()
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
                $path = Yii::app()->basePath . '/../' . Yii::app()->params['INSURANCE_FILE_ROOT'] . $to_dir;
                if (!file_exists($path)) {
                    Yii::log('excel path:' . $path);
                    mkdir($path, 0755, true);
                }
                move_uploaded_file($_FILES["file"]['tmp_name'], $path . $file);
            }
        }

        $insuraceService = new Insurance();
        $result = $insuraceService->importCodes($path . $file);
        echo CJSON::encode(array('code' => $result["code"], 'msg' => $result["msg"], 'data' => $result["data"]));
    }

    public function actionGetRemainInsuranceCounts()
    {
//        $c = new CDbCriteria();
//        $c->addCondition("product_id = " . $product['product_id']);
        $count = HtInsuranceCode::model()->count("order_id = 0");
        echo CJSON::encode(array('code' => "200", 'data' => $count));
    }

    public function processZipFile($batch_id)
    {
        $data = HtProductStockPdfHistory::model()->with('ticket')->find('batch_id = ' . $batch_id);
        $batch_info = Converter::convertModelToArray($data);

        $zipfile = Yii::app()->basePath . '/../' . Yii::app()->params['STOCK_PDF_ROOT'] . $batch_info['target_dir'] . $batch_info['target_filename'];

        $zip = new ZipArchive();
        $rs = $zip->open($zipfile);

        if ($rs !== TRUE) {
            die('解压失败!Error Code:' . $rs);
        }

        $save_relative_path = 'tmp/' . $batch_info['product_id'] . '/' . date('Ymd_His', time()) . '/';
        $savepath = Yii::app()->basePath . '/../' . Yii::app()->params['STOCK_PDF_ROOT'] . $save_relative_path;

        if (!file_exists($savepath)) {
            Yii::log('extract pdf to path:' . $savepath);
            mkdir($savepath, 0755, true);
        } else {
            $this->delTree($savepath);
            mkdir($savepath, 0755, true);
        }

        $result = $zip->extractTo($savepath);
        $zip->close();

        if ($result === false) { // 解压失败
            return -1;
        }

        // check the extracted files first. if no one duplicates to exist file, add to DB

        $md5_to_files = array();
        $file_md5s = array();

        // $pdf_files = scandir($savepath);
        // TODO search sub-directory for pdf files
        $pdf_files = array();
        $this->collectPdfs($pdf_files, $savepath);

        if (!count($pdf_files)) return 3;

        foreach ($pdf_files as $pdf_file) {
            $file = $savepath . $pdf_file;
            $md5 = md5_file($file);
            $md5_to_files[$md5] = $pdf_file;
            array_push($file_md5s, $md5);
        }

        $criteria = new CDbCriteria();
        $md5_all = "'" . implode("','", $file_md5s) . "'";
        $criteria->addCondition("file_md5 IN ($md5_all)");
        $res = Converter::convertModelToArray(HtProductStockPdf::model()->findAll($criteria));

        if (count($res) > 0) // 有重复文件
        {
            //  save duplication info and update status of batch_id
            $errfile = array();
            foreach ($res as $md5) {
                $file = $md5_to_files[$md5['file_md5']];
                $errfile[] = $md5['file_md5'] . ':' . $file;
            }

            $data = array(
                'status' => 1,
                'errfile' => implode(',', $errfile)
            );
            $item = HtProductStockPdfHistory::model()->findByPk($batch_id);
            if ($item) {
                $item['status'] = (int)$data['status'];
                $item['duplication_info'] = $data['errfile'];
                $item->update();
            }

            return 1;
        } else {
            $verified_relative_path = 'v_' . $batch_info['product_id'] . '/' . date('Ymd', time()) . '/';
            $verified_dir = Yii::app()->basePath . '/../' . Yii::app()->params['STOCK_PDF_ROOT'] . $verified_relative_path;
            if (!file_exists($verified_dir)) {
                mkdir($verified_dir, 0755, true);
            }
            foreach ($md5_to_files as $md5 => $file) {
                $target_file = $batch_id . '_' . $batch_info['ticket_id'] . '_' . $file;
                copy($savepath . $file, $verified_dir . $target_file);
                $productStockPdf = new HtProductStockPdf();
                $productStockPdf['file_md5'] = $md5;
                $productStockPdf['batch_id'] = (int)$batch_id;
                $productStockPdf['product_id'] = (int)$batch_info['product_id'];
                $productStockPdf['filename'] = $target_file;
                $productStockPdf['ticket_id'] = $batch_info['ticket_id'];
                $productStockPdf['directory'] = $verified_relative_path;
                $productStockPdf->insert();
            }
            $item = HtProductStockPdfHistory::model()->findByPk($batch_id);
            if ($item) {
                $item['status'] = 2;
                $item->update();
            }

            return 2;
        }
    }

    # recursively remove a directory
    private function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }

    private function collectPdfs(&$result, $root, $sub = '')
    {
        $full_path = $sub === '' ? $root : $root . $sub . '/';
        $files = array_diff(scandir($full_path), array('.', '..'));
        foreach ($files as $file) {
            if (!(strpos($file, 'MACOSX') === false)) {
                continue;
            }
            if (is_dir("$full_path$file")) {
                $this->collectPdfs($result, $full_path, $file);
            } else {
                if (strtolower(substr($file, -3, 3)) === 'pdf') {
                    if (strlen($sub) > 0) {
                        copy($full_path . $file, $root . $file);
                    }
                    array_push($result, $file);
                }
            }
        }
    }

    private function getProductID()
    {
        return (int)Yii::app()->request->getParam('product_id');
    }

    private function getBatchID()
    {
        return (int)Yii::app()->request->getParam('batch_id');
    }
}