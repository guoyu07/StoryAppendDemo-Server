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

    public function actionAddArticleDate() {
        $articles = HiPushArticle::model()->findAll();
        foreach($articles as $k=> $v) {
            $articles[$k]['article_date'] = date('d M Y', strtotime('+0Month+'. (925 - $k - 35) . 'Days'));
            $articles[$k]->update();
        }
        EchoUtility::echoMsgTF(1, 'aa');
//        for($i=1;$i<=34;$i++) {
//            $time = date('d F Y', strtotime('+0Month+'. ($i - 36) . 'Days'));
//            echo $time;
//        }
    }

    public function actionFavouriteArticle() {
        $article_id = $this->getParam('article_id');
        $customer_id = $this->getParam('customer_id');
        $ids = HiPushArticleFavourite::model()->findByAttributes(array('customer_id' => $customer_id));
        $result = array();
        $flag = array();
        if(empty($ids)) {
            $ids = new HiPushArticleFavourite();
            $ids['customer_id'] = $customer_id;
            $article_ids = array();
            array_push($article_ids, $article_id);
            $ids['article_ids'] = json_encode($article_ids);
            $result = $ids->insert();
            $flag = 1;
        } else {
            $ids_array = json_decode($ids['article_ids']);
            if(in_array($article_id, $ids_array)) {
                foreach($ids_array as $k=>$v) {
                    if($v == $article_id) {
                        unset($ids_array[$k]);
                    }
                }
                $ids['article_ids'] = json_encode($ids_array);
                $result = $ids->update();
                $flag = 0;
            } else {
                array_push($ids_array, $article_id);
                $ids['article_ids'] = json_encode($ids_array);
                $result = $ids->update();
                $flag = 1;
            }
        }
        EchoUtility::echoMsgTF($result, '喜欢', $flag);
    }

    public function actionGetArticleById() {
        $article_id = $this->getParam('article_id');
        $customer_id = $this->getParam('customer_id');
        $article = Converter::convertModelToArray(HiPushArticle::model()->with('sections')->findByPk($article_id));
        $c = new CDbCriteria();
        $c->addCondition("article_ids LIKE '%\"" . (int)$article_id . "\"%' and customer_id = " . $customer_id);
        $article['is_favourite'] = HiPushArticleFavourite::model()->count($c);
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