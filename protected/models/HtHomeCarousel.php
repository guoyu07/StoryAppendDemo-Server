<?php

/**
 * This is the model class for table "ht_home_carousel".
 *
 * The followings are the available columns in table 'ht_home_carousel':
 * @property integer $id
 * @property integer $type
 * @property string $country_code
 * @property string $city_code
 * @property integer $product_id
 * @property integer $activity_id
 * @property integer $city_group_id
 * @property integer $product_group_id
 * @property string $image_url
 * @property string $link_url
 * @property integer $display_order
 * @property integer $status
 */
class HtHomeCarousel extends HActiveRecord
{
    const CACHE_KEY_HOME_CAROUSELS_ARRAY = 'home_carousels_online_ARRAY';

    const T_USER_URL = 0;
    const T_ACTIVITY = 1;
    const T_COUNTY = 2;
    const T_CITY_GROUP = 3;
    const T_CITY = 4;
    const T_PRODUCT_GROUP = 5;
    const T_PRODUCT = 6;
    const T_2345_URL = 7;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtHome the static model class
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
        return 'ht_home_carousel';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('type, product_id, activity_id, city_group_id, product_group_id, display_order', 'required'),
            array('type, product_id, activity_id, city_group_id, product_group_id, display_order, status', 'numerical', 'integerOnly' => true),
            array('country_code', 'length', 'max' => 4),
            array('city_code', 'length', 'max' => 8),
            array('image_url', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, type, country_code, city_code, product_id, activity_id, city_group_id, product_group_id, image_url, display_order, status', 'safe', 'on' => 'search'),
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
            'id' => 'ID',
            'type' => 'Type',
            'country_code' => 'Country Code',
            'city_code' => 'City Code',
            'product_id' => 'Product',
            'activity_id' => 'Activity',
            'city_group_id' => 'City Group',
            'product_group_id' => 'Product Group',
            'image_url' => 'Image Url',
            'display_order' => 'Display Order',
            'status' => 'Status',
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

        $criteria->compare('id', $this->id);
        $criteria->compare('type', $this->type);
        $criteria->compare('country_code', $this->country_code, true);
        $criteria->compare('city_code', $this->city_code, true);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('activity_id', $this->activity_id);
        $criteria->compare('city_group_id', $this->city_group_id);
        $criteria->compare('product_group_id', $this->product_group_id);
        $criteria->compare('image_url', $this->image_url, true);
        $criteria->compare('display_order', $this->display_order);
        $criteria->compare('status', $this->status);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array('alias' => 'hc', 'order' => 'hc.display_order');
    }

    public function afterFind()
    {
        if ($this->link_url) {
            return;
        }

        switch ($this->type) {
            case self::T_ACTIVITY:
                $this->link_url = Yii::app()->controller->createUrl('activity/index',
                                                                    ['activity_id' => $this->activity_id]);
                break;
            case self::T_COUNTY:
                $this->link_url = Yii::app()->controller->createUrl('country/index',
                                                                    ['en_name' => $this->getCountryEnName()]);
                break;
            case self::T_CITY_GROUP:
                $this->link_url = Yii::app()->controller->createUrl('country/index',
                                                                    ['en_name' => $this->getCountryEnName()]);
                break;
            case self::T_CITY:
                list($country_name, $city_name) = $this->getCountryNameCityName($this->city_code);
                $this->link_url = Yii::app()->controller->createUrl('city/index',
                                                                    ['city_name' => $city_name, 'country_name' => $country_name]);
                break;
            case self::T_PRODUCT_GROUP:
                list($country_name, $city_name) = $this->getCountryNameCityName($this->city_code);
                $this->link_url = Yii::app()->controller->createUrl('city/index',
                                                                    ['city_name' => $city_name, 'country_name' => $country_name]);
                break;
            case self::T_PRODUCT:
                $this->link_url = Yii::app()->controller->createUrl('product/index',
                                                                    ['product_id' => $this->product_id]);
                break;
            default:
        }
    }

    protected function beforeSave() {
        HtHomeCarousel::clearCache();

        return parent::beforeSave();
    }

    private function getCountryEnName()
    {
        $country = HtCountry::model()->findByPk($this->country_code);
        if (!empty($country)) {
            $en_name = str_replace(' ', '_', $country['en_name']);

            return $en_name;
        }

        return '';
    }

    private function getCountryNameCityName($city_code)
    {
        $country_name = '';
        $city_name = '';

        $city_info = HtCity::model()->findByPk($city_code);
        if (!empty($city_info)) {
            $city_name = str_replace(' ', '_', $city_info['en_name']);
            $country_info = HtCountry::model()->findByPk($city_info['country_code']);
            if (!empty($country_info)) {
                $country_name = str_replace(' ', '_', $country_info['en_name']);
            }
        }

        return array($country_name, $city_name);
    }

    public static function clearCache() {
        $key = HtHomeCarousel::CACHE_KEY_HOME_CAROUSELS_ARRAY;
        Yii::app()->cache->delete($key);
    }

    public function getAllOnline($type = 0)
    {
        $key = HtHomeCarousel::CACHE_KEY_HOME_CAROUSELS_ARRAY.'_'.$type;
        $carousels = Yii::app()->cache->get($key);

        if (empty($carousels)) {
            $c = new CDbCriteria();
            $c->addCondition('status=1');
            $c->addCondition('type = '.$type);
            $raw = $this->findAll($c);
            $carousels = Converter::convertModelToArray($raw);
            Yii::app()->cache->set($key, $carousels, 60*60);
        }

        return $carousels;
    }
}