<?php

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/24/14
 * Time: 11:23 AM
 */
class OrderController extends Controller
{
    const PAGE_SIZE = 2;

    public function actionIndex()
    {
        $page = (int)$this->getParam('page', 1);
        $group = $this->getParam('group', 'all');

        $customer_id = 1;//@todo
        $order_list = HtOrder::model()->with('order_product', 'comments')->filterByGroup($group)->limitByPage($page,self::PAGE_SIZE)->findAllByAttributes(['customer_id' => $customer_id]);
        $order_list = Converter::convertModelToArray($order_list);
        echo CJSON::encode(array('code' => 200, 'msg' => '订单查询成功！', 'data' => $order_list));
    }

}