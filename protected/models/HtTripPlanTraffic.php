<?php

/**
 * This is the model class for table "ht_trip_plan_traffic".
 *
 * The followings are the available columns in table 'ht_trip_plan_traffic':
 * @property integer $plan_id
 * @property integer $from_point
 * @property integer $to_point
 * @property integer $trans_type
 * @property string $description
 */
class HtTripPlanTraffic extends CActiveRecord
{
    const TRANS_TYPE_NOT_KNOWN = 0;
    const TRANS_TYPE_BY_CAR = 1;
    const TRANS_TYPE_BY_BUS = 2;
    const TRANS_TYPE_BY_FOOT = 3;
    const TRANS_TYPE_BY_OTHER = 4;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_trip_plan_traffic';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('plan_id, trans_type, description', 'required'),
            array('plan_id, from_point, to_point, trans_type', 'numerical', 'integerOnly' => true),
            array('description', 'length', 'max' => 1000),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('plan_id, from_point, to_point, trans_type, description', 'safe', 'on' => 'search'),
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
            'plan_id' => 'Plan',
            'from_point' => '起点',
            'to_point' => '终点',
            'trans_type' => '1：驾车；2：公交地铁；3：步行',
            'description' => '详细描述',
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
        $criteria->compare('from_point', $this->from_point);
        $criteria->compare('to_point', $this->to_point);
        $criteria->compare('trans_type', $this->trans_type);
        $criteria->compare('description', $this->description, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtTripPlanTraffic the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array('alias' => 'tpt',
            'order' => 'tpt.from_point');
    }
}
