<?php

/**
 * This is the model class for table "ht_product_group".
 *
 * The followings are the available columns in table 'ht_product_group':
 * @property integer $group_id
 * @property integer $type
 * @property string $city_code
 * @property string $name
 * @property string $description
 * @property string $cover_image_url
 * @property integer $status
 * @property integer $display_order
 */
class HtProductGroup extends HActiveRecord
{
    const CACHE_KEY_PRODUCT_GROUP_WITH_CITY_COUNTRY = 'product_group_with_city_country_';
    const CACHE_KEY_PRODUCT_GROUPS_OF_CITY_BY_SQL = 'product_groups_of_city_by_sql_';
    const CACHE_KEY_GROUP_TREE = 'product_group_group_tree_';
    const CACHE_KEY_PRODUCTS_OF_ONE_GROUP = 'product_group_products_of_one_group_';
    const CACHE_KEY_PRODUCT_GROUPS_TOP10_HOTEL_BUNDLE_LINE = 'product_groups_top10_hotel_bundle_line_';

    public $link_url;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtProductGroup the static model class
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
        return 'ht_product_group';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_code, name, description, cover_image_url, status, display_order', 'required'),
            array('type, status, display_order', 'numerical', 'integerOnly' => true),
            array('city_code', 'length', 'max' => 4),
            array('name', 'length', 'max' => 64),
            array('description, cover_image_url', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('group_id, type, city_code, name, description, cover_image_url, status, display_order', 'safe', 'on' => 'search'),
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
            'product_group_ref' => array(self::HAS_MANY, 'HtProductGroupRef', 'group_id','condition' => 'pgr.status=2'),
            'products'          => array(self::HAS_MANY, 'HtProduct', array('product_id' => 'product_id'), 'through' => 'product_group_ref', 'condition' => 'p.status=3'),
            'products_count'    => array(self::STAT, 'HtProductGroupRef', 'group_id'),
            'city'              => array(self::BELONGS_TO, 'HtCity', 'city_code'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'group_id'        => 'Group',
            'type'            => 'Type', // 1, 全部; 2, 热门推荐; 3, 限时特惠; 5, 酒店套餐; 4, Top 10; 6, 特价商品; 7,线路商品; 8,app商品分组, 99, 自定义分组
            'city_code'       => 'City Code',
            'name'            => 'Name',
            'description'     => 'Description',
            'cover_image_url' => 'Cover Image Url',
            'status'          => 'Status',
            'display_order'   => 'Display Order',
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
        $criteria->compare('type', $this->type);
        $criteria->compare('city_code', $this->city_code, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('cover_image_url', $this->cover_image_url, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('display_order', $this->display_order);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'pg',
            'order' => 'pg.display_order ASC',
        );
    }

    public function scopes()
    {
        return array(
            'published' => array('condition' => 'pg.status = 2 OR pg.type!=99'),
        );
    }

    protected function afterFind()
    {
        $this->link_url = Yii::app()->urlManager->createUrl('group/index', array('group_id' => $this->group_id));
    }

    protected function beforeSave()
    {
        HtProductGroup::clearCache($this->group_id);

        return parent::beforeSave();
    }

    protected function beforeDelete()
    {
        HtProductGroup::clearCache($this->group_id);

        return parent::beforeDelete();
    }

    public static function clearCache($group_id)
    {
        $keys[] = HtProductGroup::CACHE_KEY_PRODUCT_GROUP_WITH_CITY_COUNTRY . $group_id;

        $pg = HtProductGroup::model()->findByPk($group_id);
        if (!empty($pg)) {
            $city_code = $pg['city_code'];
            $keys[] = HtProductGroup::CACHE_KEY_PRODUCT_GROUPS_OF_CITY_BY_SQL . $city_code;
            $keys[] = HtProductGroup::CACHE_KEY_GROUP_TREE . $city_code;
            $keys[] = HtProductGroup::CACHE_KEY_GROUP_TREE . $city_code . '_m';
            $keys[] = HtProductGroup::CACHE_KEY_PRODUCTS_OF_ONE_GROUP . $city_code . '_' . $group_id;
            $keys[] = HtProductGroup::CACHE_KEY_PRODUCTS_OF_ONE_GROUP . $city_code . '_type_4';
            $keys[] = HtProductGroup::CACHE_KEY_PRODUCTS_OF_ONE_GROUP . $city_code . '_type_5';
            $keys[] = HtProductGroup::CACHE_KEY_PRODUCTS_OF_ONE_GROUP . $city_code . '_type_7';
            $keys[] = HtProductGroup::CACHE_KEY_PRODUCT_GROUPS_TOP10_HOTEL_BUNDLE_LINE . $city_code;
        }

        CacheUtility::deleteCaches($keys);
    }

    public static function clearCacheByProduct($product_id)
    {
        $product_group_refs = HtProductGroupRef::model()->findAllByAttributes(['product_id' => $product_id]);
        foreach ($product_group_refs as $pgr) {
            HtProductGroup::clearCache($pgr['group_id']);
        }
    }

    public function getByPkWithCityCountry($group_id)
    {
        $key = HtProductGroup::CACHE_KEY_PRODUCT_GROUP_WITH_CITY_COUNTRY . $group_id;
        $group_info = Yii::app()->cache->get($key);
        if (empty($group_info)) {
            $group_info = $this->with('city.country')->findByPk($group_id);
            $group_info = Converter::convertModelToArray($group_info);

            Yii::app()->cache->set($key, $group_info, 60 * 60);
        }

        return $group_info;
    }

    public function getAllOfCityBySQL($city_code)
    {
        $key = HtProductGroup::CACHE_KEY_PRODUCT_GROUPS_OF_CITY_BY_SQL . $city_code;
        $product_groups = Yii::app()->cache->get($key);
        if (empty($product_groups)) {
            $sql = 'select pg.*,count(p.product_id) pc from ht_product_group pg ' .
                ' left join ht_product_group_ref pgr on pg.group_id = pgr.group_id ' .
                ' join ht_product p on pgr.product_id = p.product_id ' .
                ' where pg.city_code = "' . $city_code . '" and pg.status = 2 and p.status = 3 ' .
                ' group by pg.group_id having pc >0 order by pg.type, pg.display_order';
            $product_groups = Converter::convertModelToArray(HtProductGroup::model()->findAllBySql($sql));

            Yii::app()->cache->set($key, $product_groups, 5 * 60);
        }

        return $product_groups;
    }

    public function getByCity($city_code)
    {
        $c = new CDbCriteria();
        $c->addCondition('city_code ="' . $city_code . '"');
        $c->order('display_order ASC');

        return $this->findAll($c);
    }

    public function getCategorizedByCity($city_code)
    {
        $c = new CDbCriteria();
        $c->addCondition('city_code ="' . $city_code . '"');
        $c->order = 'type ASC, display_order ASC';
        $groups = Converter::convertModelToArray($this->with('products_count')->findAll($c));

        $pre_defined_groups = array();
        $user_defined_groups = array();
        $app_defined_groups = array();

        foreach ($groups as $group) {
            if(8 == $group['type']) {
                array_push($app_defined_groups, $group);
            }
            else if ($group['type'] < 99 && $group['type'] > 2) {
                array_push($pre_defined_groups, $group);
            } else if(99 == $group['type']) {
                array_push($user_defined_groups, $group);
            }
        }

        return array('pre_defined_groups' => $pre_defined_groups, 'user_defined_groups' => $user_defined_groups, 'app_defined_groups' => $app_defined_groups);
    }

    public function getCityProducts($city_code)
    {
        $c = new CDbCriteria();
        $c->addCondition('(p.city_code = "' . $city_code . '" OR p.product_id IN (' . $this->getOtherProductIDs($city_code) . '))');
        $c->addCondition('p.status = 3'); // 只显示上线商品
        $c->order = 'p.product_id DESC';
        $city_products = HtProduct::model()->with(array('description' => array('condition' => 'language_id=2')))->findAll($c);

        $city_products_grouped = $this->with('products',
                                             array('select' => 'product_id'))->findAll('pg.city_code = "' . $city_code . '"');

        $products_grouped_ids = array();
        foreach ($city_products_grouped as $product_grouped) {
            if ($product_grouped['type'] == '99') {
                foreach ($product_grouped['products'] as $product) {
                    array_push($products_grouped_ids, $product['product_id']);
                }
            }
        }

        $data = array();
        foreach ($city_products as $product) {
            $product_id = $product['product_id'];
            $p = array('product_id' => $product_id,
                       'name'       => $product['description']['name'],
            );
            //上架商品标识
            if ($product['status'] == 3) {
                $p['online'] = 1;
            } else {
                $p['online'] = 0;
            }
            if (in_array($product_id, $products_grouped_ids)) {
                $p['in_group'] = 1;
            } else {
                $p['in_group'] = 0;
            }
            $p['type'] = $product['type'];
            $data[$product_id] = $p;
        }

        return array('products' => $data);
    }

    public function getCityProductsForGroup($city_code, $group_id)
    {
        $c = new CDbCriteria();
        $c->addCondition('(p.city_code = "' . $city_code . '" OR p.product_id IN (' . $this->getOtherProductIDs($city_code) . '))');
        $c->addCondition('p.status = 3'); //禁用商品不显示
        $c->order = 'p.product_id DESC';
        $city_products = HtProduct::model()->with(array('description' => array('condition' => 'language_id=2')))->findAll($c);


        $city_products_grouped = $this->with('products',
                                             array('select' => 'product_id'))->findAll('pg.group_id = "' . $group_id . '"');

        $products_grouped_ids = array();
        foreach ($city_products_grouped as $product_grouped) {
            foreach ($product_grouped['products'] as $product) {
                array_push($products_grouped_ids, $product['product_id']);
            }
        }

        $data = array();
        foreach ($city_products as $product) {
            $product_id = $product['product_id'];
            $p = array('product_id' => $product_id,
                       'name'       => $product['description']['name'],
            );
            //上架商品标识
            if ($product['status'] == 3) {
                $p['online'] = 1;
            } else {
                $p['online'] = 0;
            }
            if (in_array($product_id, $products_grouped_ids)) {
                $p['in_group'] = 1;
            } else {
                $p['in_group'] = 0;
            }
            $data[$product_id] = $p;
        }

        return array('products' => $data);
    }

    public function readyToOnline()
    {
        if (trim($this->name) == '') {
            return array('code' => 400, 'msg' => '产品分组名称或描述信息不完整。');
        }

        $result = HtProductGroupRef::model()->haveProductsOnline($this->group_id);
        if ($result) {
            return array('code' => 200, 'msg' => 'Ok');
        } else {
            return array('code' => 400, 'msg' => '包含产品至少要有一个为上线状态。');
        }
    }

    private function getOtherProductIDs($city_code)
    {
        $other_products = HtProductCity::model()->findAllByAttributes(array('city_code' => $city_code));
        $other_product_ids = ModelHelper::getList($other_products, 'product_id');

        $result = implode(',', $other_product_ids);
        if (empty($result)) {
            $result = '-1';
        }

        return $result;
    }

    public static function getProductIds($group_id)
    {
        $connection = Yii::app()->db;
        $sql = 'select pgr.product_id from ht_product_group_ref pgr INNER JOIN ht_product p on pgr.product_id = p.product_id WHERE pgr.group_id = "' . (int)$group_id . '" and p.status = 3';
        $data = $connection->createCommand($sql)->queryAll();

        return ModelHelper::getList($data, 'product_id');
    }

    public function getProductsCount($group_id)
    {
        $connection = Yii::app()->db;
        $sql = 'select count(p.product_id) from ht_product_group_ref pgr INNER JOIN ht_product p on pgr.product_id = p.product_id WHERE pgr.group_id = "' . (int)$group_id . '" and p.status = 3';
        $count = $connection->createCommand($sql)->queryScalar();

        return $count;

    }

    public function getProductCoverImage($group_id, $product_id)
    {
        $query_result = HtProductGroup::model()->with('product_group_ref')->findByPk($group_id);
        $query_result = Converter::convertModelToArray($query_result);
        if ($query_result) {
            foreach ($query_result['product_group_ref'] as &$r) {
                if ($r['product_id'] == $product_id) {
                    return $r['product_image_url'];
                }
            }
        }

        return '';
    }

    public static function getProductsFromOneGroup($city_code, $by_type = false, $group_id = false)
    {
        $key = HtProductGroup::CACHE_KEY_PRODUCTS_OF_ONE_GROUP . $city_code . ($by_type ? '_type_' . $by_type : '_' . $group_id);
        $result = Yii::app()->cache->get($key);
        if (!empty($result)) {
            return $result;
        }

        $result = array();
        if ($by_type) {
            $query_result = Converter::convertModelToArray(HtProductGroup::model()->with('product_group_ref')->findByAttributes(['city_code' => $city_code, 'status' => 2, 'type' => $by_type]));
        } else {
            $query_result = Converter::convertModelToArray(HtProductGroup::model()->with('product_group_ref')->findByAttributes(['city_code' => $city_code, 'status' => 2, 'group_id' => $group_id]));
        }

        $result['name'] = $query_result['name'];
        $result['group_id'] = $query_result['group_id'];
        $result['link_url'] = $query_result['link_url'];
        $result['description'] = $query_result['description'];
        $result['products'] = array();
        if (isset($query_result['product_group_ref'])) {
            foreach ($query_result['product_group_ref'] as &$ref) {
                $product_id = $ref['product_id'];
                if (HtProduct::model()->isProductVisible($product_id)) {
                    $product = HtProduct::getProductInfo($product_id);
                    $product['group_cover_image'] = HtProductGroup::model()->getProductCoverImage($query_result['group_id'],
                                                                                                  $product_id);
                    if ($query_result['type'] == 5) {
                        $product['package_included_product_count'] = HtProductBundle::model()->getPackageIncludedProductCount($product_id);
                    } else if ($query_result['type'] == 4) {
                        $product['activity_info'] = Yii::app()->activity->getActivityInfo($product_id);
                    } else if ($query_result['type'] == 7) {//线路商品
                        $product['product_name'] = $ref['product_name'];

                        $line_product_info = HtProductGroup::getLineProductInfo($product_id, $ref['tour_cities'], $ref['tour_days'], $ref['line_image_url']);
                        $product = array_merge($product, $line_product_info);
                    }

                    array_push($result['products'], $product);
                }
            }
        }

        Yii::app()->cache->set($key, $result, 3 * 60);

        return $result;
    }

    public static function getLineProductInfo($product_id, $ref_tour_cities = '', $ref_tour_days = '', $ref_line_image_url = '') {
        $introduction = HtProductTripIntroduction::model()->findByPk($product_id);
        $highlight = HtTripHighlight::model()->findByAttributes(array('product_id' => $product_id));

        $tour_days = $highlight['total_days'] ? $highlight['total_days'] : $ref_tour_days;
        $suitable_time = $highlight['suitable_time'];

        $city_codes = explode(';', $highlight['tour_cities'] ? $highlight['tour_cities'] : $ref_tour_cities);
        $c = new CDbCriteria();
        $c->addInCondition('city_code', $city_codes);
        $cities = HtCity::model()->findAll($c);
        $tour_cities = ModelHelper::getList($cities, 'cn_name');
        $tour_cities = implode('-', $tour_cities);

        $line_image_url = $introduction['line_image'] ? $introduction['line_image'] : $ref_line_image_url;

        return ['tour_days' => $tour_days, 'tour_cities' => $tour_cities, 'line_image_url' => $line_image_url, 'suitable_time' => $suitable_time];
    }

    public static function getGroupTree($city_code, $country_name, $city_name, $is_mobile = false)
    {
        $key = HtProductGroup::CACHE_KEY_GROUP_TREE . $city_code . ($is_mobile ? '_m' : '');
        $group_tree = Yii::app()->cache->get($key);
        if (!empty($group_tree)) {
            return $group_tree;
        }

        $product_groups = HtProductGroup::model()->getAllOfCityBySQL($city_code);

        $groups = [];
        foreach ($product_groups as $group) {
            if (in_array((int)$group['type'], [6, 99])) {
                $groups[] = $group;
            }
        }

        if (empty($groups)) {
            return [];
        }

        $total = 0;
        $sub_groups = [];
        foreach ($groups as $group) {
            if ($is_mobile) {
                $link_url = Yii::app()->createUrl('mobile' . '#/city/' . $city_code . '/g_' . $group['group_id']);
            } else {
                $link_url = Yii::app()->createUrl($country_name . '/' . $city_name . '/group/' . $group['group_id']);
            }

            $product_ids = HtProductGroup::getProductIds($group['group_id']);
            $count = 0;
            foreach ($product_ids as $product_id) {
                if (HtProduct::model()->isProductVisible($product_id)) {
                    $count++;
                }
            }

            if ($count > 0) {
                $sub_groups[] = [
                    'group_id'        => $group['group_id'],
                    'name'            => $group['name'],
                    'description'     => $group['description'],
                    'cover_image_url' => $group['cover_image_url'],
                    'display_order'   => $group['display_order'],
                    'product_count'   => $count,
                    'link_url'        => $link_url,
                ];
            }

            $total += $count;
        }

        if ($is_mobile) {
            return $sub_groups;
        }

        // construct parent group 热门推荐

        $group_tree = ['group_id' => 0, 'name' => '热门推荐', 'description' => '', 'cover_image_url' => '', 'display_order' => 1,
                       'products' => [], 'product_count' => $total, 'sub_groups' => $sub_groups,
                       'link_url' => Yii::app()->createUrl($country_name . '/' . $city_name . '/group/' . 'all_groups')];

        Yii::app()->cache->set($key, $group_tree, 3 * 60);

        return $group_tree;
    }

    public static function getTop10AndHotelBundleAndLine($city_code)
    {
        $key = HtProductGroup::CACHE_KEY_PRODUCT_GROUPS_TOP10_HOTEL_BUNDLE_LINE . $city_code;
        $result = Yii::app()->cache->get($key);
        if (!empty($result)) {
            return $result;
        }

        $top10 = false;
        $hotel_bundle = false;
        $line = false;

        $query_result = HtProductGroup::model()->with('product_group_ref')->findAllByAttributes(['city_code' => $city_code, 'status' => 2, 'type' => array(4, 5, 7)]);
        if (!empty($query_result)) {
            foreach ($query_result as $row) {
                $group_result = $row->readyToOnline();
                if ($group_result['code'] == 200) {
                    if ($row->type == 4) {
                        $top10 = ['id' => 'top_10', 'label' => $row->name];
                    }
                    if ($row->type == 5) {
                        $hotel_bundle = ['id' => 'package', 'label' => $row->name];
                    }
                    if ($row->type == 7) {
                        $line = ['id' => 'line', 'label' => $row->name];
                    }
                }
            }
        }
        Yii::app()->cache->set($key, [$top10, $hotel_bundle, $line], 3 * 60);

        return [$top10, $hotel_bundle, $line];
    }

}
