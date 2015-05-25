<?php
/**
 * Created by PhpStorm.
 * User: Veaer
 * Date: 15/4/19
 * Time: 下午10:41
 */

class PushArticleController extends BaseController {

    public function actionGetCurrentArticle() {
        $article = HiPushArticle::model()->findByPk(Yii::app()->time_service->getIndex());
        $result = Converter::convertModelToArray($article);
        $before_article = HiPushArticle::model()->findByPk($article['article_id'] - 1);
        $result['before_image'] = $before_article['cover_image'];
        EchoUtility::echoMsgTF($result, '获取当前文章', $result);
    }

    public function actionGetArticleById() {
        $article_id = $this->getParam('article_id');
        $article = Converter::convertModelToArray(HiPushArticle::model()->with('sections')->findByPk($article_id));
        EchoUtility::echoMsgTF(1, '获取文章', $article);
    }

    public function actionGetArticleList() {
        $start = $this->getParam('start');
        $num = $this->getParam('num');
        $sql = 'select * from `hi_push_article`' . ' where article_id <=' . Yii::app()->time_service->getIndex() . ' order by article_id DESC limit ' . $start . ',' . $num;
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);

        $articles = $command->queryAll();
        $result = array();
        if(count($articles) < $num) {
            $result['has_more'] = false;
        } else {
            $result['has_more'] = true;
        }
        $result['article_list'] = $articles;
        $result['success'] = true;
        EchoUtility::echoMsgTF(1, '获取文章列表', $result);
    }
}