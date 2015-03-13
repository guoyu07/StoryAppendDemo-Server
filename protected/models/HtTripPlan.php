<?php

/**
 * This is the model class for table "ht_trip_plan".
 *
 * The followings are the available columns in table 'ht_trip_plan':
 * @property integer $plan_id
 * @property integer $product_id
 * @property string $day
 * @property string $title
 * @property string $description
 * @property integer $online
 * @property integer $display_order
 */
class HtTripPlan extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_trip_plan';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id', 'required'),
            array('product_id, online, display_order', 'numerical', 'integerOnly' => true),
            array('day', 'length', 'max' => 10),
            array('title', 'length', 'max' => 100),
            array('description', 'length', 'max' => 1000),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('plan_id, product_id, day, title, description, online, display_order', 'safe', 'on' => 'search'),
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
            'points' => array(self::HAS_MANY, 'HtTripPlanPoint', '', 'on' => 'tpp.plan_id = tp.plan_id'),
            'traffic' => array(self::HAS_MANY, 'HtTripPlanTraffic', '', 'on' => 'tpt.plan_id = tp.plan_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'plan_id' => 'Plan',
            'product_id' => 'Product',
            'day' => '第几天',
            'title' => '标题',
            'description' => '描述',
            'online' => '是否上线；0：未上线；1：已上线',
            'display_order' => '显示顺序',
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

        $criteria->compare('plan_id', $this->plan_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('day', $this->day, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('online', $this->online);
        $criteria->compare('display_order', $this->display_order);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtTripPlan the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array('alias' => 'tp',
            'order' => 'tp.display_order');
    }
}
