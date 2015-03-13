<?php

/**
 * This is the model class for table "ht_customer_third".
 *
 * The followings are the available columns in table 'ht_customer_third':
 * @property integer $customer_id
 * @property integer $otype
 * @property string $ouid
 * @property string $token
 * @property string $token_secret
 * @property string $nick_name
 * @property string $avatar_url
 * @property string $wx_openid
 */
class HtCustomerThird extends CActiveRecord
{
    const QQ = 1;
    const WEIBO = 2;
    const WEIXIN = 3;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_customer_third';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('customer_id, otype, ouid, token, token_secret, nick_name, avatar_url', 'required'),
            array('customer_id, otype', 'numerical', 'integerOnly' => true),
            array('ouid, token, token_secret, nick_name', 'length', 'max' => 64),
            array('avatar_url', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('customer_id, otype, ouid, token, token_secret, nick_name, avatar_url, wx_openid', 'safe', 'on' => 'search'),
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
            'customer' => array(self::HAS_ONE, 'HtCustomer', '', 'on' => 'customer_third.customer_id = customer.customer_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'customer_id' => 'Customer',
            'otype' => 'Otype',
            'ouid' => 'Ouid',
            'token' => 'Token',
            'token_secret' => 'Token Secret',
            'nick_name' => 'Nick Name',
            'avatar_url' => 'Avatar Url',
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
        $criteria->compare('otype', $this->otype);
        $criteria->compare('ouid', $this->ouid, true);
        $criteria->compare('token', $this->token, true);
        $criteria->compare('token_secret', $this->token_secret, true);
        $criteria->compare('nick_name', $this->nick_name, true);
        $criteria->compare('avatar_url', $this->avatar_url, true);
        $criteria->compare('wx_openid', $this->wx_openid, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtCustomerThird the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'customer_third',
        );
    }

    public function getThirdAccount($otype, $ouid, $customer_id = 0)
    {
        $c = new CDbCriteria();
        $c->addCondition('customer_id>=0');
        if ($customer_id > 0) {
            $c->addCondition('customer_id=' . $customer_id);
        }
        $c->addCondition('otype=' . (int)$otype);
        if(!empty($ouid)) {
            $c->addCondition('ouid="' . $ouid . '"');
        }

        $result = $this->find($c);

        return $result;
    }

    public function saveThirdAccount($otype, $ouid, $token, $token_secret, $nick_name, $avatar_url, $customer_id = 0, $wx_openid = '')
    {
        $item = new HtCustomerThird();
        $item['customer_id'] = (int)$customer_id;
        $item['otype'] = (int)$otype;
        $item['ouid'] = $ouid;
        $item['token'] = $token;
        $item['token_secret'] = $token_secret;
        $item['nick_name'] = $nick_name;
        $item['avatar_url'] = $avatar_url;
        $item['wx_openid'] = $wx_openid;

        return $item->insert();
    }

    public function bindThirdAccount($customer_id, $otype, $ouid)
    {
        $item = $this->getThirdAccount($otype, $ouid);
        if (empty($item)) return false;
        $item['customer_id'] = $customer_id;

        return $item->update();
    }
}
