<?php
/**
 * Created by PhpStorm.
 * User: Veaer
 * Date: 15/5/29
 * Time: 下午6:25
 */

class StoryCommentController extends BaseController {

    public function actionGetCommentByStoryId() {
        $story_id = $this->getParam('story_id');
        $comments = Converter::convertModelToArray(HiStory::model()->with('customer', 'comments.customer')->findByPk($story_id));
        EchoUtility::echoMsgTF(1, '获取段子评论', $comments);
    }

    public function actionGetCommentBySectionId() {
        $section_id = $this->getParam('section_id');
        $comments = Converter::convertModelToArray(HiStorySection::model()->with('customer', 'comments.customer')->findByPk($section_id));
        EchoUtility::echoMsgTF(1, '获取段子评论', $comments);
    }

    public function actionAddComment() {
        $section_id = $this->getParam('section_id');
        $story_id = $this->getParam('story_id');
        $customer_id = $this->getParam('customer_id');
        $content = $this->getParam('content');
        $orig_customer_id = $this->getParam('orig_customer_id');
        $newComment = new HiStoryComment();
        $newComment['customer_id'] = $customer_id;
        if(isset($section_id)) {
            $newComment['section_id'] = $section_id;
        }
        if(isset($story_id)) {
            $newComment['story_id'] = $story_id;
        }
        $newComment['orig_customer_id'] = $orig_customer_id;
        $newComment['content'] = $content;
        $newComment['insert_time'] = date('Y-m-d H:i:s', time());
        $newComment->insert();
        $comment = Converter::convertModelToArray(HiStoryComment::model()->with('customer')->findByPk($newComment->getPrimaryKey()));
        EchoUtility::echoMsgTF(1, '插入评论', $comment);
    }


}