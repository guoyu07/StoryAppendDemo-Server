<?php

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/12/14
 * Time: 1:16 PM
 */
class GroupController extends Controller
{
    public $resource_refs = 'group.res';
    public $staticData;

    public function actionIndex()
    {
        $group_id = (int)$this->getParam('group_id');

        $is_mobile = HTTPRequest::isMobile();

        $group = HtProductGroup::model()->findByPk($group_id);
        $city = HtCity::model()->getCityWithCityImage($group['city_code']);
        if ($is_mobile) {
                            $link_url = Yii::app()->createUrl('mobile' . '#/city/' . $city['city_code'] . '/g_' . $group['group_id']);
                        } else {

                            $link_url = Yii::app()->createUrl($city['country_name'] . '/' . $city['city_name'] . '/group/' . $group['group_id']);
                        }

        // redirect to city page
        $this->redirect($link_url, 301);

        return;

        $group_info = HtProductGroup::model()->getByPkWithCityCountry($group_id);

        if (empty($group_info)) {
            $this->redirect($this->createUrl('site/error'));
        }

        $this->goToGroup($group_info);
    }

    private function goToGroup($group_info)
    {
        $data = $this->initData();

        $this->staticData = array();
        $this->current_page = 'group';

        $this->header_info = array(
            'country' => array(
                'cn_name' => $group_info['city']['country']['cn_name'],
                'country_code' => $group_info['city']['country_code'],
                'link_url' => ''
            ),
            'city' => array(
                'cn_name' => $group_info['city']['cn_name'],
                'city_code' => $group_info['city_code'],
                'link_url' => ''
            )
        );
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'getOneGroup' => $this->createUrl('city/cityInfo',
                                                  ['city_code' => $group_info['city_code'], 'type' => 'group', 'id' => $group_info['group_id']]),
                'cityLink' => Yii::app()->urlManager->createUrl('city/index',
                                                                ['city_name' => $group_info['city']['en_name'], 'country_name' => $group_info['city']['country']['en_name']])
            )
        );

        $seo_setting = HtSeoSetting::model()->findByGroupCode($group_info['group_id']);
        $this->initDataBySEOSetting($seo_setting);

        $this->render('main', $data);
    }
}
