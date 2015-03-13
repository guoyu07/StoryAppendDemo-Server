<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 5/23/14
 * Time: 4:52 PM
 */
class CouponController extends AdminController
{

    public $layout = '//layouts/common';

    public function actionIndex()
    {
        $this->pageTitle = '优惠券列表';

        $request_urls = array(
            'editCouponUrl' => $this->createUrl('coupon/edit', array('coupon_id' => '')),
            'fetchCoupons' => $this->createUrl('coupon/coupons'),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );
        $this->render('index');
    }

    public function actionEdit()
    {
        $this->pageTitle = '优惠券编辑';

        $coupon_id = $this->getCouponID();

        $request_urls = array(
            'back' => $this->createUrl('coupon/index'),
            'coupon' => $this->createUrl('coupon/coupon', array(
                    'coupon_id' => $coupon_id)),
            'fetchCouponHistory' => $this->createUrl('coupon/getCouponHistory', array(
                    'coupon_id' => $coupon_id)),
            'getEmail' => $this->createUrl('coupon/getUserIdFromEmail', array('email' => '')),
            'isUserValid' => $this->createUrl('coupon/isUserIdValid', array('uid' => '')),
            'getLimitIdName' => $this->createUrl('coupon/getLimitIdName'),
            'getCouponInstances' => $this->createUrl('coupon/getCouponInstances',array(
                    'coupon_id' => $coupon_id)),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );
        $this->render('edit');
    }

    public function actionTemplate() {
        $this->pageTitle = '优惠券模版编辑';

        $template_id = $this->getTemplateID();
        $gift_coupon = HtProductGiftCoupon::model()->findByPk($template_id);
        $coupon_id = '';
        if($gift_coupon) {
            $coupon_id = $gift_coupon['coupon_id'];
        }

        $request_urls = array(
            'productCouponTemplate' => $this->createUrl('coupon/productGiftCoupon', array('id' => $template_id)),
            'templateInstances' => $this->createUrl('coupon/getCouponInstances', array('coupon_id' => $coupon_id)),
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );
        $this->render('template');
    }

    public function actionCoupons()
    {
        $data = $this->getPostJsonData();

        $c = new CDbCriteria();
        $total_count = new CDbCriteria();

        $order = '';
        foreach ($data['sort'] as $order_field => $order_dir) {
            $order .= ', ' . $order_field . ' ' . ($order_dir == 1 ? 'ASC' : 'DESC');
        }
        $c->order = substr($order, 2);
        if ($data['record_filter']) {
            $c->select = implode(',', $data['record_filter']);
        }
        if (isset($data['query_filter']) && !empty($data['query_filter']['search_text'])) {
            $c->addCondition('name like "%' . $data['query_filter']['search_text'] . '%"');
            $c->addCondition('code like "%' . $data['query_filter']['search_text'] . '%"', 'OR');
            $total_count->addCondition('name like "%' . $data['query_filter']['search_text'] . '%"');
            $total_count->addCondition('code like "%' . $data['query_filter']['search_text'] . '%"', 'OR');
        }
        $c->limit = $data['paging']['limit'];
        $c->offset = $data['paging']['start'];

        EchoUtility::echoMsgTF(true, '', array(
            'total_count' => HtCoupon::model()->count($total_count),
            'data' => Converter::convertModelToArray(HtCoupon::model()->findAll($c))
        ));
    }

    public function actionSearch()
    {
        $data = $this->getPostJsonData();

        $query_str = $data['search_text'];

        $c = new CDbCriteria();
        $c->order = 'date_added DESC';
        $c->select = 'coupon_id, name, code, date_added, status';
        $c->limit = $data["limit"];

        $this->searchByIdOrName($c, $query_str);
    }

