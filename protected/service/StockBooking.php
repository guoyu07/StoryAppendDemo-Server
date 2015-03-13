<?php
/**
 * Created by PhpStorm.
 * User: app
 * Date: 14-5-24
 * Time: 下午1:05
 */

/**
 * @project hitour.server
 * @file HiTourBooking.php
 * @author wenzi(wenzi@hitour.cc)
 * @version 1.0
 * @date 14-5-24 下午1:05
 **/
class StockBooking
{
    public function addBooking($order,$order_product)
    {
        $order_id = $order_product['order_id'];
        $product_id = $order_product['product']['product_id'];
        $quantities = $order_product['quantities'];

        $ready = true;
        $transaction = HtProductStockPdf::model()->dbConnection->beginTransaction();
        try {
            foreach ($quantities as $ticket_id => $qn) {
                $has_pdf_num = HtProductStockPdf::model()->countByAttributes(['order_id' => $order_id, 'product_id' => $product_id, 'ticket_id' => $ticket_id]);
                Yii::log('Stock retieve start: order_id[' . $order_id . ']product_id[' . $product_id . ']ticket_id[' . $ticket_id . ']has_pdf[' . $has_pdf_num . ']need_pdf[' . $qn . ']', CLogger::LEVEL_INFO);
                if ($has_pdf_num < $qn) {
                    $c = new CDbCriteria();
                    $c->addCondition(['product_id=:pid', 'ticket_id=:tid', 'status=1', 'order_id=0']);
                    $c->params = [':pid' => $product_id, ':tid' => $ticket_id];
                    $c->limit = ($qn - $has_pdf_num);
                    HtProductStockPdf::model()->updateAll(['order_id' => $order_id], $c);

                    $new_pdf_num = HtProductStockPdf::model()->countByAttributes(['order_id' => $order_id, 'product_id' => $product_id, 'ticket_id' => $ticket_id]);
                    Yii::log('Stock retieve end: order_id[' . $order_id . ']product_id[' . $product_id . ']ticket_id[' . $ticket_id . ']has_pdf[' . $new_pdf_num . ']need_pdf[' . $qn . ']', CLogger::LEVEL_INFO);
                    if ($new_pdf_num < $qn) { //仍然不足
                        $ready = false;
                        break;
                    }
                } else {
                    Yii::log('已经配货成功，不需要再次分配 pdf。Order_id:' . $order_id . ',product_id:' . $product_id, CLogger::LEVEL_INFO, 'hitour.service.StockBooking');
                }
            }

            if ($ready) {
                if ($files = $this->handleVoucherFiles($order,$order_product)) {
                    HtSupplierOrder::model()->updateByPk($order_product['supplier_order']['supplier_order_id'], ['current_status' => HtSupplierOrder::CONFIRMED, 'voucher_ref' => json_encode($files)]);
                    $transaction->commit();
                    $result['code'] = 200;
                    $result['msg'] = 'OK';
                } else {
                    $transaction->rollback();
                    $result['code'] = 400;
                    $result['msg'] = '库存充分，处理Voucher时出错！';
                }
            } else {
                $transaction->rollback();
                $result['code'] = 400;
                $result['msg'] = '库存不足！';
            }
        } catch (Exception $e) {
            $transaction->rollback();
            $result['code'] = 400;
            $result['msg'] = '库存不足！';
        }

        return $result;

    }

    private function handleVoucherFiles($order,$order_product)
    {
        $files = array();
        $order_id = $order_product['order_id'];
        $product_id = $order_product['product']['product_id'];
        $voucher_path = $order['voucher_path'];
        $stock_base_path = dirname(Yii::app()->BasePath) . Yii::app()->params['STOCK_PDF_ROOT'];

        $stock_pdfs = HtProductStockPdf::model()->findAllByAttributes(['order_id' => $order_id, 'product_id' => $product_id]);

        foreach ($stock_pdfs as $i => $sp) {
            $src = $stock_base_path . $sp->directory . $sp->filename;
            $dest = 'Voucher_' . $order_id . '_' . $product_id . '_' . $sp->ticket_id . '_' . ($i + 1) . '.pdf';

            $files[] = $dest;
            if (file_exists($src) && copy($src, $voucher_path . $dest)) {
                $sp->filename_in_order = $dest;
                if (!$sp->save()) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return $files;
    }

    public function returnRequest($order,$order_product)
    {
        HtSupplierOrder::model()->updateByPk($order_product['supplier_order']['supplier_order_id'], ['current_status' => HtSupplierOrder::RETURN_REQUEST]);
        $isok = Yii::app()->notify->notifyOP($order,$order_product);
        if (!$isok) {
            Yii::log('Return request: send mail to op failed. order_id['.$order_product['order_id'].']', CLogger::LEVEL_WARNING);
        }
        //This return request just send notify to Supplier, so return not ok.
        $result['code'] = 300; //重要:不能改为200，必须等OP等到供应商确认才能确认退货！！！
        $result['msg'] = 'Sent email to Supplier.';
        return $result;
    }

    public function returnConfirm($order,$order_product)
    {
        $order_id = $order['order_id'];
        $comments = date('Y-m-d') . '由订单[' . $order_id . ']退订回收!;';
        $affected_row = HtProductStockPdf::model()->updateAll(['order_id' => 0, 'filename_in_order' => '', 'comments' => new CDbExpression('CONCAT(comments,' . '"' . $comments . '")')], 'order_id=' . $order_id);
        Yii::log('从订单' . $order_id . '回收了' . $affected_row . '个库存', CLogger::LEVEL_INFO, 'hitour.service.booking');

        $so = HtSupplierOrder::model()->findByPk($order_product['supplier_order']['supplier_order_id']);
        $so['supplier_booking_ref'] = '';
        $so['confirmation_ref'] = '';
        $so['voucher_ref'] = json_encode([]);
        $so['additional_info'] = '';
        $so['payable_by'] = '';
        $so['current_status'] = HtSupplierOrder::CANCELED;
        if ($so->save()) {
            $result['code'] = 200;
            $result['msg'] = 'OK';
        } else {
            $result['code'] = 400;
            $result['msg'] = 'update supplier order failed';
        }
        return $result;
    }
}