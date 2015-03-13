<?php

class PromotionController extends AdminController
{
    public $layout = '//layouts/common';

    public function actionIndex()
    {
        $this->pageTitle = '活动管理';

        $request_urls = array(
            'promotion' => $this->createUrl('promotion/promotion'),
            'editPromotion' => $this->createUrl('promotion/edit', array('promotion_id' => '')),
            'previewPromotion' => $this->createUrl('promotion/index', array('preview' => 'true', 'promotion_id' => '000'), '', false),
            'getPromotionList' => $this->createUrl('promotion/getPromotionList'),
            'changePromotionStatus' => $this->createUrl('promotion/changePromotionStatus'),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('index');
    }

    public function actionEdit()
    {
        $this->pageTitle = '活动编辑';

        $promotion_id = $this->getPromotionID();

        $request_urls = array(
            'promotion' => $this->createUrl('promotion/promotion', array('promotion_id' => $promotion_id)),
            'viewPromotion' => $this->createUrl('promotion/index', array('promotion_id' => $promotion_id), '', false),
            'previewPromotion' => $this->createFrontUrl('promotion/index', array('preview' => 'true', 'promotion_id' => $promotion_id)),
            'previewMobilePromotion' => $this->createUrl('mobile#/promotion/' . $promotion_id, [], '&', false),
            'promotionGroup' => $this->createUrl('promotion/promotionGroup', array('promotion_id' => $promotion_id, 'group_id' => '')),
            'promotionGroupProduct' => $this->createUrl('promotion/promotionGroupProduct',
                                                        array('promotion_id' => $promotion_id, 'group_id' => '')),
            'deletePromotionGroupProduct' => $this->createUrl('promotion/deletePromotionGroupProduct', ['promotion_id' => $promotion_id]),
            'getPromotionDetail' => $this->createUrl('promotion/getPromotionDetail',
                                                     array('promotion_id' => $promotion_id)),
            'updatePromotionBanner' => $this->createUrl('promotion/updatePromotionBanner',
                                                        array('promotion_id' => $promotion_id)),
            'updatePromotionGroupOrder' => $this->createUrl('promotion/updateGroupOrder',
                                                            array('promotion_id' => $promotion_id)),
            'updatePromotionGroupProductOrder' => $this->createUrl('promotion/updateGroupProductOrder',
                                                                   array('promotion_id' => $promotion_id, 'group_id' => '')),
            'updatePromotionMobileBanner' => $this->createUrl('promotion/updatePromotionMobileBanner',
                                                              array('promotion_id' => $promotion_id)),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );

        $this->render('edit');
    }

    //获取促销活动列表
    public function actionGetPromotionList()
    {
        $data = $this->getPostJsonData();
        $c = new CDbCriteria();
        $total_count = new CDbCriteria();

        $c->limit = $data['paging']['limit'];
        $c->offset = $data['paging']['start'];
        EchoUtility::echoMsgTF(true, '获取促销活动列表', array(
            'total_count' => HtPromotion::model()->count($total_count),
            'data' => Converter::convertModelToArray(HtPromotion::model()->findAll($c))
        ));
    }

    //新增或更新促销活动
    public function actionPromotion()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $promotion_id = $this->getPromotionID();

        if($request_method == 'post') {
            if(empty($promotion_id)) {
                $promotion = new HtPromotion();
                $promotion['name'] = $this->getParam('name');
                $promotion['title'] = '';
                $promotion['description'] = '';
                $promotion['route'] = '';
                $promotion['status'] = 0;
                $result = $promotion->insert();
                $data = Converter::convertModelToArray($promotion);
                if($result) {
                    $promotion_rule = new HtPromotionRule();
                    $promotion_rule['promotion_id'] = $promotion->getPrimaryKey();
                    $promotion_rule['start_date'] = '';
                    $promotion_rule['end_date'] = '';
                    $promotion_rule['discount_range'] = '';
                    $promotion_rule['discount_rate'] = '';
                    $promotion_rule->insert();
                    $data['promotion_rule'] = Converter::convertModelToArray($promotion_rule);

                    $seo = new HtSeoSetting();
                    $seo['type'] = 6;
                    $seo['id'] = $promotion->getPrimaryKey();
                    $seo->insert();
                }
                EchoUtility::echoMsgTF($result, '添加活动', $promotion->getPrimaryKey());
            } else {
                $data = $this->getPostJsonData();
                $promotion = HtPromotion::model()->findByPk($promotion_id);
                $promotion_result = ModelHelper::updateItem($promotion, $data, array('title', 'description', 'route'));
                $promotion_rule = HtPromotionRule::model()->findByPk($promotion_id);
                $rule_result = ModelHelper::updateItem($promotion_rule, $data['promotion_rule'],
                                                       array('start_date', 'end_date', 'discount_range', 'discount_rate'));
                $promotion_seo = HtSeoSetting::model()->findByPromotionId($promotion_id);
                $seo_result = ModelHelper::updateItem($promotion_seo, $data['seo'],
                                                      array('title', 'description', 'keywords'));
                $result = $promotion_result && $rule_result && $seo_result;
                EchoUtility::echoMsgTF($result, '编辑活动');
            }
        }
    }