    public function actionCoupon()
    {
        $coupon_id = $this->getCouponID();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') {
            $this->getCoupon($coupon_id);
        } else if ($request_method == 'post') {
            $data = $this->getPostJsonData();
            ModelHelper::fixDateValue($data, array('date_start', 'date_end'));
            if (empty($coupon_id)) {
                $this->addCoupon($data);
            } else {
                $this->updateCoupon($coupon_id, $data);
            }
        } else if ($request_method == 'delete') {
            $this->deleteCoupon($coupon_id);
        }
    }

   //获取coupon限制ID名称
    public function actionGetLimitIdName(){
        $data = $this->getPostJsonData();
        $type = $data['valid_type'];
        $id = $data['id'];
        $return['id'] = $id;
        $return['name'] = '';
        switch($type){
            case 1 :
                $item = HtProductDescription::model()->findByPk(array('product_id'=>$id,'language_id'=>2));
                if($item)$return['name'] = $item['name'];
                break;
            case 2 :
                $item = HtCity::model()->findByPk($id);
                if($item)$return['name'] = $item['cn_name'];
                break;
            case 3 :
                $item = HtCountry::model()->findByPk($id);
                if($item)$return['name'] = $item['cn_name'];
                break;
        }
        EchoUtility::echoMsgTF(true, '', $return);
    }

    public function actionGetUserIdFromEmail() {
        $email = Yii::app()->request->getParam('email');

        $c = new CDbCriteria();
        $c->addCondition('email="' . $email . '"');

        $result = HtCustomer::model()->find($c);

        EchoUtility::echoMsgTF($result, '获取用户邮箱', $result['customer_id']);
    }

    public function actionIsUserIdValid() {
        $user_id = Yii::app()->request->getParam('uid');

        $c = new CDbCriteria();
        $c->addCondition('customer_id="' . $user_id . '"');
        $result = HtCustomer::model()->find($c);

        EchoUtility::echoMsgTF($result, '查找用户', $result);
    }

    public function actionGetCouponHistory()
    {
        $data = $this->getPostJsonData();
        $coupon_id = $this->getCouponID();

        $c = new CDbCriteria();

        $order = '';
        foreach ($data['sort'] as $order_field => $order_dir) {
            $order .= ', ' . $order_field . ' ' . ($order_dir == 1 ? 'ASC' : 'DESC');
        }
        $c->order = substr($order, 2);
        $c->limit = $data['paging']['limit'];
        $c->offset = $data['paging']['start'];
        if ($data['record_filter']) {
            $c->select = implode(',', $data['record_filter']);
        }
        $c->addCondition('coupon_id=' . $coupon_id);

        $total_c = new CDbCriteria();
        $total_c->addCondition('coupon_id=' . $coupon_id);

        $history = HtCouponHistory::model()->findAll($c);
        $totalHistory = HtCouponHistory::model()->count($total_c);

        EchoUtility::echoMsgTF(true, '', array(
            'total_count' => $totalHistory,
            'data' => $history
        ));
    }

    //获取优惠券模板生成实例
    public function actionGetCouponInstances()
    {
        $data = $this->getPostJsonData();
        $coupon_id = $this->getCouponID();

        $c = new CDbCriteria();

        $order = '';
        foreach ($data['sort'] as $order_field => $order_dir) {
            $order .= ', ' . $order_field . ' ' . ($order_dir == 1 ? 'ASC' : 'DESC');
        }
        $c->order = substr($order, 2);
        $c->limit = $data['paging']['limit'];
        $c->offset = $data['paging']['start'];
        if ($data['record_filter']) {
            $c->select = implode(',', $data['record_filter']);
        }
        $c->addCondition('rel_coupon_id=' . $coupon_id);

        $total_c = new CDbCriteria();
        $total_c->addCondition('rel_coupon_id=' . $coupon_id);

        $instances = HtCoupon::model()->findAll($c);
        if($instances){
            $instances = Converter::convertModelToArray($instances);
            foreach($instances as &$i){
                $gift_coupon = HtOrderGiftCoupon::model()->find('coupon_id = '.$i['coupon_id']);
                if($gift_coupon)$i['order_id'] = $gift_coupon['order_id'];
            }
        }
        $totalInstances = HtCoupon::model()->count($total_c);

        EchoUtility::echoMsgTF(true, '', array(
            'total_count' => $totalInstances,
            'data' => $instances
        ));
    }

    //商品优惠券挂接规则
    public function actionProductCouponRule()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'post') {
            $data = $this->getPostJsonData();
            if (empty($data['product_id'])) {
                $rule = new HtProductCouponRule();
                ModelHelper::fillItem($rule,$data);
                $result = $rule->insert();
                EchoUtility::echoMsgTF($result, '添加', $rule->getPrimaryKey());
            } else {
                $rule = HtProductCouponRule::model()->findByPk($data['product_id']);
                $result = ModelHelper::updateItem($rule, $data);
                EchoUtility::echoMsgTF($result, '编辑',Converter::convertModelToArray($rule));
            }
        }else{
            $return = HtProductCouponRule::model()->findByPk((int)$this->getParam('product_id'));
            EchoUtility::echoMsgTF(true, '获取',Converter::convertModelToArray($return));
        }
    }

    //商品优惠券挂接模板
    public function actionProductGiftCoupon()
    {
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'post') {
            $data = $this->getPostJsonData();
            //验证
            if(empty($data['coupon_id'])){
                EchoUtility::echoCommonFailed('请填写正确的coupon_id');return;
            }
            if (empty($data['id'])) {
                $gift_coupon = new HtProductGiftCoupon();
                ModelHelper::fillItem($gift_coupon,$data,array('product_id','coupon_id','quantity','date_type','date_start','date_end','start_offset','end_range','customer_limit','status'));
                $result = $gift_coupon->insert();
                EchoUtility::echoMsgTF($result, '添加', $gift_coupon->getPrimaryKey());
            } else {
                $gift_coupon = HtProductGiftCoupon::model()->findByPk($data['id']);
                $result = ModelHelper::updateItem($gift_coupon, $data,array('coupon_id','quantity','date_type','date_start','date_end','start_offset','end_range','customer_limit','status'));
                $gift = HtProductGiftCoupon::model()->with('template_coupon.use_limit')->findByPk($data['id']);
                $gift = Converter::convertModelToArray($gift);
                if($gift['template_coupon']){
                    $valid_type = '';
                    $limit_type = 1;
                    if(is_array($gift['template_coupon']['use_limit']) && count($gift['template_coupon']['use_limit']) > 0){
                        $valid_type = $gift['template_coupon']['use_limit'][0]['valid_type'];
                        $limit_type = $gift['template_coupon']['use_limit'][0]['limit_type'];
                    }
                    $gift['template_coupon']['limit_type'] = $limit_type;
                    $gift['template_coupon']['valid_type'] = $valid_type;
                    $ids = HtCouponUseLimit::model()->getLimitIds($gift['coupon_id'],$valid_type);
                    $gift['template_coupon'] = array_merge($gift['template_coupon'],$ids);
                }
                EchoUtility::echoMsgTF($result, '编辑',$gift);
            }
        }else if ($request_method == 'delete') {
            $result = HtProductGiftCoupon::model()->deleteByPk((int)$this->getParam('id'));
            EchoUtility::echoMsgTF($result > 0, '删除');
        }else{
            $result = HtProductGiftCoupon::model()->with('template_coupon.use_limit')->findByPk((int)$this->getParam('id'));
            $return = Converter::convertModelToArray($result);
            if($result){
                $description = HtProductDescription::model()->findByAttributes(array('product_id'=>$result['product_id'],'language_id'=>2));
                $return['product_name'] = $description['name'];
            }
            if($return['template_coupon']){
                $valid_type = '';
                $limit_type = 1;
                if(is_array($return['template_coupon']['use_limit']) && count($return['template_coupon']['use_limit']) > 0){
                    $valid_type = $return['template_coupon']['use_limit'][0]['valid_type'];
                    $limit_type = $return['template_coupon']['use_limit'][0]['limit_type'];
                }
                $return['template_coupon']['limit_type'] = $limit_type;
                $return['template_coupon']['valid_type'] = $valid_type;
                $ids = HtCouponUseLimit::model()->getLimitIds($return['coupon_id'],$valid_type);
                $return['template_coupon'] = array_merge($return['template_coupon'],$ids);
            }
            EchoUtility::echoMsgTF($return, '获取',$return);
        }
    }

    //获取商品挂接优惠券模板列表
    public function actionGetProductGiftCouponList()
    {
        $product_id = $this->getProductID();
        $result = HtProductGiftCoupon::model()->with('template_coupon.use_limit')->findAll('product_id = '.$product_id);
        $result = Converter::convertModelToArray($result);
        if($result){
            foreach($result as &$template){
                if($template['template_coupon']){
                    $valid_type = '';
                    if(is_array($template['template_coupon']['use_limit']) && count($template['template_coupon']['use_limit']) > 0){
                        $valid_type = $template['template_coupon']['use_limit'][0]['valid_type'];
                        $template['template_coupon']['valid_type'] = $valid_type;
                        $template['template_coupon']['limit_type'] = $template['template_coupon']['use_limit'][0]['limit_type'];
                    }
                    $ids = HtCouponUseLimit::model()->getLimitIds($template['coupon_id'],$valid_type);
                    $template['template_coupon'] = array_merge($template['template_coupon'],$ids);
                }
            }
        }
        EchoUtility::echoMsgTF(true, '获取',$result);
    }

    private function searchByIdOrName($c, $query_str)
    {
        $c->addCondition('name like "%' . $query_str . '%"');
        $c->addCondition('code like "%' . $query_str . '%"', 'OR');

        $data = HtCoupon::model()->findAll($c);

        $total_coupons = HtCoupon::model()->count($c);
        $return = array('total_coupons' => $total_coupons, 'coupons' => Converter::convertModelToArray($data));

        EchoUtility::echoMsgTF(true, '', $return);
    }

    private function getCoupon($coupon_id)
    {
        if ($coupon_id == 0) {
            $data = Converter::convertModelToArray(new HtCoupon());
            $data['use_type'] = 1;
            $data['status'] = 0;
            $data['type'] = 'F';
            $data['discount'] = 0;

            EchoUtility::echoCommonMsg(401, '', $data);

            return;
        }
        $data = Converter::convertModelToArray(HtCoupon::model()->with('use_limit')->findByPk($coupon_id));

        $email_c = new CDbCriteria();
        $email_c->addCondition('customer_id="' . $data['customer_id'] . '"');
        $email_result = HtCustomer::model()->find($email_c);

        $data['customer_email'] = $email_result['email'];

        $valid_type = 0;
        $limit_type = 1;
        if(is_array($data['use_limit']) && count($data['use_limit']) > 0){
            $valid_type = $data['use_limit'][0]['valid_type'];
            $limit_type = $data['use_limit'][0]['limit_type'];
        }
        $data['valid_type'] = $valid_type;
        $data['limit_type'] = $limit_type;
        $ids = HtCouponUseLimit::model()->getLimitIds($coupon_id,$data['valid_type']);
        if (!empty($data)) {
            $data = array_merge($data, $ids);
        }

        EchoUtility::echoMsgTF(!empty($data), '获取ID为' . $coupon_id . '的优惠券', $data);
    }

    private function addCoupon($data)
    {
        // TODO add field 只给单个客户使用，须指定客户ID

        $code = trim($data['code']);
        if (strlen($code) == 0) {
            EchoUtility::echoCommonFailed('优惠券代码必须填写！');

            return;
        }
        $item = HtCoupon::model()->findByAttributes(array('code' => $code));
        if ($item != null) {
            EchoUtility::echoCommonFailed('优惠券代码已存在，请更改后再试！');

            return;
        }

        $item = new HtCoupon();
        ModelHelper::fillItem($item, $data, array('name', 'code', 'use_type', 'type', 'discount', 'logged', 'total',
            'product_min', 'product_max', 'date_start', 'date_end', 'uses_total', 'uses_customer', 'customer_id', 'status'));
//        $item['status'] = 1;
        $item['date_added'] = date("Y-m-d H:i:s", time());

        $result = $item->insert();

        $final_data = Converter::convertModelToArray($item);
        // handle fields could not be filled from data directly
        if ($result) {
            $coupon_id = $item['coupon_id'];
            $final_data['edit_url'] = $this->createUrl('coupon/edit', array('coupon_id' => $coupon_id));
        }

        EchoUtility::echoMsgTF($result, '添加', $final_data);
    }

    private function updateCoupon($coupon_id, $data)
    {
        $info = HtCoupon::model()->findByAttributes(array('code' => $data['code']));
        if ($info != null && $info['coupon_id'] != $coupon_id) {
            EchoUtility::echoCommonFailed('优惠券代码已存在，请更改后再试！');

            return;
        }
        if($data['valid_type'] == 0){
            $data['limit_type'] = 1;
        }
        $item = HtCoupon::model()->findByPk($coupon_id);
        $result = ModelHelper::updateItem($item, $data,
                                          array('name', 'description' , 'code', 'use_type', 'type', 'discount', 'logged', 'total',
                                              'product_min', 'product_max', 'date_start', 'date_end', 'uses_total', 'uses_customer', 'customer_id',
                                              'status'));

        $final_data = Converter::convertModelToArray($item);
        if ($result == 1) {
            HtCouponUseLimit::model()->deleteAllByAttributes(array('coupon_id'=>$coupon_id));
            $limitIds = $data['limit_ids'];
            foreach ($limitIds as $id) {
                $result = HtCouponUseLimit::addNew( array('coupon_id' => $coupon_id, 'id' => $id['id'],'limit_type'=>$data['limit_type'],'valid_type'=>$data['valid_type']));
                if($result == false) break;
            }
            $final_data = array_merge($final_data, $limitIds);
        }

        EchoUtility::echoMsgTF($result, '更新', $final_data);
    }

    private function deleteCoupon($coupon_id)
    {
        $result = HtCoupon::model()->deleteByPk($coupon_id);
        HtCouponHistory::model()->deleteAll('coupon_id=' . $coupon_id);
        HtCouponUseLimit::model()->deleteAllByAttributes(array('coupon_id'=>$coupon_id));
        EchoUtility::echoMsgTF($result > 0, '删除');
    }

    private function getCouponID()
    {
        return (int)Yii::app()->request->getParam('coupon_id');
    }

    private function getProductID()
    {
        return (int)Yii::app()->request->getParam('product_id');
    }

    private function getTemplateID()
    {
        return (int)Yii::app()->request->getParam('template_id');
    }
}
