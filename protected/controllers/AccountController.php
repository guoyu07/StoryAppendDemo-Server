<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 5/28/14
 * Time: 2:14 PM
 */

require_once "account/AccountHelper.php";

class AccountController extends Controller
{
    public $overlay = '';
    public $resource_refs;

    public function actions()
    {
        return array(
            // favorite related
            'addFavoriteProduct' => 'application.controllers.account.myfavorite.AddFavoriteProductAction',
            'deleteFavoriteProduct' => 'application.controllers.account.myfavorite.DeleteFavoriteProductAction',
            'getFavoriteProducts' => 'application.controllers.account.myfavorite.GetFavoriteProductsAction',
            // coupon related
            'getCoupons' => 'application.controllers.account.GetCouponsAction',
            'getFundInfo' => 'application.controllers.account.GetFundInfoAction',
            'shareCoupon' => 'application.controllers.account.ShareCouponAction',
            'generateShareCoupon' => 'application.controllers.account.GenerateShareCouponAction',
            'alreadyPicked' => 'application.controllers.account.AlreadyPickedAction',
            'pickupCoupon' => 'application.controllers.account.PickupCouponAction',
            'loginToGetCoupon' => 'application.controllers.account.LoginToGetCouponAction',
            'phoneToGetCoupon' => 'application.controllers.account.PhoneToGetCouponAction',

            'pickActivityCoupon' => 'application.controllers.account.PickActivityCouponAction',

            'updateOrRegisterWX' => 'application.controllers.account.UpdateOrRegisterWXAction',
        );
    }

