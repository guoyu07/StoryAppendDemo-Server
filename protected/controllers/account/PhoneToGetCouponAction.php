<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 8/29/14
 * Time: 2:19 PM
 */

require_once "AccountHelper.php";

class PhoneToGetCouponAction extends CAction
{

    public function run()
    {
        $did = (int)$this->controller->getParam('did');
        $openid = $this->controller->getParam('openid');
        $unionid = $this->controller->getParam('unionid');
        $nickname = $this->controller->getParam('nickname');
        $avatar_url = $this->controller->getParam('avatar_url');

        $phone = (int)$this->controller->getParam('phone');

        if ($did == 0 || empty($unionid) || empty($phone)) {
            EchoUtility::echoCommonFailed('请求数据不合法。');

            return;
        }

        // TODO check whether customer exists for phone
        $customer_id = 0;
        $customer = HtCustomer::model()->findByAttributes(array('telephone' => $phone, 'bind_phone' => 1));
        if (!empty($customer)) {
            $customer_id = $customer['customer_id'];
        } else {
            $result = AccountHelper::addPhoneCustomer($phone, $nickname);

            if (empty($result['customer'])) {
                EchoUtility::echoCommonFailed('创建账号失败。');

                return;
            }
            $customer = $result['customer'];
            $customer_id = $result['customer']['customer_id'];
        }
        // login user
        AccountHelper::doLogin($phone, '', false, true);

        $customer['wx_openid'] = $openid;
        $customer->update();

        list($code, $msg) = AccountHelper::bindToWX($customer_id, $unionid, $nickname, $avatar_url);
        if ($code != 200) {
            EchoUtility::echoCommonFailed($msg);

            return;
        }

        $dandelion = HtDandelion::model()->findByPk($did);
        if (empty($dandelion)) {
            EchoUtility::echoCommonFailed('无效的did。');

            return;
        }

        $result = false;
        try {
            // pickup one coupon
            $item = new HtDandelionPickup();
            $item['pick_type'] = HtDandelionPickup::PT_BY_PICKUP;
            $item['did'] = $did;
            $item['from_order_id'] = 0;
            $item['coupon_id'] = $dandelion['coupon_id'];
            $item['customer_id'] = $customer_id;
            $item['pick_date'] = date('Y-m-d H:i:s');
            $item['used_order_id'] = 0;
            $item['used_date'] = '0000-00-00';
            $result = $item->insert();
        } catch (Exception $e) {
            Yii::log('保存分享券出错。did: ' . $did . ', $customer_id: ' . $customer_id . ', error code: ' . $e->getCode() .
                     ', error message: ' . $e->getMessage(), CLogger::LEVEL_ERROR);
        }

        EchoUtility::echoMsgTF($result, '领取');
    }
} 