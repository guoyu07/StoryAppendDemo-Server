<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 5/29/14
 * Time: 7:06 PM
 */
class Customer extends CComponent
{

    private $customer_info;

    public function __construct()
    {
        if (isset(Yii::app()->session['customer_id'])) {
            $session_customer_id = Yii::app()->session['customer_id'];

            if ($this->customerId != $session_customer_id) {
                $this->customer_info = HtCustomer::model()->findByPk($session_customer_id);
            }

            if (empty($this->customer_info)) {
                $this->logout();
            }
        }
        if (isset($_COOKIE['customer'])) {
            $encryption = new Encryption(Setting::instance()->get('config_encryption'));
            $login_data = @unserialize($encryption->decrypt($_COOKIE['customer']));
            if (!empty($login_data['auto']) && !empty($login_data['email']) && !empty($login_data['password'])) {
                $this->login($login_data['email'], $login_data['password']);
            }
        }
    }

    public function init()
    {
        return true;
    }

    public function isLogged()
    {
        return $this->customer_info && $this->customer_info->customer_id;
    }

    public function isCustomerLogged()
    {
        return $this->isLogged();
    }

    public function isThird() {
        return $this->customer_info && $this->customer_info->bind_third == 1;
    }

    public function getCustomerInfo()
    {
        return $this->customer_info;
    }

    public function getCustomerId()
    {
        if ($this->customer_info) {
            return $this->customer_info['customer_id'];
        }

        return 0;
    }

    public function getCustomerName()
    {
        if ($this->customer_info) {
            return $this->customer_info['firstname'];
        }

        return '';
    }

    public function login($email, $password, $override = false)
    {
        $this->logout();

        if (!$override && empty($password)) return false;

        $c = new CDbCriteria();
        if(strpos($email, '@') > 0 || !is_int($email)) {
            $c->addCondition('LOWER(email) = "' . $email . '"');
        } else {
            $c->addCondition('telephone = "' . $email . '"');
            $c->addCondition('bind_phone = 1');
        }
        $c->addCondition('status=1');
        if (!$override) {
            $c->addCondition("(password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $password . "'))))) OR password = '" . md5($password) . "')");
        }
        $this->customer_info = HtCustomer::model()->find($c);

        if ($this->customer_info) {
            Yii::app()->session['customer_id'] = $this->customer_info['customer_id'];

            return true;
        } else {
            return false;
        }
    }

    public function logout()
    {
        unset(Yii::app()->session['customer_id']);
        unset(Yii::app()->session['new_register']);
        unset(Yii::app()->session['customer_email']);
        $this->customer_info = null;

        // TODO clear cart
    }

    public function bindWeixin($weixin_openid = '', $nickname = '', $avatar_url = '') {
        // TODO check whether the weixin_openid has been used first
        $item = HtCustomerThird::model()->getThirdAccount(HtCustomerThird::WEIXIN, $weixin_openid);
        if(empty($item)) {
            $result = HtCustomerThird::model()->saveThirdAccount(HtCustomerThird::WEIXIN, $weixin_openid, '', $nickname, $avatar_url, $this->getCustomerId());
            Yii::log('Add customer third result: ' . $result . ', weixin_openid: ' . $weixin_openid, CLogger::LEVEL_INFO);
            return $result;
        } else {
            return false;
        }
        // TODO return more info -- whether weixin_openid has already been used.
    }

    public function backgroundLogin($firstname, $email, $telephone)
    {
        if (!trim($firstname) || !trim($telephone) || !trim($email)) {
            $msg = '信息不完整!';

            return $msg;
        }
        $this->logout();

        $password = substr($telephone, -6);

        $this->customer_info = HtCustomer::model()->findAllByAttributes(array('email' => $email));
        if ($this->customer_info == null) {
            $this->customer_info = HtCustomer::model()->addBackgroundCustomer($email, $password, $telephone,
                                                                              $firstname);

            $new_register = true;
            // send email
            $config_name = Setting::instance()->get('config_name');
            $subject = sprintf('%s - 感谢您的注册', $config_name);
            $data = array(
                'firstName' => $this->customer_info['firstname'],
                'email' => $this->customer_info['email'],
                'background_login' => true,
                'BASE_URL' => Yii::app()->getBaseUrl(true),
                'LOGIN_URL' => Yii::app()->homeUrl
            );

            Mail::sendToCustomer($email, $subject, Mail::getBody($data, HtNotifyTemplate::REGISTER_OK));
        } else {
            $new_register = false;
        }

        if ($this->login($email, '', true)) {
            Yii::app()->session['customer_email'] = $email;
            Yii::app()->session['new_register'] = $new_register;
        }

        return '';
    }

}
