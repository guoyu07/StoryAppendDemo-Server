<?php

/**
 * Created by PhpStorm.
 * User: godsong
 * Date: 14-5-5
 * Time: 上午10:39
 */
class AccountController extends Controller
{

    public function actionIndex()
    {
        $data = array();
        $data['test'] = '这是接口测试';
        EchoUtility::echoMsgTF(true, '获取文章列表', $data);
    }

    public function actionTest()
    {
        $user = HiUser::model()->findByPk(1);
        $user['screen_name'] = 'bu';
        $user['password'] = 'er';
        $user['total']= mt_rand(0,100);
        $user->update();
        EchoUtility::echoMsgTF(true, '登录', $user);
    }
}