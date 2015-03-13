<?php

/**
 * This is the model class for table "ht_product_tour_operation".
 *
 * The followings are the available columns in table 'ht_product_tour_operation':
 * @property integer $operation_id
 * @property integer $product_id
 * @property string $from_date
 * @property string $to_date
 * @property string $close_dates
 * @property string $frequency
 * @property integer $confirmation_type
 * @property string $languages
 * @property string $language_code
 * @property string $language_list_code
 */
class HtProductTourOperation extends HActiveRecord
{
    public $language_code;
    public $language_name;
    public $language_list_code;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_tour_operation';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, from_date, to_date, close_dates, frequency, confirmation_type, languages', 'required'),
            array('product_id, confirmation_type', 'numerical', 'integerOnly' => true),
            array('frequency', 'length', 'max' => 32),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('operation_id, product_id, from_date, to_date, close_dates,frequency, confirmation_type, languages', 'safe', 'on' => 'search'),
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
            'item' => array(self::HAS_MANY, 'HtProductDateRule', '', 'on'=>'pdr.product_id = pto.product_id'),
            'check' => array(self::HAS_MANY, 'HtProduct', '', 'on'=>'p.product_id = pto.product_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'operation_id' => 'Operation',
            'product_id' => 'Product',
            'from_date' => 'From Date',
            'to_date' => 'To Date',
            'close_dates' => 'Close Dates',
            'frequency' => 'Frequency',
            'confirmation_type' => '1:IM 2:OR',
            'languages' => 'Languages',
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

        $criteria->compare('operation_id', $this->operation_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('from_date', $this->from_date, true);
        $criteria->compare('to_date', $this->to_date, true);
        $criteria->compare('close_dates', $this->close_dates, true);
        $criteria->compare('frequency', $this->frequency, true);
        $criteria->compare('confirmation_type', $this->confirmation_type);
        $criteria->compare('languages', $this->languages, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'pto',
            'order' => 'pto.from_date ASC',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductTourOperation the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function afterFind()
    {
        if ($this->languages) {
            if ($lan_arr = json_decode($this->languages, true)) {
                foreach ($lan_arr as $k => $l) {
                    if ($k == 0 || $l['code'] == 'E' || $l['code'] == 'M') {
                        $this->language_code = $l['code'];
                        $this->language_name = $l['name'];
                        $this->language_list_code = $l['listcode'];
                        if ($l['code'] == 'M') break;
                    }
                }
            }
        }
    }

    public function getFromTo($product_id)
    {
        $sql = 'SELECT min(from_date) as from_date, max(to_date) as to_date FROM ' . $this->tableName();
        $sql .= ' WHERE product_id = ' . (int)$product_id;

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $result = $command->queryRow();

        if (!empty($result)) {
            return array(
                'from_date' => $result['from_date'],
                'to_date' => $result['to_date']
            );
        } else {
            return array();
        }
    }

    public function findByTourDate($product_id, $tour_date)
    {
        return $this->findByAttributes(['product_id' => $product_id], ':td BETWEEN from_date AND to_date', [':td' => $tour_date]);
    }

    public function TourOperationCheck()
    {
        $tour_operation_check = HtProductTourOperation::model()->with('item','check')->findAll(array(
            'select' => array('pto.product_id','pto.to_date'),
            'condition' => 'p.status = 3 AND pdr.need_tour_date = 1 AND pto.to_date < "2015-02-01"',
            'order' => 'pto.to_date',
        ));
        return $tour_operation_check;
    }
}
