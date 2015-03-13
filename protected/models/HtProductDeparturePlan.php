<?php

/**
 * This is the model class for table "ht_product_departure_plan".
 *
 * The followings are the available columns in table 'ht_product_departure_plan':
 * @property integer $departure_plan_id
 * @property integer $product_id
 * @property string $departure_code
 * @property integer $valid_region
 * @property string $from_date
 * @property string $to_date
 * @property string $time
 * @property string $additional_limit
 * @property integer $status
 */
class HtProductDeparturePlan extends HActiveRecord
{
    public $short_time;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_departure_plan';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, departure_code, time', 'required'),
            array('product_id, valid_region, status', 'numerical', 'integerOnly' => true),
            array('departure_code', 'length', 'max' => 16),
            array('additional_limit', 'length', 'max' => 255),
            array('from_date, to_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('departure_plan_id, product_id, departure_code, valid_region, from_date, to_date, time, additional_limit, status', 'safe', 'on' => 'search'),
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
            'departures' => array(self::HAS_MANY, 'HtProductDeparture', '', 'on' => 'pdplan.departure_code = pdep.departure_code and pdplan.product_id = pdep.product_id '),
            'departure' => array(self::HAS_ONE, 'HtProductDeparture', '', 'on' => 'pdplan.departure_code = pdep.departure_code and pdplan.product_id = pdep.product_id and pdep.language_id = 2'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'departure_plan_id' => 'Departure Plan',
            'product_id' => 'Product',
            'departure_code' => 'Departure Code',
            'valid_region' => 'Valid Region',
            'from_date' => 'From Date',
            'to_date' => 'To Date',
            'time' => 'Time',
            'additional_limit' => 'Additional Limit',
            'status' => 'Status', // 状态，1：启用；0：禁用
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

        $criteria->compare('departure_plan_id', $this->departure_plan_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('departure_code', $this->departure_code, true);
        $criteria->compare('valid_region', $this->valid_region);
        $criteria->compare('from_date', $this->from_date, true);
        $criteria->compare('to_date', $this->to_date, true);
        $criteria->compare('time', $this->time, true);
        $criteria->compare('additional_limit', $this->additional_limit, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductDeparturePlan the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'pdplan',
            'order' => 'pdplan.from_date, pdplan.to_date, pdplan.time'
        );
    }

    protected function afterFind()
    {
        $this->short_time = date('H.i', strtotime($this->time));
    }

    public function alreadyExists($product_id, $departure_code, $valid_region, $from_date,
                                  $to_date, $time)
    {
        $criteria = new CDbCriteria;
        $criteria->addCondition('product_id = ' . $product_id);
        $criteria->addCondition('departure_code = "' . $departure_code . '"');
        $criteria->addCondition('valid_region = "' . $valid_region . '"');
        $criteria->addCondition('from_date = "' . $from_date . '"');
        $criteria->addCondition('to_date = "' . $to_date . '"');
        $criteria->addCondition('time = "' . $time . '"');

        return $this->find($criteria);

    }

    public function getFromTo($product_id)
       {
           $sql = 'SELECT min(from_date) as min_date, max(to_date) as max_date FROM `' . $this->tableName() . '` WHERE product_id=' . $product_id;
           $connection = Yii::app()->db;
           $command = $connection->createCommand($sql);
           $result = $command->queryRow();

           return array('min_date' => $result['min_date'], 'max_date' => $result['max_date']);
       }

    public function addPlan($product_id, $departure_code, $valid_region, $from_date, $to_date,
                            $time, $additional_limit)
    {
        $item = new HtProductDeparturePlan();
        $item['product_id'] = $product_id;
        $item['departure_code'] = $departure_code;
        $item['valid_region'] = $valid_region;
        $item['from_date'] = $from_date;
        $item['to_date'] = $to_date;
        $item['time'] = $time;
        $item['additional_limit'] = $additional_limit;

        return $item->insert();
    }

    public function needDeparture($product_id) {
        $departure_plan = HtProductDeparturePlan::model()->find('product_id = ' . $product_id);
        if(!empty($departure_plan)) {
            return true;
        }
        return false;
    }

}
