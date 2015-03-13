<?php

/**
 * Created by hitour.server.
 * User: xingminglister
 * Date: 9/11/14
 * Time: 11:46 AM
 */
class ProductCommentController extends AdminController
{

    public function actionIndex()
    {
        echo 'Hello, world!';
    }

    public function actionComments()
    {
        $product_id = $this->getProductID();
        if ($product_id == 0) {
            EchoUtility::echoCommonFailed('Invalid product_id.');

            return;
        }

        $page_index = $this->getParam('page_index', 0);
        $count_pre_page = $this->getParam('count_per_page', 20);

        $comments = HtProductComment::getComments($product_id, $page_index, $count_pre_page, false);

        $stat_info = HtProductComment::getStatInfo($product_id);

        EchoUtility::echoCommonMsg(200, '',
                                   array('comments' => Converter::convertModelToArray($comments), 'stat' => $stat_info));
    }

    public function actionComment()
    {
        $product_id = $this->getProductID();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($request_method == 'get') {
        } elseif ($request_method == 'post') {
            $data = $this->getPostJsonData();
            $comment_id = (int)$data['comment_id'];
            if (empty($comment_id)) {
                $comment = new HtProductComment();

                ModelHelper::fillItem($comment, $data,
                                      array('customer_id', 'hitour_service_level', 'supplier_service_level', 'content'));
                $comment['insert_time'] = date('Y:m:d H:i:s');
                $comment['product_id'] = $product_id;

                $result = $comment->insert();

                EchoUtility::echoMsgTF($result, '添加', array('comment' => Converter::convertModelToArray($comment),
                                                            'stat'    => HtProductComment::getStatInfo($product_id)));
            } else {
                $comment = HtProductComment::model()->findByPk($comment_id);
                $result = ModelHelper::updateItem($comment, $data,
                                                  array('customer_id', 'hitour_service_level', 'supplier_service_level', 'content'));

                EchoUtility::echoMsgTF($result == 1, '更新',
                                       array('stat' => HtProductComment::getStatInfo($data['product_id'])));
            }
        } elseif ($request_method == 'delete') {
            $comment_id = (int)$this->getParam('comment_id');

            $result = HtProductComment::model()->deleteByPk($comment_id);
            HtProductComment::clearCache($product_id);

            EchoUtility::echoMsgTF($result > 0, '删除', ['stat' => HtProductComment::getStatInfo($this->getProductID())]);
        }
    }

    public function actionGetCustomer()
    {
        $product_id = $this->getParam('product_id');

        $id_array = $this->getCustomerIDs($product_id);

        if (count($id_array) > 0) {
            $random = $this->pickUpCustomerIdRandom($product_id, $id_array);
            $customer = HtCustomer::model()->findByPk($id_array[$random]);
            EchoUtility::echoCommonMsg(200, '', $customer);
        } else {
            EchoUtility::echoCommonMsg(400, '查询用户失败', []);
        }
    }

    private function getCustomerIDs($product_id)
    {
        $key = 'CACHE_KEY_ORDER_CUSTOMER_IDS_ALL';
        $result = $this->getCached($key);
        if(!empty($result)) {
            return $result;
        }

        $c = new CDbCriteria();
        $c->addCondition('status_id=3');
        $c->select = 'customer_id';
        $c->distinct = true;
        $orders = HtOrder::model()->findAll($c);
        $id_array = ModelHelper::getList($orders, 'customer_id');

//        $customer_ids = HtSetting::model()->find("`key` = 'virtual_user_ids'");
//        if (!empty($customer_ids)) {
//            $virtual_id_array = explode(",", $customer_ids["value"]);
//            $id_array = array_merge($id_array, $virtual_id_array);
//        }

        $this->setCache($key, $id_array, 12*60*60);

        return $id_array;
    }

    public function actionGetStatInfo()
    {
        $result = HtProductComment::getStatInfo($this->getProductID());
        EchoUtility::echoCommonMsg(200, '', $result);
    }

    private function getProductID()
    {
        return (int)$this->getParam('product_id');
    }

    private function pickUpCustomerIdRandom($product_id, $id_array)
    {
        // TODO this function may recycle forever!!!
        $random = mt_rand(0, count($id_array) - 1);
        $comments = HtProductComment::model()->findAll("product_id = " . $product_id);
        if (!empty($comments)) {
            foreach ($comments as $item) {
                if ($item['customer_id'] == $id_array[$random]) {
                    return $this->pickUpCustomerIdRandom($product_id, $id_array);
                }
            }
        }

        return $random;
    }
} 