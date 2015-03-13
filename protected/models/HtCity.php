<?php

/**
 * This is the model class for table "ht_city".
 *
 * The followings are the available columns in table 'ht_city':
 * @property string $country_code
 * @property string $city_code
 * @property string $cn_name
 * @property string $en_name
 * @property string $pinyin
 * @property string $link_url
 * @property string $city_name
 * @property string $country_name
 * @property integer $has_product
 * @property integer $has_online_product
 */
class HtCity extends HActiveRecord
{
    const CACHE_KEY_CITY_BY_COUNTRY_CODE_EN_NAME = 'city_by_country_code_en_name_';
    const CACHE_KEY_CITY_RECOMMEND_LIST = 'city_recommend_list';
    const CACHE_KEY_CITY_WITH_CITY_IMAGE = 'city_with_city_image_';
    const CACHE_KEY_CITIES_WITH_CITY_IMAGE_HAVE_PRODUCTS_ONLINE = 'cities_with_city_image_has_product_online_';

    public $link_url;
    public $link_url_m;
    public $city_name;
    public $country_name;
    public $country_cn_name;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_city';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('country_code, city_code, cn_name, en_name', 'required'),
            array('has_product, has_online_product', 'numerical', 'integerOnly' => true),
            array('country_code', 'length', 'max' => 2),
            array('city_code', 'length', 'max' => 4),
            array('cn_name, en_name, pinyin', 'length', 'max' => 128),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('country_code, city_code, cn_name, en_name, pinyin, link_url, country_name, city_name, has_product, has_online_product', 'safe', 'on' => 'search'),
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
            'products' => array(self::HAS_MANY, 'HtProduct', 'city_code'),
            'product_groups' => array(self::HAS_MANY, 'HtProductGroup', 'city_code'),
            'city_image' => array(self::HAS_ONE, 'HtCityImage', 'city_code'),
            'country' => array(self::BELONGS_TO, 'HtCountry', 'country_code'),
        );
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'city',
            'order' => 'city.pinyin ASC',
        );
    }

    public function scopes()
    {
        return array(
//            'has_product' => array('join' => 'RIGHT JOIN ht_product p ON city.city_code=p.city_code AND p.status IN (3,5)', 'group' => 'city.city_code'),
//            'has_product' => ['condition' => 'city.has_product = 1'],
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'country_code' => 'Country Code',
            'city_code' => 'City Code',
            'cn_name' => 'Cn Name',
            'en_name' => 'En Name',
            'pinyin' => 'Pinyin',
            'has_product' => 'Has Product',
            'has_online_product' => 'Has Online Product',
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

        $criteria->compare('country_code', $this->country_code, true);
        $criteria->compare('city_code', $this->city_code, true);
        $criteria->compare('cn_name', $this->cn_name, true);
        $criteria->compare('en_name', $this->en_name, true);
        $criteria->compare('pinyin', $this->pinyin, true);
        $criteria->compare('has_product', $this->has_product);
        $criteria->compare('has_online_product', $this->has_online_product);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtCity the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function afterFind()
    {
        if (empty($this->country_code)) {
            return;
        }

        $country = HtCountry::model()->findByPk($this->country_code);
        $country_name = str_replace(' ', '_', $country->en_name);
        $city_name = str_replace(' ', '_', $this->en_name);
        $this->link_url = Yii::app()->urlManager->createUrl('city/index',
                                                            array('city_name' => $city_name, 'country_name' => $country_name));
        $this->link_url_m = Yii::app()->createUrl('mobile#/city/' . $this->city_code);
        $this->country_name = $country_name;
        $this->city_name = $city_name;
        $this->country_cn_name = $country->cn_name;
    }

    protected function beforeSave()
    {
        HtCity::clearCache($this->country_code, $this->en_name, $this->city_code);

        return parent::beforeSave();
    }

    public static function clearCache($country_code, $en_name, $city_code)
    {
        if (!empty($country_code)) {
            $key = HtCity::CACHE_KEY_CITY_BY_COUNTRY_CODE_EN_NAME . $country_code . '_' . $en_name;
            Yii::app()->cache->delete($key);

            $key = HtCity::CACHE_KEY_CITY_RECOMMEND_LIST;
            Yii::app()->cache->delete($key);
        }

        $key = HtCity::CACHE_KEY_CITY_WITH_CITY_IMAGE . $city_code;
        Yii::app()->cache->delete($key);

        if (empty($country_code)) {
            $item = HtCity::model()->findByPk($city_code);
            $country_code = $item['country_code'];
        }
        $key = HtCity::CACHE_KEY_CITIES_WITH_CITY_IMAGE_HAVE_PRODUCTS_ONLINE . $country_code;
        Yii::app()->cache->delete($key);
    }

    public function getCountryCityInfo($cityIDs)
    {
        $city_codes = "'" . implode("','", $cityIDs) . "'";

        $sql = "SELECT DISTINCT continent_id, country.cn_name as country_name, country.country_code as country_code,
    				city_code, city.cn_name AS city_name, city.en_name AS city_en_name, city.pinyin AS city_pinyin
    				FROM ht_city AS city LEFT JOIN ht_country as country on city.country_code=country.country_code
    				WHERE city_code
    				IN (" . $city_codes . ")
    				ORDER BY city.pinyin";
        // ORDER BY continent_id, country_name, city_name";


        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);

        $rows = $command->queryAll();

        foreach ($rows as $result) {
            $data[] = array(
                'continent_id' => $result['continent_id'],
                'country_name' => $result['country_name'],
                'country_code' => $result['country_code'],
                'city_code' => $result['city_code'],
                'city_name' => $result['city_name'],
                'city_en_name' => $result['city_en_name'],
                'city_pinyin' => $result['city_pinyin']
            );
        }

        return $data;
    }

    public function getByCountryCodeEnName($country_code, $en_name)
    {
        $key = HtCity::CACHE_KEY_CITY_BY_COUNTRY_CODE_EN_NAME . $country_code . '_' . $en_name;
        $city_info = Yii::app()->cache->get($key);
        if (empty($city_info)) {
            $city_info = $this->findByAttributes(array('country_code' => $country_code, 'en_name' => $en_name));
            $city_info = Converter::convertModelToArray($city_info);

            Yii::app()->cache->set($key, $city_info, 60 * 60);
        }

        return $city_info;
    }

    public function getCityRecommendList()
    {
        $key = HtCity::CACHE_KEY_CITY_RECOMMEND_LIST;
        $city_recommend_list = Yii::app()->cache->get($key);
        if (empty($city_recommend_list)) {
            $city_recommend_str = HtSetting::model()->find("`group` = 'city' and `key` = 'city_recommend'");
            $city_recommend = explode(",", $city_recommend_str['value']);
            $city_recommend_list = [];
            if (!empty($city_recommend)) {
                foreach ($city_recommend as $item) {
                    if (!empty($item)) {
                        $c_city = HtCity::model()->getCityWithCityImage($item);
                        if (!empty($c_city) && $c_city['has_product'] == 1) {
                            array_push($city_recommend_list, $c_city);
                        }
                    }
                }
            }
            Yii::app()->cache->set($key, $city_recommend_list, 60 * 60);
        }

        return $city_recommend_list;
    }

    public function getCityWithCityImage($city_code)
    {
        $key = HtCity::CACHE_KEY_CITY_WITH_CITY_IMAGE . $city_code;
        $city = Yii::app()->cache->get($key);
        if (empty($city)) {
            $city = HtCity::model()->with('city_image')->findByPk($city_code);
            $city = Converter::convertModelToArray($city);

            Yii::app()->cache->set($key, $city, 60 * 60);
        }

        return $city;
    }

    public function getCitiesWithCityImageHasProductsOnline($country_code)
    {
        $key = HtCity::CACHE_KEY_CITIES_WITH_CITY_IMAGE_HAVE_PRODUCTS_ONLINE . $country_code;
        $cities = Yii::app()->cache->get($key);
        if (empty($cities)) {
            $cities = $this->with('city_image')->findAllByAttributes(['country_code' => $country_code, 'has_online_product' => 1]);
            $cities = Converter::convertModelToArray($cities);

            Yii::app()->cache->set($key, $cities, 60 * 60);
        }

        return $cities;
    }

    public function getByCode($city_code)
    {
        $c = new CDbCriteria();
        $c->addCondition('city_code="' . $city_code . '"');

        return HtCity::model()->find($c);
    }

    public function getCitiesHaveProductsOnline($country_code)
    {
        $c = new CDbCriteria();
        $c->addCondition('country_code="' . $country_code . '"');
        $c->addCondition('has_online_product=1');

        return $this->findAll($c);
    }

    public function getAllCitiesHaveProductsOnline()
    {
        $c = new CDbCriteria();
        $c->addCondition('has_online_product=1');

        return $this->findAll($c);
    }

    public function getCityIDsHaveProduct()
    {
        $criteria = new CDbCriteria;
        $criteria->distinct = true;
        $criteria->select = 'city_code';
        $criteria->AddCondition('has_product=1');

        $result = $this->findAll($criteria);

        return ModelHelper::getList($result, 'city_code');
    }

    public function haveProductsOnline($city_code)
    {
        $products = $this->with('products')->findByPk($city_code);
        foreach ($products['products'] as $product) {
            if ($product['status'] == 3) {
                return true;
            }
        }

        $products = HtProductCity::model()->with('product')->findAll('pc.city_code="' . $city_code . '"');
        foreach ($products as $product) {
            if ($product['product']['status'] == 3) {
                return true;
            }
        }

        return false;
    }

    public function getCitiesHaveIncompleteInfo()
    {
        $sql = "SELECT c.city_code, c.cn_name FROM ht_city c LEFT JOIN ht_city_image ci ON ci.city_code = c.city_code ";
        $sql .= " LEFT JOIN ht_seo_setting ss ON ss.type=3 AND ss.id = c.city_code ";
        $sql .= " WHERE c.has_online_product = 1 ";
        $sql .= " AND (ci.banner_image_url is null OR ci.banner_image_url = '' " .
            " OR ci.grid_image_url is null OR ci.grid_image_url = ''";
        $sql .= " OR ss.title is null)";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);

        $rows = $command->queryAll();

        return $rows;
    }

    public function getCitiesHaveNewGroupInfo()
    {
        $sql = " SELECT DISTINCT c.city_code, c.cn_name FROM `ht_city` c left join `ht_product_group` pg on c.city_code = pg.city_code WHERE pg.type = 99";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);

        $row = $command->queryAll();

        return $row;
    }

    public function getCitiesMissingGroupCover()
    {
        $sql = "
        SELECT c.city_code, c.cn_name, pg.group_id
        FROM `ht_product_group` AS pg
        JOIN `ht_city` AS c
        ON c.city_code = pg.city_code
        WHERE `status` = 2
        AND pg.type = 99
        AND `cover_image_url` = ''
        ";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);

        $row = $command->queryAll();

        return $row;
    }

    public static function updateCityHasOnlineProduct($city_code)
    {
        // check whether city has product online and update field has_online_product
        $result = 0;
        if (HtCity::model()->haveProductsOnline($city_code)) {
            $result = 1;
        }
        HtCity::model()->updateByPk($city_code, array('has_online_product' => $result));
    }

}
