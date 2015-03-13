<?php

/**
 * Created by hitour.server.
 * User: xingminglister
 * Date: 10/15/14
 * Time: 2:24 PM
 */
class ProductHotelController extends AdminController
{
    public function actionRateSources()
    {
        $sources = HtProductHotelRateSource::model()->findall();

        EchoUtility::echoCommonFailed('', 200, $sources);
    }

    public function actionServiceItems()
    {
        $items = HtProductHotelServiceItem::model()->findAll();


        EchoUtility::echoCommonFailed('', 200, $items);
    }

    public function actionBankcardItems()
    {
        $items = HtProductHotelBankcardItem::model()->findAll();


        EchoUtility::echoCommonFailed('', 200, $items);
    }

    public function actionHotelInfo()
    {
        $product_id = $this->getProductId();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') {
            $this->getHotelInfo($product_id);
        } else if ($request_method == 'post') {
            $this->saveOrUpdateHotelInfo($product_id);
        } else if ($request_method == 'delete') {
            $this->deleteHotelInfo($product_id);
        }
    }


    private function getHotelInfo($product_id)
    {
        $hotel_info = $this->doGetHotelInfo($product_id);
        $rates = $hotel_info['rates'];
        $bankcards = $hotel_info['bankcards'];

        $new_rates = [];
        if(is_array($rates)){
            foreach($rates as $rate) {
                $new_rates[$rate['source_id']] = $rate;
            }
        }

        $new_bankcards = [];
        if(is_array($bankcards)){
            foreach($bankcards as $bankcard) {
                $new_bankcards[$bankcard['bankcard_id']] = $bankcard;
            }
        }

        $hotel_info['rates'] = $new_rates;
        $hotel_info['bankcards'] = $new_bankcards;
        $hotel_info['product_id'] = $product_id;

        EchoUtility::echoByResult($hotel_info, '获取酒店信息成功。', '获取酒店信息失败。');
    }

    private function saveOrUpdateHotelInfo($product_id)
    {
        $data = $this->getPostJsonData();

        $hotel_info = HtProductHotel::model()->findByPk($product_id);
        if (empty($hotel_info)) {
            $hotel_info = new HtProductHotel();
            $hotel_info['product_id'] = $product_id;
            ModelHelper::fillItem($hotel_info, $data, ['location', 'address_en', 'address_zh', 'latlng',
                'star_level', 'highlight', 'facilities', 'food_service', 'parking_lot', 'check_in_time', 'check_out_time']);
            $result = $hotel_info->insert();

            if (false == $result) {
                EchoUtility::echoCommonFailed('保存酒店信息失败。');

                return;
            }
        } else {
            $result = ModelHelper::updateItem($hotel_info, $data,
                                              ['location', 'address_en', 'address_zh', 'latlng',
                                                  'star_level', 'highlight', 'facilities', 'food_service', 'parking_lot', 'check_in_time', 'check_out_time']);
            if (1 != $result) {
                EchoUtility::echoCommonFailed('更新酒店信息失败。');

                return;
            }
        }

        HtProductHotelBankcard::model()->deleteAllByAttributes(array('product_id' => $product_id));
        $bankcards = $data['bankcards'];
        foreach ($bankcards as $bankcard) {
            if($bankcard){
                $item = new HtProductHotelBankcard();
                ModelHelper::fillItem($item, $bankcard, ['product_id', 'bankcard_id']);
                $item->insert();
            }
        }

        HtProductHotelRate::model()->deleteAllByAttributes(array('product_id' => $product_id));
        $rates = $data['rates'];
        foreach ($rates as $rate) {
            if($rate){
                $item = new HtProductHotelRate();
                ModelHelper::fillItem($item, $rate, ['product_id', 'source_id', 'rate']);
                $item->insert();
            }
        }

        EchoUtility::echoCommonMsg(200, '保存成功。', $this->doGetHotelInfo($product_id));
    }

    private function deleteHotelInfo($product_id)
    {
        HtProductHotelService::model()->deleteAllByAttributes(array('product_id' => $product_id));
        HtProductHotelRate::model()->deleteAllByAttributes(array('product_id' => $product_id));
        HtProductHotel::model()->deleteByPk($product_id);

        EchoUtility::echoCommonMsg(200, '删除成功。');
    }

    private function doGetHotelInfo($product_id) {
        $hotel_info = HtProductHotel::model()->with('rates','bankcards')->findByPk($product_id);

        return Converter::convertModelToArray($hotel_info);
    }

    //酒店房型
    public function actionHotelRoomType()
    {
        $product_id = $this->getProductId();
        $room_type_id = $this->getRoomTypeId();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') {
            $room_types = HtHotelRoomType::model()->with('services','images','policies')->findAll('rt.product_id = '.$product_id);
            $room_types = Converter::convertModelToArray($room_types);
            if($room_types){
                foreach($room_types as &$room_type){
                    $special_info = HtProductSpecialCombo::model()->getOneSpecialInfo($room_type['product_id'],$room_type['special_code']);
                    $room_type['special_info'] = $special_info;
                }
            }
            EchoUtility::echoMsgTF(true,'获取酒店房型信息',$room_types);
        } else if ($request_method == 'post') {
            $data = $this->getPostJsonData();
            if (empty($room_type_id)) {
                $room_info = new HtHotelRoomType();
                $room_info['product_id'] = $product_id;
                ModelHelper::fillItem($room_info, $data, ['name', 'area', 'highlight', 'price_include',
                    'facilities', 'bed_type', 'bed_size', 'second_bed_type', 'second_bed_size', 'capacity', 'max_capacity', 'bed_policy_md', 'breakfast_md']);
                $result = $room_info->insert();
                EchoUtility::echoMsgTF($result, '添加',$room_info);
            } else {
                $room = HtHotelRoomType::model()->findByPk($room_type_id);
                $result = ModelHelper::updateItem($room, $data, ['name', 'area', 'highlight', 'price_include',
                    'facilities', 'bed_type', 'bed_size', 'second_bed_type', 'second_bed_size', 'capacity', 'max_capacity', 'bed_policy_md', 'breakfast_md']);
                //services
                HtProductHotelService::model()->deleteAllByAttributes(array('room_type_id' => $room_type_id));
                $services = $data['services'];
                foreach ($services as $service) {
                    if($service){
                        $item = new HtProductHotelService();
                        ModelHelper::fillItem($item, $service, ['product_id', 'room_type_id', 'service_id', 'service_info']);
                        $item->insert();
                    }
                }

                //policies
                HtHotelBedPolicy::model()->deleteAllByAttributes(array('room_type_id' => $room_type_id));
                $policies = $data['policies'];
                foreach ($policies as $policy) {
                    if($policy){
                        $item = new HtHotelBedPolicy();
                        ModelHelper::fillItem($item, $policy, ['age_range', 'policy', 'room_type_id']);
                        $item->insert();
                    }
                }
                //TODO:insert special_code
                if($result == 1){
                    $special_info = HtProductSpecialCombo::model()->getOneSpecialInfo($room['product_id'],$room['special_code']);
                    $group = HtProductSpecialGroup::model()->find('product_id = '.$room['product_id']);//酒店产品只有一个分组
                    if(!empty($special_info)){
                        $special_item = HtProductSpecialItem::model()->findByPk(array('group_id'=>$special_info['group_id'],'special_code'=>$special_info['special_code']));
                        $special_item['cn_name'] = $data['name'];
                        $special_item['en_name'] = $data['special_info']['en_name'];
                        $special_item['product_origin_name'] = $data['special_info']['product_origin_name'];
                        $special_item->update();
                    }else{
                        $special_code = substr(md5(microtime() + mt_rand(1, 32768)), 0, 8);
                        $special_item = new HtProductSpecialItem;
                        $special_item['group_id'] = $group['group_id'];
                        $special_item['special_code'] = $special_code;
                        $special_item['cn_name'] = $data['name'];
                        $special_item['en_name'] = $data['special_info']['en_name'];
                        $special_item['product_origin_name'] = $data['special_info']['product_origin_name'];
                        $res = $special_item->insert();
                        if($res){
                            HtProductSpecialCombo::updateSpecialCombo($room['product_id']);
                            $specials = HtProductSpecialCombo::getSpecialDetail($room['product_id']);
                            if(is_array($specials) && count($specials) > 0){
                                foreach($specials as $special){
                                    if(($special['items'][0]['group_id'] == $group['group_id']) && ($special['items'][0]['special_code'] == $special_code)){
                                        ModelHelper::updateItem($room, array('special_code'=>$special['special_id']), ['special_code']);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                EchoUtility::echoMsgTF(1==$result, '更新',Converter::convertModelToArray($room));
            }
        } else if ($request_method == 'delete') {
            //将special_code删除\禁用
            $room_type = HtHotelRoomType::model()->findByPk($room_type_id);
            $order_product = HtOrderProduct::model()->findByAttributes(array('product_id'=>$room_type['product_id'],'special_code'=>$room_type['special_code']));
            $combo = HtProductSpecialCombo::model()->findByAttributes(array('product_id'=>$room_type['product_id'],'special_id'=>$room_type['special_code']));
            if($order_product){
                if($combo){
                    $combo['status'] = 0;
                    $combo->update();
                    $special_item = HtProductSpecialItem::model()->findByPk(array('group_id'=>$combo['group_info_expanded'][0]['group_id'],'special_code'=>$combo['group_info_expanded'][0]['special_code']));
                    if($special_item){
                        $special_item['status'] = 0;
                        $special_item->update();
                    }
                }
            }else{
                if($combo){
                    HtProductSpecialCombo::model()->deleteByPk($combo['special_combo_id']);
                    HtProductSpecialItem::model()->deleteByPk(array('group_id'=>$combo['group_info_expanded'][0]['group_id'],'special_code'=>$combo['group_info_expanded'][0]['special_code']));
                }
            }
            $plans = HtProductPricePlan::model()->findAll('product_id = '.$room_type['product_id']);
            $plans = Converter::convertModelToArray($plans);
            $special_plans = HtProductPricePlanSpecial::model()->findAll('product_id = '.$room_type['product_id']);
            $special_plans = Converter::convertModelToArray($special_plans);
            HtProductPricePlan::model()->removeSpecialCodePricePlan($plans, $room_type['special_code']);
            HtProductPricePlan::model()->removeSpecialCodePricePlan($special_plans, $room_type['special_code'], 1);
            HtProductPricePlan::clearCache($product_id);

            HtHotelRoomType::model()->deleteByPk($room_type_id);
            HtProductHotelService::model()->deleteAllByAttributes(array('room_type_id'=>$room_type_id));
            HtHotelRoomImage::model()->deleteAllByAttributes(array('room_type_id'=>$room_type_id));
            HtHotelBedPolicy::model()->deleteAllByAttributes(array('room_type_id'=>$room_type_id));

            EchoUtility::echoCommonMsg(200, '删除成功。');
        }
    }

    //酒店房型图片
    public function actionUpdateRoomImage()
    {
        $room_type_id = $_POST['room_type_id'];
        $image_id = $_POST['image_id'];
        $display_order = $_POST['display_order'];

        $room = HtHotelRoomType::model()->findByPk($room_type_id);
        if (empty($room)) {
            EchoUtility::echoCommonFailed('未找到ID为' . $room_type_id . '的房型。');
            return;
        }

        $to_dir = 'image/upload/hotel/' . $room_type_id . '/';
        $result = FileUtility::uploadFile($to_dir);

        if ($result['code'] == 200) {
            $file = $result['file'];
            $image_url = FileUtility::uploadToQiniu($to_dir . $file);
            if ($image_url == '') {
                $image_url = Yii::app()->getBaseUrl(true) . '/' . $to_dir . $file;
            }
            if(empty($image_id)){
                $room_image = new HtHotelRoomImage();
                $room_image['room_type_id'] = $room_type_id;
                $room_image['image_url'] = $image_url;
                $room_image['image'] = $to_dir . $file;
                $room_image['display_order'] = $display_order;
                $result = $room_image->insert();
            }else{
                $room_image = HtHotelRoomImage::model()->findByPk($image_id);
                $room_image['image_url'] = $image_url;
                $room_image['image'] = $to_dir . $file;
                $result = $room_image->update();
            }

            EchoUtility::echoMsgTF($result, '更新', Converter::convertModelToArray($room_image));
        } else {
            EchoUtility::echoCommonFailed($result['msg']);
        }
    }

    //删除房型图片
    public function actionDeleteRoomImage()
    {
        $data = $this->getPostJsonData();
        $image_id = $data['image_id'];
        $image = HtHotelRoomImage::model()->findByPk($image_id);
        if ($image && !empty($image['image'])) {
            $file = Yii::app()->params['DIR_UPLOAD_ROOT'] . $image['image'];
            FileUtility::deleteFile($file);
        }
        $result = HtHotelRoomImage::model()->deleteByPk($image_id);
        EchoUtility::echoMsgTF($result, '删除');
    }

    //房型图片顺序
    public function actionUpdateRoomImageOrder()
    {
        $data = $this->getPostJsonData();
        foreach ($data as $order) {
            $image = HtHotelRoomImage::model()->findByPk($order['image_id']);
            if ($image) {
                $image['display_order'] = $order['display_order'];
                $image->update();
            }
        }
        echo CJSON::encode(array('code' => 200, 'msg' => '更新完毕！'));
    }

    //加床政策
    public function actionBedPolicy()
    {
        $room_type_id = $this->getRoomTypeId();
        $bed_policy_id = $this->getBedPolicyId();
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($request_method == 'get') {
            $bed_policies = HtHotelBedPolicy::model()->findAll('room_type_id = '.$room_type_id);
            EchoUtility::echoMsgTF(true,'获取加床政策',Converter::convertModelToArray($bed_policies));
        } else if ($request_method == 'post') {
            $data = $this->getPostJsonData();
            if (empty($bed_policy_id)) {
                $bed_policy = new HtHotelBedPolicy();
                $bed_policy['policy_id'] = $bed_policy_id;
                ModelHelper::fillItem($bed_policy, $data, ['age_range', 'policy']);
                $result = $bed_policy->insert();
                EchoUtility::echoMsgTF($result, '添加',array('policy_id'=>$bed_policy->getPrimaryKey()));
            } else {
                $bed_policy = HtHotelBedPolicy::model()->findByPk($bed_policy_id);
                $result = ModelHelper::updateItem($bed_policy, $data, ['age_range', 'policy']);
                EchoUtility::echoMsgTF(1==$result, '更新',Converter::convertModelToArray($bed_policy));
            }
        } else if ($request_method == 'delete') {
            HtHotelBedPolicy::model()->deleteByPk($bed_policy_id);
            EchoUtility::echoCommonMsg(200, '删除成功。');
        }
    }

    private function getProductId()
    {
        return (int)$this->getParam('product_id');
    }
    private function getRoomTypeId()
    {
        return (int)$this->getParam('room_type_id');
    }
    private function getImageId()
    {
        return (int)$this->getParam('image_id');
    }
    private function getBedPolicyId()
    {
        return (int)$this->getParam('policy_id');
    }
} 