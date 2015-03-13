<?php

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 14-07-15
 * Time: 上午10:39
 */
class ExportController extends Controller
{

    public function actionTest(){
        echo "TEST";
    }
    public function actionSettourHrs()
    {
        $from_date = $this->getParam('from_date', date('Y-m-d', strtotime('+6day')));
        $to_date = $this->getParam('to_date', $from_date);
        $download = $this->getParam('download', 0);

        $settour = new Settour();
        $result = $settour->export_hsr($from_date, $to_date);

        if ($result['code'] != 200) {
            echo json_encode($result);
            return;
        }

        if ($download) {
            if ($result['order_num'] == 0) {
                $result['msg'] = '指定的日期范围内没有查到订单！' . PHP_EOL;
                echo json_encode($result);
            } else {
                $this->download_excel($result['export_file']);
            }
            return;
        } else {
            if (Yii::app()->params['PAYMENT_REALLY']) {
//                $to = 'alex@shasettour.com,1257492737@qq.com,yalynn929@settour.com.tw';
//                $cc = 'snowy@hitour.cc,wuqq@hitour.cc';
                $to = 'settour@hitour.cc';
                $cc = 'wenzi@hitour.cc';
            } else {
                $to = 'settour@hitour.cc';
                $cc = 'wenzi@hitour.cc';
            }
            $subject = basename($result['export_file']);
            if ($result['order_num'] == 0) {
                $body = $from_date . ' 无需要兑换订单！';
                $attachment = array();
            } else {
                $body = $from_date . '订单共' . $result['order_num'] . '条，详情见附件！';
                $attachment = array($result['export_file']);
            }

            Mail::sendToSupplier($to, $subject, $body, $attachment, 1, $cc);

            echo json_encode($result);
            return;
        }
    }

    public function actionTransIslandOrder()
    {
        $from_date = $this->getParam('from_date', date('Y-m-d', strtotime('+1day')));
        $to_date = $this->getParam('to_date', $from_date);
        $download = $this->getParam('download', 0);

        $transisland = new TransIsland();
        $result = $transisland->export_order($from_date, $to_date);

        if ($result['code'] != 200) {
            echo json_encode($result);
            return;
        }

        if ($download) {
            if ($result['order_num'] == 0) {
                $result['msg'] = '指定的日期范围内没有查到订单！' . PHP_EOL;
                echo json_encode($result);
            } else {
                $this->download_excel($result['export_file']);
            }
            return;
        } else {
            if (Yii::app()->params['PAYMENT_REALLY']) {
//                $to = 'baomavip@sina.cn,amygu@gogotil.com';
//                $cc = 'snowy@hitour.cc';
                $to = 'transisland@hitour.cc';
                $cc = 'wenzi@hitour.cc';
            } else {
                $to = 'transisland@hitour.cc';
                $cc = 'wenzi@hitour.cc';
            }
            $subject = basename($result['export_file']);
            if ($result['order_num'] == 0) {
                $body = $from_date . ' 无需要兑换订单！';
                $attachment = array();
            } else {
                $body = $from_date . '订单共' . $result['order_num'] . '条，详情见附件！';
                $attachment = array($result['export_file']);
            }

            Mail::sendToSupplier($to, $subject, $body, $attachment, 1, $cc);

            echo json_encode($result);
            return;
        }
    }

    public function actionXintaichangOrder()
    {
        $from_date = $this->getParam('from_date', date('Y-m-d', strtotime('-1day')));
        $to_date = $this->getParam('to_date', date('Y-m-d'));
        $download = $this->getParam('download', 0);

        $xintaichang = new Xintaichang();
        $result = $xintaichang->export_order($from_date, $to_date);

        if ($result['code'] != 200) {
            echo json_encode($result);
            return;
        }

        if ($download) {
            if ($result['order_num'] == 0) {
                $result['msg'] = '指定的日期范围内没有查到订单！' . PHP_EOL;
                echo json_encode($result);
            } else {
                $this->download_excel($result['export_file']);
            }
            return;
        } else {
            if (Yii::app()->params['PAYMENT_REALLY']) {
                $to = 'xintaichang@hitour.cc';
                $cc = 'wenzi@hitour.cc';
            } else {
                $to = 'xintaichang@hitour.cc';
                $cc = 'wenzi@hitour.cc';
            }
            $subject = basename($result['export_file']);
            if ($result['order_num'] == 0) {
                $body = $from_date . ' 无需要兑换订单！';
                $attachment = array();
            } else {
                $body = $from_date . '订单共' . $result['order_num'] . '条，详情见附件！';
                $attachment = array($result['export_file']);
            }

            Mail::sendToSupplier($to, $subject, $body, $attachment, 1, $cc);

            echo json_encode($result);
            return;
        }
    }

