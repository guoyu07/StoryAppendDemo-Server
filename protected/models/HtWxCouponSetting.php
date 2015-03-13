<?php

/**
 * This is the model class for table "ht_wx_coupon_setting".
 *
 * The followings are the available columns in table 'ht_wx_coupon_setting':
 * @property integer $activity_id
 * @property string $slogan
 * @property integer $coupon_base_id
 * @property string $insert_time
 */
class HtWxCouponSetting extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_wx_coupon_setting';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('slogan, coupon_base_id, insert_time', 'required'),
            array('coupon_base_id', 'numerical', 'integerOnly' => true),
            array('slogan', 'length', 'max' => 32),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('activity_id, slogan, coupon_base_id, insert_time', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'activity_id' => 'Activity',
            'slogan' => 'Slogan',
            'coupon_base_id' => 'Coupon',
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
        $criteria->compare('slogan', $this->slogan, true);
        $criteria->compare('coupon_base_id', $this->coupon_base_id);
        $criteria->compare('insert_time', $this->insert_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtWxCouponSetting the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getActivityID($slogan) {
        if(!empty($slogan)) {
            $item = $this->findByAttributes(array('slogan'=>$slogan));
            if(!empty($item)) {
                return $item['activity_id'];
            }
        }

        return 0;
    }
}
