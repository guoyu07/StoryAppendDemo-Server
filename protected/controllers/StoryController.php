<?php
/**
 * Created by PhpStorm.
 * User: Veaer
 * Date: 15/4/19
 * Time: 下午10:41
 */

class StoryController extends BaseController {

    public function actionAdd() {

        for($i = 0; $i < 10; $i++) {
            $story = new HiStory();
            $story['title'] = '这一条绝逼是手动插的';
            $story['owner_id'] = 1;
            $story['insert_time'] = date('Y-m-d H:i:s',time());
            $story->insert();
        }
        EchoUtility::echoMsgTF(true, '手动插入');
    }

    public function actionGetCurrentArticle() {
        $article = HiPushArticle::model()->findByPk(Yii::app()->TimeService->getIndex());
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

    public function actionGetStoryList() {
        $start = $this->getParam('start');
        $num = $this->getParam('num');
        $c = new CDbCriteria();
        $c->offset = $start;
        $c->limit = $num;
        $stories = Converter::convertModelToArray(HiStory::model()->with('customer')->findAll($c));
        $result = array();
        if(count($stories) < $num) {
            $result['has_more'] = false;
        } else {
            $result['has_more'] = true;
        }
        $result['story_list'] = $stories;
        $result['success'] = true;
        EchoUtility::echoMsgTF(1, '获取故事列表', $result);
    }
}