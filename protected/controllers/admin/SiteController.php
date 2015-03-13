<?php

class SiteController extends AdminController
{

    public function filters()
    {
        return array(); // remove filter access control to avoid redirect loop
    }

    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        if (Yii::app()->user->isGuest) {
            $this->redirect($this->createUrl('site/login'));
        } else {
            $this->redirect($this->createUrl('product/index'));
        }
    }

    /**
     * This is the test action for all backend CSS modules
     */
    public function actionTest()
    {
        $this->layout = '//layouts/common';
        $this->base_url = Yii::app()->params['WEB_PREFIX'];
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'upload' => $this->createUrl('site/testUpload'),
                'verifyPhone' => $this->createUrl('mobile/verifyPhone', array(), '', false),
            )
        );
        $this->render('test');
    }

    public function actionViewMap()
    {
        $this->layout = '//layouts/common';
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'upload' => $this->createUrl('site/testUpload'),
                'verifyPhone' => $this->createUrl('mobile/verifyPhone', array(), '', false),
            )
        );
        $this->render('map');
    }

    public function actionTestUpload()
    {
        sleep(3);

        echo CJSON::encode(array('code' => 200, 'msg' => '', 'data' => 'http://hitour.qiniudn.com/ab1794ac5e76d9d016e39e965f88d2a0.jpg?imageView/5/w/180/h/110'));
    }

    public function actionGrid()
    {
        $this->layout = '//layouts/common';
        $this->base_url = Yii::app()->params['WEB_PREFIX'];
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'fetchData' => $this->createUrl('site/testGrid')
            )
        );
        $this->render('grid');
    }

    public function actionTestGrid()
    {
        sleep(1);

        echo CJSON::encode(array('code' => 200, 'msg' => '', 'data' => array(
            total_count => 120,
            data => array(
                array(
                    'product_id' => 1,
                    'product_name' => '我是产品1',
                    'price' => '528',
                    'orig_price' => '700'
                ),
                array(
                    'product_id' => 2,
                    'product_name' => '我是产品2',
                    'price' => '300',
                    'orig_price' => '305'
                )
            )
        )));
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->homeUrl . 'admin/product/index');
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl . 'admin/site/login');
    }
}