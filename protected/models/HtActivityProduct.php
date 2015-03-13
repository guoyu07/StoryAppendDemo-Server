<?php

/**
 * This is the model class for table "ht_activity_product".
 *
 * The followings are the available columns in table 'ht_activity_product':
 * @property integer $activity_id
 * @property integer $phase_id
 * @property string $phase_title
 * @property string $start_date
 * @property string $end_date
 * @property string $product_ids
 */
class HtActivityProduct extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtActivityProduct the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_activity_product';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('activity_id, phase_id, phase_title, start_date, end_date, product_ids', 'required'),
            array('activity_id, phase_id', 'numerical', 'integerOnly' => true),
            array('phase_title', 'length', 'max' => 32),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('activity_id, phase_id, phase_title, start_date, end_date, product_ids', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function defaultScope()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'alias' => 'activity_p',
            'order' => 'activity_p.phase_id',
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'activity_id' => 'Activity',
            'phase_id' => 'Phase',
            'phase_title' => 'Phase Title',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'product_ids' => 'Product Ids',
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
        $criteria->compare('phase_id', $this->phase_id);
        $criteria->compare('phase_title', $this->phase_title, true);
        $criteria->compare('start_date', $this->start_date, true);
        $criteria->compare('end_date', $this->end_date, true);
        $criteria->compare('product_ids', $this->product_ids, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    protected function beforeSave()
    {
        Yii::app()->cache->delete(HtActivity::CACHE_ALL_WITH_ACTIVITY_PRODUCT_ACTIVITY_RULE_PREFIX . $this->activity_id);
        Yii::app()->cache->delete(HtActivity::CACHE_ALL_WITH_ACTIVITY_PRODUCT_ACTIVITY_RULE_PREFIX . $this->activity_id . '_1');
        Yii::app()->cache->delete(HtActivitys::CACHE_ALL_WITH_ACTIVITY_PRODUCT_ACTIVITY_RULE_PREFIX . 0 . '_1');
        Yii::app()->cache->delete(HtActivity::CACHE_ONE_WITH_ACTIVITY_PRODUCT_ACTIVITY_RULE_PREFIX . $this->activity_id);

        return parent::beforeSave();
    }
}
