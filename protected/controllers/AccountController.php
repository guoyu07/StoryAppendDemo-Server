<?php

/**
 * Created by PhpStorm.
 * User: Veaer
 * Date: 14-5-5
 * Time: 上午10:39
 */
class AccountController extends BaseController {

    public function actionIndex() {
        $data = array();
        $data['test'] = '这是接口测试';
        EchoUtility::echoMsgTF(true, '获取文章列表', $data);
    }

    public function actionTest() {
        $user = HiUser::model()->findByPk(1);
        $user['screen_name'] = 'bu';
        $user['password'] = 'er';
        $user['total']= mt_rand(0,100);
        $user->update();
        EchoUtility::echoMsgTF(true, '登录', $user);
    }

    public function  actionLogin() {
        $screen_name = $this->getParam('name');
        $avatar_url = $this->getParam('avatar');
        $sex = $this->getParam('sex');
        $city = $this->getParam('city');
        $province = $this->getParam('province');
        $birthday = $this->getParam('birthday');
        $openid = $this->getParam('openid');
        $bind_third = $this->getParam('bind_third');

        $customer = HiCustomer::model()->findByAttributes(array('openid' => $openid, 'bind_third' => $bind_third));
        if(empty($customer)) {
            $customer = new HiCustomer();

            $customer['screen_name'] = $screen_name;
            $customer['avatar_url'] = $avatar_url;
            $customer['sex'] = $sex;
            $customer['city'] = $city;
            $customer['province'] = $province;
            $customer['birthday'] = $birthday;
            $customer['openid'] = $openid;
            $customer['bind_third'] = $bind_third;

            $customer->insert();
        } else {
            $customer['screen_name'] = $screen_name;
            $customer['avatar_url'] = $avatar_url;
            $customer['sex'] = $sex;
            $customer['city'] = $city;
            $customer['province'] = $province;
            $customer['birthday'] = $birthday;
            $customer['openid'] = $openid;
            $customer['bind_third'] = $bind_third;
            $customer->update();
        }

        EchoUtility::echoMsgTF(true, '登录', $customer);
    }

    public function actionGetFavouriteStory() {
        $customer_id = $this->getParam("customer_id");
        $ids = HiStoryFavourite::model()->findByAttributes(array('customer_id' => $customer_id));
        $result = array();
        if(!empty($ids)){
            $c = new CDbCriteria();
            $c->addInCondition('story_id', json_decode($ids['story_ids']));
            $result = Converter::convertModelToArray(HiStory::model()->with('customer')->findAll($c));
        }
        EchoUtility::echoMsgTF(true, '获取收藏的故事', $result);
    }

    public function actionGetFavouritePushArticle() {
        $customer_id = $this->getParam("customer_id");
        $ids = HiPushArticleFavourite::model()->findByAttributes(array('customer_id' => $customer_id));
        $result = array();
        if(!empty($ids)){
            $c = new CDbCriteria();
            $c->addInCondition('article_id', json_decode($ids['article_ids']));
            $result = Converter::convertModelToArray(HiPushArticle::model()->findAll($c));
        }
        EchoUtility::echoMsgTF(true, '获取收藏的故事', $result);
    }

}