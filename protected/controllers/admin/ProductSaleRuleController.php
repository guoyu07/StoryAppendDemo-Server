<?php
/**
 * Created by PhpStorm.
 * User: hotblue
 * Date: 5/21/14
 * Time: 14:16 PM
 */

class ProductSaleRuleController extends AdminController
{
    /**
     *
     */
    public function actionEdit() {
    }



    /**
     *获取售卖方式
     */
    public function actionGetSaleRule()
    {
        $product_id = $this->getProductID();
        $data = $this->getSaleRule($product_id);
        echo CJSON::encode(array('code' => 200, 'msg' => '获取售卖方式成功！', 'data' => $data));
    }

    /**
     *保存售卖方式
     */
    public function actionSaveSaleRule()
    {
        $product_id = $this->getProductID();
        $data = $this->getPostJsonData();
        $item = HtProductSaleRule::model()->findByPk($product_id);
        if(empty($item)){
            $item['product_id'] = $product_id;
            $item['sale_in_package'] = $data['sale_rule']['sale_in_package'];
            $item['min_num'] = $data['sale_rule']['min_num'];
            $item['max_num'] = $data['sale_rule']['max_num'];
            $result = $item->insert();
            if(!$result) {
                EchoUtility::echoCommonFailed('保存失败!');
                return;
            }
        }else{
            $item['sale_in_package'] = $data['sale_rule']['sale_in_package'];
            $item['min_num'] = $data['sale_rule']['min_num'];
            $item['max_num'] = $data['sale_rule']['max_num'];
            $result = $item->update();
            if(!$result) {
                EchoUtility::echoCommonFailed('保存失败!');
                return;
            }
        }

        if($data['sale_rule']['sale_in_package'] == 1){//按套出售保存
            $tickets = HtProductTicketRule::model()->findAll('product_id = '.$product_id);
            $has_ticket_id_99 = false;
            foreach($tickets as $ticket){
                if($ticket['ticket_id'] == 99) {
                    $has_ticket_id_99 = true;
                }
                $ticket['is_independent'] = 0;
                $ticket['min_num'] = 0;
                $ticket->update();
            }
            if(!$has_ticket_id_99) {
                $ticket = new HtProductTicketRule();
                $ticket['product_id'] = $product_id;
                $ticket['ticket_id'] = 99;
                $ticket->insert();
            }

            foreach($data['package_rule'] as $prule){
                $pk = array('product_id' => $product_id, 'ticket_id' => $prule['ticket_id']);
                $item = HtProductPackageRule::model()->findByPk($pk);
                if (empty($item)) {//新增
                    $item = new HtProductPackageRule();
                    $item['product_id'] = $product_id;
                    $item['base_product_id'] = $product_id;
                    $item['ticket_id'] = $prule['ticket_id'];
                    $item['quantity'] = $prule['quantity'];
                    $result = $item->insert();
                    if(!$result) break;
                }else{//更新
                    $item['base_product_id'] = $product_id;
                    $item['quantity'] = $prule['quantity'];
                    $result = $item->update();
                    if(!$result) break;
                }
            }

        }else{//不按套出售保存
            HtProductPackageRule::model()->deleteAll('product_id=' . $product_id);

            HtProductTicketRule::model()->deleteByPk(array('product_id'=>$product_id, 'ticket_id' => 99));

            foreach($data['ticket_rule'] as $trule){
                $pk = array('product_id' => $product_id, 'ticket_id' => $trule['ticket_id']);
                $item = HtProductTicketRule::model()->findByPk($pk);
                $item['is_independent'] = $trule['is_independent']?$trule['is_independent']:0;
                $item['min_num'] = $trule['min_num']?$trule['min_num']:0;
                $result = $item->update();
                if(!$result) break;
            }
        }
        EchoUtility::echoMsgTF($result, '保存',$this->getSaleRule($product_id));
    }

    private function getProductID()
    {
        return (int)Yii::app()->request->getParam('product_id');
    }

    private function getSaleRule($product_id)
    {
        $saleRule = HtProductSaleRule::model()->findByPk($product_id);
        $data['sale_rule'] = Converter::convertModelToArray($saleRule);
        if($saleRule['sale_in_package'] == 1){//按套卖
            $packageRule = HtProductPackageRule::model()->with('ticket_type')->findAll('product_id=' . $product_id);
            $data['package_rule'] = Converter::convertModelToArray($packageRule);
        }else{//不按套卖
            $ticketRule = HtProductTicketRule::model()->with('ticket_type')->findAll('product_id=' . $product_id);
            $data['ticket_rule'] = Converter::convertModelToArray($ticketRule);
        }
        return $data;
    }
}