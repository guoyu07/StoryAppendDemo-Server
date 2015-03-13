<?php

/**
 * This is the model class for table "ht_wx_coupon_history".
 *
 * The followings are the available columns in table 'ht_wx_coupon_history':
 * @property integer $activity_id
 * @property string $wx_openid
 * @property integer $coupon_id
 * @property string $insert_time
 */
class HtWxCouponHistory extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_wx_coupon_history';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('wx_openid, coupon_id, insert_time', 'required'),
            array('coupon_id', 'numerical', 'integerOnly' => true),
            array('wx_openid', 'length', 'max' => 64),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('activity_id, wx_openid, coupon_id, insert_time', 'safe', 'on' => 'search'),
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
            'coupon' => array(self::HAS_ONE, 'HtCoupon', '', 'on' => 'coupon.coupon_id = wch.coupon_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'activity_id' => 'Activity',
            'wx_openid' => 'Wx Openid',
            'coupon_id' => 'Coupon',
            'insert_time' => 'Insert Time',
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

        $criteria->compare('activity_id', $this->activity_id);
        $criteria->compare('wx_openid', $this->wx_openid, true);
        $criteria->compare('coupon_id', $this->coupon_id);
        $criteria->compare('insert_time', $this->insert_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtWxCouponHistory the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'wch',
        );
    }

    public function picked($activity_id, $wx_openid)
    {
        $item = $this->findByAttributes(array('activity_id' => $activity_id, 'wx_openid' => $wx_openid));

        return !empty($item);
    }

    public function pick($activity_id, $wx_openid)
    {
        if ($this->picked($activity_id, $wx_openid)) {
            return $this->with('coupon')->findByAttributes(array('activity_id' => $activity_id, 'wx_openid' => $wx_openid));
        }

        $setting = HtWxCouponSetting::model()->findByPk($activity_id);

        if (!empty($setting)) {
            // generate new coupon
            list($result, $coupon_id, $coupon_code) = HtCouponBase::model()->generateCouponByBase($setting['coupon_base_id']);

            if ($result) {
                $history = new HtWxCouponHistory();
                $history['activity_id'] = $activity_id;
                $history['wx_openid'] = $wx_openid;
                $history['coupon_id'] = $coupon_id;
                $history['insert_time'] = date('Y:m:d H:i:s');

                $history->insert();
            }
        }

        return $this->with('coupon')->findByAttributes(array('activity_id' => $activity_id, 'wx_openid' => $wx_openid));
    }

}
