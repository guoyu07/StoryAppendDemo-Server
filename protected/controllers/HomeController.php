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

    public function actionGetHomeData() {
        $result = array();
        $story_c = new CDbCriteria();
        $story_c->addCondition('follow_count != 0');
        $story_c->order = 'follow_count DESC';
        $stories = Converter::convertModelToArray(HiStory::model()->with('customer')->findAll($story_c));
        $result['story'] = $stories[0];
        $article = Converter::convertModelToArray(HiPushArticle::model()->findByPk(Yii::app()->time_service->getIndex()));
        $result['article'] = $article;
        $question = Converter::convertModelToArray(HiQuestion::model()->findByPk(2));
        $question['cover_image'] = 'http://77fkpo.com5.z0.glb.clouddn.com/603b2cffffeb7c5c950a4c9517e8ee9e.jpg';
        $result['question'] = $question;
        $book = Converter::convertModelToArray(HiBook::model()->with('customer')->findByPk(1));
        $result['book'] = $book;
        EchoUtility::echoMsgTF(true, '获取首页数据', $result);
    }
}