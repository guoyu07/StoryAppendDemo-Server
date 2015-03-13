<?php

/**
 * This is the model class for table "ht_country".
 *
 * The followings are the available columns in table 'ht_country':
 * @property integer $country_code
 * @property integer $continent_id
 * @property string $cn_name
 * @property string $en_name
 * @property string $fullname
 * @property string $pinyin
 * @property string $description
 * @property string $currency_code
 * @property string $link_url
 */
class HtCountry extends HActiveRecord
{
    const CACHE_KEY_COUNTRY_WITH_COUNTRY_IMAGE = 'country_with_country_image_';
    const CACHE_KEY_COUNTRY_BY_PK = 'country_by_pk_';

    public $link_url;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_country';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('continent_id, cn_name, en_name, fullname, pinyin, description, currency_code', 'required'),
            array('continent_id', 'numerical', 'integerOnly' => true),
            array('cn_name, en_name, fullname', 'pinyin', 'length', 'max' => 64),
            array('currency_code', 'length', 'max' => 3),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('country_code, continent_id, cn_name, en_name, fullname, pinyin, description, currency_code', 'safe', 'on' => 'search'),
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
            'cities' => array(self::HAS_MANY, 'HtCity', 'country_code', 'condition' => 'city.has_online_product=1'),
            'city_groups' => array(self::HAS_MANY, 'HtCityGroup', '', 'on' => 'cnt.country_code=cg.country_code'),
            'city_group_count' => array(self::STAT, 'HtCityGroup', 'country_code'),
            'country_image' => array(self::HAS_ONE, 'HtCountryImage', 'country_code'),
        );
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'cnt',
            'order' => 'cnt.pinyin ASC',
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'country_code' => 'Country',
            'continent_id' => 'Continent',
            'cn_name' => 'Name',
            'en_name' => 'Enname',
            'fullname' => 'Fullname',
            'pinyin' => '拼音',
            'description' => 'Description',
            'currency_code' => 'Currency Code',
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

        $criteria->compare('country_code', $this->country_code);
        $criteria->compare('continent_id', $this->continent_id);
        $criteria->compare('cn_name', $this->cn_name, true);
        $criteria->compare('en_name', $this->en_name, true);
        $criteria->compare('fullname', $this->fullname, true);
        $criteria->compare('pinyin', $this->fullname, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('currency_code', $this->currency_code, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtCountry the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function afterFind()
    {
        $en_name = $this->getEnNameInUrl();

        $this->link_url = Yii::app()->urlManager->createUrl('country/index', array('en_name' => $en_name));
    }

    protected function beforeSave()
    {
        HtCountry::clearCache($this->country_code);

        return parent::beforeSave();
    }

    public static function clearCache($country_code)
    {
        $key = HtCountry::CACHE_KEY_COUNTRY_WITH_COUNTRY_IMAGE . $country_code;
        Yii::app()->cache->delete($key);
        $key = HtCountry::CACHE_KEY_COUNTRY_BY_PK . $country_code;
        Yii::app()->cache->delete($key);
        Yii::app()->cache->delete('continents_all_with_countries_cities');

    }

    public function getCountryWithCountryImage($country_code)
    {
        $key = HtCountry::CACHE_KEY_COUNTRY_WITH_COUNTRY_IMAGE . $country_code;
        $country = Yii::app()->cache->get($key);
        if(empty($country))
        {
            $country = $this->with('country_image')->findByPk($country_code);
            $country = Converter::convertModelToArray($country);

            Yii::app()->cache->set($key, $country, 2*60*60);
        }

        return $country;
    }

    public function getByPk($country_code)
    {
        $key = HtCountry::CACHE_KEY_COUNTRY_BY_PK . $country_code;
        $country = Yii::app()->cache->get($key);
        if(empty($country))
        {
            $country = $this->findByPk($country_code);
            $country = Converter::convertModelToArray($country);

            Yii::app()->cache->set($key, $country, 2*60*60);
        }

        return $country;
    }

    public function getEnNameInUrl()
    {
        return str_replace(' ', '_', $this->en_name);
    }

    public function getByEnName($en_name)
    {
        $country_info_key = 'country_info_' . $en_name;
        $country_info = Yii::app()->cache->get($country_info_key);
        if (empty($country_info)) {
            $country_info = HtCountry::model()->findByAttributes(array('en_name' => $en_name));
            $country_info = Converter::convertModelToArray($country_info);
            Yii::app()->cache->set($country_info_key, $country_info, 36000);
        }

        return $country_info;
    }

    public function getCountriesHaveProductsOnline()
    {
        $country_code_list = $this->getCountryCodesHaveProductsOnline();

        $c = new CDbCriteria();
        $c->addInCondition('country_code', $country_code_list);

        return $this->findAll($c);
    }

    public function getCountriesHaveProducts()
    {
        $country_code_list = $this->getCountryCodesHaveProducts();

        $c = new CDbCriteria();
        $c->addInCondition('country_code', $country_code_list);

        return $this->findAll($c);
    }

    public function getCountriesHaveCitiesNotGrouped()
    {
        //  国家含有未被分组的城市
        $country_code_list = $this->getCountryCodesHaveGroups();

        $incomplete_country_code_list = array();
        //  check them one by one
        foreach ($country_code_list as $country_code) {
            $city_code_list = ModelHelper::getList(HtCity::model()->getCitiesHaveProductsOnline($country_code),
                                                   'city_code');

            $city_code_list_in_group = HtCityGroup::model()->getCityCodesOfCountry($country_code);

            $result = array_diff($city_code_list, $city_code_list_in_group);
            if (count($result) > 0) {
                array_push($incomplete_country_code_list, $country_code);
            }
        }

        if (count($incomplete_country_code_list) > 0) {
            $c = new CDbCriteria();
            $c->addInCondition('country_code', $incomplete_country_code_list);

            return $this->findAll($c);
        }
    }

    public function getIncompleteCountries()
    {
        // 国家信息不完整 -- 无封面图
        $result = array();
        $country_code_list = $this->getCountryCodesHaveProductsOnline();

        $c = new CDbCriteria();
        $c->addInCondition('country_code', $country_code_list);
        $c->addCondition('cover_url="" or mobile_url=""');
        $countries_no_cover = ModelHelper::getList(HtCountryImage::model()->findAll($c), 'country_code');

        $c = new CDbCriteria();
        $c->addInCondition('country_code', $countries_no_cover);

        return HtCountry::model()->findAll($c);
    }

    public function getIncompleteTabCountries()
    {
        //未编辑完成新版国家页的国家
        $country_code_list = $this->getCountryCodesHaveProductsOnline();

        $c = new CDbCriteria();
        $c->addInCondition('country_code', $country_code_list);
        $c->addCondition('status = 1');
        $countries_no_tab = ModelHelper::getList(HtCountryTab::model()->findAll($c), 'country_code');

        $c = new CDbCriteria();
        $c->addInCondition('country_code', $countries_no_tab);

        return HtCountry::model()->findAll($c);
    }

    private function getCountryCodesHaveProductsOnline()
    {
        $c = new CDbCriteria();
        $c->addCondition('has_online_product=1');
        $c->distinct = 'country_code';
        $c->select = 'country_code';

        $cities = HtCity::model()->findAll($c);

        $country_code_list = ModelHelper::getList($cities, 'country_code');

        return $country_code_list;
    }

    private function getCountryCodesHaveProducts()
    {
        $city_codes = HtCity::model()->getCityIDsHaveProduct();

        $c = new CDbCriteria();
        $c->addInCondition('city_code', $city_codes);
        $c->distinct = 'country_code';
        $c->select = 'country_code';

        $cities = HtCity::model()->findAll($c);

        $country_code_list = ModelHelper::getList($cities, 'country_code');

        return $country_code_list;
    }

    private function getCountryCodesHaveGroups()
    {
        $c = new CDbCriteria();
        $c->select = 'country_code';
        $c->distinct = 'country_code';

        $city_groups = HtCityGroup::model()->findAll($c);
        $country_code_list = array();
        foreach ($city_groups as $city_group) {
            array_push($country_code_list, $city_group['country_code']);
        }

        return $country_code_list;
    }


}