    //变更促销活动状态
    public function actionChangePromotionStatus()
    {
        //TODO: protection if has offline product
        $promotion_id = $this->getPromotionID();
        $promotion = HtPromotion::model()->findByPk($promotion_id);
        if(empty($promotion)) {
            EchoUtility::echoCommonFailed('找不到ID为' . $promotion_id . '的促销活动。');

            return;
        }
        $data = $this->getPostJsonData();
        $result = ModelHelper::updateItem($promotion, $data, array('status'));
        EchoUtility::echoMsgTF($result, '状态更新', Converter::convertModelToArray($promotion));
    }

    //获取促销详情
    public function actionGetPromotionDetail()
    {
        $promotion_id = $this->getPromotionID();
        $result = HtPromotion::model()->fetchPromotionDetail($promotion_id);

        if($result['is_hotelplus']) {
            $result['hotelplus'] = Converter::convertModelToArray(HtCityHotelPlus::model()->findByAttributes(['promotion_id' => $promotion_id]));

            $product_query = ['type' => HtProduct::T_HOTEL_BUNDLE, 'city_code' => $result['hotelplus']['city_code']];
            $description_query = ['description' => [
                'select' => 'name',
                'condition' => 'language_id=2'
            ]];
            $result['hotelplus_products'] = Converter::convertModelToArray(HtProduct::model()->published()->with($description_query)->findAllByAttributes($product_query));

            function formatProduct(&$item) {
                $item = [
                    'product_id' => $item['product_id'],
                    'name' => $item['description']['name']
                ];
            }

            array_walk($result['hotelplus_products'], 'formatProduct');
        }
        EchoUtility::echoMsgTF(true, '获取促销详情', $result);
    }

    //更新活动图片
    public function actionUpdatePromotionBanner()
    {
        //传入promotion_id和image，返回成功失败
        $promotion_id = $this->getPromotionID();
        $promotion = HtPromotion::model()->findByPk($promotion_id);
        if(empty($promotion)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $promotion_id . '的活动。');

            return;
        }

        $to_dir = 'image/upload/promotion/' . $promotion_id . '/';
        $result = FileUtility::uploadFile($to_dir);

        if($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $promotion['image'] = $image_url;
            $result = $promotion->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    //更新活动手机图片
    public function actionUpdatePromotionMobileBanner()
    {
        //传入promotion_id和image，返回成功失败
        $promotion_id = $this->getPromotionID();
        $promotion = HtPromotion::model()->findByPk($promotion_id);
        if(empty($promotion)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $promotion_id . '的活动。');

            return;
        }

        $to_dir = 'image/upload/promotion/' . $promotion_id . '/mobile/';
        $result = FileUtility::uploadFile($to_dir);

        if($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }

            $promotion['mobile_image'] = $image_url;
            $result = $promotion->update();

            EchoUtility::echoMsgTF($result, '更新', $image_url);
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    //活动分组
    public function actionPromotionGroup()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $promotion_id = $this->getPromotionID();
        $group_id = $this->getGroupID();

        if($request_method == 'post') {
            if(empty($group_id)) {
                $group = new HtPromotionGroup();
                $group['name'] = '新的分组';
                $group['description'] = '';
                $group['promotion_id'] = $promotion_id;
                $group['attach_url'] = '';
                $group['display_order'] = $this->getParam('display_order');
                $result = $group->insert();
                $data = Converter::convertModelToArray($group);
                EchoUtility::echoMsgTF($result, '添加活动分组', $data);
            } else {
                $data = $this->getPostJsonData();
                $group = HtPromotionGroup::model()->findByPk($data['group_id']);
                $result = ModelHelper::updateItem($group, $data,
                                                  array('name', 'description', 'attach_url'));
                EchoUtility::echoMsgTF($result, '更新活动分组信息', $group);
            }
        } else if($request_method == 'delete') {
            $result = HtPromotionGroup::model()->deleteByPk($group_id);
            HtPromotion::clearCache($promotion_id);
            EchoUtility::echoMsgTF($result > 0, '删除活动分组');
        }
    }

