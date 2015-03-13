<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 5/8/14
 * Time: 11:37 AM
 */
class CountryController extends AdminController
{

    public $layout = '//layouts/common';

    public function actionIndex()
    {
        $this->pageTitle = '国家管理';

        $request_urls = array(
            'editCountry' => $this->createUrl('country/edit', array('country_code' => '')),
            'countryInfo' => $this->createUrl('country/countries'),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );
        $this->render('index');
    }

    public function actionEdit()
    {
        $this->pageTitle = '国家编辑';

        $country_code = Yii::app()->request->getParam('country_code');

        $request_urls = array(
            'fetchOneCountry' => $this->createUrl('country/country', array('country_code' => $country_code)),
            'fetchOneCountryCities' => $this->createUrl('country/countryCities',
                                                        array('country_code' => $country_code)),
            'countrySeo' => $this->createUrl('country/countrySeo', array('country_code' => $country_code)),
            'cityGroups' => $this->createUrl('country/cityGroups', array('country_code' => $country_code)),
            'oneCityGroup' => $this->createUrl('country/cityGroup',
                                               array('country_code' => $country_code, 'group_id' => '')),
            'updateOneCityGroupInfo' => $this->createUrl('country/updateCityGroupInfo'),
            'updateOneCityGroupCities' => $this->createUrl('country/updateCityGroupCities'),
            'updateOneCountryCover' => $this->createUrl('country/countryCover', array('country_code' => $country_code)),
            'updateOneCountryMobileCover' => $this->createUrl('country/countryMobileCover', array('country_code' => $country_code)),
            'updateOneCityGroupCover' => $this->createUrl('country/cityGroupCover'),
            'updateCityGroupsOrder' => $this->createUrl('country/changeCityGroupsDisplayOrder'),

            'countryGroup' => $this->createUrl('country/countryGroup', array('country_code' => $country_code)),
            'changeCountryGroupsDisplayOrder' => $this->createUrl('country/changeCountryGroupsDisplayOrder', array('country_code' => $country_code,'group_id' => '')),
            'countryGroupCover' => $this->createUrl('country/countryGroupCover', array('country_code' => $country_code)),
            'changeCountryGroupStatus' => $this->createUrl('country/changeCountryGroupStatus', array('country_code' => $country_code,'group_id' => '')),
            'countryTab' => $this->createUrl('country/countryTab', array('country_code' => $country_code,'tab_id' => '')),
            'changeCountryTabsDisplayOrder' => $this->createUrl('country/changeCountryTabsDisplayOrder', array('country_code' => $country_code,'group_id' => '')),
//            'addCountryGroupRef' => $this->createUrl('country/addCountryGroupRef', array('country_code' => $country_code,'group_id' => '')),
            'changeRefDisplayOrder' => $this->createUrl('country/changeRefDisplayOrder', array('group_id' => '')),
            'countryGroupRef' => $this->createUrl('country/countryGroupRef', array('group_id' => '')),
            'getCountryArticles' => $this->createUrl('country/getCountryArticles', array('country_code' => $country_code)),
            'CountryRefCover' => $this->createUrl('country/CountryRefCover', array('country_code' => $country_code)),


        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );
        $this->render('edit');
    }

