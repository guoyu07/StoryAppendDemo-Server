<?php

/**
 * This is the model class for table "ht_promotion_rule".
 *
 * The followings are the available columns in table 'ht_promotion_rule':
 * @property integer $promotion_id
 * @property string $start_date
 * @property string $end_date
 * @property string $discount_range
 * @property string $discount_rate
 */
class HtPromotionRule extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_promotion_rule';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('promotion_id, start_date, end_date, discount_range, discount_rate', 'required'),
            array('promotion_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('promotion_id, start_date, end_date, discount_range, discount_rate', 'safe', 'on' => 'search'),
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
            'promotion_id' => 'Promotion',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'discount_range' => 'Discount Range',
            'discount_rate' => 'Discount Rate',
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

        $criteria->compare('promotion_id', $this->promotion_id);
        $criteria->compare('start_date', $this->start_date, true);
        $criteria->compare('end_date', $this->end_date, true);
        $criteria->compare('discount_range', $this->discount_range, true);
        $criteria->compare('discount_rate', $this->discount_rate, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtPromotionRule the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function beforeDelete()
    {
        HtPromotion::clearCache($this->promotion_id);

        return parent::beforeDelete();
    }

    protected function beforeSave()
    {
        HtPromotion::clearCache($this->promotion_id);

        return parent::beforeSave();
    }
}
