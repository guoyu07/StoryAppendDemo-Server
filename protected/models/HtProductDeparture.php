<?php

/**
 * This is the model class for table "ht_product_departure".
 *
 * The followings are the available columns in table 'ht_product_departure':
 * @property integer $product_id
 * @property string $departure_code
 * @property string $departure_point
 * @property string $address_lines
 * @property string $telephone
 * @property string $description
 * @property string $first_service
 * @property string $last_service
 * @property integer $intervals
 * @property integer $language_id
 */
class HtProductDeparture extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_departure';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, departure_code, departure_point, language_id', 'required'),
            array('product_id, intervals, language_id', 'numerical', 'integerOnly' => true),
            array('departure_code', 'length', 'max' => 16),
            array('departure_point', 'length', 'max' => 128),
            array('address_lines', 'length', 'max' => 128),
            array('telephone', 'length', 'max' => 32),
            array('description, first_service, last_service', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('product_id, departure_code, departure_point, address_lines, telephone, description, first_service, last_service, intervals, language_id', 'safe', 'on' => 'search'),
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
            'product_id' => 'Product',
            'departure_code' => 'Departure Code',
            'departure_point' => 'Departure Point',
            'address_lines' => 'Address Lines',
            'telephone' => 'Telephone',
            'description' => 'Description',
            'first_service' => 'First Service',
            'last_service' => 'Last Service',
            'intervals' => 'Intervals',
            'language_id' => 'Language',
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

        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('departure_code', $this->departure_code, true);
        $criteria->compare('departure_point', $this->departure_point, true);
        $criteria->compare('address_lines', $this->address_lines, true);
        $criteria->compare('telephone', $this->telephone, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('first_service', $this->first_service, true);
        $criteria->compare('last_service', $this->last_service, true);
        $criteria->compare('intervals', $this->intervals);
        $criteria->compare('language_id', $this->language_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductDeparture the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function defaultScope()
    {
        return array(
            'alias' => 'pdep',
            'order' => 'pdep.departure_code, pdep.departure_point',
        );
    }

    public function addDeparture($data, $columns = array())
    {
        $item = new HtProductDeparture();
        ModelHelper::fillItem($item, $data, $columns);

        return $item->insert();
    }

    public function getDepartures($product_id)
    {
        $data = array();
        $departures = $this->findAllByAttributes(array('product_id' => $product_id));
        foreach ($departures as $departure) {
            $departure_code = $departure['departure_code'];
            if (empty($data) || !in_array($departure_code, array_keys($data))) {
                $data[$departure_code] = Converter::convertModelToArray($departure);
            }

            if ($departure['language_id'] == 2) {
                $data[$departure_code]['cn_departure_point'] = $departure['departure_point'];
            } else {
                $data[$departure_code]['en_departure_point'] = $departure['departure_point'];
            }
        }

        return $data;
    }

    public function getDepartureInfo($product_id, $departure_point) {
        $data = array();
        $departures = $this->findAllByAttributes(array('product_id' => $product_id, ''));
        foreach ($departures as $departure) {
            $departure_code = $departure['departure_code'];
            if (empty($data) || !in_array($departure_code, array_keys($data))) {
                $data[$departure_code] = Converter::convertModelToArray($departure);
            }

            if ($departure['language_id'] == 2) {
                $data[$departure_code]['cn_departure_point'] = $departure['departure_point'];
            } else {
                $data[$departure_code]['en_departure_point'] = $departure['departure_point'];
            }
        }

        return $data;
    }
}