    public function actionWayouOrder()
    {
        $from_date = $this->getParam('from_date', date('Y-m-d', strtotime('-1day')). ' 16:00:00');
        $to_date = $this->getParam('to_date', date('Y-m-d').' 15:59:59');
        $download = $this->getParam('download', 0);

        $wayou = new Wayou();
        $result = $wayou->export_order($from_date, $to_date);

        if ($result['code'] != 200) {
            echo json_encode($result);
            return;
        }

        if ($download) {
            if ($result['order_num'] == 0) {
                $result['msg'] = '指定的日期范围内没有查到订单！' . PHP_EOL;
                echo json_encode($result);
            } else {
                $this->download_excel($result['export_file']);
            }
            return;
        } else {
            if (Yii::app()->params['PAYMENT_REALLY']) {
                $to = 'wayou@hitour.cc';
                $cc = '15295092@qq.com';
            } else {
                $to = 'blue@hitour.cc';
                $cc = '15295092@qq.com';
            }
            $subject = basename($result['export_file']);
            if ($result['order_num'] == 0) {
                $body = $from_date . ' 无需要兑换订单！';
                $attachment = array();
            } else {
                $body = $from_date . '订单共' . $result['order_num'] . '条，详情见附件！';
                $attachment = array($result['export_file']);
            }

            Mail::sendToSupplier($to, $subject, $body, $attachment, 1, $cc);

            echo json_encode($result);
            return;
        }
    }

    public function actionHanaTourOrder()
    {
        $from_date = $this->getParam('from_date', date('Y-m-d', strtotime('-1day')));
        $to_date = $this->getParam('to_date', date('Y-m-d'));
        $download = $this->getParam('download', 0);

        $hanatour = new HanaTour();
        $result = $hanatour->export_order($from_date, $to_date);

        if ($result['code'] != 200) {
            echo json_encode($result);
            return;
        }

        if ($download) {
            if ($result['order_num'] == 0) {
                $result['msg'] = '指定的日期范围内没有查到订单！' . PHP_EOL;
                echo json_encode($result);
            } else {
                $this->download_excel($result['export_file']);
            }
            return;
        } else {
            if (Yii::app()->params['PAYMENT_REALLY']) {
                $to = 'hanatour@hitour.cc';
                $cc = '';
//                $cc = 'hanatour@hitour.cc';
            } else {
                $to = 'hanatour@hitour.cc';
                $cc = '';
//                $cc = 'hanatour@hitour.cc';
            }
            $subject = basename($result['export_file']);
            if ($result['order_num'] == 0) {
                $body = $from_date . ' 无需要兑换订单！';
                $attachment = array();
            } else {
                $body = $from_date . '订单共' . $result['order_num'] . '条，详情见附件！';
                $attachment = array($result['export_file']);
            }

            Mail::sendToSupplier($to, $subject, $body, $attachment, 1, $cc);

            echo json_encode($result);
            return;
        }
    }

