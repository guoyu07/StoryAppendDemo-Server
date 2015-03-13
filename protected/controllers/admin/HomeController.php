<?php

class HomeController extends AdminController
{
    const MIN_ITEMS_COUNT = 6;
    const MAX_ITEMS_COUNT = 8;
    public $layout = '//layouts/common';

    public function actionIndex()
    {
        $this->pageTitle = '首页管理';
        $request_urls = array(
            'back' => $this->createUrl('home/index'),
            'getHomeGroups' => $this->createUrl('home/getHomeGroups'),
            'editHomeGroup' => $this->createUrl('home/edit', array('group_id' => '')),
            'addHomeGroup' => $this->createUrl('home/addHomeGroup'),
            'deleteHomeGroup' => $this->createUrl('home/deleteHomeGroup', array('group_id' => '')),
            'changeHomeGroupStatus' => $this->createUrl('home/changeHomeGroupStatus', array('group_id' => '')),
            'updateHomeGroupOrder' => $this->createUrl('home/updateHomeGroupOrder'),
            'addHomeImage' => $this->createUrl('home/addHomeCarousel'),
            'uploadHomeImage' => $this->createUrl('home/updateHomeCarouselImage'),
            'updateHomeImagesOrders' => $this->createUrl('home/updateHomeCarouselOrders'),
            'homeSeo' => $this->createUrl('home/homeSeo'),
            'homeCarousel' => $this->createUrl('home/homeCarousel', array('id'=>'')),
            'hotCountry' => $this->createUrl('country/hotCountry', array('country_code'=>'')),
        );
        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );
        $this->render('index');
    }

    public function actionEdit()
    {
        $this->pageTitle = '首页分组编辑';
        $group_id = $this->getGroupID();

        $request_urls = array(
            'fetchHomeGroup' => $this->createUrl('home/getHomeGroup', array('group_id' => $group_id)),
            'updateHomeGroup' => $this->createUrl('home/updateHomeGroup', array('group_id' => $group_id)),
            'addHomeGroupItem' => $this->createUrl('home/addHomeGroupItem', array('group_id' => $group_id)),
            'updateHomeGroupItem' => $this->createUrl('home/updateHomeGroupItem', array('group_id' => $group_id)),
            'updateHomeGroupItemImage' => $this->createUrl('home/updateHomeGroupItemImage',
                                                           array('group_id' => $group_id)),
            'deleteHomeGroupItem' => $this->createUrl('home/deleteHomeGroupItem', array('group_id' => $group_id)),
            'updateHomeGroupType' => $this->createUrl('home/updateHomeGroupType', array('group_id' => $group_id)),
            'updateHomeGroupItemOrder' => $this->createUrl('home/updateHomeGroupItemsOrders',
                                                           array('group_id' => $group_id)),
        );
        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );
        $this->render('edit');
    }

    public function actionGetHomeGroups()
    {
        $data = HtHomeRecommend::model()->with('items_count')->getAll();

        EchoUtility::echoMsgTF(true, '', Converter::convertModelToArray($data));
    }

    public function actionUpdateHomeGroupOrder()
    {
        $data = $this->getPostJsonData();
        $groups = $data['group_order'];

        $result = true;
        foreach ($groups as $g) {
            $hg = HtHomeRecommend::model()->findByPk($g['group_id']);
            if (empty($hg)) {
                $result = false;
            } else {
                $hg['display_order'] = $g['display_order'];
                $r = $hg->update();
                $result = $result && $r;
            }

            if (!$result) {
                break;
            }
        }

        EchoUtility::echoMsgTF($result, '推荐分组排序');
    }

    public function actionUpdateHomeGroupType()
    {
        $group_id = $this->getGroupID();
        $data = $this->getPostJsonData();
        $type = $data['type'];
        $hg = $this->getHomeGroup();
        if (empty($hg)) {
            return;
        }
        $hg['type'] = $type;
        $result = $hg->update();
        if ($result) {
            HtHomeRecommendItem::model()->deleteAll('group_id=' . $group_id);
            HtHomeRecommend::clearCache($group_id);
        }

        EchoUtility::echoMsgTF($result, '更改');
    }

    public function actionAddHomeGroup()
    {
        $newHG = new HtHomeRecommend();
        $newHG['name'] = '新分组';
        $newHG['title'] = '大标题';
        $newHG['brief'] = '简介';
        $newHG['type'] = 1;
        $newHG['status'] = 1;
        $newHG['display_order'] = 1;

        $result = $newHG->insert();
        if ($result) {
            $newHG['display_order'] = $newHG['group_id'];
            $newHG->update();
        }
        $data = HtHomeRecommend::model()->with('items_count')->getAll();

        EchoUtility::echoMsgTF($result, '添加', Converter::convertModelToArray($data));
    }

    public function actionGetHomeGroup()
    {
        $group_id = $this->getGroupID();
        $hg = $this->getHomeGroup();
        $hg_items = HtHomeRecommendItem::model()->getByGroupID($group_id);
        $items = array();
        foreach ($hg_items as $item) {
            $item_a = Converter::convertModelToArray($item);
            $city = HtCity::model()->getByCode($item['city_code']);
            if ($hg['type'] == 1 || $hg['type'] == 3) {
                $item_a['price'] = $this->getProductPrice($item['product_id']);
                $item_a['city_name'] = $city['cn_name'];
            } else {
                $item_a['cn_name'] = $city['cn_name'];
                $item_a['en_name'] = $city['en_name'];
            }
            array_push($items, $item_a);
        }

        if (!empty($hg)) {
            echo CJSON::encode(array('code' => 200, 'msg' => '获取成功！', 'data' => array('home_group' => $hg, 'items' => $items)));
        }
    }

    public function actionUpdateHomeGroup()
    {
        $data = $this->getPostJsonData();
        $group_id = $this->getGroupID();
        $hg = HtHomeRecommend::model()->findByPk($group_id);
        $result = ModelHelper::updateItem($hg, $data, array('name', 'title', 'brief', 'type'));

        EchoUtility::echoMsg($result, '', '', $hg);
    }

    public function actionDeleteHomeGroup()
    {
        $data = $this->getPostJsonData();
        $group_id = (int)$data['group_id'];
        $hg = $this->getHomeGroup($group_id);
        if (empty($hg)) return;
        $hg->delete();
        HtHomeRecommendItem::model()->deleteByGroupID($group_id);
        HtHomeRecommend::clearCache($group_id);

        echo CJSON::encode(array('code' => 200, 'msg' => '删除成功！'));
    }

    public function actionChangeHomeGroupStatus()
    {
        $data = $this->getPostJsonData();
        $status = $data['status'];

        $hg = HtHomeRecommend::model()->with('items_count')->findByPk($data['group_id']);
        $hg_data = Converter::convertModelToArray($hg);
        if (empty($hg)) return;

        if (in_array($status, array(1, 2))) {
            if ($status == 2) {
                //  if $status == 2, validate the data first
                if ($hg_data['items_count'] < self::MIN_ITEMS_COUNT || $hg_data['items_count'] > self::MAX_ITEMS_COUNT) {
                    EchoUtility::echoCommonFailed('包含点数不合要求（' . self::MIN_ITEMS_COUNT . '～' . self::MAX_ITEMS_COUNT . '）。请编辑后再试。');

                    return;
                }
            }

            $hg['status'] = $status;
            $result = $hg->update();
            EchoUtility::echoMsgTF($result, '状态更新', $status);
        } else {
            EchoUtility::echoCommonFailed('状态参数非法。');
        }
    }

    public function actionAddHomeGroupItem()
    {
        $group_id = $this->getGroupID();
        $data = $this->getPostJsonData();

        $hg = HtHomeRecommend::model()->with('items_count')->findByPk($group_id);
        // check the item count first -- should be less than 9
        if ($hg->items_count == self::MAX_ITEMS_COUNT) {
            EchoUtility::echoCommonFailed('已经有' . self::MAX_ITEMS_COUNT . '个点了，无法再添加。');

            return;
        }

        if ($hg['type'] == 1 || $hg['type'] == 3) { // product
            $product_id = (int)$data['qs'];


            $product = HtProduct::model()->getProductDetail($product_id);
            if (empty($product)) {
                EchoUtility::echoCommonFailed('未找到ID为' . $product_id . '的产品。');

                return;
            } else {
                if (HtHomeRecommendItem::model()->isExists($group_id, 1, $product_id)) {
                    EchoUtility::echoCommonFailed('产品已添加。');

                    return;
                } else if ($product['status'] != 3) {
                    EchoUtility::echoCommonFailed('产品状态不是已上架。请检查后再试。');

                    return;
                }
            }

            $item = new HtHomeRecommendItem();
            $item['group_id'] = $group_id;
            $item['city_code'] = $product['city_code'];
            $item['product_id'] = $product_id;
            $item['cover_url'] = HtProductImage::model()->getProductCover($product_id);
            $item['product_name'] = $product['name'];
            $item['product_desc'] = $product['summary'];
            $item['display_order'] = 1;

            $result = $item->insert();
            $return_data = Converter::convertModelToArray($item);
            if ($result) {
                $return_data['price'] = $this->getProductPrice($product_id);
                $city = HtCity::model()->getByCode($product['city_code']);
                $return_data['city_name'] = $city['cn_name'];
            }

            EchoUtility::echoMsgTF($result, '添加', $return_data);
        } else if ($hg['type'] == 2) { // city
            $city_code = $data['qs'];
            $city = HtCity::model()->getByCode($city_code);
            if (empty($city)) {
                EchoUtility::echoCommonFailed('未找到ID为' . $city_code . '的城市。');

                return;
            } else if (HtHomeRecommendItem::model()->isExists($group_id, 2, 0, $city_code)) {
                EchoUtility::echoCommonFailed('城市已添加。');

                return;
            }

            $item = new HtHomeRecommendItem();
            $item['group_id'] = $group_id;
            $item['city_code'] = $city_code;
            $item['product_id'] = 0;
            $item['cover_url'] = HtCityImage::model()->getGridImageUrl($city_code);
            $item['product_name'] = '';
            $item['product_desc'] = '';
            $item['display_order'] = 1;

            $result = $item->insert();
            $return_data = Converter::convertModelToArray($item);
            if ($result) {
                $return_data['cn_name'] = $city['cn_name'];
                $return_data['en_name'] = $city['en_name'];
            }

            EchoUtility::echoMsgTF($result, '添加', $return_data);
        }

    }

    public function actionUpdateHomeGroupItem()
    {
        $group_id = $this->getGroupID();
        $hg = HtHomeRecommend::model()->findByPk($group_id);

        if ($hg['type'] == 2) {
            EchoUtility::echoCommonFailed('不能编辑城市信息。');

            return;
        }

        $data = $this->getPostJsonData();
        $item_id = $data['id'];
        $item = HtHomeRecommendItem::model()->findByPk($item_id);
        $result = ModelHelper::updateItem($item, $data, array('product_name', 'product_desc'));
        $return_data = Converter::convertModelToArray($item);
        if ($result) {
            $return_data['price'] = $this->getProductPrice($item['product_id']);
            $city = HtCity::model()->getByCode($item['city_code']);
            $return_data['city_name'] = $city['cn_name'];
        }

        EchoUtility::echoMsg($result, '', '', $return_data);
    }

    public function actionUpdateHomeGroupItemImage()
    {
        $item_id = $_POST['id'];
        if ($item_id == -1) {
            // hacked to update home recommend cover_url
            $group_id = $this->getGroupID();
            $this->updateHomeRecommendCover($group_id);

            return;
        }

        $item = HtHomeRecommendItem::model()->findByPk($item_id);
        if (empty($item)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $item_id . '的点。');

            return;
        }

        $to_dir = 'image/upload/ht_home_recommend_item/' . $item_id . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $item['cover_url'] = $image_url;
            $result = $item->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    private function updateHomeRecommendCover($group_id)
    {
        $home_group = HtHomeRecommend::model()->findByPk($group_id);
        if (empty($home_group)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $group_id . '的推荐分组。');

            return;
        }

        $to_dir = 'image/upload/ht_home_recommend/' . $group_id . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $home_group['cover_url'] = $image_url;
            $result = $home_group->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    public function actionDeleteHomeGroupItem()
    {
        $data = $this->getPostJsonData();
        $item_id = (int)$data['id'];
        $item = HtHomeRecommendItem::model()->findByPk($item_id);
        $result = true;
        if(!empty($item)) {
            $group_id = $item['group_id'];

            $result = HtHomeRecommendItem::model()->deleteByPk($item_id) > 0;
            HtHomeRecommend::clearCache($group_id);
        }

        EchoUtility::echoMsgTF($result, '删除');
    }

    public function actionDeleteHomeGroupItemsAll()
    {
        $group_id = $this->getGroupID();
        HtHomeRecommendItem::model()->deleteAll('group_id=' . $group_id) > 0;
        HtHomeRecommend::clearCache($group_id);

        EchoUtility::echoMsgTF(true, '删除');
    }

    public function actionUpdateHomeGroupItemsOrders()
    {
        $data = $this->getPostJsonData();
        $result = true;
        foreach ($data as $item_order) {
            $item = HtHomeRecommendItem::model()->findByPk($item_order['id']);
            if (empty($item)) {
                $result = false;
            } else {
                $item['display_order'] = $item_order['display_order'];
                $r = $item->update();
                $result = $result && $r;
            }

            if (!$result) {
                break;
            }
        }

        EchoUtility::echoMsgTF($result, '更新');
    }

    //获取轮播图片列表
    public function actionGetHomeCarousel()
    {
        $hc = new HtHomeCarousel();
        $data = $hc->findAll();

        echo CJSON::encode(array('code' => 200, 'msg' => '', 'data' => Converter::convertModelToArray($data)));
    }

    //新增轮播图片项
    public function actionAddHomeCarousel()
    {
        $item = new HtHomeCarousel();
        $result = $item->insert();
        if ($result) {
            $item['display_order'] = $item->getPrimaryKey();
            $item->update();
        }
        EchoUtility::echoMsgTF($result, '新增', array('id' => $item->getPrimaryKey()));
    }

    //更新轮播图片链接
    public function actionUpdateHomeCarousel()
    {
        $data = $this->getPostJsonData();
        $result = true;
        $link_url = strtolower($data['link_url']);
        if (!(strpos($link_url, 'http://') === 0 || strpos($link_url, 'https://') === 0)) {
            EchoUtility::echoCommonFailed("链接格式有误。请检查修改后再试。");

            return;
        }

        $hc = new HtHomeCarousel();
        $item = $hc->findByPk($data['id']);
        if (empty($item)) {
            $result = false;
        } else {
            $item['link_url'] = $link_url;
            if (strlen($item['image_url']) > 10) {
                $item['status'] = 1;
            }

            $r = $item->update();
            $result = $result && $r;
        }
        EchoUtility::echoMsgTF($result, '保存');
    }

    //更新轮播图片顺序
    public function actionUpdateHomeCarouselOrders()
    {
        $data = $this->getPostJsonData();
        $result = true;
        foreach ($data as $item_order) {
            $hc = new HtHomeCarousel();
            $item = $hc->findByPk($item_order['id']);
            if (empty($item)) {
                $result = false;
            } else {
                $item['display_order'] = $item_order['display_order'];
                $r = $item->update();
                $result = $result && $r;
            }

            if (!$result) {
                break;
            }
        }
        EchoUtility::echoMsgTF($result, '更新');
    }

    //更新（上传）轮播图片
    public function actionUpdateHomeCarouselImage()
    {
        $id = (int)$_POST['id'];
        $hc = new HtHomeCarousel();
        $item = $hc->findByPk($id);
        if (empty($item)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $id . '的轮播图片。');

            return;
        }
        $to_dir = Yii::app()->params['HOME_IMAGE_ROOT'] . $id . '/';
        $result = FileUtility::uploadFile($to_dir);
        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . $to_dir . $file;
            }
            $item['image_url'] = $image_url;
            $result = $item->update();

            EchoUtility::echoMsgTF($result, '保存', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    //删除轮播图片项
    public function actionDeleteHomeCarousel()
    {
        $data = $this->getPostJsonData();
        $id = (int)$data['id'];
        $dir = Yii::app()->params['DIR_UPLOAD_ROOT'] . Yii::app()->params['HOME_IMAGE_ROOT'] . $id;
        @FileUtility::deleteDir($dir);
        $hc = new HtHomeCarousel();
        $result = $hc->deleteByPk($id) > 0;
        EchoUtility::echoMsgTF($result, '删除');
    }

    public function actionHomeCarousel()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') {
            $hc = new HtHomeCarousel();
            $data = $hc->findAll('type = 0');

            EchoUtility::echoMsgTF(true, '', $data);
        } else if ($request_method == 'post') {
            $data = $this->getPostJsonData();
            $result = true;
            $link_url = strtolower($data['link_url']);
            if (!(strpos($link_url, 'http://') === 0 || strpos($link_url, 'https://') === 0)) {
                EchoUtility::echoCommonFailed("链接格式有误。请检查修改后再试。");

                return;
            }

            $hc = new HtHomeCarousel();
            $item = $hc->findByPk($data['id']);
            if (empty($item)) {
                $result = false;
            } else {
                $item['link_url'] = $link_url;
                if (strlen($item['image_url']) > 10) {
                    $item['status'] = 1;
                }

                $r = $item->update();
                $result = $result && $r;
            }

            EchoUtility::echoMsgTF($result, '保存');
        } else if ($request_method == 'delete') {
            $id = (int)$this->getParam('id', 0);
            if(empty($id)) {
                EchoUtility::echoMsgTF(false, '删除');
                return;
            }
            $dir = Yii::app()->params['DIR_UPLOAD_ROOT'] . Yii::app()->params['HOME_IMAGE_ROOT'] . $id;
            @FileUtility::deleteDir($dir);
            $hc = new HtHomeCarousel();
            $result = $hc->deleteByPk($id) > 0;

            HtHomeCarousel::clearCache();

            EchoUtility::echoMsgTF($result, '删除');
        }
    }

    public function actionHomeSeo()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') {
            $home_seo = HtSeoSetting::model()->findHomeSeoSetting();

            EchoUtility::echoMsgTF(true, '获取首页SEO', $home_seo);
        } else if ($request_method == 'post') {
            $new_data = $this->getPostJsonData();

            $home_seo = HtSeoSetting::model()->findHomeSeoSetting();
            if ($home_seo == null) {
                $home_seo = new HtSeoSetting();
                $home_seo['id'] = 'home';
                $home_seo['type'] = HtSeoSetting::TYPE_HOME;
                ModelHelper::fillItem($home_seo, $new_data, array('title', 'description', 'keywords'));
                $result = $home_seo->insert();
            } else {
                $result = ModelHelper::updateItem($home_seo, $new_data, array('title', 'description', 'keywords'));
            }

            EchoUtility::echoMsgTF($result, '更新首页SEO');
        }
    }

    private function getGroupID()
    {
        return (int)Yii::app()->request->getParam('group_id');
    }

    private function getHomeGroup($group_id = 0)
    {
        if ($group_id == 0) {
            $group_id = $this->getGroupID();
        }
        $hg = HtHomeRecommend::model()->findByPk($group_id);
        if (empty($hg)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $group_id . '的分组。');
        }

        return $hg;
    }

    private function getProductPrice($product_id)
    {
        $prices = HtProductPricePlan::model()->getShowPrices($product_id);

        return $prices['price'];
    }
}