    public function actionAccount()
    {
        $data = $this->initData();
        $this->current_page = 'account';
        $this->overlay = 'orderDetail.partial';
        if (!Yii::app()->customer->isLogged()) {
            $this->doRedirect(false, $this->createUrl('home/index'));

            return;
        }
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'getAccount' => $this->createUrl('account/customerInfo'),
                'getOrders' => $this->createUrl('account/orders'),
                'getCoupon' => $this->createUrl('account/getCoupons'),
                'getOrderDetail' => $this->createUrl('account/orderDetail', array('order_id' => '')),
                'updateAddress' => $this->createUrl('account/addOrUpdateAddress'),
                'deleteAddress' => $this->createUrl('account/deleteAddress', array('address_id' => '{address_id}')),
                'changePassword' => $this->createUrl('account/changePassword'),

            )
        );

        $this->render('account', $data);
    }

    private function doRedirect($isAjaxPost, $redirectUrl, $username = '')
    {
        if ($isAjaxPost) {
            EchoUtility::echoMsgTF(true, '', array('redirect' => $redirectUrl, 'username' => $username));
        } else {
            $this->redirect($redirectUrl);
        }
    }

    public function actionSuccess()
    {
        $this->echoSimpleErrorPage("Hello, account success");
    }

    public function actionLogin()
    {
        $redirectUrl = $this->getRedirectUrl();

        $ajax_post = $this->getPost('ajax_post');
        if (Yii::app()->customer->isLogged()) {
            $this->doRedirect($ajax_post, $redirectUrl, Yii::app()->customer->customerName);

            return;
        }

        $email = $this->getPost('email');
        $password = $this->getpost('password');
        $auto_login = $this->getPost('auto_login');

        $customer = HtCustomer::model()->getCustomer($email);
        if (empty($customer)) {
            EchoUtility::echoCommonFailed('用户名或密码不正确。');

            return;
        }

        $result = AccountHelper::doLogin($email, $password, $auto_login);

        if ($result) {
            $this->doRedirect($ajax_post, $redirectUrl, Yii::app()->customer->customerName);
        } else {
            EchoUtility::echoCommonFailed('用户名或密码不正确。');
        }
    }

    private function getRedirectUrl($default_redirect = 'account/account')
    {
        $redirectUrl = $this->getParam('redirect');
        if (empty($redirectUrl)) {
            $redirectUrl = $this->createUrl($default_redirect);
        } else {
            $redirectUrl = str_replace('&amp;', '&', $redirectUrl);
        }

        return $redirectUrl;
    }

    public function actionLogout()
    {
        Yii::app()->customer->logout();

        $login_data['auto'] = false;
        $login_data['email'] = '';
        $login_data['password'] = '';

        $encryption = new Encryption(Setting::instance()->get('config_encryption'));
        setcookie('customer', $encryption->encrypt(serialize($login_data)), time() + 3600 * 24 * 90, '/',
                  $_SERVER['HTTP_HOST'], false, true);

        $this->doRedirect(true, '');
    }

    public function actionRegister()
    {
        $redirectUrl = $this->getRedirectUrl('account/success');

        $ajax_post = $this->getPost('ajax_post');
        if (Yii::app()->customer->isLogged()) {
            $this->doRedirect($ajax_post, $redirectUrl, Yii::app()->customer->customerName);

            return;
        }

        $email = $this->getPost('email');
        $password = $this->getPost('password');
        $confirm_password = $this->getPost('confirm');

        $customer = HtCustomer::model()->addCustomer($email, $password, $confirm_password);
        if ($customer != null && count($customer->getErrors()) == 0) {
            // send email
            $config_name = Setting::instance()->get('config_name');
            $subject = sprintf('%s - 感谢您的注册', $config_name);
            $data = array(
                'firstName' => $customer['firstname'],
                'email' => $customer['email'],
                'background_login' => false,
                'BASE_URL' => Yii::app()->getBaseUrl(true),
                'LOGIN_URL' => Yii::app()->homeUrl
            );

            Mail::sendToCustomer($email, $subject, Mail::getBody($data, HtNotifyTemplate::REGISTER_OK));

            $result = AccountHelper::doLogin($email, $password);

            if ($result) {
                $this->doRedirect($ajax_post, $redirectUrl, Yii::app()->customer->customerName);
            } else {
                EchoUtility::echoCommonFailed('登录失败。');
            }
        } else {
            EchoUtility::echoMsgTF(false, '注册',
                                   empty($customer) ? array('error_msg' => '保存数据失败。') : $customer->getErrors());
        }

    }

    public function actionRequestResetPassword()
    {
        $email = $this->getParam('email');
        // TODO send user an email with link to reset password

    }

    public function actionResetPassword()
    {
        // handle input of telephone
        $email = $this->getParam('email');
        if (strpos($email, '@') === false) {
            $customer = HtCustomer::model()->findByAttributes(array('telephone' => $email, 'bind_phone' => 1));
            if (empty($customer)) {
                EchoUtility::echoCommonFailed('用户不存在。');
            } else {
                $password = HtCustomer::model()->resetPassword($customer['customer_id']);
                if (empty($password)) {
                    EchoUtility::echoCommonFailed('重置密码失败。');
                } else {
                    $sms = new Sms();
                    $sms->send($email, sprintf('您玩途账号密码已重置，当前密码：' . $password));
                    EchoUtility::echoCommonMsg(200, '密码重置成功，已发送新密码至手机。');
                }
            }

            return;
        }

        $customer = HtCustomer::model()->findByAttributes(array('email' => $email));
        if (empty($customer)) {
            EchoUtility::echoCommonFailed('该邮箱用户不存在。');

            return;
        }

        $password = HtCustomer::model()->resetPassword($customer['customer_id']);
        if (empty($password)) {
            EchoUtility::echoCommonFailed('重置密码失败。');

            return;
        }

        $config_name = Setting::instance()->get('config_name');

        $subject = sprintf('%s - 会员新密码', $config_name);
        $data = array(
            'firstName' => $customer['firstname'],
            'email' => $customer['email'],
            'NEW_PASSWORD' => $password,
            'BASE_URL' => Yii::app()->getBaseUrl(true)
        );

        $result = Mail::sendToCustomer($email, $subject, Mail::getBody($data, HtNotifyTemplate::RESET_PASSWORD));

        if ($result === true) {
            EchoUtility::echoCommonMsg(200, '邮件已发送至您的注册邮箱，请查收并按提示操作。');
        } else {
            EchoUtility::echoCommonMsg(200, '发送重置密码邮件失败。请检查您的邮箱地址，修改后再试。');
        }
    }

    public function actionChangePassword()
    {
        $redirectUrl = $this->getRedirectUrl('account/success');

        $ajax_post = $this->getPost('ajax_post');
        if (!Yii::app()->customer->isLogged()) {
            $this->doRedirect($ajax_post, $redirectUrl, Yii::app()->customer->customerName);

            return;
        }

        $old_password = $this->getpost('old_password');
        $password = $this->getpost('password');
        $confirm = $this->getpost('confirm');

        $errors = HtCustomer::model()->changePassword(Yii::app()->customer->customerId, $old_password, $password,
                                                      $confirm);

        if (empty($errors)) {
            $login_data['auto'] = true;
            $login_data['email'] = Yii::app()->customer->getCustomerInfo()['email'];
            $login_data['password'] = $password;

            $encryption = new Encryption(Setting::instance()->get('config_encryption'));
            setcookie('customer', $encryption->encrypt(serialize($login_data)), time() + 3600 * 24 * 90, '/',
                      $_SERVER['HTTP_HOST'], false, true);
        }

        EchoUtility::echoMsgTF(empty($errors), '密码更新');
    }

    public function actionBindThird()
    {
        $otype = (int)$this->getParamOrSession('otype');
        $ouid = $this->getParamOrSession('ouid');
        $token = $this->getParamOrSession(('accessToken'));
        $token_secret = $this->getParamOrSession(('token_secret'));
        $nick_name = $this->getParamOrSession(('nick_name'));
        $avatar_url = $this->getParamOrSession(('avatar_url'));
        $email = $this->getParamOrSession(('email'));

        if (empty($ouid) || $otype < 0) {
            EchoUtility::echoCommonFailed('第三方帐号参数为空。');

            return;
        }

        $account = HtCustomerThird::model()->getThirdAccount($otype, $ouid);

        if (!Yii::app()->customer->isLogged()) {
            if (!empty($account)) {
                $customer_id = $account['customer_id'];
                $customer = HtCustomer::model()->findByPk($customer_id);
                if (!empty($customer) && !empty($customer['email'])) {
                    $this->loginWithoutPassword($customer['email'], '已绑定账号且登录成功。', '已绑定帐号但登录未成功。');
                } else {
                    if (empty($email)) {
                        $customer = HtCustomer::model()->addThirdCustomer($ouid, $nick_name, 1, $avatar_url);
                        if (empty($customer)) {
                            EchoUtility::echoCommonMsg(400, '创建非邮箱绑定的三方账号失败。');
                        } else {
                            $result = HtCustomerThird::model()->bindThirdAccount($customer['customer_id'], $otype,
                                                                                 $ouid);
                            if ($result) {
                                $this->loginWithoutPassword($ouid, '创建非邮箱绑定的三方账号且登录成功。', '创建非邮箱绑定的三方账号并绑定但登录未成功。');
                            } else {
                                EchoUtility::echoCommonMsg(400, '创建非邮箱绑定的三方账号成功但绑定失败。');
                            }
                        }
                    } else {
                        $customer = HtCustomer::model()->findByAttributes(array('email' => $email));
                        if (empty($customer)) {
                            $customer = HtCustomer::model()->addThirdCustomer($email, $nick_name, 2, $avatar_url);
                            if (empty($customer)) {
                                EchoUtility::echoCommonMsg(400, '创建邮箱绑定的三方账号未成功。');
                            } else {
                                $result = HtCustomerThird::model()->bindThirdAccount($customer['customer_id'], $otype,
                                                                                     $ouid);
                                if ($result) {
                                    $this->loginWithoutPassword($email, '创建账号绑定且登录成功。', '创建账号并绑定但登录未成功。');

                                } else {
                                    EchoUtility::echoCommonMsg(400, '创建账号成功但绑定失败。');
                                }
                            }
                        } else {
                            EchoUtility::echoCommonMsg(400, '此邮箱账号已存在。');
                        }
                    }
                }
            } else {
                $result = HtCustomerThird::model()->saveThirdAccount($otype, $ouid, $token, $token_secret, $nick_name,
                                                                     $avatar_url);
                if ($result) {
                    $customer = HtCustomer::model()->addThirdCustomer($ouid, $nick_name, 1, $avatar_url);
                    if (empty($customer)) {
                        EchoUtility::echoCommonMsg(400, '创建非邮箱绑定的三方账号失败。');
                    } else {
                        $result = HtCustomerThird::model()->bindThirdAccount($customer['customer_id'], $otype, $ouid);
                        if ($result) {
                            $this->loginWithoutPassword($ouid, '创建非邮箱绑定的三方账号且登录成功。', '创建非邮箱绑定的三方账号但登录未成功。');
                        } else {
                            EchoUtility::echoCommonMsg(400, '创建非邮箱绑定的三方账号成功但绑定失败。');
                        }
                    }
                } else {
                    EchoUtility::echoCommonMsg(400, '保存第三方账号失败。');
                }
            }
        } else {
            $customer_id = Yii::app()->customer->customerId;
            if (!empty($account) && $account['customer_id'] == $customer_id) {
                EchoUtility::echoCommonMsg(200, '已登录且已经绑定账号。');
            } else {
                if (HtCustomerThird::model()->saveThirdAccount($otype, $ouid, $token, $token_secret, $nick_name,
                                                               $avatar_url, $customer_id)
                ) {
                    EchoUtility::echoCommonMsg(200, '已登录且绑定账号成功。');
                } else {
                    EchoUtility::echoCommonMsg(400, '已登录但绑定账号失败。');
                }
            }
        }
    }

    private function loginWithoutPassword($email, $ok_msg, $fail_msg)
    {
        $result = AccountHelper::doLogin($email, '', false, true);

        if ($result) {
            EchoUtility::echoCommonMsg(200, $ok_msg);
        } else {
            EchoUtility::echoCommonMsg(400, $fail_msg);
        }
    }

    public function actionCustomerInfo()
    {
        if (!Yii::app()->customer->isLogged()) {
            EchoUtility::echoCommonFailed('用户尚未登录。');
        } else {
            $customer_id = Yii::app()->customer->customerId;
            $customer = HtCustomer::model()->with('addresses', 'coupons')->findByPk($customer_id);
            $data = Converter::convertModelToArray($customer);
            unset($data['password']);
            unset($data['salt']);
            $data['isThird'] = Yii::app()->customer->isThird();
            if (!empty($customer)) {
                EchoUtility::echoMsgTF(true, '', $data);
            } else {
                EchoUtility::echoMsgTF(false, '获取用户信息');
            }
        }
    }

    public function actionWeixinScan()
    {
        $email = $this->getParam('email');
        $telephone = $this->getParam('telephone');
        $wx_openid = $this->getParam('openid');
        $wx_unionid = $this->getParam('unionid');
        $nickname = $this->getParam('nickname');
        $avatar_url = $this->getParam('avatar_url');

        /* result code:
         * weixin has bind to someone -- 201
         * customer has bind to some weixin -- 202
         * has sent user coupon and coupon has been used -- 203
         * bind customer to weixin -- 200
        */
        // validate email, telephone
        if (strlen($email) > 96 || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {
            EchoUtility::echoCommonFailed('E-mail 格式有错。');

            return;
        }

        if (strlen($telephone) < 6) {
            EchoUtility::echoCommonFailed('电话位数小于6，不正确。');

            return;
        }

        $customer_id = Yii::app()->customer->getCustomerId();
        if ($customer_id == 0) {
            $item = HtCustomer::model()->findByAttributes(array('email' => $email));
            if (!empty($item)) {
                $customer_id = $item['customer_id'];
                //  login user or customer could not use coupon
                $result = AccountHelper::doLogin($email, '', false, true);
                if (false === $result) {
                    EchoUtility::echoCommonFailed('用户登录失败。');

                    return;
                }
            }
        }

        if ($customer_id > 0) {
            $item = HtCustomerThird::model()->getThirdAccount(HtCustomerThird::WEIXIN, '', $customer_id);
            if (!empty($item)) {
                if ($item['ouid'] != $wx_unionid) {
                    EchoUtility::echoCommonMsg(202, '当前用户已绑定过其它微信账号。');
                } else {
                    // check whether user has used the coupon, if not, return one;
                    list($generated, $used, $code) = HtCoupon::model()->getWeixinScanCoupon($customer_id);
                    if ($generated && $used) {
                        EchoUtility::echoCommonMsg(203, '您已经享受过微信扫码优惠了 亲！不能太贪心呀！');
                    } else if (!$generated) {
                        list($coupon_id, $coupon_code) = HtCoupon::model()->generateWeixinScanCoupon($wx_unionid, 10,
                                                                                                     $customer_id);

                        $customer = HtCustomer::model()->findByPk($customer_id);
                        $customer['wx_openid'] = $wx_openid;
                        $customer->update();

                        EchoUtility::echoCommonMsg(200, '绑定微信账号成功。', array('coupon' => $coupon_code));
                    } else {
                        EchoUtility::echoCommonMsg(200, '绑定微信账号成功。', array('coupon' => $code));
                    }
                }

                return;
            }
        }

        $item = HtCustomerThird::model()->getThirdAccount(HtCustomerThird::WEIXIN, $wx_unionid);
        if (!empty($item)) {
            EchoUtility::echoCommonMsg(201, '您的微信账号已被别人占用了！亲！你来晚了！');

            return;
        }

        // register customer by email if not registered yet
        if ($customer_id == 0) {
            $firstname = substr($email, 0, strpos($email, '@'));
            Yii::app()->customer->backgroundLogin($firstname, $email, $telephone);
            $customer_id = Yii::app()->customer->getCustomerId();
        }

        // bind weixin, return a new coupon
        if ($customer_id == 0) {
            EchoUtility::echoCommonFailed('注册新用户失败。');
        } else {
            $result = HtCustomerThird::model()->saveThirdAccount(HtCustomerThird::WEIXIN, $wx_unionid, '', '',
                                                                 $nickname, $avatar_url,
                                                                 $customer_id, $wx_openid);
            if ($result) {
                // get a new coupon
                list($coupon_id, $coupon_code) = HtCoupon::model()->generateWeixinScanCoupon($wx_unionid, 10,
                                                                                             $customer_id);

                $customer = HtCustomer::model()->findByPk($customer_id);
                $customer['wx_openid'] = $wx_openid;
                $customer->update();

                EchoUtility::echoCommonMsg(200, '绑定微信账号成功。', array('coupon' => $coupon_code));
            } else {
                EchoUtility::echoCommonFailed('绑定微信账号失败。');
            }
        }
    }

    public function actionGetAddresses()
    {
        if (!Yii::app()->customer->isLogged()) {
            EchoUtility::echoCommonFailed('用户尚未登录。');
        } else {
            $customer_id = Yii::app()->customer->customerId;
            $customer = HtCustomer::model()->with('addresses')->findByPk($customer_id);
            if (!empty($customer)) {
                EchoUtility::echoMsgTF(true, '', Converter::convertModelToArray($customer));
            } else {
                EchoUtility::echoMsgTF(false, '获取用户信息');
            }
        }
    }

    public function actionAddOrUpdateAddress()
    {
        $address_id = (int)$this->getParam('address_id', 0);
        $email = $this->getParam('email');
        $telephone = $this->getParam('telephone');
        $firstname = $this->getParam('firstname');

        $customer_id = Yii::app()->customer->customerId;
        if ($address_id > 0) {
            $result = HtAddress::model()->updateAddress($customer_id, $address_id, $email, $telephone, $firstname);
            EchoUtility::echoMsgTF($result, '更新');
        } else {
            $result = HtAddress::model()->addAddress($customer_id, $email, $telephone, $firstname);
            EchoUtility::echoMsgTF($result > 0, '添加', array('address_id' => $result));
        }
    }

    public function actionDeleteAddress()
    {
        $address_id = (int)$this->getParam('address_id', 0);
        $result = HtAddress::model()->deleteByPk($address_id);
        EchoUtility::echoMsgTF($result > 0, '删除');
    }

    public function actionLoginImages()
    {
        // TODO get files from config -- urls for images on Qiniu
        $path = dirname(Yii::app()->basePath) . '/image/login';
        $files = FileUtility::collectFiles($path);

        $result = array();
        foreach ($files as $file) {
            array_push($result, Yii::app()->getBaseUrl(true) . '/image/login/' . $file);
        }

        EchoUtility::echoMsgTF(true, '', $result);
    }

    public function actionViewVoucher()
    {
        $order_id = $this->getParam('order_id');
//        if(Yii::app()->user->isGuest){
//            EchoUtility::echoCommonFailed('还未登录！',404,['redirect'=>'account/index']);
//        }else{
//            $order = HtOrder::model()->findByPk($order_id,'customer_id=:oid',[':oid'=>$order_id]);
//            if(!$order){
//                EchoUtility::echoCommonFailed('订单没有找到！',404,['redirect'=>'account/index']);
//            }
//        }


    }

    public function actionOrders()
    {
        if (!Yii::app()->customer->isLogged()) {
            EchoUtility::echoCommonFailed('您还未登录!');

            return;
        }
        $customer_id = Yii::app()->customer->customerId;
        $criteria = new CDbCriteria();
        $criteria->addCondition('o.status_id !=' . HtOrderStatus::ORDER_CANCELED);

        $criteria->limit = 100; //TODO: wenzi

        $orders = HtOrder::model()->with('status', 'order_product.product',
                                         'order_product.product_description')->findAllByAttributes(['customer_id' => $customer_id],
                                                                                                   ['order' => 'o.order_id DESC, op.order_product_id ASC'],
                                                                                                   $criteria);
        $orders = Converter::convertModelToArray($orders);

        $data = array();
        foreach ($orders as $o) {
            $d['order_id'] = $o['order_id'];
            $d['date_added'] = $o['date_added'];
            $d['status_id'] = $o['status_id'];
            $d['status_name'] = $o['status']['cn_name_customer'];
            $d['product_name'] = $o['order_product']['product_description']['name'];
            $d['product_url'] = $o['order_product']['product']['link_url'];
            $d['detail_url'] = $o['detail_url'];
            $d['payment_url'] = $o['payment_url'];
            $d['cancel_url'] = $o['cancel_url'];
            $d['return_url'] = $o['return_url'];
            $d['download_voucher_url'] = $o['download_voucher_url'];
            //'COUPON' product CAN'T download voucher
            if ($o['order_product']['product']['type'] == HtProduct::T_COUPON) {
                $d['download_voucher_url'] = '';
            }
            if (in_array($o['status_id'], [
                HtOrderStatus::ORDER_BOOKING_FAILED,
                HtOrderStatus::ORDER_PAYMENT_SUCCESS,
                HtOrderStatus::ORDER_PAID_EXPIRED
            ])
            ) {
                $d['allow_return'] = 1;
            } else if ($o['status_id'] == HtOrderStatus::ORDER_SHIPPED && $o['order_product']['return_expire_date'] >= date('Y-m-d')) {
                $d['allow_return'] = 1;
            } else {
                $d['allow_return'] = 0;
            }

            $data[] = $d;
        }

        EchoUtility::echoJson($data);
    }

    public function actionOrderDetail()
    {
        $order_id = $this->getParam('order_id');

        if (!Yii::app()->customer->isLogged()) {
            EchoUtility::echoCommonFailed('您还未登录!');

            return;
        }

        $customer_id = Yii::app()->customer->customerId;

        //order
        $order = HtOrder::model()->with('status')->findByPk($order_id, 'customer_id=:cid', [':cid' => $customer_id]);
//        $order = HtOrder::model()->with('status')->findByPk($order_id);
        if (!$order) {
            EchoUtility::echoCommonFailed('订单不存在!订单号:' . $order_id);

            return;
        }

        $order_data_custom = array(
            'order' => array(
                'order_id' => $order['order_id'],
                'order_date' => date('Y-m-d', strtotime($order['date_added'])),
                'status_id' => $order['status_id'],
                'status_name' => $order['status']['cn_name_customer'],
                'status_shortname' => $order['status_shortname'],
                'payment_url' => $order['payment_url'],
                'cancel_url' => $order['cancel_url'],
                'return_url' => $order['return_url'],
                'download_voucher_url' => '',
                'send_voucher_url' => $order['send_voucher_url'],
            ),
        );

        $order_id = $order['order_id'];

        //order_product
        $order_products = HtOrderProduct::model()->with(['departure', 'product.description'])->findAllByAttributes(['order_id' => $order_id]);

        if (count($order_products) > 1) {
            $main_order_product = $order_products[0];
            $main_product_id = $main_order_product['product_id'];
            $bundle = HtProductBundle::model()->findAllByAttributes(['product_id' => $main_product_id]);
            $bundle_ids = ModelHelper::getList($bundle, 'bundle_id');

            $grouped_product = ['group_0' => [], 'group_1' => [], 'group_2' => [], 'group_3' => []];
            foreach ($order_products as $order_product) {
                if ($order_product['product_id'] == $main_product_id) {
                    $product = $this->getOrderProductInfo($order, $order_product);
                } else {
                    $product = $this->getOrderProductInfo($order, $order_product, $bundle_ids);
                    if (empty($bundle_ids)) {
                        $product['group_type'] = 3;
                    }
                }
                $product['passenger'] = $this->getOrderPassenger($order, $order_product);

                $grouped_product['group_' . $product['group_type']][] = $product;
            }
            // hacked to solve that the total of product in group_1 was recorded in product of group_0
            if (count($grouped_product['group_1']) > 0 && count($grouped_product['group_0']) > 0) {
                $grouped_product['group_1'][0]['total'] = $grouped_product['group_0'][0]['total'];
            }

            $order_data_custom['product'] = $grouped_product;

        } else {
            $main_order_product = $order_products[0];

            $order_data_custom['product'] = $this->getOrderProductInfo($order, $main_order_product);
            $order_data_custom['product']['passenger'] = $this->getOrderPassenger($order, $main_order_product);
        }

        $order_data_custom['order']['download_voucher_url'] = $main_order_product['product']['type'] == HtProduct::T_COUPON ? '' : $order['download_voucher_url'];
        $order_data_custom['product_type'] = $main_order_product['product']['type'];
        $order_data_custom['is_combo'] = $main_order_product['product']['is_combo'];

        //insurance_codes
        $order_data_custom['insurance_codes'] = Converter::convertModelToArray(HtInsuranceCode::model()->with('company')->findAllByAttributes(['order_id' => $order_id]));

        //gift coupon
        $order_data['gift_coupon'] = Converter::convertModelToArray(HtOrderGiftCoupon::model()->with('coupon')->findAllByAttributes(['order_id' => $order_id]));

        EchoUtility::echoJson(array('data' => $order_data_custom));
    }

    private function getOrderProductInfo($order, $order_product, $bundle_ids = [])
    {
        $group_type = 0; // 1：N选1；2：必选；3：可选; 0: not in group
        if (!empty($bundle_ids)) {
            $bundle_info = HtProductBundleItem::model()->getBundleInfo($order_product['product_id'], $bundle_ids);
            $group_type = $bundle_info['bundle']['group_type'];
        }

        //Product Options
        $product = array(
            'product_id' => $order_product['product_id'],
            'name' => $order_product['product']['description']['name'],
            'total' => $order_product['total'],
            'group_type' => $group_type,
            'info' => array(),
            'date' => array(),
            'special' => array(),
        );

        $special_info = HtProductSpecialCombo::getSpecialDetail($order_product['product_id'],$order_product['special_code']);

        if (!empty($special_info)) {
            foreach($special_info[0]['items'] as $item){
                $product['special'][$item['group_title']] = $item['cn_name'];
            }
            //$product['info']['special'] = [$order_product['product']['description']['special_title'] => $order_product['special']['cn_name']];
            //$product['info'][$order_product['product']['description']['special_title']] = $order_product['special']['cn_name'];
        }
        if (!empty($order_product['departure']) && strlen($order_product['departure']['departure_point']) > 0) {
            //$product['info']['departure'] = [$order_product['product']['description']['departure_title'] => $order_product['departure']['departure_point'] . ' / ' . $order_product['departure_time']];
            $product['info'][$order_product['product']['description']['departure_title']] = $order_product['departure']['departure_point'] . ' / ' . $order_product['departure_time'];
        }

        $date_rule = HtProductDateRule::model()->findByPk($order_product['product_id']);
        if ($date_rule['need_tour_date'] == '1') {
            $product['date'][$order_product['product']['description']['tour_date_title']] = $order_product['tour_date'];
        } else if ($order['status_id'] == '3' || $order['status_id'] == '24') {
            $product['date']['兑换截止日期'] = $order_product['redeem_expire_date'];
        }

        return $product;
    }

    private function getOrderPassenger($order, $order_product)
    {
        $passenger = array('summary' => '', 'lead' => [], 'everyone' => [], 'all' => []);

        $order_product_id = $order_product['order_product_id'];
        $product_id = $order_product['product_id'];

        $quantities = HtOrderProductPrice::model()->calcRealQuantities($order_product_id, $product_id);
        $ticket_types = HtProductTicketRule::model()->getTicketRuleMapForOrder($product_id);

        //passengers & meta &pax rule
        $passengers = HtOrderPassenger::model()->findAllByOrder($order['order_id'], $order_product_id);

        $passenger_rule = HtProductPassengerRule::model()->getPassengerRule($product_id);

        // refactor code to extract method for repeated code
        //Passenger Summary
        foreach ($quantities as $key => $val) {
            $passenger['summary'] .= $ticket_types[$key]['ticket_type']['cn_name'] . ' x ' . $val . '&nbsp;&nbsp;';
        }
        //Lead Passenger
        if ($passenger_rule['pax_rule']['need_lead'] == '1') {
            $passenger['has_lead'] = true;
            $lead_fields = $passenger_rule['pax_rule']['lead_ids'];
            if (isset($passengers[0])) {
                $lead_info = $passengers[0];
                $lead_result = $this->fillFieldLabel($passenger_rule, $lead_fields, $lead_info);
                $passenger['lead'] = $lead_result;
            }
        }
        //Everyone Else
        if ($passenger_rule['pax_rule']['need_passenger_num'] == '0') {
            $order_data_custom['passenger']['has_everyone'] = true;
            $everyone_set = $passenger_rule['pax_rule']['need_lead'] == '1' ? array_slice($passengers,
                                                                                          1) : $passengers;
            $everyone_result = array();
            foreach ($everyone_set as $key => $person) {
                $person_fields = $passenger_rule['pax_rule']['id_map'][$person['ticket_id']];
                $person_result = $this->fillFieldLabel($passenger_rule, $person_fields, $person);
                $everyone_result[$key] = $person_result;
            }
            $passenger['everyone'] = $everyone_result;
        }

        for ($i = 0, $len = count($passengers); $i < $len; $i++) {
            array_push($passenger['all'],
                       $this->getFields($passengers[$i], $passengers[$i]['ticket_id'], $ticket_types));
        }

        return $passenger;
    }

    private function getFields($one_pax, $ticket_type, $ticket_types_info)
    {
        $tmp = $one_pax['zh_name'];

        if (!($ticket_type == 1 || $ticket_type == 99)) {
            $tmp .= ' (' . $ticket_types_info[$ticket_type]['ticket_type']['description'] . ') ';
        }

        return $tmp;
    }

    public function actionCancelOrder()
    {
        //  cancel order
        $order_id = $this->getOrderId();
        $result = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_CANCELED);

        if ($result) {
            HtCouponHistory::model()->deleteAllByAttributes(['order_id' => $order_id]);
        }

        //$this->redirect($this->createUrl('account/account', array('view' => 'orders')));

        EchoUtility::echoCommonMsg($result ? 200 : 400, $result ? '订单已取消。' : '订单取消失败。');
    }

    private function getOrderId()
    {
        return (int)$this->getParam('order_id');
    }

    public function actionReturnOrder()
    {
        $result = array('code' => 200, 'msg' => '退订申请已提交,我们会尽快处理!');
        $order_id = $this->getOrderId();

        if (empty($order_id)) {
            $result = array('code' => 400, 'msg' => '退订申请已发送成功,我们将及时联系您处理相关事宜！');
        } else {
            $result = Yii::app()->returning->returnRequest($order_id, 1);
            if ($result['code'] == 200) {
                $result = Yii::app()->returning->returnConfirm($order_id);
                if ($result['code'] == 200) {
                    $result = Yii::app()->returning->refundOrder($order_id);
                }
            }
        }

        EchoUtility::echoCommonMsg($result['code'], '退订申请已发送成功，我们将及时联系您处理相关事宜！');
    }

    public function actionSendVoucher()
    {
        $order_id = $this->getParam('order_id');

        if (!Yii::app()->customer->isLogged()) {
            EchoUtility::echoCommonFailed('您还未登录!');

            return;
        }

        $customer_id = Yii::app()->customer->customerId;
        //order
        $order = HtOrder::model()->with('status')->findByPk($order_id, 'customer_id=:cid', [':cid' => $customer_id]);
        if (!$order) {
            EchoUtility::echoCommonFailed('订单不存在!订单号:' . $order_id);

            return;
        }

        $shippingResult = Yii::app()->shipping->shippingOrder($order_id);
        if ($shippingResult['code'] == 200) {
            $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_SHIPPED);
            Yii::log('[Shipping check]: Order[' . $order_id . '] shipping successfully.', CLogger::LEVEL_INFO);
        } else {
            $isok = Yii::app()->stateMachine->switchStatus($order_id, HtOrderStatus::ORDER_SHIPPING_FAILED);
            Yii::log('[Shipping check]: Order[' . $order_id . '] shipping failed. code[' . $shippingResult['code'] . ']',
                     CLogger::LEVEL_ERROR);
        }
        echo json_encode($shippingResult);
    }

    public function actionDownloadVoucher()
    {
        $order_id = (int)$this->getParam('order_id');

        if (!Yii::app()->customer->isLogged()) {
            $this->echoSimpleErrorPage('请您先登录。');

            return;
        }

        $customer_id = Yii::app()->customer->customerId;
        //order
        $order = HtOrder::model()->with('status')->findByPk($order_id, 'customer_id=:cid',
                                                            [':cid' => $customer_id]);
        if (!$order) {
            $this->echoSimpleErrorPage('订单不存在！订单号:' . $order_id);

            return;
        }

        $this->doDownloadVoucher($order);
    }

    public function actionDownloadVoucherByExtractCode()
    {
        $order_id = (int)$this->getParam('order_id');

        $extract_code = $this->getParam('extract_code');
        $order = $this->getOrderByExtractCode($order_id, $extract_code);

        if (empty($order)) {
            $this->echoSimpleErrorPage('提取码验证失败。请检查后再试。');

            return;
        }

        $this->doDownloadVoucher($order);
    }

    public function actionDownload()
    {
        $this->initData();

        $order_id = $this->getParam('order_id');

//        $is_logged = Yii::app()->customer->isLogged();

        $is_mobile = HTTPRequest::isMobile();

        $download_url = $this->createAbsoluteUrl('account/downloadVoucherByExtractCode',
                                                 ['order_id' => $order_id, 'extract_code' => '']);

//        $data = ['is_logged' => $is_logged, 'is_mobile' => $is_mobile, 'download_url' => $download_url];
        $data = ['is_mobile' => $is_mobile, 'download_url' => $download_url];

        $this->render('voucher_download', $data);
    }

    private function getOrderByExtractCode($order_id, $extract_code)
    {
        if (empty($order_id) || empty($extract_code)) {
            return false;
        } else {
            $order = HtOrder::model()->findByAttributes(['order_id' => $order_id, 'extract_code' => $extract_code]);

            return $order;
        }
    }

    public function actionFavorite()
    {
        $this->initData();
        $this->current_page = 'account';
        $this->resource_refs = 'favorite.res';
        if (!Yii::app()->customer->isLogged()) {
            $this->doRedirect(false, $this->createUrl('home/index'));

            return;
        }
        $this->render('favorite');
    }

    public function actionTourFund()
    {
        $this->initData();
        $this->current_page = 'account';
        $this->resource_refs = 'tourfund.res';
        if (!Yii::app()->customer->isLogged()) {
            $this->doRedirect(false, $this->createUrl('home/index'));

            return;
        }
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'getFundInfo' => $this->createUrl('account/getFundInfo'),
            )
        );
        $this->render('tourfund');
    }

    public function actionRegisterByPhone()
    {
        $phone_no = $this->getParam('phone_no');
        $ajax_post = $this->getParam('ajax_post');
        $redirectUrl = $this->getRedirectUrl('account/success');

        $result = AccountHelper::addPhoneCustomer($phone_no);

        if ($result['customer'] != null && count($result['customer']->getErrors()) == 0) {
            if ($result['password'] == '') {
                $login_result = AccountHelper::doLogin($phone_no, '', false, true);
            } else {
                $login_result = AccountHelper::doLogin($phone_no, $result['password']);
            }

            if ($login_result) {
                $this->doRedirect($ajax_post, $redirectUrl, Yii::app()->customer->customerName);
            } else {
                EchoUtility::echoCommonFailed('登录失败。');
            }
        } else {
            EchoUtility::echoMsgTF(false, '注册',
                                   empty($customer) ? array('error_msg' => '保存数据失败。') : $customer->getErrors());
        }
    }

    public function actionWeixinLogin()
    {
        $code = $this->getParam('code');
        $state = $this->getParam('state');
        $this->redirect('http://' . $_SERVER['SERVER_NAME'] . ':60001/oauth/wLogin?code=' . $code . '&state=' . $state);
    }

    public function actionWechatLogin()
    {
        $openid = $this->getParam('openid');
        $unionid = $this->getParam('unionid');

        $token = $this->getParam('token');
        $nonce = $this->getParam('nonce');
        $redirect_url = $this->getParam('redirect_url');
        if (sha1($unionid . 'Hitour' . $nonce) == $token) {
            $has_customer = false;
            if (!empty($unionid)) {
                list($has_customer_third, $has_customer, $customer, $customer_third) = AccountHelper::getCustomer($unionid);
            }

            if ($has_customer) {
                $result = AccountHelper::doLogin($customer['email'], '', false, true);
                if ($result) {
                    $this->redirect(urldecode($redirect_url));
                } else {
                    $this->redirect($this->createUrl('site/error'));
                    Yii::log('invalid unionid:' . $unionid);
                }
            } else { // get customer by openid
                $customer_third = HtCustomerThird::model()->findByAttributes(array('otype' => HtCustomerThird::WEIXIN, 'wx_openid' => $openid));
                if (!empty($customer_third)) {
                    $customer = HtCustomer::model()->findByPk($customer_third['customer_id']);
                    if (!empty($customer)) {
                        $result = AccountHelper::doLogin($customer['email'], '', false, true);
                        if ($result) {
                            $this->redirect(urldecode($redirect_url));
                        } else {
                            $this->redirect($this->createUrl('site/error'));
                            Yii::log('invalid unionid:' . $unionid);
                        }

                        return;
                    }
                }

                $this->redirect($this->createUrl('site/error'));
                Yii::log('account not found!');
            }
        } else {
            $this->redirect($this->createUrl('site/error'));
            Yii::log('invalid token!');
        }
    }

    private function fillFieldLabel($passenger_rule, $person_fields, $lead_info)
    {
        $lead_result = [];
        foreach ($person_fields as $field) {
            if ($passenger_rule['pax_meta'][$field]['input_type'] == 'enum') {
                $field_tmp = json_decode($passenger_rule['pax_meta'][$field]['range']);
                foreach ($field_tmp as $sub_field) {
                    if ($sub_field->value == $lead_info[$passenger_rule['pax_meta'][$field]['storage_field']]) {
                        $lead_result[$passenger_rule['pax_meta'][$field]['label']] = $sub_field->title;
                    }
                }
            } else {
                $lead_result[$passenger_rule['pax_meta'][$field]['label']] = empty($lead_info[$passenger_rule['pax_meta'][$field]['storage_field']])?'':$lead_info[$passenger_rule['pax_meta'][$field]['storage_field']];
            }
        }

        return $lead_result;
    }

    /**
     * @param $order_id
     * @param $order
     */
    private function doDownloadVoucher($order)
    {
        $order_id = $order['order_id'];
        $download_path = dirname(Yii::app()->BasePath) . Yii::app()->params['VOUCHER_PATH'] . 'download' . DIRECTORY_SEPARATOR;
        $voucher_zip = $download_path . $order_id . '_兑换单.zip';

        $to_windows = UserAgentChecker::isWindows(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        $result = FileUtility::zipFilesWithFilter($voucher_zip, $order['voucher_path'], '.pdf', $to_windows);

        if ($result == false) {
            EchoUtility::echoCommonFailed('下载兑换单失败!订单号:' . $order_id);

            return;
        }

        // Stream the file to the client
        header("Content-Type: application/zip");
        header("Content-Length: " . filesize($voucher_zip));
        header("Content-Disposition: attachment; filename=" . basename($voucher_zip));
        readfile($voucher_zip);
        unlink($voucher_zip);
    }

    private function echoSimpleErrorPage($error_msg)
    {
        echo '<!DOCTYPE html><html><head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
        echo '<title>玩途提醒您</title>';
        echo '</head><body>';
        echo '<p>' . $error_msg . '</p>';
        echo '</body></html>';
    }

    /* public function actionGetUnionId(){
       $raw=HtCustomerThird::model()->with('customer')->findAll("customer.wx_openid<>'' and customer_third.otype=3 and customer_third.token=''");
       foreach($raw as $record){
         $unionid=file_get_contents('http://test.hitour.cc:60001/updateUser?openid='.$record->customer->wx_openid);
         $record->wx_openid=$record->customer->wx_openid;

         if(!empty($unionid)){
           $record->token='123';
           $record->ouid=$unionid;
         }
         $record->update(array('ouid','token','wx_openid'));
         echo $record->ouid,'------',$record->customer->wx_openid,'<br>';
       }
       echo '';

   }*/

}