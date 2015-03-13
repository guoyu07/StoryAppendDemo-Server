<?php

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/12/14
 * Time: 1:16 PM
 */
class CityController extends Controller
{
    const SHORT_NAME = 'CityController';
    public $resource_refs = 'city.res';
    public $staticData;
    private $city_code = '';

    public function actionIndex()
    {
        $country_name = str_replace('_', ' ', $this->getParam('country_name'));
        $city_name = str_replace('_', ' ', $this->getParam('city_name'));

        list($country_info, $city_info) = $this->getCountryCity($country_name, $city_name);

        if (empty($country_info) || empty($city_info)) {
            $this->redirect($this->createUrl('site/error'));
        }

        $data = $this->initData();
        $this->staticData = array();

        $this->city_code = $city_code = $city_info['city_code'];
        $this->current_page = 'city';

        $this->header_info = array(
            'country' => array(
                'cn_name'      => $country_info['cn_name'],
                'country_code' => $country_info['country_code'],
                'link_url'     => ''
            ),
            'city'    => array(
                'cn_name'   => $city_info['cn_name'],
                'city_code' => $city_info['city_code'],
                'link_url'  => ''
            ),
        );
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'getAllGroups'             => $this->createUrl('city/cityInfo', array('city_code' => $city_code)),
                'getOneTag'                => $this->createUrl('city/cityInfo',
                                                               array('city_code' => $city_code, 'type' => 'tag', 'part_info' => 'true', 'id' => '000')),
                'getOneTagComingFromGroup' => $this->createUrl('city/cityInfo',
                                                               array('city_code' => $city_code, 'type' => 'tag', 'id' => '000')),
                'search'                   => $this->createUrl('city/cityInfo',
                                                               array('city_code' => $city_code, 'type' => 'search')),
                'cityLink'                 => Yii::app()->urlManager->createUrl('city/index',
                                                                                ['city_name' => $city_info['city_name'], 'country_name' => $city_info['country_name']])
            )
        );

        //城市的SEO
        $seo_setting = HtSeoSetting::model()->findByCityCode($city_info['city_code']);
        $group_id = $this->getParam('tag_or_group');
        if ($group_id) {
            $this->request_urls['getAllGroups'] = $this->createUrl('city/cityInfo',
                                                                   array('city_code' => $city_code, 'type' => 'group', 'id' => $group_id));
            $seo_setting = HtSeoSetting::model()->findByGroupCode($group_id);
        }

        $this->initDataBySEOSetting($seo_setting);

        $this->render('city-v4', $data);
    }

    public function actionCity()
    {
        $city_code = $this->getParam('city_id');
        $city_info = HtCity::model()->findByPk($city_code);
        if (empty($city_info)) {
            $this->redirect($this->createUrl('site/error'));
        }
        $country_info = HtCountry::model()->findByPk($city_info['country_code']);
        if (empty($country_info)) {
            $this->redirect($this->createUrl('site/error'));
        }
        $country_name = str_replace(' ', '_', $country_info['en_name']);
        $city_name = str_replace(' ', '_', $city_info['en_name']);

        $this->redirect($this->createUrl('city/index',
                                         array('country_name' => $country_name, 'city_name' => $city_name)),
                        true, 301);
    }

    public function actionHotelplus()
    {
        $this->resource_refs = 'hotel_plus.res';
        $country_name = str_replace('_', ' ', $this->getParam('country_name'));
        $city_name = str_replace('_', ' ', $this->getParam('city_name'));

        list($country_info, $city_info) = $this->getCountryCity($country_name, $city_name);

        if (empty($country_info) || empty($city_info)) {
            $this->redirect($this->createUrl('site/error'));
        }

        $this->initData();

        $city_code = $city_info['city_code'];
        $result = Converter::convertModelToArray(HtCityHotelPlus::model()->findByPk($city_code));

        if (!empty($result)  && $result['status'] == 1) {

            $this->request_urls = array_merge(
                $this->request_urls,
                array(
                    'getHotelplusDetail' => $this->createUrl('city/hotelplusDetail',
                                                             array('city_code' => $city_code, 'promotion_id' => $result['promotion_id']))
                )
            );

            $seo_setting = HtSeoSetting::model()->findByPromotionId($result['promotion_id']);

            $this->initDataBySEOSetting($seo_setting);

            $this->render('hotel_plus');
        } else {
            $this->redirect($this->createUrl('site/error'));
        }
    }

    public function actionHotelplusDetail($city_code, $promotion_id)
    {
        //城市信息
        $result['city'] = HtCity::model()->getCityWithCityImage($city_code);
        $result['city']['country'] = HtCountry::model()->getByPk($result['city']['country_code']);
        $result['hotel_plus'] = HtPromotion::model()->fetchHotelplusDetail($promotion_id);

        EchoUtility::echoCommonMsg(200, '', $result);
    }

    public function actionTest()
    {
        $city_code = $this->getParam('city_code');
        $result = Converter::convertModelToArray(HtCityHotelPlus::model()->findByPk($city_code));

        if (!empty($result)  && $result['status'] == 1) {
            $data = HtPromotion::model()->fetchHotelplusDetail($result['promotion_id']);

            EchoUtility::echoJson($data);
        } else {
            $this->redirect($this->createUrl('site/error'));
        }
    }

    private function getCountryCity($country_name, $city_name)
    {
        $country_info = HtCountry::model()->getByEnName($country_name);

        $city_info = [];
        if (!empty($country_info)) {
            $city_info = HtCity::model()->getByCountryCodeEnName($country_info['country_code'], $city_name);
        }

        return [$country_info, $city_info];
    }

    //基本信息：seo等
    private function getCityBaseInfo(&$result, $city_code, $is_mobile = false, $type = '')
    {
        //城市信息
        $result['city'] = HtCity::model()->getCityWithCityImage($city_code);

        if (empty($result['city'])) {
            EchoUtility::echoCommonFailed('城市不存在！');

            return false;
        } else {
            $result['city']['country'] = HtCountry::model()->getByPk($result['city']['country_code']);
        }

        //分组tree
        if (!$is_mobile) {
            $result['group_tree'] = HtProductGroup::getGroupTree($city_code, $result['city']['country_name'],
                                                                 $result['city']['city_name'], $is_mobile);
        }

        //标签tree
        if (!($is_mobile && $type == 'group')) {
            list($top_tag, $result['tag_tree']) = HtProductTag::getTagTree($city_code, $is_mobile);
        }

        //V2tree
        $result['v2_tree'] = [];
        list ($top10, $hotel_bundle, $line) = HtProductGroup::getTop10AndHotelBundleAndLine($city_code);
        if ($top10) {
            $result['v2_tree'][] = $top10;
        }
        if ($hotel_bundle) {
            $result['v2_tree'][] = $hotel_bundle;
        }
        if ($line) {
            $result['v2_tree'][] = $line;
        }

        //Hotel Plus
        $hotel_plus = HtCityHotelPlus::model()->getCityPromotion($city_code);
        if (!empty($hotel_plus)) {
            $result['hotel_plus'] = $hotel_plus;
            $result['hotel_plus']['hotel_count'] = HtPromotion::getHotelCount($hotel_plus['promotion_id']);
            $result['hotel_plus']['promotion_url'] = $result['city']['link_url'] . '/hotel_plus';
//                $this->createUrl('city/hotelplus', array(
//                'country_name' => $result['city']['country']['en_name'],
//                'city_name' => $result['city']['en_name']
//            ));
        }

        $articles = HtArticle::model()->getCityArticles($city_code);
        if (!empty($articles['data'])) {
            $result['v2_tree'][] = ['id' => 'experience', 'label' => $articles['name']];
        }

        return true;
    }

    private function getArticlesFromOneColumn($city_code, $type = 1)
    {
        $articles = HtArticle::model()->getCityArticles($city_code, $type);

        if (!empty($articles['data'])) {
            foreach ($articles['data'] as &$a) {
                $a['link_url'] = $this->createUrl('column/index', ['column_id' => $a['article_id']]);
            }
        }

        return $articles;
    }

    private function getProductsFromOneGroup($city_code, $by_type = false, $group_id = false)
    {
        return HtProductGroup::getProductsFromOneGroup($city_code, $by_type, $group_id);
    }

    private function getProductsFromOneTag($city_code, $tag_id)
    {
        return HtProductTag::getProductsFromOneTag($city_code, $tag_id);
    }

    private function getArticleFromOneGroup($city_code, $group_id)
    {
        return HtArticle::getArticleFromOneGroup($city_code, $group_id);
    }

    private function assembleProductGroups(&$result, $city_code, $is_mobile)
    {
        $groups = array();
        //Get Groups First
        $query_result = Converter::convertModelToArray(HtProductGroup::model()->findAll([
            'condition' => 'city_code = "' . $city_code .'" AND type in (6, 99) AND status =2',
            'order' => 'type, display_order']));

        foreach ($query_result as &$g) {
            $one_group = $this->getProductsFromOneGroup($city_code, false, $g['group_id']);
            if (count($one_group['products']) > 0) {
                if ($is_mobile) {
                    $link_url = Yii::app()->createUrl('mobile' . '#/city/' . $city_code . '/g_' . $one_group['group_id']);
                } else {
                    $city = HtCity::model()->getCityWithCityImage($city_code);
                    $link_url = Yii::app()->createUrl($city['country_name'] . '/' . $city['city_name'] . '/group/' . $one_group['group_id']);
                }
                $one_group['link_url'] = $link_url;
                $groups[] = $one_group;
            }
        }

        //Get Tags If Groups Not Avail - Only For PC
        if (count($groups) == 0 && !$is_mobile) {
            foreach ($result['tag_tree'] as $g) { //Only Parent Tag
                $one_group = $this->getProductsFromOneTag($city_code, $g['tag_id']);
                if (count($one_group['products']) > 0) {
                    $one_group['is_tag'] = true;
                    $one_group['tag_id'] = $g['tag_id'];
                    $one_group['tag_name'] = $g['name'];
                    $groups[] = $one_group;
                }
            }
        }

        $result['products_groups'] = $groups;
    }

    private function assembleMainGroups(&$result, $city_code)
    {
        $sub_result = array();

        $sub_result['top_10'] = $this->getProductsFromOneGroup($city_code, '4');
        $sub_result['package'] = $this->getProductsFromOneGroup($city_code, '5');
        $sub_result['line'] = $this->getProductsFromOneGroup($city_code, '7');
        $sub_result['experience'] = $this->getArticlesFromOneColumn($city_code);

        if (empty($sub_result['top_10']['products'])) {
            unset($sub_result['top_10']);
        }
        if (empty($sub_result['package']['products'])) {
            unset($sub_result['package']);
        }
        if (empty($sub_result['line']['products'])) {
            unset($sub_result['line']);
        }
        if (empty($sub_result['experience']['data'])) {
            unset($sub_result['experience']);
        }

        $result = array_merge($result, $sub_result);

        return !empty($sub_result);
    }

    private function getCityInfo($city_code, $type = false, $id = 0, $is_mobile = false, $all_info = true)
    {
        $result = array();
        $continue = true;
        $mobile_count = 2;
        if ($all_info) {
            $continue = $this->getCityBaseInfo($result, $city_code, $is_mobile, $type);
        }

        if ($continue) {
            $result['type'] = $type;
            if ($type == 'tag') { //获取单个tag内容
                if ($is_mobile) {
                    $tag_tree = array();

                    foreach ($result['tag_tree'] as $tag) {
                        if ($tag['tag_id'] == $id) {
                            $tag_tree = $tag;
                            break;
                        } else {
                            foreach ($tag['sub_tags'] as $sub_tag) {
                                if ($sub_tag['tag_id'] == $id) {
                                    $tag_tree = $tag;
                                    break;
                                }
                            }
                            if (!empty($tag_tree)) {
                                break;
                            }
                        }
                    }
                    $result['tag_tree'] = $tag_tree;
                } else {
                    if (!$is_mobile && $all_info) {
                        $tag_tree = array();

                        foreach ($result['tag_tree'] as $tag) {
                            $formatted_id = strtolower(str_replace(' ', '', $id));
                            if (strtolower(str_replace(' ', '', $tag['en_name'])) == $formatted_id) {
                                $tag_tree = $tag;
                                break;
                            } else {
                                foreach ($tag['sub_tags'] as $sub_tag) {
                                    if (strtolower(str_replace(' ', '', $sub_tag['en_name'])) == $formatted_id) {
                                        $tag_tree = $tag;
                                        break;
                                    }
                                }
                                if (!empty($tag_tree)) {
                                    break;
                                }
                            }
                        }

                        if ($tag_tree) {
                            $id = $tag_tree['tag_id'];
                        } else {
                            return false;
                        }
                    }
                }

                $result['tag_id'] = $id;
                $result['products_groups'] = array($this->getProductsFromOneTag($city_code, $id));
            } else if ($type == 'search') {
                $hwords = array();
                Yii::app()->search->setCity($city_code);
                $result['products'] = Yii::app()->search->query($this->getParam('words'), $hwords);
                if (empty($result['products'])) {
                    $res = $this->getProductsFromOneGroup($city_code, '4');
                    $result['products_top_10'] = $res['products'];
                }
                $result['hwords'] = $hwords;
            } else if ($type == 'group') { //获取单个group内容
                $result['group_id'] = $id;
                $result['products_groups'] = array($this->getProductsFromOneGroup($city_code, false, $id));
                $result['article'] = $this->getArticleFromOneGroup($city_code, $id);
            } else {
                $result['use_v2'] = true;
                $success = $this->assembleMainGroups($result, $city_code); //如果有explore，返回四大块
                $result['products_groups'] = array();

                if (!$success || ($is_mobile && !$type)) { //如果没有四大块或者是手机初始态，返回所有商品分组
                    $this->assembleProductGroups($result, $city_code, $is_mobile);
                    //手机分组内的商品和文章裁剪
                    //if ($is_mobile) {
                    foreach ($result['products_groups'] as &$g) {
                        $g['product_count'] = count($g['products']);
                        $g['products'] = array_slice($g['products'], 0, $mobile_count);
                    }
                    //}
                }

                if (!$success) { //如果没有四大块则不是v2
                    $result['use_v2'] = false;
                } else {
                    if ($success && $is_mobile && !$type) {
                        if (isset($result['top_10'])) {
                            $result['top_10']['product_count'] = count($result['top_10']['products']);
                            $result['top_10']['products'] = array_slice($result['top_10']['products'], 0,
                                $mobile_count);
                        }
                        if (isset($result['package'])) {
                            $result['package']['product_count'] = count($result['package']['products']);
                            $result['package']['products'] = array_slice($result['package']['products'], 0, 1);
                        }
                        if (isset($result['line'])) {
                            $result['line']['product_count'] = count($result['line']['products']);
                            $result['line']['products'] = array_slice($result['line']['products'], 0, 1);
                        }
                        if (isset($result['experience'])) {
                            $result['experience']['article_count'] = count($result['experience']['data']);
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function actionCityInfo()
    {
        $city_code = $this->getParam('city_code');
        $result = $this->getCityInfo($city_code, $this->getParam('type'), $this->getParam('id'), false,
                                     !$this->getParam('part_info'));

        if ($result) {
            $result['seo'] = Converter::convertModelToArray(HtSeoSetting::model()->findByCityCode($city_code));
            EchoUtility::echoJson($result);
        }
    }

    public function actionMobileCityInfo()
    {
        $city_code = $this->getParam('city_code');
        $result = $this->getCityInfo($city_code, $this->getParam('type'), $this->getParam('id'), true,
                                    !$this->getParam('part_info'));

        if ($result) {
            EchoUtility::echoJson($result);
        }
    }
}