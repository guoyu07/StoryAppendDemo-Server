<?php

/**
 * This is the model class for table "ht_product_tag".
 *
 * The followings are the available columns in table 'ht_product_tag':
 * @property integer $product_id
 * @property integer $tag_id
 */
class HtProductTag extends CActiveRecord
{
    const CACHE_KEY_TAG_TREE = 'product_tag_tag_tree_';
    const CACHE_KEY_PRODUCTS_OF_ONE_TAG = 'product_tag_products_of_one_tag_';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_tag';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, tag_id', 'required'),
            array('product_id, tag_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('product_id, tag_id', 'safe', 'on' => 'search'),
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
            'tag'     => array(self::HAS_ONE, 'HtTag', '', 'on' => 'pt.tag_id = t.tag_id'),
            'product' => array(self::BELONGS_TO, 'HtProduct', '', 'on' => 'pt.product_id = p.product_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'product_id' => 'Product',
            'tag_id'     => 'Tag',
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
        $criteria->compare('tag_id', $this->tag_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductTag the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'pt',
        );
    }

    protected function beforeSave()
    {
        HtProductTag::clearCache($this->product_id, $this->tag_id);

        return parent::beforeSave();
    }

    protected function beforeDelete()
    {
        HtProductTag::clearCache($this->product_id, $this->tag_id);

        return parent::beforeDelete();
    }

    public static function clearCache($product_id, $tag_id)
    {
        $product = HtProduct::model()->findByPk($product_id);
        if (!empty($product)) {
            $city_code = $product['city_code'];
            $keys[] = HtProductTag::CACHE_KEY_TAG_TREE . $city_code;
            $keys[] = HtProductTag::CACHE_KEY_TAG_TREE . $city_code . '_m';

            $keys[] = HtProductTag::CACHE_KEY_PRODUCTS_OF_ONE_TAG . $city_code . $tag_id;

            $tag = HtTag::model()->findByPk($tag_id);
            if ($tag['parent_tag_id'] > 0) {
                $parent_tag = HtTag::model()->findByPk($tag['parent_tag_id']);
                $keys[] = HtProductTag::CACHE_KEY_PRODUCTS_OF_ONE_TAG . $city_code . $parent_tag['tag_id'];
            }
        }

        CacheUtility::deleteCaches($keys);
    }

    public static function clearCacheByProduct($product_id)
    {
        $tags = HtProductTag::model()->findAllByAttributes(['product_id' => $product_id]);
        foreach ($tags as $tag) {
            HtProductTag::clearCache($product_id, $tag['tag_id']);
        }
    }

    public static function getProductTags($product_ids)
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('product_id', $product_ids);

        $product_tags = Converter::convertModelToArray(HtProductTag::model()->with('tag')->findAll($criteria));

        return $product_tags;
    }

    public static function getProductsBelongToTags($tag_ids, $product_ids)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'distinct product_id';
        $criteria->addInCondition('tag_id', $tag_ids);
        $criteria->addInCondition('product_id', $product_ids);

        return ModelHelper::getList(HtProductTag::model()->findAll($criteria), 'product_id');
    }

    public static function getTagsOfProduct($product_id)
    {
        $tags = HtProductTag::model()->with('tag')->findAllByAttributes(['product_id' => $product_id]);
        if ($tags) {
            $tags = Converter::convertModelToArray($tags);
            foreach ($tags as &$t) {
                if ($t['tag']['parent_tag_id'] != 0) {
                    $tag = HtTag::model()->findByPk($t['tag']['parent_tag_id']);
                    $t['tag']['parent_tag_name'] = $tag['name'];
                }
            }
        }

        return $tags;
    }

    public static function updateTags($product_id, $tags)
    {
        HtProductTag::model()->deleteAll('product_id = ' . $product_id);
        foreach ($tags as $tag) {
            $item = new HtProductTag();
            $item['product_id'] = $tag['product_id'];
            $item['tag_id'] = $tag['tag_id'];
            $item->insert();
        }
    }

    public static function getTagTree($city_code, $is_mobile = false)
    {
        $key = HtProductTag::CACHE_KEY_TAG_TREE . $city_code . ($is_mobile ? '_m' : '');
        $result = Yii::app()->cache->get($key);
        if (!empty($result)) {
            return $result;
        }

        $product_ids = HtProduct::model()->getProductIDsOfCity($city_code);
        // filter tags in activity
        $filtered_ids = [];
        foreach ($product_ids as $product_id) {
            if (HtProduct::model()->isProductVisible($product_id)) {
                $filtered_ids[] = $product_id;
            }
        }

        $product_ids = $filtered_ids;

        $product_tags = HtProductTag::getProductTags($product_ids);

        $total = count($product_ids);
        $tags_tree = [];
        if (!empty($product_tags)) {
            $tag_ids = [];
            foreach ($product_tags as $product_tag) {
                $tag = $product_tag['tag'];
                $tag['link_url'] = HtProductTag::getTagLink($city_code, $tag['tag_id'], $is_mobile);
                $tag_id = $tag['tag_id'];
                if (!in_array($tag_id, $tag_ids)) {
                    array_push($tag_ids, $tag['tag_id']);
                    $tag['product_count'] = 0;
                    $tags[$tag_id] = $tag;
                }
                $tags[$tag_id]['product_count'] = $tags[$tag_id]['product_count'] + 1;
            }

            if (!function_exists('sort_tags')) {
                function sort_tags($tag1, $tag2)
                {
                    return $tag1['display_order'] >= $tag2['display_order'];
                }
            }

            //  construct tree
            $tags_tree = [];
            // find out top level tags
            foreach ($tags as $tag_id => $tag) {
                if ($tag['parent_tag_id'] == 0) {
                    $tags_tree[$tag_id] = $tag;
                    $tags_tree[$tag_id]['sub_tags'] = [];
                }
            }

            // attach tags have parent to top level tag
            foreach ($tags as $tag_id => $tag) {
                $parent_tag_id = $tag['parent_tag_id'];
                if ($parent_tag_id > 0) {
                    if (!isset($tags_tree[$parent_tag_id])) {
                        $parent_tag = Converter::convertModelToArray(HtTag::model()->findByPk($parent_tag_id));
                        $parent_tag['product_count'] = 0;
                        $parent_tag['link_url'] = HtProductTag::getTagLink($city_code, $parent_tag_id, $is_mobile);
                        $tags_tree[$parent_tag_id] = $parent_tag;
                    }
                    $tags_tree[$parent_tag_id]['sub_tags'][] = $tag;
                    $tags_tree[$parent_tag_id]['product_count'] += $tag['product_count'];
                }
            }
            foreach ($tags_tree as $tag_id => &$tag) {
                if (!empty($tag['sub_tags'])) {
                    usort($tag['sub_tags'], 'sort_tags');
                }
            }

            usort($tags_tree, 'sort_tags');
        }

        $top_tag = ['tag_id'        => 0, 'parent_tag_id' => 0, 'name' => '全部商品', 'en_name' => 'All', 'display_order' => 1,
                    'product_count' => $total, 'products' => [], 'sub_tags' => [],
                    'link_url'      => HtProductTag::getTagLink($city_code, 0, $is_mobile)];

        Yii::app()->cache->set($key, [$top_tag, $tags_tree], 3 * 60);

        return [$top_tag, $tags_tree];
    }

    public static function getTagLink($city_code, $tag_id, $is_mobile = false)
    {
        if ($is_mobile) {
            return Yii::app()->createUrl('mobile#/city/' . $city_code . '/' . $tag_id);
        } else {
            return Yii::app()->createUrl('city/productsByTag', ['city_code' => $city_code, 'tag_id' => $tag_id]);
        }
    }

    public static function getProductsFromOneTag($city_code, $tag_id)
    {
        $key = HtProductTag::CACHE_KEY_PRODUCTS_OF_ONE_TAG . $city_code . '_' . $tag_id;
        $result = Yii::app()->cache->get($key);
        if (!empty($result)) {
            return $result;
        }

        $result = array(
            'products' => array()
        );

        //获取该城市的所有商品ID
        $product_ids = HtProduct::model()->getProductIDsOfCity($city_code);
        if (empty($product_ids)) {
            Yii::log('参数有误。无法获取城市“' . $city_code . '”的tag_id为“' . $tag_id . '” 的商品。', CLogger::LEVEL_ERROR);

            return $result;
        }

        //收集标签和他的子标签
        $tag = Converter::convertModelToArray(HtTag::model()->findByPk($tag_id));
        if (empty($tag)) {
            Yii::log('参数有误。无法获取标签', CLogger::LEVEL_ERROR);

            return $result;
        }
        $result['name'] = $tag['name'];
        $result['products'] = [];

        $sub_tags = HtTag::model()->findAllByAttributes(['parent_tag_id' => $tag_id]);
        if (!empty($sub_tags)) {
            $tag_ids = ModelHelper::getList($sub_tags, 'tag_id');
        }
        $tag_ids[] = $tag_id;

        $tag_product_ids = HtProductTag::model()->getProductsBelongToTags($tag_ids, $product_ids);

        foreach ($tag_product_ids as &$pid) {
            if (HtProduct::model()->isProductVisible($pid)) {
                $result['products'][] = HtProduct::getProductInfo($pid);
            }
        }

        Yii::app()->cache->set($key, $result, 3 * 60);

        return $result;
    }
}
