<?php

/**
 * This is the model class for table "ht_activity".
 *
 * The followings are the available columns in table 'ht_activity':
 * @property integer $activity_id
 * @property string $name
 * @property string $title
 * @property string $slogan
 * @property string $description
 * @property string $start_date
 * @property string $end_date
 * @property integer $status
 */
class HtActivity extends CActiveRecord
{
    const AS_CLOSED = 0;
    const AS_IN_SALE = 1;
    //以下三种状态是 in sale 的进一步细分
    const AS_PENDING = 2;
    const AS_ONGOING = 3;
    const AS_OUTDATED = 4;

    const CACHE_ALL_WITH_ACTIVITY_PRODUCT_ACTIVITY_RULE_PREFIX = 'HtActivity_all_with_activity_product_activity_rule_';
    const CACHE_ONE_WITH_ACTIVITY_PRODUCT_ACTIVITY_RULE_PREFIX = 'HtActivity_one_with_activity_product_activity_rule_';
    const CACHE_ONE_WITH_ACTIVITY_RULE_PREFIX = 'HtActivity_one_with_activity_rule_';
    const CACHE_ONE_PREFIX = 'HtActivity_one_';

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtActivity the static model class
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
        return 'ht_activity';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, title, slogan, description, start_date, end_date, status', 'required'),
            array('status', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 32),
            array('title, slogan', 'length', 'max' => 64),
            array('description', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('activity_id, name, title, slogan, description, start_date, end_date, status', 'safe', 'on' => 'search'),
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
            'activity_rule' => array(self::HAS_ONE, 'HtActivityRule', 'activity_id'),
            'activity_product' => array(self::HAS_MANY, 'HtActivityProduct', 'activity_id'),
        );
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'activity',
            'order' => 'activity.start_date',
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'activity_id' => 'Activity',
            'name' => 'Name',
            'title' => 'Title',
            'slogan' => 'Slogan',
            'description' => 'Description',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'status' => 'Status',
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('slogan', $this->slogan, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('start_date', $this->start_date, true);
        $criteria->compare('end_date', $this->end_date, true);
        $criteria->compare('status', $this->status);

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
        Yii::app()->cache->delete(HtActivity::CACHE_ONE_WITH_ACTIVITY_RULE_PREFIX . $this->activity_id);
        Yii::app()->cache->delete(HtActivity::CACHE_ONE_PREFIX . $this->activity_id);

        return parent::beforeSave();
    }

    public function getByPkWithActivityProductAndActivityRule($activity_id)
    {
        $key = HtActivity::CACHE_ONE_WITH_ACTIVITY_PRODUCT_ACTIVITY_RULE_PREFIX . $activity_id;
        $activity = Yii::app()->cache->get($key);
        if (empty($activity)) {
            $activity = $this->with('activity_product', 'activity_rule')->findByPk($activity_id);
            $activity = Converter::convertModelToArray($activity);
            Yii::app()->cache->set($key, $activity, 1 * 60 * 60);
        }

        return $activity;
    }
}