    //活动分组排序
    public function actionUpdateGroupOrder()
    {
        $data = $this->getPostJsonData();
        foreach($data as $group) {
            $one_group = HtPromotionGroup::model()->findByPk($group['group_id']);
            $result = ModelHelper::updateItem($one_group, $group,
                                              array('display_order'));
            if(!$result) {
                EchoUtility::echoMsgTF(false, '更新活动分组排序');

                return;
            }
        }
        EchoUtility::echoMsgTF(true, '更新活动分组排序');
    }

    //活动分组商品
    public function actionPromotionGroupProduct()
    {
        $request_data = $this->getPostJsonData();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        $product_id = $this->getProductID();
        $promotion_id = $this->getPromotionID();

        if($request_method == 'post') {
            $promotion_rule = HtPromotionRule::model()->findByPk($promotion_id);
            $existing_result = HtPromotionProduct::model()->findByPk(array('group_id' => $request_data['group_id'], 'product_id' => $product_id));
            $is_hotelplus = HtCityHotelPlus::model()->isPromotionHotelplus($promotion_id);
            $product = Yii::app()->product->getSimplifiedData($product_id, $promotion_rule['start_date']);

            if($is_hotelplus && $product['type'] != 8) {
                EchoUtility::echoCommonFailed('聚合页面已经被城市挂接，只能添加酒店套餐商品');

                return;
            }
            if($product['status'] != 3) {
                EchoUtility::echoCommonFailed('该商品还未上架');

                return;
            }
            if(!empty($existing_result)) {
                EchoUtility::echoCommonFailed('已经有此商品');

                return;
            }

            $new_product = new HtPromotionProduct();

            $new_product['group_id'] = $request_data['group_id'];
            $new_product['product_id'] = $product_id;
            $new_product['promotion_id'] = $promotion_id;
            $new_product['display_order'] = $request_data['display_order'];

            $data = array_merge(Converter::convertModelToArray($new_product), $product);

            $result = $new_product->insert();

            EchoUtility::echoMsgTF($result, '增加活动分组', $data);
        }
    }

    public function actionDeletePromotionGroupProduct()
    {
        $group_id = $this->getGroupID();
        $product_id = $this->getProductID();
        $promotion_id = $this->getPromotionID();

        $result = HtPromotionProduct::model()->deleteByPk(array('group_id' => $group_id, 'product_id' => $product_id));
        HtPromotion::clearCache($promotion_id);

        EchoUtility::echoMsgTF($result > 0, '删除活动分组');
    }

    //活动分组商品排序
    public function actionUpdateGroupProductOrder()
    {
        $data = $this->getPostJsonData();
        $group_id = $this->getGroupId();

        foreach($data as $group) {
            $one_group = HtPromotionProduct::model()->findByPk(array('group_id' => $group_id, 'product_id' => $group['product_id']));

            $result = ModelHelper::updateItem($one_group, $group, array('display_order'));
            if(!$result) {
                EchoUtility::echoMsgTF(false, '更新商品排序');

                return;
            }
        }

        EchoUtility::echoMsgTF(true, '更新商品排序');
    }

    private function getPromotionID()
    {
        return (int)$this->getParam('promotion_id');
    }

    private function getGroupID()
    {
        return (int)$this->getParam('group_id');
    }

    private function getProductID()
    {
        return (int)$this->getParam('product_id');
    }
}