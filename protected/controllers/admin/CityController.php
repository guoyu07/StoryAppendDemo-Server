<?php

class CityController extends AdminController
{

    public $layout = '//layouts/common';

    public function actionIndex()
    {
        $this->pageTitle = '城市管理';

        $request_urls = array(
            'edit' => $this->createUrl('city/edit', array('city_code' => '')),
            'fetchIncompleteCities' => $this->createUrl('city/citiesHaveIncompleteInfo'),
            'fetchHaveNewGroupCities' => $this->createUrl('city/citiesHaveNewGroupInfo'),
            'fetchAllCitiesHaveProductsOnline' => $this->createUrl('city/allCitiesHaveProductsOnline'),
            'updateCityRecommend' => $this->createUrl('city/updateCityRecommend'),
            'fetchCityRecommend' => $this->createUrl('city/fetchCityRecommend'),
            'fetchMissingGroupCoverCities' => $this->createUrl('city/citiesMissingGroupCover'),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('index');
    }

    public function actionEdit()
    {
        $this->pageTitle = '编辑城市';

        $city_code = $this->getCityCode();

        $request_urls = array(
            //城市基本信息
            'cityImages' => $this->createUrl('city/cityImages', array('city_code' => $city_code)),
            'addOrUpdateCityImage' => $this->createUrl('city/addOrUpdateCityImage', array('city_code' => $city_code)),
            'citySeo' => $this->createUrl('city/citySeo', array('city_code' => $city_code)),
            //城市商品分组
            'getProductGroups' => $this->createUrl('city/getProductGroups', array('city_code' => $city_code)),
            'changeProductGroupsDisplayOrder' => $this->createUrl('city/changeProductGroupsDisplayOrder',
                                                                  array('city_code' => $city_code)),
            'productGroup' => $this->createUrl('city/productGroup',
                                               array('city_code' => $city_code, 'group_id' => '')),
            'productGroupImage' => $this->createUrl('city/addOrUpdateProductGroupImage',
                                                    array('city_code' => $city_code, 'group_id' => '')),
            'changeProductGroupStatus' => $this->createUrl('city/changeProductGroupStatus',
                                                           array('city_code' => $city_code, 'group_id' => '')),
            //城市分组内商品
            'getProducts' => $this->createUrl('city/getProducts', array('city_code' => $city_code, 'group_id' => '')),
            'getCityProducts' => $this->createUrl('city/getCityProducts', array('city_code' => $city_code)),
            'addProduct' => $this->createUrl('city/addProduct', array('city_code' => $city_code, 'group_id' => '')),
            'updateProduct' => $this->createUrl('city/updateProduct', array('city_code' => $city_code, 'group_id' => '')),
            'copyProduct' => $this->createUrl('city/copyProduct', array('city_code' => $city_code, 'group_id' => '')),
            'addOrUpdateProductImage' => $this->createUrl('city/addOrUpdateProductImage',
                                                          array('city_code' => $city_code)),
            'addOrUpdateProductLineImage' => $this->createUrl('city/addOrUpdateProductLineImage',
                                                              array('city_code' => $city_code)),
            'deleteProduct' => $this->createUrl('city/deleteProduct',
                                                array('city_code' => $city_code, 'group_id' => '')),
            'changeProductDisplayOrder' => $this->createUrl('city/changeProductDisplayOrder',
                                                            array('city_code' => $city_code, 'group_id' => '')),
            //聚合页面
            'updateCityPromotionCover' => $this->createUrl('city/linkPromotionToCity', array('city_code' => $city_code)),
            'linkPromotionToCity' => $this->createUrl('city/linkPromotionToCity', array('city_code' => $city_code, 'promotion_id' => '')),
            'cityPromotion' => $this->createUrl('city/cityPromotion', array('city_code' => $city_code)),
            'promotion' => $this->createUrl('promotion/promotion', array('promotion_id' => '')),
            'viewPromotionList' => $this->createUrl('promotion/index'),
            'editPromotion' => $this->createUrl('promotion/edit', array('promotion_id' => '')),
            //体验分组接口
            'cityColumn' => $this->createUrl('city/cityColumn', array('city_code' => $city_code, 'column_id' => '')),
            'cityColumnRef' => $this->createUrl('city/cityColumnRef',
                                                array('city_code' => $city_code, 'column_id' => '')),
            'articleDisplayOrder' => $this->createUrl('city/articleDisplayOrder',
                                                      array('city_code' => $city_code, 'column_id' => '')),
            'articleImage' => $this->createUrl('city/articleImage',
                                               array('city_code' => $city_code, 'column_id' => '')),
            'getCityArticles' => $this->createUrl('city/getArticles', array('city_code' => $city_code)),
            'editArticleUrl' => $this->createUrl('article/edit', array('article_id' => '')),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('edit');
    }

    public function actionGetCities()
    {
        $city_ids = HtCity::model()->getCityIDsHaveProduct();
        $data = HtCity::model()->getCountryCityInfo($city_ids);

        EchoUtility::echoMsgTF(true, '获取城市列表', $data);
    }

    public function actionAllCitiesHaveProductsOnline()
    {
        $cities = HtCity::model()->getAllCitiesHaveProductsOnline();
        $data = Converter::convertModelToArray($cities);

        EchoUtility::echoMsgTF(true, '获取城市列表', $data);
    }

    public function actionCitiesHaveIncompleteInfo()
    {
        $cities = HtCity::model()->getCitiesHaveIncompleteInfo();
        EchoUtility::echoMsgTF(true, '', $cities);
    }

    public function actionCitiesHaveNewGroupInfo()
    {
        $cities = HtCity::model()->getCitiesHaveNewGroupInfo();
        EchoUtility::echoMsgTF(true, '', $cities);
    }

    public function actionCitiesMissingGroupCover()
    {
        $cities = HtCity::model()->getCitiesMissingGroupCover();
        EchoUtility::echoMsgTF(true, '', $cities);
    }

    public function actionCityImages()
    {
        $city_code = $this->getCityCode();
        $city_info = HtCity::model()->with('city_image')->findByPk($city_code);

        $data = Converter::convertModelToArray($city_info);

        EchoUtility::echoMsgTF(!empty($data), '', $data);
    }

    public function actionAddOrUpdateCityImage()
    {
        $type = $_POST['type'];
        $city_code = $this->getCityCode();
        $city = HtCity::model()->findByPk($city_code);

        if (empty($city)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $city_code . '的城市。');

            return;
        }

        $city_image = HtCityImage::model()->findByPk($city_code);
        if (empty($city_image)) {
            $city_image = new HtCityImage();
            $city_image['city_code'] = $city_code;
            $city_image['banner_image_url'] = '';
            $city_image['grid_image_url'] = '';
            $city_image['app_image_url'] = '';
            $city_image['app_strip_image_url'] = '';
            $city_image->insert();
        }

        $to_dir = 'image/upload/country/' . $city_code . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }
            if ($type == 1) {
                $city_image['banner_image_url'] = $image_url;
            } else if ($type == 2) {
                $city_image['grid_image_url'] = $image_url;
            } else if ($type == 3) {
                $city_image['app_image_url'] = $image_url;
            } else if ($type == 4) {
                $city_image['app_strip_image_url'] = $image_url;
            }
            $result = $city_image->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionGetProductGroups()
    {
        $city_code = $this->getCityCode();
        $groups = HtProductGroup::model()->getCategorizedByCity($city_code);
        foreach ($groups['pre_defined_groups'] as &$group) {
            $group['products'] = HtProductGroupRef::model()->getProductsOfGroup($group['group_id'],$group['type']);
            $group['seo'] = HtSeoSetting::model()->findByAttributes(array('type' => 5, 'id' => $group['group_id']));
        }
        foreach ($groups['user_defined_groups'] as &$group) {
            $group['products'] = HtProductGroupRef::model()->getProductsOfGroup($group['group_id']);
            $group['seo'] = HtSeoSetting::model()->findByAttributes(array('type' => 5, 'id' => $group['group_id']));
        }
        foreach ($groups['app_defined_groups'] as &$group) {
            $group['products'] = HtProductGroupRef::model()->getProductsOfGroup($group['group_id']);
        }

        EchoUtility::echoMsgTF(true, '获取产品分组信息', $groups);
    }

    public function actionChangeProductGroupsDisplayOrder()
    {
        $data = $this->getPostJsonData();
        $result = true;
        foreach ($data as $group_order) {
            $product_group = HtProductGroup::model()->findByPk($group_order['group_id']);
            $part_result = ModelHelper::updateItem($product_group,
                                                   array('display_order' => $group_order['display_order']));
            if ($part_result != 1) {
                $result = false;
                break;
            }
        }

        EchoUtility::echoMsgTF($result, '产品分组现实顺序');
    }

    public function actionProductGroup()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $group_id = $this->getGroupID();
        if ($request_method == 'post') {
            $data = $this->getPostJsonData();
            if (empty($group_id)) {
                $city_code = $this->getCityCode();
                $product_group = new HtProductGroup();
                $product_group['type'] = $data['type'] ? $data['type'] : 99;
                $product_group['city_code'] = $city_code;
                $product_group['name'] = '分组名称_' . date('H:i:s', time());
                $product_group['description'] = '分组描述';
                $product_group['brief'] = '分组简言';
                $product_group['cover_image_url'] = '';
                $product_group['status'] = 1;
                $product_group['display_order'] = 1;

                $result = $product_group->insert();
                $data = Converter::convertModelToArray($product_group);
                $data['products_count'] = 0;
                $data['products'] = array();

                //添加seo_setting
                if ($result && ($product_group['type'] == 99)) {
                    $seo = new HtSeoSetting();
                    $seo['type'] = 5;
                    $seo['id'] = $product_group->getPrimaryKey();
                    $seo->insert();
                }

                EchoUtility::echoMsgTF($result, '添加', $data);
            } else {
                $product_group = HtProductGroup::model()->findByPk($group_id);
                try {
                    $result = ModelHelper::updateItem($product_group, $data, array('name', 'description', 'brief'));
                    $seo = HtSeoSetting::model()->findByAttributes(array('type' => 5, 'id' => $group_id));
                    if ($seo) ModelHelper::updateItem($seo, $data['seo'], array('title', 'description', 'keywords'));
                    EchoUtility::echoMsg($result, '分组信息', '', $product_group);
                } catch (Exception $exception) {
                    EchoUtility::echoCommonFailed('保存失败：分组名称不可重复。'); //. $exception->getMessage());
                }
            }

        } else if ($request_method == 'delete') {
            if (!empty($group_id)) {
                HtProductGroup::clearCache($group_id);
                $result = HtProductGroup::model()->deleteByPk($group_id);
                if ($result) {
                    HtProductGroupRef::model()->deleteAll('group_id=' . $group_id);
                    HtProductGroupRef::clearCache($group_id);
                }
                EchoUtility::echoMsgTF($result > 0, '删除');
            } else {
                EchoUtility::echoCommonFailed('未找到ID为' . $group_id . '的分组。');
            }
        }
    }