    public function actionCountrySeo()
    {
        $country_code = $this->getCountryCode();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') {
            $country_seo = HtSeoSetting::model()->findByCountryCode($country_code);

            EchoUtility::echoMsgTF(true, '获取国家SEO', Converter::convertModelToArray($country_seo));
        } else if ($request_method == 'post') {
            $new_data = $this->getPostJsonData();

            $country_seo = HtSeoSetting::model()->findByCountryCode($country_code);
            if ($country_seo == null) {
                $country_seo = new HtSeoSetting();
                $country_seo['type'] = HtSeoSetting::TYPE_COUNTRY;
                $country_seo['id'] = $country_code;
                ModelHelper::fillItem($country_seo, $new_data, array('title', 'description', 'keywords'));
                $result = $country_seo->insert();
            } else {
                $result = ModelHelper::updateItem($country_seo, $new_data, array('title', 'description', 'keywords'));
            }

            EchoUtility::echoMsgTF($result, '更新国家SEO');
        }
    }

    public function actionGetCountries()
    {
        $countries = HtCountry::model()->getCountriesHaveProducts();
        EchoUtility::echoMsgTF(true, '获取国家列表', $countries);
    }

    public function actionCountries()
    {
        $countries = HtCountry::model()->getCountriesHaveProducts();
        $need_editing_groups_countries = HtCountry::model()->getCountriesHaveCitiesNotGrouped();
        $incomplete_countries = HtCountry::model()->getIncompleteCountries();
        $incomplete_tab_countries = HtCountry::model()->getIncompleteTabCountries();

        EchoUtility::echoMsgTF(true, '获取国家列表', array('countries' => $countries,
            'need_editing_groups_countries' => $need_editing_groups_countries,
            'incomplete_countries' => $incomplete_countries,
            'incomplete_tab_countries' => $incomplete_tab_countries,
        ));
    }

    public function actionCountry()
    {
        $country_code = $this->getCountryCode();
        $country = HtCountry::model()->with('country_image')->findByPk($country_code);
        $data = Converter::convertModelToArray($country);

        EchoUtility::echoMsgTF(!empty($data), '获取国家列表', $data);
    }

    public function actionCountryCover()
    {
        $country_code = $this->getCountryCode();
        $country = HtCountry::model()->findByPk($country_code);

        if (empty($country)) {
            EchoUtility::echoCommonFailed('未找到code为' . $country_code . '的国家。');

            return;
        }
        $cs = HtCountryImage::model()->findByPk($country_code);
        if (empty($cs)) {
            $cs = new HtCountryImage();
            $cs['country_code'] = $country_code;
            $cs['cover_url'] = '';
            $cs->insert();
        }

        $to_dir = 'image/upload/country/' . $country_code . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $cs['cover_url'] = $image_url;
            $result = $cs->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionCountryMobileCover()
    {
        $country_code = $this->getCountryCode();
        $country = HtCountry::model()->findByPk($country_code);

        if (empty($country)) {
            EchoUtility::echoCommonFailed('未找到code为' . $country_code . '的国家。');

            return;
        }
        $cs = HtCountryImage::model()->findByPk($country_code);
        if (empty($cs)) {
            $cs = new HtCountryImage();
            $cs['country_code'] = $country_code;
            $cs['cover_url'] = '';
            $cs->insert();
        }

        $to_dir = 'image/upload/country/' . $country_code . '/mobile/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $cs['mobile_url'] = $image_url;
            $result = $cs->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionCountryCities()
    {
        $cities = HtCity::model()->getCitiesHaveProductsOnline($this->getCountryCode());
        EchoUtility::echoMsgTF(true, '获取城市列表', Converter::convertModelToArray($cities));
    }

    public function actionCityGroups()
    {
        $country_code = $this->getCountryCode();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') { // 获取城市所有分组信息 -- 名称，描述，图片，包含城市
            $cgs = HtCityGroup::model()->findAll('country_code="' . $country_code . '"');
            EchoUtility::echoMsgTF(true, '获取城市分组列表', Converter::convertModelToArray($cgs));

        } else if ($request_method == 'post') { //  更新分组信息
            $data = $this->getPostJsonData();
            $result = 1;
            foreach ($data as $group) {
                $cg = HtCityGroup::model()->findByPk($group['group_id']);
                $tmp_result = ModelHelper::updateItem($cg, $group,
                                                      array('name', 'description', 'city_codes', 'display_order'));
                if ($tmp_result != 1) break;
            }

            $cgs = HtCityGroup::model()->findAll('country_code="' . $country_code . '"');
            EchoUtility::echoMsg($result, '城市分组信息', '', $cgs);
        }
    }

    public function actionChangeCityGroupsDisplayOrder()
    {
        $data = $this->getPostJsonData();
        $result = true;
        foreach ($data as $group_order) {
            $city_group = HtCityGroup::model()->findByPk($group_order['group_id']);
            $part_result = ModelHelper::updateItem($city_group,
                                                   array('display_order' => $group_order['display_order']));
            if ($part_result != 1) {
                $result = false;
                break;
            }
        }

        EchoUtility::echoMsgTF($result, '城市分组排序');
    }

    public function actionCityGroup()
    {
        $country_code = $this->getCountryCode();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') { // 获取城市一个分组信息 -- 名称，描述，图片，包含城市

        } else if ($request_method == 'post') {
            $data = $this->getPostJsonData();

            if (isset($data['group_id'])) { //更新分组
                /*$cg = HtCityGroup::model()->findByPk($data['group_id']);
                $result = ModelHelper::updateItem($cg, $data, array('name', 'description', 'city_codes', 'display_order'));

                EchoUtility::echoMsg($result, '成功更新分组', '');*/
            } else { //添加分组
                $cg = new HtCityGroup();
                $cg['country_code'] = $country_code;
                $cg['name'] = '名称';
                $cg['description'] = '简介';
                $cg['cover_url'] = '';
                $cg['city_codes'] = '';
                $cg['display_order'] = 1;

                $result = $cg->insert();
                EchoUtility::echoMsgTF($result, '添加城市分组', Converter::convertModelToArray($cg));
            }
        } else if ($request_method == 'delete') { // 删除分组
            $group_id = Yii::app()->request->getParam('group_id');
            $item = HtCityGroup::model()->findByPk($group_id);
            if(!empty($item))
            {
                $country_code = $item['country_code'];
                $result = HtCityGroup::model()->deleteByPk($group_id);
                HtCityGroup::clearCache($country_code);
            }
            EchoUtility::echoMsgTF($result, '删除城市分组');
        }
    }

    public function actionUpdateCityGroupCities()
    {
        $data = $this->getPostJsonData();

        $cg = HtCityGroup::model()->findByPk($data['group_id']);
        $result = ModelHelper::updateItem($cg, $data, array('city_codes'));

        EchoUtility::echoMsg($result, '成功更新分组城市', '');
    }

    public function actionUpdateCityGroupInfo()
    {
        $data = $this->getPostJsonData();

        $cg = HtCityGroup::model()->findByPk($data['group_id']);
        $result = ModelHelper::updateItem($cg, $data, array('name', 'description', 'display_order'));

        EchoUtility::echoMsg($result, '成功更新分组信息', '');
    }

    public function actionCityGroupCover()
    {
        // 更新分组图片
        $group_id = Yii::app()->request->getParam('group_id');
        $cg = HtCityGroup::model()->findByPk($group_id);

        if (empty($cg)) {
            EchoUtility::echoCommonFailed('未找到id为' . $group_id . '的分组。');

            return;
        }

        $to_dir = 'image/upload/country/' . $cg['country_code'] . '/' . $group_id . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $cg['cover_url'] = $image_url;
            $result = $cg->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionCountryGroup()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $group_id = $this->getGroupID();
        $country_code = $this->getCountryCode();
        if ($request_method == 'get') { // 获取国家所有分组信息 -- 名称，描述，图片，包含城市
            $cg = HtCountryGroup::model()->findAll('country_code="' . $country_code . '"');
            $cg = Converter::convertModelToArray($cg);
            if($cg){
                foreach ($cg as &$group) {
                    $group['refs'] = HtCountryGroupRef::model()->getRefOfGroup($group['group_id'],$group['type']);
                }
            }
            EchoUtility::echoMsgTF(true, '获取国家分组列表', $cg);
        }else if ($request_method == 'post') {
            $data = $this->getPostJsonData();
            if (empty($group_id)) {
                $country_code = $this->getCountryCode();
                $product_group = new HtCountryGroup();
                $product_group['country_code'] = $country_code;
                $product_group['name'] = '名称_' . date('H:i:s', time());
                $product_group['brief'] = '简介';
                $product_group['summary'] = '';
                $product_group['description'] = '';
                $product_group['type'] = $data['type'] ? $data['type'] : 1;
                $product_group['status'] = 1;
                $product_group['display_order'] = 1;

                $result = $product_group->insert();
                $data = Converter::convertModelToArray($product_group);
                EchoUtility::echoMsgTF($result, '添加', $data);
            } else {

                $country_group = HtCountryGroup::model()->findByPk($group_id);
                try {
                    $result = ModelHelper::updateItem($country_group, $data, array('type', 'name', 'brief', 'summary', 'description', 'tab_id', 'display_order', 'link_url', 'city_code'));
                    EchoUtility::echoMsg($result, '分组信息', '', Converter::convertModelToArray($country_group));
                } catch (Exception $exception) {
                    EchoUtility::echoCommonFailed('保存失败:分组名称不可重复。'); //. $exception->getMessage());
                }
            }

        } else if ($request_method == 'delete') {
            if (!empty($group_id)) {
                $result = HtCountryGroup::model()->deleteByPk($group_id);
                if ($result) {
                    HtCountryGroupRef::model()->deleteAll('group_id=' . $group_id);
                    HtCountryTab::clearCache($country_code);
                }
                EchoUtility::echoMsgTF($result > 0, '删除');
            } else {
                EchoUtility::echoCommonFailed('未找到ID为' . $group_id . '的分组。');
            }
        }
    }

    public function actionChangeCountryGroupsDisplayOrder()
    {
        $data = $this->getPostJsonData();
        $result = true;
        foreach ($data as $group_order) {
            $country_group = HtCountryGroup::model()->findByPk($group_order['group_id']);
            $part_result = ModelHelper::updateItem($country_group,
                                                   array('display_order' => $group_order['display_order']));
            if ($part_result != 1) {
                $result = false;
                break;
            }
        }

        EchoUtility::echoMsgTF($result, '国家分组排序');
    }

    public function actionCountryGroupCover()
    {
        // 更新分组图片
        $group_id = $_POST['group_id'];
        $cg = HtCountryGroup::model()->findByPk($group_id);

        if (empty($cg)) {
            EchoUtility::echoCommonFailed('未找到id为' . $group_id . '的分组。');

            return;
        }

        $to_dir = 'image/upload/country/' . $cg['country_code'] . '/' . $group_id . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $cg['cover_image_url'] = $image_url;
            $result = $cg->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionChangeCountryGroupStatus()
    {
        $group_id = $this->getGroupID();
        $group = HtCountryGroup::model()->findByPk($group_id);
        if (empty($group)) {
            EchoUtility::echoCommonFailed('找不到ID为' . $group_id . '的国家分组。');

            return;
        }

        $data = $this->getPostJsonData();
        $status = $data['status'];
        if ($status == 2) {
//            $valid = $group->readyToOnline();
//            if ($valid['code'] != 200) {
//                EchoUtility::echoCommonFailed($valid['msg']);
//
//                return;
//            }
        }

        $result = ModelHelper::updateItem($group, $data);
        EchoUtility::echoMsg($result, '国家分组状态', '', $group);
    }

    public function actionCountryTab()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $country_code = $this->getCountryCode();
        $tab_id = $this->getTabID();
        $data = $this->getPostJsonData();
        if ($request_method == 'get') { // 获取国家所有tab信息
            $ct = HtCountryTab::model()->with('groups')->findAll("ct.country_code = '$country_code'");
            EchoUtility::echoMsgTF(true, '获取国家分组列表', Converter::convertModelToArray($ct));
        }else if ($request_method == 'post') {
            if (empty($tab_id)) {
                $display_order = $data['display_order'];
                $country_tab = new HtCountryTab();
                $country_tab['country_code'] = $country_code;
                $country_tab['name'] = '名称_' . date('H:i:s', time());
                $country_tab['status'] = 1;
                $country_tab['display_order'] = $display_order;
                $result = $country_tab->insert();
                $data = Converter::convertModelToArray($country_tab);
                EchoUtility::echoMsgTF($result, '添加', $data);
            } else {
                $data = $this->getPostJsonData();
                $country_tab = HtCountryTab::model()->findByPk($tab_id);
                try {
                    $result = ModelHelper::updateItem($country_tab, $data, array('name', 'title', 'brief', 'summary', 'description', 'status', 'display_order'));
                    EchoUtility::echoMsg($result, '国家tab信息', '', Converter::convertModelToArray($country_tab));
                } catch (Exception $exception) {
                    EchoUtility::echoCommonFailed('保存失败'); //. $exception->getMessage());
                }
            }

        } else if ($request_method == 'delete') {
            if (!empty($tab_id)) {
                $result = HtCountryTab::model()->deleteByPk($tab_id);
                if ($result) {
                    $groups = HtCountryGroup::model()->findAllByAttributes(array('tab_id'=>$tab_id));
                    if($groups){
                        foreach($groups as $group){
                            ModelHelper::updateItem($group, array('tab_id'=>0), array('tab_id'));
                        }
                    }
                    HtCountryTab::clearCache($country_code);
                }
                EchoUtility::echoMsgTF($result > 0, '删除');
            } else {
                EchoUtility::echoCommonFailed('未找到ID为' . $tab_id . '的tab。');
            }
        }
    }

    public function actionChangeCountryTabsDisplayOrder()
    {
        $data = $this->getPostJsonData();
        $result = true;
        foreach ($data as $tab_order) {
            $country_tab = HtCountryTab::model()->findByPk($tab_order['tab_id']);
            $part_result = ModelHelper::updateItem($country_tab,
                                                   array('display_order' => $tab_order['display_order']));
            if ($part_result != 1) {
                $result = false;
                break;
            }
        }

        EchoUtility::echoMsgTF($result, '国家tab排序');
    }

    public function actionCountryGroupRef()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $group_id = $this->getGroupID();
        $data = $this->getPostJsonData();
        $id = $data['id'] ? $data['id'] : $this->getRefID();
        if ($request_method == 'get') { //

        }else if ($request_method == 'post') {
            $ref = HtCountryGroupRef::model()->findByPk(array('group_id'=>$group_id,'id'=>$id));
            if (!$ref) {
                $display_order = $data['display_order'];
                $country_group_ref = new HtCountryGroupRef();
                $country_group_ref['group_id'] = $group_id;
                $country_group_ref['id'] = $id;
                $country_group_ref['display_order'] = $display_order;
                if(isset($data['status'])){
                    $country_group_ref['status'] = $data['status'];
                }
                try {
                    if($data['type'] == HtCountryGroup::TYPE_PRODUCT || $data['type'] == HtCountryGroup::TYPE_LINE){
                        $product = HtProduct::model()->with(array('description' => array('condition' => 'language_id=2')))->findByPk($id);
                        if(!$product) {
                            EchoUtility::echoCommonFailed('未找到该商品。');
                            return;
                        }
                    }
                    $result = $country_group_ref->insert();
                } catch (Exception $e) {
                    EchoUtility::echoCommonFailed('添加失败。请检查是否已添加过。');
                    return;
                }
                $return = Converter::convertModelToArray($country_group_ref);
                if ($result) {
                    //普通商品和线路商品
                    if($data['type'] == HtCountryGroup::TYPE_PRODUCT || $data['type'] == HtCountryGroup::TYPE_LINE){
                        $return['product_name'] = $product['description']['name'];
                    }else if($data['type'] == HtCountryGroup::TYPE_GROUP){//分组
                        $group = HtCountryGroup::model()->findByPk($id);
                        $return['group_name'] = $group['name'];
                    }else if($data['type'] == HtCountryGroup::TYPE_ARTICLE){//文章
                        $article = HtArticle::model()->findByPk($id);
                        $return['article_name'] = $article['title'];
                    }else if($data['type'] == HtCountryGroup::TYPE_CITY){//城市
                        $city = HtCity::model()->findByPk($id);
                        $return['city_name'] = $city['cn_name'];
                    }
                }
                EchoUtility::echoMsgTF($result, '添加', $return);
            } else {
                try {
                    $result = ModelHelper::updateItem($ref, $data, array('group_id', 'id', 'name', 'display_order', 'status'));
                    EchoUtility::echoMsg($result, '分组ref信息', '', Converter::convertModelToArray($ref));
                } catch (Exception $exception) {
                    EchoUtility::echoCommonFailed('保存失败:ref信息不能重复'); //. $exception->getMessage());
                }
            }

        } else if ($request_method == 'delete') {
            $result = HtCountryGroupRef::model()->deleteByPk(array('group_id'=>$group_id,'id'=>$id));
            if ($result) {
                //TODO:clear cache
                HtCountryGroupRef::clearCache($group_id);
            }
            EchoUtility::echoMsgTF($result > 0, '删除');
        }
    }

    public function actionChangeRefDisplayOrder()
    {
        $group_id = $this->getGroupID();
        $data = $this->getPostJsonData();
        $result = true;
        foreach ($data as $cgf) {
            $country_group_ref = HtCountryGroupRef::model()->findByPk(array('group_id' => $group_id, 'id' => $cgf['id']));
            $part_result = ModelHelper::updateItem($country_group_ref,
                                                   array('display_order' => $cgf['display_order']));
            if ($part_result != 1) {
                $result = false;
                break;
            }
        }

        EchoUtility::echoMsgTF($result, '更新顺序');
    }

    public function actionGetCountryArticles()
    {
        $country_code = $this->getCountryCode();
        $cities = HtCity::model()->findAll("country_code = '$country_code'");
        $return = array();
        if($cities){
            foreach($cities as $city){
                $city_article = HtArticle::model()->findAllByAttributes(['city_code' => $city['city_code'], 'status' => 1]);
                $city_article = Converter::convertModelToArray($city_article);
                $return = array_merge($return,$city_article);
            }
        }
        EchoUtility::echoMsgTF(true, '获取国家文章列表', $return);
    }

    public function actionCountryRefCover()
    {
        $country_code = $this->getCountryCode();
        $group_id = $_POST['group_id'];
        $id = $_POST['id'];
        $ref = HtCountryGroupRef::model()->findByPk(array('group_id'=>$group_id,'id'=>$id));

        if (empty($ref)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $id . '的ref。');
            return;
        }
        $to_dir = 'image/upload/country/' . $country_code . '/' .$group_id . '/' . $id . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $ref['image_url'] = $image_url;
            $result = $ref->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionHotCountry()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $data = $this->getPostJsonData();
        if ($request_method == 'get') { // 获取热门国家
            $c = HtContinent::model()->with('countries')->findAll("is_hot = 1");
            EchoUtility::echoMsgTF(true, '获取热门国家', Converter::convertModelToArray($c));
        }else if ($request_method == 'post') {
            $country_code = $data['country_code'];
            $country = HtCountry::model()->findByPk($country_code);
            $result = ModelHelper::updateItem($country, array('is_hot'=>1), array('is_hot'));

            EchoUtility::echoMsg($result, '热门国家信息');
        }else if ($request_method == 'delete') {
            $country_code =  $this->getCountryCode();
            $country = HtCountry::model()->findByPk($country_code);
            $result = ModelHelper::updateItem($country, array('is_hot'=>0), array('is_hot'));
            if ($result) {
                //TODO:clear cache
                Yii::app()->cache->delete('continents_all_with_countries_cities');
            }
            EchoUtility::echoMsgTF($result > 0, '删除热门国家');
        }
    }

    private function getGroupID()
    {
        return Yii::app()->request->getParam('group_id');
    }

    private function getRefID()
    {
        return Yii::app()->request->getParam('id');
    }

    private function getTabID()
    {
        return Yii::app()->request->getParam('tab_id');
    }

    private function getCountryCode()
    {
        return Yii::app()->request->getParam('country_code');
    }

} 