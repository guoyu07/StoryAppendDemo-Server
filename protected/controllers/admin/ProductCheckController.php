<?php

/**
 * Created by hitour.server.
 * User: xingminglister
 * Date: 1/7/15
 * Time: 4:52 PM
 */
class ProductCheckController extends AdminController
{

    public $layout = '//layouts/common';

    public function actionIndex()
    {
        $this->pageTitle = '商品检查';

        $request_urls = array(
            'validateAll' => $this->createUrl('productCheck/validateAll'),
            'checkDateRule' => $this->createUrl('productCheck/checkDateRule'),
            'productEdit' => $this->createUrl('product/detail', ['product_id' => '']),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );
        $this->render('index');
    }

    public function actionValidateAll()
    {
        $key = 'CACHE_KEY_PRODUCT_VALIDATE_ALL';
        $result = $this->getCached($key);
        if (empty($result)) {
            $data = HtProduct::validateAll();

            $result = ['check_time' => date('m-d H:i:s'), 'data' => $data];

            $this->setCache($key, $result, 1 * 60 * 60);
        }

        EchoUtility::echoCommonMsg(200, '', $result);
    }

    public function actionCheckDateRule()
    {
        $key = 'CACHE_KEY_PRODUCT_CHECK_DATE_RULE';
        $result = $this->getCached($key);
        if (empty($result)) {
            $to_date = date('Y-m-d', strtotime('+14Days'));

            $products = HtProduct::model()->with('date_rule',
                                               'description')->findAll(['condition' => 'p.status = 3 AND pdr.need_tour_date = 1 AND pdr.to_date < "' . $to_date . '"']);

            $data = [];
            foreach ($products as $product) {
                $data[] = [
                    'product_id' => $product['product_id'],
                    'name'       => $product['description']['name'],
                    'to_date'    => $product['date_rule']['to_date'],
                ];
            }

            $result = ['check_time' => date('m-d H:i:s'), 'data' => $data];

            $this->setCache($key, $result, 1 * 60 * 60);
        }

        EchoUtility::echoCommonMsg(200, '', $result);
    }

    public function actionPricePlanCheck()
    {
        $result = HtProductPricePlan::model()->ProductPricePlanCheck();


        EchoUtility::echoMsgTF(true, '快到期产品列表', $result);
    }

    public function actionTourOperation()
    {
        $result = HtProductTourOperation::model()->TourOperationCheck();

        EchoUtility::echoMsgTF(true, '快到期产品检查', $result);
    }
}