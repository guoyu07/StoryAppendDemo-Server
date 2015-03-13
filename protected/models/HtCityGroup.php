<?php

/**
 * This is the model class for table "ht_city_group".
 *
 * The followings are the available columns in table 'ht_city_group':
 * @property integer $group_id
 * @property string $country_code
 * @property string $name
 * @property string $description
 * @property string $cover_url
 * @property string $city_codes
 * @property integer $display_order
 */
class HtCityGroup extends CActiveRecord
{
    const CACHE_KEY_FOR_ALL = 'city_group_all';
    const CACHE_KEY_ALL_OF_COUNTRY = 'city_group_all_of_country_code_';

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtCityGroup the static model class
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
        return 'ht_city_group';
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'cg',
            'order' => 'cg.display_order ASC',
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('country_code, name, description, cover_url, city_codes, display_order', 'required'),
            array('display_order', 'numerical', 'integerOnly' => true),
            array('country_code', 'length', 'max' => 4),
            array('name', 'length', 'max' => 32),
            array('description, cover_url, city_codes', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('group_id, country_code, name, description, cover_url, city_codes, display_order', 'safe', 'on' => 'search'),
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
            'cities' => array(self::HAS_MANY, 'HtCity', '', 'on' => 'city.city_code IN cg.city_codes')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'group_id' => 'Group',
            'country_code' => 'Country Code',
            'name' => 'Name',
            'description' => 'Description',
            'cover_url' => 'Cover Url',
            'city_codes' => 'City Codes',
            'display_order' => 'Order',
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

        $criteria->compare('group_id', $this->group_id);
        $criteria->compare('country_code', $this->country_code, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('cover_url', $this->cover_url, true);
        $criteria->compare('city_codes', $this->city_codes, true);
        $criteria->compare('display_order', $this->display_order, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    protected function beforeDelete()
    {
        HtCityGroup::clearCache($this->country_code);

        return parent::beforeDelete();
    }

    protected function beforeSave()
    {
        HtCityGroup::clearCache($this->country_code);

        return parent::beforeSave();
    }

    public static function clearCache($country_code)
    {
        Yii::app()->cache->delete(HtCityGroup::CACHE_KEY_FOR_ALL);

        $key = HtCityGroup::CACHE_KEY_ALL_OF_COUNTRY . $country_code;
        Yii::app()->cache->delete($key);
    }

    public function getCityCodesOfCountry($country_code)
    {
        $c = new CDbCriteria();
        $c->addCondition('country_code="' . $country_code . '"');
        $c->select = 'city_codes';

        $city_codes_list = ModelHelper::getList($this->findAll($c), 'city_codes');

        $city_codes_str = implode(",", $city_codes_list);

        return explode(",", $city_codes_str);
    }

    public function getAllCached()
    {
        $city_groups = Yii::app()->cache->get(HtCityGroup::CACHE_KEY_FOR_ALL);

        if (empty($city_groups)) {
            $raw = $data = HtCityGroup::model()->findAll();
            $city_groups = Converter::convertModelToArray($raw);

            Yii::app()->cache->set(HtCityGroup::CACHE_KEY_FOR_ALL, $city_groups, 60 * 60);
        }

        return $city_groups;
    }

    public function getAllOfCountry($country_code)
    {
        $key = HtCityGroup::CACHE_KEY_ALL_OF_COUNTRY . $country_code;
        $city_groups = Yii::app()->cache->get($key);

        if (empty($city_groups)) {
            $raw = $data = HtCityGroup::model()->findAllByAttributes(['country_code' => $country_code]);
            $city_groups = Converter::convertModelToArray($raw);

            Yii::app()->cache->set($key, $city_groups, 60 * 60);
        }

        return $city_groups;
    }
}