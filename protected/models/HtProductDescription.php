<?php

/**
 * This is the model class for table "ht_product_description".
 *
 * The followings are the available columns in table 'ht_product_description':
 * @property integer $product_id
 * @property integer $language_id
 * @property string $name
 * @property string $origin_name
 * @property string $slogan
 * @property string $summary
 * @property string $description
 * @property string $benefit
 * @property string $how_it_works
 * @property string $please_note
 * @property string $service_include
 * @property string $schedule
 * @property string $tour_date_title
 * @property string $special_title
 * @property string $departure_title
 * @property string $package_service_title
 * @property string $package_gift_title
 * @property string $package_recommend_title
 * @property string $package_service
 * @property string $package_gift
 * @property string $package_recommend
 */
class HtProductDescription extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtProductDescription the static model class
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
        return 'ht_product_description';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, language_id, name, origin_name, slogan, summary, description, how_it_works, please_note, service_include, package_service_title, package_gift_title, package_recommend_title, package_service, package_gift, package_recommend', 'required'),
            array('product_id, language_id', 'numerical', 'integerOnly' => true),
            array('name, origin_name', 'length', 'max' => 128),
            array('slogan, summary', 'length', 'max' => 255),
            array('tour_date_title, special_title, departure_title, package_service_title, package_gift_title, package_recommend_title', 'length', 'max' => 32),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('product_id, language_id, name, origin_name, slogan, summary, description, benefit, how_it_works, please_note, service_include, schedule, tour_date_title, special_title, departure_title, package_service_title, package_gift_title, package_recommend_title, package_service, package_gift, package_recommend', 'safe', 'on' => 'search'),
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
            'language_id' => 'Language',
            'name' => 'Name',
            'origin_name' => 'Oriignal Name',
            'slogan' => 'Slogan',
            'summary' => 'Summary',
            'description' => 'Description',
            'benefit' => 'Benefit',
            'how_it_works' => 'How It Works',
            'please_note' => 'Please Note',
            'service_include' => 'Service Include',
            'schedule' => 'Schedule',
            'tour_date_title' => 'Tour Date Title',
            'special_title' => 'Special Title',
            'departure_title' => 'Departure Title',
            'package_service_title' => 'Package Service Title',
            'package_gift_title' => 'Package Gift Title',
            'package_recommend_title' => 'Package Recommend Title',
            'package_service' => 'Package Service',
            'package_gift' => 'Package Gift',
            'package_recommend' => 'Package Recommend',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('language_id', $this->language_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('origin_name', $this->origin_name, true);
        $criteria->compare('slogan', $this->slogan, true);
        $criteria->compare('summary', $this->summary, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('benefit', $this->description, true);
        $criteria->compare('how_it_works', $this->how_it_works, true);
        $criteria->compare('please_note', $this->please_note, true);
        $criteria->compare('service_include', $this->service_include, true);
        $criteria->compare('schedule', $this->schedule, true);
        $criteria->compare('tour_date_title', $this->tour_date_title, true);
        $criteria->compare('special_title', $this->special_title, true);
        $criteria->compare('departure_title', $this->departure_title, true);
        $criteria->compare('package_service_title', $this->package_service_title, true);
        $criteria->compare('package_gift_title', $this->package_gift_title, true);
        $criteria->compare('package_recommend_title', $this->package_recommend_title, true);
        $criteria->compare('package_service', $this->package_service, true);
        $criteria->compare('package_gift', $this->package_gift, true);
        $criteria->compare('package_recommend', $this->package_recommend, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }


    public function defaultScope()
    {
        return array(
            'alias' => 'pd',
            'order' => 'pd.language_id ASC',
        );
    }

    public function getFieldValues($product_id, $field_name)
    {
        $value_prefix = array(1 => 'en_', 2 => 'cn_');

        $data = array();
        $product_description = $this->findAll('product_id=' . $product_id);
        foreach ($product_description as $pd) {
            if (isset($value_prefix[$pd['language_id']])) {
                $prefix = $value_prefix[$pd['language_id']];
                if (is_array($field_name)) {
                    foreach ($field_name as $field) {
                        $data[$prefix . $field] = $pd[$field];
                    }
                } else {
                    $data[$prefix . $field_name] = $pd[$field_name];
                }
            }
        }

        return $data;
    }

    public function updateFieldValues($product_id, $field_name, $values)
    {
        $result = true;
        $value_prefix = array(1 => 'en_', 2 => 'cn_');

        $product_description = $this->findAll('product_id=' . $product_id);
        foreach ($product_description as $pd) {
            if (isset($value_prefix[$pd['language_id']])) {
                $prefix = $value_prefix[$pd['language_id']];
                $bChanged = false;
                if (is_array($field_name)) {
                    foreach ($field_name as $field) {
                        if (isset($values[$prefix . $field])) {
                            $pd[$field] = $values[$prefix . $field];
                            $bChanged = true;
                        }
                    }
                } else {
                    if (isset($values[$prefix . $field_name])) {
                        $pd[$field_name] = $values[$prefix . $field_name];
                        $bChanged = true;
                    }
                }

                if ($bChanged) {
                    $ret = $pd->update();
                    $result = $ret;
                    if (!$ret) {
                        break;
                    }
                }
            }
        }

        return $result;
    }
}