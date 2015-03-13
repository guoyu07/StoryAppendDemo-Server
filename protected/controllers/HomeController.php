<?php

/**
 * Created by PhpStorm.
 * User: godsong
 * Date: 14-5-5
 * Time: 上午10:39
 */
class HomeController extends Controller
{
    public $resource_refs = 'home.res';
    public $staticData;

    public function actionIndex()
    {
        $data = array();
//        $data = $this->initData();
//        $this->current_page = 'home';
//        $this->header_info = array();
//        $this->request_urls = array_merge(
//            $this->request_urls,
//            array(
//                'getCities' => $this->createUrl('home/cities'),
//                'getCarousel' => $this->createUrl('home/carousel'),
//                'getRecommend' => $this->createUrl('home/recommend'),
//            )
//        );
//
//        $seo_setting = HtSeoSetting::model()->findHomeSeoSetting();
//        $this->initDataBySEOSetting($seo_setting);
//
//        $this->staticData = array(
//            str_replace(array("/","?","=","&"),'_',$this->request_urls['getCities'])=>$this->datamation($this->actionCities(true)),
//            str_replace(array("/","?","=","&"),'_',$this->request_urls['getRecommend'])=>$this->datamation($this->actionRecommend(true)),
//            str_replace(array("/","?","=","&"),'_',$this->request_urls['getCarousel'])=>$this->datamation($this->actionCarousel(true)),
//        );

//        $this->render('main', $data);
        $data['test'] = '这是接口测试';
        EchoUtility::echoMsgTF(true, '获取文章列表', $data);
    }

    public function actionTestInt()
    {
        $data = array();
        $data['test'] = '这是接口第二次测试';
        EchoUtility::echoMsgTF(true, '获取文章列表', $data);
    }

    public function actionRecommend($rawData = false)
    {
        $recommend_groups = HtHomeRecommend::model()->findAllWithItemsCityCached();

        foreach ($recommend_groups as &$hg) {
            if (in_array($hg['type'], [HtHomeRecommend::TYPE_PRODUCT, HtHomeRecommend::TYPE_ACTIVITY])) {
                foreach ($hg['items'] as &$p) {
                    $p['show_prices'] = HtProductPricePlan::model()->getShowPrices($p['product_id']);
                    $p['link_url'] = $this->createUrl('product/index', array('product_id' => $p['product_id']));
                    $p['activity_info'] = Yii::app()->activity->getActivityInfo($p['product_id']);
                }
            }
        }

        if ($rawData) {
            return $recommend_groups;
        } else {
            EchoUtility::echoJson($recommend_groups);
        }
    }

    public function actionActivities()
    {
        $activities = Yii::app()->activity->getMobileHomeActivities();
        EchoUtility::echoJson($activities);
    }

    public function actionCities($rawData = false)
    {
        $continents = HtContinent::model()->findAllWithContriesCities();
        foreach ($continents as &$continent) {
            foreach ($continent['countries'] as &$country) {
                //封面图
                $image = HtCountryImage::model()->findByPk($country['country_code']);
                $country['cover_image'] = $image['cover_url'];
                $country['cover_image_mobile'] = $image['mobile_url'];
            }
        }
        if ($rawData) {
            return $continents;
        } else {
            EchoUtility::echoJson($continents);
        }

    }

    public function actionCitiesInGroup($rawData = false)
    {
        $continents = HtContinent::model()->findAllWithContriesCities();

        $city_groups = HtCityGroup::model()->getAllCached();

        foreach ($continents as &$continent) {
            foreach ($continent['countries'] as &$country) {
                if ($this->hasGroup($country['country_code'], $city_groups)) {
                    $country['city_groups'] = $this->getGroups($country, $city_groups);
                } else {
                    $cities = $country['cities'];
                    $country['city_groups'] = array(array('name' => '全部', 'cities' => $cities));
                }
                //封面图
                $image = HtCountryImage::model()->findByPk($country['country_code']);
                $country['cover_image'] = $image['cover_url'];
                $country['cover_image_mobile'] = $image['mobile_url'];
                unset($country['cities']);
            }
        }


        if ($rawData) {
            return $continents;
        } else {
            EchoUtility::echoJson($continents);
        }
    }

    private function getGroups($country, $city_groups)
    {
        $result = array();
        foreach ($city_groups as $city_group) {
            if ($country['country_code'] == $city_group['country_code']) {
                $result[] = array('name' => $city_group['name'],
                    'cities' => $this->getCities($city_group['city_codes'], $country['cities']));
            }
        }

        return $result;
    }

    private function getCities($city_codes, $cities)
    {
        $result = array();
        $city_code_list = explode(",", $city_codes);
        foreach ($cities as $city) {
            if (in_array($city['city_code'], $city_code_list)) {
                array_push($result, $city);
            }
        }

        return $result;
    }

    private function hasGroup($country_code, $city_groups)
    {
        $found = false;
        foreach ($city_groups as $city_group) {
            if ($country_code == $city_group['country_code']) {
                $found = true;

                break;
            }
        }

        return $found;
    }

    public function actionCarousel($rawData = false)
    {
        $carousels = HtHomeCarousel::model()->getAllOnline();

        if ($rawData) {
            return $carousels;
        } else {
            EchoUtility::echoJson($carousels);
        }

    }
}