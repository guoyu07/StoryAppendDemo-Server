<?php

/**
 * Created by PhpStorm.
 * User: godsong
 * Date: 14-5-5
 * Time: 上午10:39
 */
class HomeController extends Controller
{

    public function actionIndex()
    {
        $data = $command->queryAll();

        EchoUtility::echoMsgTF(true, '获取文章列表', $data);
    }

    public function actionTestInt()
    {
        $data = array();
        $data['test'] = '这是接口第二次测试';
        EchoUtility::echoMsgTF(true, '获取文章列表', $data);
    }
}