    public function actionAddOrUpdateProductGroupImage()
    {
        $group_id = $_POST['group_id'];

        $city_code = $this->getCityCode();

        $product_group = HtProductGroup::model()->findByPk($group_id);
        if (empty($product_group)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $group_id . '的产品分组。');

            return;
        }

        $to_dir = 'image/upload/country/' . $city_code . '/' . $group_id . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }


            $product_group['cover_image_url'] = $image_url;
            $result = $product_group->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionChangeProductGroupStatus()
    {
        $group_id = $this->getGroupID();
        $group = HtProductGroup::model()->findByPk($group_id);
        if (empty($group)) {
            EchoUtility::echoCommonFailed('找不到ID为' . $group_id . '的产品分组。');

            return;
        }

        $data = $this->getPostJsonData();
        $status = $data['status'];
        if ($status == 2) {
            $valid = $group->readyToOnline();
            if ($valid['code'] != 200) {
                EchoUtility::echoCommonFailed($valid['msg']);

                return;
            }
        }

        $result = ModelHelper::updateItem($group, $data);
        EchoUtility::echoMsg($result, '产品分组状态', '', $group);
    }

    public function actionGetProducts()
    {
        $data = HtProductGroupRef::model()->getProductsOfGroup($this->getGroupID());
        EchoUtility::echoMsgTF(true, '', $data);
    }

    public function actionGetCityProducts()
    {
        $result = HtProductGroup::model()->getCityProducts($this->getCityCode());
        $result = array_values($result['products']);

        EchoUtility::echoMsgTF(true, '', $result);
    }

    public function actionAddProduct()
    {
        $group_id = $this->getGroupID();
        $data = $this->getPostJsonData();
        $product_id = $data['product_id'];
        $display_order = $data['display_order'];

        $product_group_ref = new HtProductGroupRef();
        $product_group_ref['group_id'] = $group_id;
        $product_group_ref['product_id'] = $product_id;
        $product_group_ref['display_order'] = $display_order;
        if($data['status']){
            $product_group_ref['status'] = $data['status'];
        }

        try {
            $result = $product_group_ref->insert();
        } catch (Exception $e) {
            EchoUtility::echoCommonFailed('添加失败。请检查是否已添加过。');

            return;
        }

        $data = Converter::convertModelToArray($product_group_ref);
        if ($result) {
            $product = HtProduct::model()->with(array('description' => array('condition' => 'language_id=2')))->findByPk($product_id);
            $data['name'] = $product['description']['name'];
        }

        EchoUtility::echoMsgTF($result, '添加', $data);
    }

    //城市聚合页
    public function actionLinkPromotionToCity() {
        $city_code = $this->getCityCode();
        $promotion_id = $this->getPromotionId();

        if($city_code && $promotion_id) {
            $result = HtCityHotelPlus::model()->findByPk($city_code);

            //TODO: 上架检测，promotion挂接重复提示
            if(!empty($result)) {
                try {
                    $data['city_code'] = $city_code;
                    $result = ModelHelper::updateItem($result, array('city_code' => $city_code, 'promotion_id' => $promotion_id), array('city_code', 'promotion_id'));

                    EchoUtility::echoMsg($result, '聚合信息', '', $data);
                    return;
                } catch (Exception $exception) {
                }
            } else {
                $hotel_plus = new HtCityHotelPlus();
                $hotel_plus['city_code'] = $city_code;
                $hotel_plus['promotion_id'] = $promotion_id;

                EchoUtility::echoMsgTF($hotel_plus->insert(), '添加城市聚合信息');
                return;
            }
        }

        EchoUtility::echoCommonFailed('保存失败。'); //. $exception->getMessage());
    }

    public function actionCityPromotion() {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $city_code = $this->getCityCode();

        if($request_method == 'get') {
            $result = HtCityHotelPlus::model()->findByPk($city_code);

            EchoUtility::echoMsgTF(true, '获取城市聚合', Converter::convertModelToArray($result));
        } else if($request_method == 'post') {
            $data = $this->getPostJsonData();
            $result = HtCityHotelPlus::model()->findByPk($city_code);

            if(!empty($result)) {
                try {
                    $data['city_code'] = $city_code;
                    $result = ModelHelper::updateItem($result, $data, array('city_code', 'promotion_id', 'introduction_title', 'introduction_description', 'status'));
                    EchoUtility::echoMsg($result, '聚合信息', '', $data);
                } catch (Exception $exception) {
                    EchoUtility::echoCommonFailed('保存失败。'); //. $exception->getMessage());
                }
            } else {
                $props = array('promotion_id', 'introduction_title', 'introduction_description', 'status');
                $hotel_plus = new HtCityHotelPlus();
                $hotel_plus['city_code'] = $city_code;
                foreach($props as $prop) {
                    $hotel_plus[$prop] = $data[$prop];
                }

                EchoUtility::echoMsgTF($hotel_plus->insert(), '添加城市聚合信息', Converter::convertModelToArray($hotel_plus));
            }
        }
    }

    public function actionUpdateCityPromotionCover() {
        $city_code = $this->getCityCode();

        $to_dir = 'image/upload/country/' . $city_code . '/hotelplus/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $result = HtCityHotelPlus::model()->findByPk($city_code);
            if (empty($result)) {
                $hotel_plus = new HtCityHotelPlus();
                $hotel_plus['city_code'] = $city_code;
                $hotel_plus['introduction_image'] = $image_url;

                EchoUtility::echoMsgTF($hotel_plus->insert(), '新增图片', $image_url);
            } else {
                $result = ModelHelper::updateItem($result, array('introduction_image' => $image_url), array('introduction_image'));

                EchoUtility::echoMsgTF($result, '图片更新', $image_url);
            }
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    //线路商品保存
    public function actionUpdateProduct()
    {
        $group_id = $this->getGroupID();
        $data = $this->getPostJsonData();

        $return = true;
        $product_group_ref = HtProductGroupRef::model()->findByPk(array('group_id' => $group_id, 'product_id' => $data['product_id']));
        $result = ModelHelper::updateItem($product_group_ref,
                                          array('product_name' => $data['product_name'], 'status' => $data['status']));
        if ($result != 1) {
            $return = false;
        }

        EchoUtility::echoMsgTF($return, '更新产品');
    }

    //复制线路商品
    public function actionCopyProduct()
    {
        $result = true;
        $data = $this->getPostJsonData();
        if(is_array($data['tour_cities']) && count($data['tour_cities']) > 0){

            $cities = array();
            foreach($data['tour_cities'] as $city)
            {
                array_push($cities,$city['city_code']);
            }
            $tour_cities = implode(';',$cities);

            foreach($data['tour_cities'] as $city)
            {
                $group = HtProductGroup::model()->findByAttributes(array('type'=>7,'city_code'=>$city['city_code']));
                if($group){
                    $ref = HtProductGroupRef::model()->findByPk(array('group_id'=>$group['group_id'],'product_id'=>$data['product_id']));
                    if(!$ref){
                        $item = new HtProductGroupRef();
                        $item['group_id'] = $group['group_id'];
                        ModelHelper::fillItem($item, $data,
                                              array('product_id', 'product_name', 'product_image_url', 'display_order','status'));
                        $part_result = $item->insert();
                        if (!$part_result) {
                            $result = false;
                            break;
                        }
                    }
                }
            }
        }
        EchoUtility::echoMsgTF($result, '复制线路商品');
    }

    public function actionDeleteProduct()
    {
        $group_id = $this->getGroupID();
        $data = $this->getPostJsonData();
        $product_id = $data['product_id'];

        $result = HtProductGroupRef::model()->deleteByPk(array('group_id' => $group_id, 'product_id' => $product_id));
        $grouped = HtProductGroupRef::model()->productGrouped($product_id);
        HtProductGroupRef::clearCache($group_id);
        HtProductGroup::clearCache($group_id);

        EchoUtility::echoMsgTF($result > 0, '删除', $grouped);
    }

    public function actionChangeProductDisplayOrder()
    {
        $group_id = $this->getGroupID();
        $data = $this->getPostJsonData();
        $result = true;
        foreach ($data as $pgref) {
            $product_group_ref = HtProductGroupRef::model()->findByPk(array('group_id' => $group_id, 'product_id' => $pgref['product_id']));
            $part_result = ModelHelper::updateItem($product_group_ref,
                                                   array('display_order' => $pgref['display_order']));
            if ($part_result != 1) {
                $result = false;
                break;
            }
        }

        EchoUtility::echoMsgTF($result, '更新产品顺序');
    }

    public function actionAddOrUpdateProductImage()
    {
        $group_id = $this->getParam('group_id');
        $product_id = $this->getParam('product_id');
        if (empty($group_id) || empty($product_id)) {
            EchoUtility::echoCommonFailed('参数不完整。');

            return;
        }

        $pgr = HtProductGroupRef::model()->findByPk(['group_id' => $group_id, 'product_id' => $product_id]);

        if (empty($pgr)) {
            EchoUtility::echoCommonFailed('未找到group id为' . $group_id . ', product id为' . $product_id . '记录。');

            return;
        }

        $city_code = $this->getParam('city_code');

        $to_dir = 'image/upload/country/' . $city_code . '/' . $group_id . '/' . $product_id . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if (empty($image_url)) {
                EchoUtility::echoCommonFailed('上传文件到七牛失败。');

                return;
            }

            $pgr['product_image_url'] = $image_url;
            $result = $pgr->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    //线路图片上传
    public function actionAddOrUpdateProductLineImage()
    {
        $group_id = $this->getParam('group_id');
        $product_id = $this->getParam('product_id');
        if (empty($group_id) || empty($product_id)) {
            EchoUtility::echoCommonFailed('参数不完整。');

            return;
        }

        $pgr = HtProductGroupRef::model()->findByPk(['group_id' => $group_id, 'product_id' => $product_id]);

        if (empty($pgr)) {
            EchoUtility::echoCommonFailed('未找到group id为' . $group_id . ', product id为' . $product_id . '记录。');

            return;
        }

        $city_code = $this->getParam('city_code');

        $to_dir = 'image/upload/country/' . $city_code . '/' . $group_id . '/' . $product_id . '/line_images/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if (empty($image_url)) {
                EchoUtility::echoCommonFailed('上传文件到七牛失败。');

                return;
            }

            $pgr['line_image_url'] = $image_url;
            $result = $pgr->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionCitySeo()
    {
        $city_code = $this->getCityCode();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($request_method == 'get') {
            $city_seo = HtSeoSetting::model()->findByCityCode($city_code);

            EchoUtility::echoMsgTF(true, '获取城市SEO', Converter::convertModelToArray($city_seo));
        } else if ($request_method == 'post') {
            $new_data = $this->getPostJsonData();

            $city_seo = HtSeoSetting::model()->findByCityCode($city_code);
            if ($city_seo == null) {
                $city_seo = new HtSeoSetting();
                $city_seo['type'] = HtSeoSetting::TYPE_CITY;
                $city_seo['id'] = $city_code;
                ModelHelper::fillItem($city_seo, $new_data, array('title', 'description', 'keywords'));
                $result = $city_seo->insert();
            } else {
                $result = ModelHelper::updateItem($city_seo, $new_data, array('title', 'description', 'keywords'));
            }

            EchoUtility::echoMsgTF($result, '更新城市SEO');
        }
    }

    public function actionUpdateCityRecommend()
    {
        $city_codes = $this->getPostJsonData();
        if (implode(",", $city_codes) == implode(",", array_unique($city_codes))) {
            $city_code_str = implode(",", $city_codes);
            $recommend = HtSetting::model()->find("`group` = 'city' and `key` = 'city_recommend' ");
            $recommend['value'] = $city_code_str;
            $result = $recommend->update();
            EchoUtility::echoMsgTF($result, '更新');
        } else {
            EchoUtility::echoMsg(-1, '', '城市有重复，无法保存');
        }
    }

    public function actionFetchCityRecommend()
    {
        $city_recommend_str = HtSetting::model()->find("`group` = 'city' and `key` = 'city_recommend' ");
        $city_recommend = explode(",", $city_recommend_str['value']);
        $city_recommend_list = [];
        if (!empty($city_recommend)) {
            foreach ($city_recommend as $item) {
                if (!empty($item)) {
                    $city = HtCity::model()->findByPk(array('city_code' => $item, 'has_product' => '1'));
                    if (!empty($city)) {
                        array_push($city_recommend_list, Converter::convertModelToArray($city));
                    }
                }
            }
        }
        EchoUtility::echoMsgTF(true, '获取城市推荐列表', $city_recommend_list);
    }

    // 体验分组获取，更新
    public function actionCityColumn()
    {
        $city_code = $this->getCityCode();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ('get' === $request_method) {
            $column = HtCityColumn::model()->with('columns.article')->findAllByAttributes(['city_code' => $city_code]);

            $data = Converter::convertModelToArray($column);

            EchoUtility::echoCommonMsg(200, '', $data);
        } else if ('post' === $request_method) {
            $data = $this->getPostJsonData();
            $column_id = (int)$this->getParam('column_id');

            $column = HtCityColumn::model()->findByPk($column_id);
            $result = ModelHelper::updateItem($column, $data, ['name']);

            EchoUtility::echoMsgTF(1 == $result, '更新', $column);
        }
    }

    // 体验分组文章添加，删除，更新
    public function actionCityColumnRef()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ('get' === $request_method) {

        } else if ('post' === $request_method) {
            $data = $this->getPostJsonData();

            $column_id = (int)$this->getParam('column_id');
            $article_id = (int)$data['article_id'];
            if (empty($column_id) || empty($article_id)) {
                EchoUtility::echoCommonFailed('参数有误。');

                return;
            }

            $item = HtCityColumnRef::model()->findByPk(['column_id' => $column_id, 'article_id' => $article_id]);
            if (empty($item)) {
                $article = HtArticle::model()->findByPk($article_id);
                if (empty($article) || $article['status'] == 0) {
                    EchoUtility::echoCommonFailed('文章不存在或者尚未上线。');

                    return;
                }
                $item = new HtCityColumnRef();
                $item['column_id'] = $column_id;
                ModelHelper::fillItem($item, $data, ['article_id', 'display_order']);
                try {
                    $result = $item->insert();
                } catch(Exception $e) {
                    EchoUtility::echoCommonFailed('添加失败。');

                    return;
                }

                $return_data = Converter::convertModelToArray($item);
                $return_data['article'] = Converter::convertModelToArray($article);

                EchoUtility::echoMsgTF($result, '添加', $return_data);
            } else {
//                $result = ModelHelper::updateItem($item, $data, ['display_order']);

                EchoUtility::echoCommonFailed('文章已添加。');
            }
        } else if ('delete' === $request_method) {
            $column_id = (int)$this->getParam('column_id');
            $article_id = (int)$this->getParam('article_id');

            HtCityColumnRef::model()->deleteByPk(['column_id' => $column_id, 'article_id' => $article_id]);

            EchoUtility::echoCommonMsg(200, '删除成功！');
        }
    }

    //更改体验分组文章顺序
    public function actionArticleDisplayOrder()
    {
        $column_id = $this->getParam('column_id');
        $data = $this->getPostJsonData();
        $result = true;
        foreach ($data as $column_ref) {
            $item = HtCityColumnRef::model()->findByPk(['column_id' => $column_id, 'article_id' => $column_ref['article_id']]);
            $part_result = ModelHelper::updateItem($item, $column_ref, ['display_order']);
            if ($part_result != 1) {
                $result = false;
                break;
            }
        }
        EchoUtility::echoMsgTF($result, '更改体验分组文章顺序');
    }

    public function actionArticleImage()
    {
        $column_id = $this->getParam('column_id');
        $article_id = $this->getParam('article_id');
        if (empty($column_id) || empty($article_id)) {
            EchoUtility::echoCommonFailed('参数不完整。');

            return;
        }

        $ccr = HtCityColumnRef::model()->findByPk(['column_id' => $column_id, 'article_id' => $article_id]);

        if (empty($ccr)) {
            EchoUtility::echoCommonFailed('未找到column id为' . $column_id . ', article id为' . $article_id . '记录。');

            return;
        }

        $city_code = $this->getParam('city_code');

        $to_dir = 'image/upload/country/' . $city_code . '/' . $column_id . '/' . $article_id . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if (empty($image_url)) {
                EchoUtility::echoCommonFailed('上传文件到七牛失败。');

                return;
            }

            $ccr['article_image_url'] = $image_url;
            $result = $ccr->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    //获取城市文章
    public function actionGetArticles()
    {
        $city_code = $this->getCityCode();
        $query_result = HtArticle::model()->findAllByAttributes(['city_code' => $city_code, 'status' => 1]);
        EchoUtility::echoJson(Converter::convertModelToArray($query_result));
    }

    private function getCityCode()
    {
        return Yii::app()->request->getParam('city_code');
    }

    private function getGroupID()
    {
        return (int)Yii::app()->request->getParam('group_id');
    }

    private function getPromotionId()
    {
        return (int)Yii::app()->request->getParam('promotion_id');
    }
}
