<?php

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/22/14
 * Time: 16:00 PM
 */
class CheckoutController extends Controller
{
    public $resource_refs = 'checkout.res';

    public function actionIndex()
    {
      $this->resource_refs = 'checkout.new.res';
        $data = $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'checkoutData' => $this->createUrl('checkout/checkoutData'),
                'addOrder' => $this->createUrl('checkout/addOrder'),
                'customerInfo' => $this->createUrl('account/customerInfo'),
                'validateCoupon' => $this->createUrl('checkout/validateCoupon'),
                'clearCoupon' => $this->createUrl('checkout/clearCoupon'),
                'weixinScan' => $this->createUrl('account/weixinScan'),
            )
        );
        $this->render('new', $data);
    }

    public function actionCart()
    {
        $this->resource_refs = 'checkout.cart.res';
        $data = $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'cartData' => $this->createUrl('checkout/cartData'),
                'appendCart' => $this->createUrl('checkout/appendCart'),
                'checkout' => $this->createUrl('checkout/index'),
            )
        );
        //Todo:to @嵩嵩
        $this->render('cart', $data);
    }

    public function actionAddCart()
    {
        $product = $this->getPostJsonData();
        if(empty($product)) {
            $product = $_POST;
        }
        Yii::log(print_r($product,1),CLogger::LEVEL_INFO,'biz.cart.addCart');

        if(empty($product)){
            $result['msg'] = '信息不完整，请您刷新后重试！';
            EchoUtility::echoCommonFailed($result['msg']);
            return;
        }

        $result = Yii::app()->cart->addCart($product);

        if($result['code']!=200){
            if (empty($result['msg'])) {
                $result['msg'] = '信息不完整，请核查后重试！';
            }
            EchoUtility::echoCommonFailed($result['msg']);
            return;
        }

        //check activity
        if (!empty($product['activity_id'])) {
            $activity_id = $product['activity_id'];
            $activity_result = Yii::app()->activity->checkActivity($product['product_id'], $activity_id);
            if ($activity_result['code'] != 200) {
                Yii::app()->cart->clearCart();
                $result['msg'] = $activity_result['msg'];
                EchoUtility::echoCommonFailed($result['msg']);
                exit;
            }

            $activity_rule = $activity_result['activity_rule'];
            if ($activity_rule && $activity_rule['coupon']) {
                $coupon_result = Yii::app()->cart->addCoupon($activity_rule['coupon']['code'],
                    $activity_rule['activity_coupon_title']);
                if ($coupon_result['code'] != 200) {
                    Yii::app()->cart->clearCart();
                    $result['msg'] = '参加活动失败，请您联系我们！';
                    Yii::log('购买活动产品时，内置优惠券失败！activity_id=' . $activity_id . ',product_id=' . $product['product_id'] . ',coupon_code=' . $activity_rule['coupon']['code'],
                        CLogger::LEVEL_ERROR, 'hitour.service.cart');
                    EchoUtility::echoCommonFailed($result['msg']);
                    exit;
                }
            }
        }

        $p_model = HtProduct::model()->findByPk($product['product_id']);
        if($p_model['type']==HtProduct::T_HOTEL_BUNDLE){
            $data['checkout_url'] = $this->createUrl('checkout/cart');
        }else{
            $data['checkout_url'] = $this->createUrl('checkout/index');
        }

        EchoUtility::echoJson($data);
    }

    public function actionCartData()
    {
        $data = Yii::app()->cart->getProductForCartData();

        if ($data) {
            EchoUtility::echoJson($data);
        } else {
            EchoUtility::echoCommonFailed('系统繁忙，请您稍后再试！');
        }
    }

    public function actionAppendCart(){
        Yii::log(print_r($_POST,1),CLogger::LEVEL_INFO,'biz.cart.appendCart');

        $products = $this->getPostJsonData();
        if(empty($products)) {
            $products = $this->getParam('products');
        }
        if(!empty($products)){
            $result = Yii::app()->cart->appendCart($products);
        }else{
            $result = ['code'=>200,'msg'=>'OK'];
        }

        if($result['code']!=200){
            if (empty($result['msg'])) {
                $result['msg'] = '信息不完整，请核查后重试！';
            }
            EchoUtility::echoCommonFailed($result['msg']);
            return;
        }

        $data['checkout_url'] = $this->createUrl('checkout/index');

        EchoUtility::echoJson($data);
    }

    public function actionCheckoutData()
    {
        $data = Yii::app()->cart->getProductForCheckoutData();

        //coupon limit
        $data['allow_use_coupon'] = 1;
        $activity_id = $data['raw_data']['activity_id'];
        if(!empty($activity_id)){
            $activity_rule = HtActivityRule::model()->findOneByPk($activity_id);
            if($activity_rule){
                $data['allow_use_coupon'] =  $activity_rule['allow_use_coupon'];
            }
        }

        //hack forbidden coupon for 补差价 products
        if(in_array($data['raw_data']['product_id'], [3112, 3113, 3539, 3540, 3664, 3686])){
            $data['allow_use_coupon'] = 0;
        }

        //Hitour TTS账号下单，总是可用 Coupon
        if (Yii::app()->customer->isLogged()) {
            if(in_array(Yii::app()->customer->getCustomerId(),[15007,11164,798,13881])){
                $data['allow_use_coupon'] = 1;
            }
        }

        $data['coupon'] = Yii::app()->cart->getCoupon();

        //payment methods
        $data['payment_methods'] = Yii::app()->activity->getPaymentMethods($activity_id);

        if ($data) {
            EchoUtility::echoJson($data);
        } else {
            EchoUtility::echoCommonFailed('系统繁忙，请您稍后再试！');
        }
    }

    //@todo 未登录不能使用优惠券
    public function actionValidateCoupon()
    {
        if (!Yii::app()->customer->isLogged()) {
            $result['code'] = 300;
            $result['msg'] = '请先登录后再使用优惠券！';
            echo json_encode($result);
            return;
        }

        $coupon = trim($this->getParam('coupon'));
        if (!$coupon) {
            $result['code'] = 400;
            $result['msg'] = '请先输入优惠券编码，再验证！';
            echo json_encode($result);
            return;
        }

        $coupon_title = $this->getParam('coupon_title');
        $result = Yii::app()->cart->addCoupon($coupon, $coupon_title);
        echo json_encode($result);

    }

    public function actionClearCoupon()
    {
        $coupon = $this->getParam('coupon');
        $result = Yii::app()->cart->clearCoupon($coupon);
        echo json_encode($result);
        
    }

    public function actionAddOrder()
    {
        Yii::log(print_r($_POST,1),CLogger::LEVEL_INFO,'biz.checkout.addOrder');

        //check contact and login
        $address = $this->getParam('address');
        if (Yii::app()->customer->isLogged()) {
            $customer_id = Yii::app()->customer->customerId;
            HtAddress::model()->updateAddress($customer_id, $address['address_id'], $address['email'], $address['telephone'], $address['firstname']);
        } else {
            Yii::app()->customer->backgroundLogin($address['firstname'], $address['email'], $address['telephone']);
            $customer_id = Yii::app()->customer->customerId;
            if (empty($customer_id)) {
                $result['code'] = 401;
                $result['msg'] = '预定失败，请检查联系人 Email 地址！';
                echo json_encode($result);
                return;
            }
        }

        $blacklist = [18994];
        if(in_array($customer_id,$blacklist )){
            $result['code'] = 401;
            $result['msg'] = '预定失败，请联系客服！';
            echo json_encode($result);
            return;
        }

        //add order
        $order_data = $_POST;
        $order_data['customer_id'] = $customer_id;
        $result = Yii::app()->order->addOrder($order_data);

        echo json_encode($result);
    }

    public function actionSuccess()
    {
        $data = $this->initData();
        $this->resource_refs = 'checkout_status.res';

        $trade_info = PayUtility::parseOutTradeNo($this->getParam('out_trade_no'));
        $order_id = $trade_info['order_id'] ? $trade_info['order_id'] : $this->getParam('order_id');
        $data['result_str'] = HtOrderProduct::model()->getShippingDesc($order_id);

        $this->render('complete', $data);
    }

    public function actionFail()
    {
        $data = $this->initData();
        $this->resource_refs = 'checkout_status.res';
        $this->render('fail', $data);
    }

    public function actionTest()
    {
        $this->resource_refs = 'checkout.cart.res';
        $this->initData();
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'addCart' => $this->createUrl('checkout/addCart'),
                'cartData' => $this->createUrl('checkout/cartData'),
                'appendCart'=>$this->createUrl('checkout/appendCart'),
            )
        );
        $this->render('cart');
    }

    public function actionTest2()
    {
        $this->initData();
        $this->resource_refs = 'checkout.new.res';
        $this->render('new');
    }

}
