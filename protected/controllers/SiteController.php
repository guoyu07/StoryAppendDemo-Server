<?php

class SiteController extends Controller
{
    public $resource_refs = 'site.res';

    public function actionAbout()
    {
        $data = $this->initData();
        $this->render('about', $data);
    }

    public function actionContact()
    {
        $data = $this->initData();
        $this->render('contact', $data);
    }

    public function actionError()
    {
        $data = $this->initData();
        $error_page_id = (int)Yii::app()->request->getParam('error_page_id');
        $error_page = HtErrorPage::model()->getRandomErrorPage($error_page_id);
        $data = array_merge($data,$error_page);

        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'home' => $this->createUrl('home/index'),
                'target' => $this->createUrl('city/index',
                                             array('city_name' => $error_page['city']['en_name'], 'country_name' => $error_page['city']['country_name'])),
                'product' => $this->createUrl('product/index',array('product_id'=>$error_page['product_id']))
            )
        );

        $error = Yii::app()->errorHandler->error;
        if (Yii::app()->request->isAjaxRequest) {
            echo $error['message'];
        } else {
            $this->render('error', $data);
        }
    }
}