<?php

/**
 * Created by PhpStorm.
 * User: Veaer
 * Date: 14-5-5
 * Time: 上午10:39
 */
class HomeController extends BaseController {

    public function actionIndex() {
        var_dump(Yii::app()->db);
//        EchoUtility::echoMsgTF(true, '获取文章列表', $data);
    }

    public function actionTestInt() {
        $data = array();
        $data['test'] = '这是接口第二次测试';
        EchoUtility::echoMsgTF(true, '获取文章列表', $data);
    }
}