    public function actionXintaichangTour()
    {
        $from_date = $this->getParam('from_date', date('Y-m-d', strtotime('-1day')));
        $to_date = $this->getParam('to_date', date('Y-m-d'));
        $download = $this->getParam('download', 0);

        $xintaichang = new Xintaichang();
        $result = $xintaichang->export_tour($from_date, $to_date);

        if ($result['code'] != 200) {
            echo json_encode($result);
            return;
        }

        if ($download) {
            if ($result['order_num'] == 0) {
                $result['msg'] = '指定的日期范围内没有查到订单！' . PHP_EOL;
                $this->download_excel($result['export_file']);
                echo json_encode($result);
            } else {
                $this->download_excel($result['export_file']);
            }
            return;
        } else {
            if (Yii::app()->params['PAYMENT_REALLY']) {
                $to = 'xintaichang@hitour.cc';
                $cc = 'wenzi@hitour.cc';
            } else {
                $to = 'xintaichang@hitour.cc';
                $cc = 'wenzi@hitour.cc';
            }
            $subject = basename($result['export_file']);
            if ($result['order_num'] == 0) {
                $body = $from_date . ' 无需要兑换订单！';
                $subject = $body;
                $attachment = array();
            } else {
                $body = $from_date . '订单共' . $result['order_num'] . '条，详情见附件！';
                $attachment = array($result['export_file']);
            }

            Mail::sendToSupplier($to, $subject, $body, $attachment, 1, $cc);

            echo json_encode($result);
            return;
        }
    }

    public function actionMigrationVoucher(){
        $max_order_id = $this->getParam('max_id');
        $migration = new Migration();
//        $result = $migration->migrateVoucher('/Users/wenzi/Workspace/hicart.server/upload/data/voucher',20000);
        $result = $migration->migrateVoucher('/home/app/git/hicart.server/upload/data/voucher',20000);
        echo json_encode($result);
    }

    public function actionHuapangOrder()
    {
        $order_id = (int)$this->getParam('order_id');
        $download = $this->getParam('download', 0);

        $huapang = new HuaPang();
        $result = $huapang->impl_export_order($order_id);

        if ($result['code'] != 200) {
            echo json_encode($result);
            return;
        }

        if ($download) {
            if ($result['order_num'] == 0) {
                $result['msg'] = '指定的日期范围内没有查到订单！' . PHP_EOL;
                echo json_encode($result);
            } else {
                $this->download_excel($result['export_file']);
            }
            return;
        } else {
            if (Yii::app()->params['PAYMENT_REALLY']) {
                $to = 'xintaichang@hitour.cc';
                $cc = 'wenzi@hitour.cc';
            } else {
                $to = 'xintaichang@hitour.cc';
                $cc = 'wenzi@hitour.cc';
            }
            $subject = basename($result['export_file']);
            if ($result['order_num'] == 0) {
                $body = '无符合条件的兑订单！';
                $attachment = array();
            } else {
                $body = '订单共' . $result['order_num'] . '条，详情见附件！';
                $attachment = array($result['export_file']);
            }

            Mail::sendToSupplier($to, $subject, $body, $attachment, 1, $cc);

            echo json_encode($result);
            return;
        }
    }

    private function download_excel($file_path)
    {
        $ext = pathinfo($file_path, PATHINFO_EXTENSION);
        if(strtoupper($ext)=='ZIP'){
            header('Content-Type: application/zip');
        }else{
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        }

        header('Content-Disposition: attachment;filename="' . basename($file_path) . '"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        readfile($file_path);
    }

    public function actionRefundOrder()
    {
        $download = $this->getParam('download', 0);

        $hitour = new Hitour();
        $result = $hitour->export_refund_order();

        if ($result['code'] != 200) {
            echo json_encode($result);
            return;
        }

        if ($download) {
            if ($result['order_num'] == 0) {
                $result['msg'] = '没有查到订单！' . PHP_EOL;
                echo json_encode($result);
            } else {
                $this->download_excel($result['export_file']);
            }
            return;
        } else {
            if (Yii::app()->params['PAYMENT_REALLY']) {
                $to = 'blue@hitour.cc';
                $cc = '15295092@qq.com';
            } else {
                $to = 'blue@hitour.cc';
                $cc = '15295092@qq.com';
            }
            $subject = basename($result['export_file']);
            if ($result['order_num'] == 0) {
                $body = ' 没有查到退款订单！';
                $attachment = array();
            } else {
                $body = '退款订单共' . $result['order_num'] . '条，详情见附件！';
                $attachment = array($result['export_file']);
            }

            Mail::sendToSupplier($to, $subject, $body, $attachment, 1, $cc);

            echo json_encode($result);
            return;
        }
    }
}