<?php

class ErrorPageController extends AdminController
{

    public $layout = '//layouts/common';

    public function actionIndex()
    {
        $this->pageTitle = '错误页面列表';

        $request_urls = array(
            'getErrorPageList' => $this->createUrl('errorPage/getErrorPageList'),
            'addErrorPage' => $this->createUrl('errorPage/addErrorPage'),
            'edit' => $this->createUrl('errorPage/edit', array('error_page_id' => ''))
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('index');
    }

    public function actionEdit()
    {
        $this->pageTitle = '错误页面编辑';

        $page_id = $this->getParam("error_page_id");

        $request_urls = array(
            'addErrorPage' => $this->createUrl('errorPage/addErrorPage'),
            'getErrorPageDetail' => $this->createUrl('errorPage/getErrorPageDetail', array("error_page_id" => $page_id)),
            'uploadBGImage' => $this->createUrl('errorPage/uploadBGImage', array("error_page_id" => $page_id)),
            'uploadMobileBGImage' => $this->createUrl('errorPage/uploadMobileBGImage', array("error_page_id" => $page_id)),
            'saveErrorPage' => $this->createUrl('errorPage/saveErrorPage', array("error_page_id" => $page_id)),
            'bindingProduct' => $this->createUrl('errorPage/bindingProduct', array("product_id" => '')),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('edit');
    }

    public function actionGetErrorPageList()
    {
        $result['data'] = HtErrorPage::model()->findAll();
        EchoUtility::echoMsgTF(true, '获取Error Page列表', $result);
    }

    public function actionGetErrorPageDetail()
    {
        $page_id = $this->getParam("error_page_id");
        $result = HtErrorPage::model()->findByPk($page_id);
        if (!empty($result)) {
            EchoUtility::echoMsgTF(true, '获取Error Page detail', $result);
        } else {
            EchoUtility::echoMsgTF(false, '获取Error Page detail');
        }
    }

    public function actionAddErrorPage()
    {
        $data = $this->getPostJsonData();
        $item = new HtErrorPage();
        $item['status'] = 0;
        ModelHelper::fillItem($item, $data,array('product_id','product_name','product_description','city_code','country_code','status'));
        $result = $item->insert();
        EchoUtility::echoMsgTF($result, '添加', array('error_page_id'=>$item->getPrimaryKey()));
    }

    public function actionBindingProduct()
    {
        $product_id = $this->getParam('product_id');
        $product = HtProduct::model()->with(['description', 'city.country'])->findByPk($product_id);
        EchoUtility::echoMsgTF(true, '获取绑定商品信息', Converter::convertModelToArray($product));
    }

    public function actionUploadBGImage()
    {
        //传入error_page_id和img_url，返回成功失败
        $error_page_id = $this->getErrorPageID();
        $error_page = HtErrorPage::model()->findByPk($error_page_id);
        if (empty($error_page)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $error_page_id . '的错误页面');
            return;
        }

        $to_dir = 'image/upload/error_page/' . $error_page_id . '/web_img/';
        $result = FileUtility::uploadFile($to_dir);

        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $error_page['bg_image_url'] = $image_url;
            $result = $error_page->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionUploadMobileBGImage()
    {
        $error_page_id = $this->getErrorPageID();
        $error_page = HtErrorPage::model()->findByPk($error_page_id);
        if (empty($error_page)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $error_page_id . '的错误页面');
            return;
        }

        $to_dir = 'image/upload/error_page/' . $error_page_id . '/mobile_img/';
        $result = FileUtility::uploadFile($to_dir);

        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $error_page['mobile_image_url'] = $image_url;
            $result = $error_page->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionUpdateStatus()
    {

    }

    public function actionSaveErrorPage()
    {
        $data = $this->getPostJsonData();
        $error_page_id = $this->getErrorPageID();
        $item = HtErrorPage::model()->findByPk($error_page_id);
        $result = ModelHelper::updateItem($item, $data);
        EchoUtility::echoMsgTF($result, '保存', Converter::convertModelToArray($item));
    }

    private function getErrorPageID()
    {
        return $this->getParam('error_page_id');
    }
}
