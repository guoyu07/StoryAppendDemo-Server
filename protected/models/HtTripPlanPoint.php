<?php

/**
 * This is the model class for table "ht_trip_plan_point".
 *
 * The followings are the available columns in table 'ht_trip_plan_point':
 * @property integer $point_id
 * @property integer $plan_id
 * @property integer $type
 * @property string $the_id
 * @property string $the_alias
 * @property integer $display_order
 * @property string $description
 * @property string $latlng
 */
class HtTripPlanPoint extends CActiveRecord
{
    const T_HOTEL = 1;
    const T_PRODUCT = 2;
    const T_LAND = 3;
    const T_TEXT = 4;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_trip_plan_point';
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
            array('plan_id, type, display_order', 'numerical', 'integerOnly' => true),
            array('the_id', 'length', 'max' => 20),
            array('the_alias, latlng', 'length', 'max' => 100),
            array('description', 'length', 'max' => 1000),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('point_id, plan_id, type, the_id, the_alias, display_order, description, latlng', 'safe', 'on' => 'search'),
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
            'images' => array(self::HAS_MANY, 'HtTripPlanPointImage', '', 'on' => 'tppi.point_id = tpp.point_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'point_id' => 'Point',
            'plan_id' => 'Plan',
            'type' => '点类型；1：酒店；2：景点；3：文本；4：商品',
            'the_id' => '如果是酒店，景点，商品，这里是其主键',
            'the_alias' => '酒店、商品的别名',
            'display_order' => '显示顺序',
            'description' => '文本点内容',
            'latlng' => '景点坐标',
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

        $criteria->compare('point_id', $this->point_id);
        $criteria->compare('plan_id', $this->plan_id);
        $criteria->compare('type', $this->type);
        $criteria->compare('the_id', $this->the_id, true);
        $criteria->compare('the_alias', $this->the_alias, true);
        $criteria->compare('display_order', $this->display_order);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('latlng', $this->latlng, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtTripPlanPoint the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array('alias' => 'tpp',
            'order' => 'tpp.display_order');
    }
}
