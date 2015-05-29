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
            $story = new HiStorySection();
            $story['content'] = '这一条绝逼是手动插的';
            $story['story_id'] = 1;
            $story['customer_id'] = 1;
            $story['section_layer'] = $i;
            $story['insert_time'] = date('Y-m-d H:i:s',time());
            $story->insert();
        }
        EchoUtility::echoMsgTF(true, '手动插入');
    }

    public function actionGetStoryById() {
        $story_id = $this->getParam('story_id');
        $customer_id = $this->getParam('customer_id');
        $story = Converter::convertModelToArray(HiStory::model()->with('customer', 'section.customer')->findByPk($story_id));
        if(isset($story['section'])) {
            $story['section'] = $this->checkFirstSection($story['section']);
            $story['section'] = $this->checkEndSection($story['section']);
            $story['section'] = $this->checkLayerSection($story['section']);
        }
        $c = new CDbCriteria();
        $c->addCondition("story_ids LIKE '%\"" . (int)$story_id . "\"%' and customer_id = " . $customer_id);
        $story['is_favourite'] = HiStoryFavourite::model()->count($c);
        EchoUtility::echoMsgTF(1, '获取段子', $story);
    }

    public function actionFavouriteStory() {
        $story_id = $this->getParam('story_id');
        $customer_id = $this->getParam('customer_id');
        $ids = HiStoryFavourite::model()->findByAttributes(array('customer_id' => $customer_id));
        $result = array();
        $flag = array();
        if(empty($ids)) {
            $ids = new HiStoryFavourite();
            $ids['customer_id'] = $customer_id;
            $story_ids = array();
            array_push($story_ids, $story_id);
            $ids['story_ids'] = json_encode($story_ids);
            $result = $ids->insert();
            $story = HiStory::model()->findByPk($story_id);
            $story['follow_count'] = (int)$story['follow_count'] + 1;
            $story->update();
            $flag = 1;
        } else {
            $ids_array = json_decode($ids['story_ids']);
            if(in_array($story_id, $ids_array)) {
                foreach($ids_array as $k=>$v) {
                    if($v == $story_id) {
                        unset($ids_array[$k]);
                    }
                }
                $ids['story_ids'] = json_encode($ids_array);
                $result = $ids->update();
                $story = HiStory::model()->findByPk($story_id);
                $story['follow_count'] = (int)$story['follow_count'] - 1;
                $story->update();
                $flag = 0;
            } else {
                array_push($ids_array, $story_id);
                $ids['story_ids'] = json_encode($ids_array);
                $result = $ids->update();
                $story = HiStory::model()->findByPk($story_id);
                $story['follow_count'] = (int)$story['follow_count'] + 1;
                $story->update();
                $flag = 1;
            }
        }
        EchoUtility::echoMsgTF($result, '喜欢', $flag);
    }

    public function actionGetNextSectionGroup() {
        $parent_id = $this->getParam('parent_id');
        $section_layer = $this->getParam('section_layer');
        $section_group = $this->getParam('section_group');
        $section = Converter::convertModelToArray(HiStorySection::model()->with('customer')->findByAttributes(array('parent_id' => $parent_id, 'section_layer' => $section_layer, 'section_group' => (int)$section_group + 1)));
        $section['is_first'] = 0;
        $section = $this->checkEndSection($section);
        $section = $this->checkLayerSection($section);
        EchoUtility::echoMsgTF(1, '获取分组', $section);
    }

    public function actionGetBeforeSectionGroup() {
        $parent_id = $this->getParam('parent_id');
        $section_layer = $this->getParam('section_layer');
        $section_group = $this->getParam('section_group');
        $section = Converter::convertModelToArray(HiStorySection::model()->with('customer')->findByAttributes(array('parent_id' => $parent_id, 'section_layer' => $section_layer, 'section_group' => (int)$section_group - 1)));
        $section['is_end'] = 0;
        $section = $this->checkFirstSection($section);
        $section = $this->checkLayerSection($section);
        EchoUtility::echoMsgTF(1, '获取分组', $section);
    }

    public function actionGetNextLayerSection() {
        $parent_id = $this->getParam('parent_id');
        $section_layer = $this->getParam('section_layer');
        $section_group = $this->getParam('section_group');
        $section = Converter::convertModelToArray(HiStorySection::model()->with('customer')->findByAttributes(array('parent_id' => $parent_id)));
        $section = $this->checkFirstSection($section);
        $section = $this->checkEndSection($section);
        $section = $this->checkLayerSection($section);
        EchoUtility::echoMsgTF(1, '获取分组', $section);
    }

    //region 检测是否有下一个
    public function checkFirstSection($section) {
        $first_c = new CDbCriteria();
        $first_c->addCondition('section_group < ' . $section['section_group'] . ' and section_layer = ' . $section['section_layer'] . ' and parent_id = ' . $section['parent_id'] );
        $section['is_first'] = (HiStorySection::model()->count($first_c)) == 0 ? 1 : 0;
        return $section;
    }

    public function checkEndSection($section) {
        $end_c = new CDbCriteria();
        $end_c->addCondition('section_group > ' . $section['section_group'] . ' and section_layer = ' . $section['section_layer'] . ' and parent_id = ' . $section['parent_id'] );
        $section['is_end'] = (HiStorySection::model()->count($end_c)) == 0 ? 1 : 0;
        return $section;
    }

    public function checkLayerSection($section) {
        $layer_c = new CDbCriteria();
        $layer_c->addCondition('parent_id = ' . $section['section_id']);
        $section['has_layer'] = (HiStorySection::model()->count($layer_c)) > 0 ? 1 : 0;
        $section['is_bottom'] = 1;
        return $section;
    }
    //endregion

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
        EchoUtility::echoMsgTF(1, '获取段子列表', $result);
    }
}