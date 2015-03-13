<?php

/**
 * This is the model class for table "ht_product_tour_plan_group".
 *
 * The followings are the available columns in table 'ht_product_tour_plan_group':
 * @property integer $group_id
 * @property integer $plan_id
 * @property string $title
 * @property string $time
 * @property integer $display_order
 */
class HtProductTourPlanGroup extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_tour_plan_group';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('plan_id', 'required'),
            array('plan_id, display_order', 'numerical', 'integerOnly' => true),
            array('title', 'length', 'max' => 50),
            array('time', 'length', 'max' => 64),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('group_id, plan_id, title, time, display_order', 'safe', 'on' => 'search'),
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
            'items' => array(self::HAS_MANY, 'HtProductTourPlanItem', 'group_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'group_id' => 'Group',
            'plan_id' => 'Plan',
            'title' => 'Title',
            'time' => 'Time',
            'display_order' => 'Display Order',
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

        $criteria->compare('group_id', $this->group_id);
        $criteria->compare('plan_id', $this->plan_id);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('time', $this->time, true);
        $criteria->compare('display_order', $this->display_order);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductTourPlanGroup the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScopes()
    {
        return array(
            'alias' => 'ptpg',
            'order' => 'ptpg.display_order ASC',
        );
    }
}
