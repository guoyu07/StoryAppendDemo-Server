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

class B2BBooking {

    public function addBooking($order,$order_product)
    {
        $isok = Yii::app()->notify->notifyOP($order,$order_product);
        if (!$isok) {
            Yii::log('addBooking: send mail to op failed. order_id['.$order_product['order_id'].']', CLogger::LEVEL_WARNING);
        }

        //This booking just send notify to OP, so return not ok.
        $result['code'] = 300;
        $result['msg'] = 'Sent email to OP.';
        return $result;
    }


    public function returnRequest($order,$order_product)
    {
        HtSupplierOrder::model()->updateByPk($order_product['supplier_order']['supplier_order_id'], ['current_status' => HtSupplierOrder::RETURN_REQUEST]);
        $isok = Yii::app()->notify->notifyOP($order,$order_product);
        if (!$isok) {
            Yii::log('returnRequest: send mail to op failed. order_id['.$order_product['order_id'].']', CLogger::LEVEL_WARNING);
        }

        //This return request just send notify to Supplier, so return not ok.
        $result['code'] = 300;//重要:不能改为200，必须等OP等到供应商确认才能确认退货！！！
        $result['msg'] = 'Sent email to OP.';
        return $result;
    }

    public function returnConfirm($order,$order_product)
    {
        $so = HtSupplierOrder::model()->findByPk($order_product['supplier_order']['supplier_order_id']);
        if (empty($so)) {
            $result['code'] = 401;
            $result['msg'] = '未找到供应商订单记录';
        }else{
            $so['supplier_booking_ref'] = '';
            $so['confirmation_ref'] = '';
            $so['voucher_ref'] = json_encode([]);
            $so['additional_info'] = '';
            $so['payable_by'] = '';
            $so['current_status'] = HtSupplierOrder::CANCELED;
            if($so->save()){
                $result['code'] = 200;
                $result['msg'] = 'OK';
            }else{
                $result['code'] = 400;
                $result['msg'] = '更新供应商订单记录失败';
            }
        }
        return $result;
    }

}