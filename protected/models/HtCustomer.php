<?php

/**
 * This is the model class for table "ht_customer".
 *
 * The followings are the available columns in table 'ht_customer':
 * @property integer $customer_id
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $telephone
 * @property string $password
 * @property string $salt
 * @property integer $address_id
 * @property integer $status
 * @property string $date_added
 * @property integer $bind_third
 * @property integer $bind_email
 * @property integer $bind_phone
 * @property string $hitour_fund
 * @property string $reset_password_token
 * @property string $reset_password_expire
 * @property string $avatar_url
 * @property string $wx_openid
 *
 */
class HtCustomer extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_customer';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('email, password', 'required'),
            array('address_id, status, bind_third, bind_email, bind_phone, hitour_fund', 'numerical', 'integerOnly' => true),
            array('firstname, lastname, telephone', 'length', 'max' => 32),
            array('email', 'length', 'max' => 96),
            array('password', 'length', 'max' => 40),
            array('salt', 'length', 'max' => 9),
            array('date_added', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('customer_id, firstname, lastname, email, telephone, password, salt, address_id, status, date_added,
            bind_third, reset_password_token, reset_password_expire, wx_openid', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'address' => array(self::HAS_ONE, 'HtAddress', '', 'on' => 'address.address_id = customer.address_id'),
            'coupons' => array(self::HAS_MANY, 'HtCoupon', '', 'on' => 'coupon.customer_id = customer.customer_id'),
            'addresses' => array(self::HAS_MANY, 'HtAddress', '', 'on' => 'address.customer_id = customer.customer_id'),
            'customer_third'=>array(self::HAS_ONE, 'HtCustomerThird', '', 'on' => 'customer_third.customer_id = customer.customer_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'customer_id' => 'Customer',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'email' => 'Email',
            'telephone' => 'Telephone',
            'password' => 'Password',
            'salt' => 'Salt',
            'address_id' => 'Address',
            'status' => 'Status',
            'date_added' => 'Date Added',
            'bind_third' => '0：未绑定三方帐号；1：绑定第三方帐号未绑邮箱；2：绑定第三方帐号且绑定邮箱',
            'bind_email' => '0：未绑定；1：已绑定',
            'bind_phone' => '0：未绑定；1：已绑定',
            'hitour_fund' => '玩途旅行基金',
            'reset_password_token' => 'Reset Password Token',
            'reset_password_expire' => 'Reset Password Expire',
            'avatar_url' => 'Avatar URL',
            'wx_openid' => 'Weixin OpenID',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('customer_id', $this->customer_id);
        $criteria->compare('firstname', $this->firstname, true);
        $criteria->compare('lastname', $this->lastname, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('telephone', $this->telephone, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('salt', $this->salt, true);
        $criteria->compare('address_id', $this->address_id);
        $criteria->compare('status', $this->status);
        $criteria->compare('date_added', $this->date_added, true);
        $criteria->compare('bind_third', $this->bind_third);
        $criteria->compare('bind_email', $this->bind_email);
        $criteria->compare('bind_phone', $this->bind_phone);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtCustomer the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'customer',
        );
    }

    public function afterDelete() {
        parent::afterDelete();
        HtAddress::model()->deleteAllByAttributes(array('customer_id' => $this->customer_id));
    }

    public function getCustomer($email_or_phone)
    {
        if (strpos($email_or_phone, '@') > 0) {
            $customer = HtCustomer::model()->findByAttributes(array('email' => $email_or_phone));

            return $customer;
        } else {
            // support login by either email or telephone
            $customer = HtCustomer::model()->findByAttributes(array('telephone' => $email_or_phone, 'bind_phone' => 1));

            return $customer;
        }
    }

    public function addCustomer($email, $password, $confirm_password)
    {
        HtCustomer::model()->clearErrors();
        if (!$this->myValidate($email, $password, $confirm_password, true)) {
            return $this;
        }

        $firstname = explode("@", $email)[0];

        $salt = substr(md5(uniqid(mt_rand(), true)), 0, 9);

        $customer = new HtCustomer();
        $customer['email'] = $email;
        $customer['salt'] = $salt;
        $customer['password'] = sha1($salt . sha1($salt . sha1($password)));
        $customer['firstname'] = $firstname;
        $customer['date_added'] = date("Y-m-d H:i:s", time());
        $customer['bind_third'] = 0;
        $customer['bind_email'] = 1;
        $customer['status'] = 1;

        $result = $customer->insert();

        //  add address and update address_id
        if ($result) {
            $this->addAddress($customer);
        }

        return $customer;
    }

    public function addCustomerByPhone($telephone, $password, $nickname = '')
    {
        $salt = substr(md5(uniqid(mt_rand(), true)), 0, 9);

        $customer = new HtCustomer();
        $customer['email'] = $telephone;
        $customer['telephone'] = $telephone;
        $customer['salt'] = $salt;
        $customer['password'] = sha1($salt . sha1($salt . sha1($password)));
        $customer['firstname'] = $nickname;
        $customer['date_added'] = date("Y-m-d H:i:s", time());
        $customer['bind_third'] = 0;
        $customer['bind_email'] = 0;
        $customer['bind_phone'] = 1;
        $customer['status'] = 1;

        $result = $customer->insert();

        //  add address and update address_id
        if ($result) {
            $this->addAddress($customer);
        }

        return $customer;
    }

    public function addBackgroundCustomer($email, $password, $telephone, $firstname)
    {
        if (!$this->myValidate($email, $password, $password, true)) {
            return null;
        }
        $salt = substr(md5(uniqid(mt_rand(), true)), 0, 9);

        $customer = new HtCustomer();
        $customer['email'] = $email;
        $customer['salt'] = $salt;
        $customer['password'] = sha1($salt . sha1($salt . sha1($password)));
        $customer['firstname'] = $firstname;
        $customer['telephone'] = $telephone;
        $customer['date_added'] = date("Y-m-d H:i:s", time());
        $customer['bind_third'] = 0;
        $customer['bind_email'] = 1;
        $customer['status'] = 1;

        $result = $customer->insert();

        //  add address and update address_id
        if ($result) {
            $this->addAddress($customer);
        }

        return $result ? $customer : null;
    }

    public function addThirdCustomer($ouid, $nick_name, $bind_third = 1, $avatar_url = '')
    {
        $password = substr(md5(uniqid(mt_rand(), true)), 0, 8);

        $salt = substr(md5(uniqid(mt_rand(), true)), 0, 9);

        $customer = new HtCustomer();
        $customer['email'] = $ouid;
        $customer['salt'] = $salt;
        $customer['password'] = sha1($salt . sha1($salt . sha1($password)));
        $customer['firstname'] = $nick_name;
        $customer['date_added'] = date("Y-m-d H:i:s", time());
        $customer['bind_third'] = $bind_third;
        $customer['status'] = 1;
        $customer['avatar_url'] = $avatar_url;

        $result = $customer->insert();

        //  add address and update address_id
        if ($result) {
            $this->addAddress($customer);
        }

        return $result ? $customer : null;
    }

    public function resetPassword($customer_id)
    {
        $customer = HtCustomer::model()->findByPk($customer_id);
        if (!empty($customer)) {
            $salt = substr(md5(uniqid(mt_rand(), true)), 0, 9);
            $password = substr(md5(uniqid(mt_rand(), true)), 0, 8);

            $customer['salt'] = $salt;
            $customer['password'] = sha1($salt . sha1($salt . sha1($password)));

            $result = $customer->update();
            if ($result) {
                return $password;
            }
        }

        return '';
    }

    public function changePassword($customer_id, $old_password, $password, $confirm)
    {
        $errors = array();
        $customer = HtCustomer::model()->findByPk($customer_id);
        if (empty($customer)) {
            array_push($errors, '找不到该用户。');
        } else {
            $salt = $customer['salt'];
            if (sha1($salt . sha1($salt . sha1($old_password))) != $customer['password']) {
                array_push($errors, '原密码不对。');
            }

            if ((strlen($password) < 4) || (strlen($password) > 20)) {
                array_push($errors, '密码长度应该为4～20。');
            }

            if ($confirm != $password) {
                array_push($errors, '密码和确认密码不一致。');
            }

            $customer['password'] = sha1($salt . sha1($salt . sha1($password)));

            $customer->update();
        }

        return $errors;
    }

    public function validatePassword($password)
    {
        $salt = $this->salt;

        if ($this->password == sha1($salt . sha1($salt . sha1($password))) ||
            $this->password == md5($password)
        ) {
            return true;
        }

        return false;
    }

    public function myValidate($email, $password, $confirm = '', $isRegister = false)
    {

        if (strlen($email) > 96 || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {
            $this->addError('email', 'E-mail 格式有错。');
        }

        if ($this->findByAttributes(array('email' => $email))) {
            $this->addError('email', '该E-mail 已注册。');
        }

        if ((strlen($password) < 4) || (strlen($password) > 20)) {
            $this->addError('password', '密码长度应该为4～20。');
        }

        if ($isRegister && $confirm != $password) {
            $this->addError('confirm', '密码和确认密码不一致。');
        }

        return !$this->hasErrors();
    }

    private function addAddress($customer)
    {
        $email = $customer->email;
        if(strpos($email, '@') === false) {
            $email = '';
        }

        $address_id = HtAddress::model()->addAddress($customer->customer_id, $email, $customer->telephone,
                                                     $customer->firstname);
        if ($address_id > 0) {
            return HtCustomer::model()->updateByPk($customer->customer_id, array('address_id' => $address_id)) > 0;
        }

        return false;
    }

    public function checkCustomerStatus($email, $weixin_openid)
    {
        $result = array('customer_id' => 0, 'has_bind_weixin' => false, 'already_binded' => false, 'customer_third_id' => 0, 'has_customer_third' => false);
        $customer = $this->findByAttributes(array('email' => $email));
        if (!empty($customer)) {
            $result['customer_id'] = $customer['customer_id'];
            // TODO check whether customer has bind with some weixin_openid
            $item = HtCustomerThird::model()->findByAttributes(array('customer_id' => $customer['customer_id']));
            if (!empty($item)) {
                $result['has_bind_weixin'] = true;
            }
        }

        $customer_third = HtCustomerThird::model()->getThirdAccount(HtCustomerThird::WEIXIN, $weixin_openid);
        if (!empty($customer_third)) {
            $result['has_customer_third'] = true;
            $result['customer_third_id'] = $customer_third['customer_id'];
            if ($customer_third['customer_id'] == $customer['customer_id']) {
                $result['already_binded'] = true;
            }
        }

        return $result;
    }
    public function getAccountByOpenId($openid){
      return $this->with('customer_third')->find("customer.wx_openid='".$openid."'");
    }
}
