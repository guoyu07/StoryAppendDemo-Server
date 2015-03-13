<?php

/**
 * This is the model class for table "ht_article".
 *
 * The followings are the available columns in table 'ht_article':
 * @property integer $article_id
 * @property integer $category
 * @property string $city_code
 * @property string $head_image_url
 * @property string $title
 * @property string $brief
 * @property string $link_to
 * @property string $date_added
 * @property string $status
 */
class HtArticle extends CActiveRecord
{
    const CACHE_KEY_CITY_ARTICLES = 'ht_article_city_articles_';
    const CACHE_KEY_CITY_GROUP_ARTICLE = 'ht_article_city_group_article_';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_article';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('category,status', 'numerical', 'integerOnly' => true),
            array('city_code', 'length', 'max' => 4),
            array('head_image_url, link_to', 'length', 'max' => 255),
            array('title', 'length', 'max' => 100),
            array('brief', 'length', 'max' => 2000),
            array('date_added', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('article_id, city_code, category, head_image_url, title, brief, link_to, date_added, status', 'safe', 'on' => 'search'),
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
            'sections' => array(self::HAS_MANY, 'HtArticleSection', '', 'on' => 'as.article_id=a.article_id'),
            'group'    => array(self::HAS_ONE, 'HtProductGroup', '', 'on' => 'pg.group_id = a.link_to and pg.status=2 and pg.city_code = a.city_code'),
            'seo'      => array(self::HAS_ONE, 'HtSeoSetting', '', 'on' => 'seo.id = a.article_id and seo.type = '.HtSeoSetting::TYPE_ARTICLE),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'article_id'     => 'Article',
            'category'       => '1：热卖；2：酒店；3：行程；4：体验',
            'head_image_url' => 'Head Image Url',
            'title'          => 'Title',
            'brief'          => 'Brief',
            'link_to'        => 'Link To',
            'date_added'     => 'Date Added',
            'city_code'      => 'City Code',
            'status'         => 'Status',
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

        $criteria->compare('article_id', $this->article_id);
        $criteria->compare('category', $this->category);
        $criteria->compare('city_code', $this->city_code, true);
        $criteria->compare('head_image_url', $this->head_image_url, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('brief', $this->brief, true);
        $criteria->compare('link_to', $this->link_to, true);
        $criteria->compare('date_added', $this->date_added, true);
        $criteria->compare('status', $this->status);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtArticle the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'a',
        );
    }

    protected function beforeSave()
    {
        HtProductGroup::clearCache($this->city_code, $this->link_to);

        return parent::beforeSave();
    }

    protected function beforeDelete()
    {
        HtProductGroup::clearCache($this->city_code, $this->link_to);

        return parent::beforeDelete();
    }

    public static function clearCache($city_code, $link_to)
    {
        $keys[] = HtArticle::CACHE_KEY_CITY_ARTICLES . $city_code;
        $keys[] = HtArticle::CACHE_KEY_CITY_ARTICLES . $city_code . '_type_1';
        $keys[] = HtArticle::CACHE_KEY_CITY_ARTICLES . $city_code . '_type_2';
        $keys[] = HtArticle::CACHE_KEY_CITY_GROUP_ARTICLE . $city_code . '_' . $link_to;

        CacheUtility::deleteCaches($keys);
    }

    public function getProductCount($article_id)
    {
        $result = $this->with('sections.items')->findByAttributes(['article_id' => $article_id]);
        $count = 0;
        if (!empty($result)) {
            foreach ($result['sections'] as $s) {
                foreach ($s['items'] as $i) {
                    if ($i['type'] == 3 && HtProduct::model()->isProductVisible($i['product_id'])) {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    public function getProductInfo($product_id)
    {
        $result = false;
        if (HtProduct::model()->isProductVisible($product_id)) {
            $product = Yii::app()->product->getSimplifiedData($product_id);
            if (empty($product)) {
                return $product;
            }
            $result = array(
                'type'        => $product['type'],
                'rules'       => $product['rules'],
                'rating'      => HtProductComment::getStatInfo($product_id),
                'product_id'  => $product_id,
                'description' => array(
                    'name'            => $product['description']['name'],
                    'summary'         => $product['description']['summary'],
                    'service_include' => $product['description']['service_include']
                ),
                'cover_image' => array(
                    'image_url' => $product['cover_image']['image_url']
                ),
                'show_prices' => $product['show_prices'],
                'link_url'    => $product['link_url'],
                'link_url_m'  => $product['link_url_m'],
            );
        }

        return $result;
    }

    public function getRawCityArticles($city_code, $type)
    {
        $attributes = array('city_code' => $city_code);

        if ($type) {
            $attributes['type'] = $type;
        }
        $query_result = HtCityColumn::model()->with('columns.article_online.group')->findByAttributes($attributes);

        return Converter::convertModelToArray($query_result);
    }

    public function getCityArticles($city_code, $type = false, $exclude_article_id = false)
    {
        $could_cache = !$exclude_article_id;
        $key = HtArticle::CACHE_KEY_CITY_ARTICLES . $city_code . ($type ? '_type_' . $type : '');
        if ($could_cache) {
            $result = Yii::app()->cache->get($key);
            if (!empty($result)) {
                return $result;
            }
        }

        $result = array();
        $raw_data = $this->getRawCityArticles($city_code, $type);

        if (!empty($raw_data)) {
            $result['name'] = $raw_data['name'];
            foreach ($raw_data['columns'] as $key => &$a) {
                if ($exclude_article_id && $a['article_id'] == $exclude_article_id) {
                } else {
                    if (!empty($a['article_online'])) {
                        $tmp = $a['article_online'];
                        $tmp['group_cover_image'] = $a['article_image_url'];
                        $tmp['product_count'] = $this->getProductCount($a['article_id']);
                        $result['data'][] = $tmp;
                    }
                }
            }
        }

        if ($could_cache) {
            Yii::app()->cache->set($key, $result, 3 * 60);
        }

        return $result;
    }

    public static function getArticleFromOneGroup($city_code, $group_id)
    {
        $key = HtArticle::CACHE_KEY_CITY_GROUP_ARTICLE . $city_code . '_' . $group_id;
        $article = Yii::app()->cache->get($key);
        if(!empty($article)) {
            return $article;
        }

        $article = HtArticle::model()->with('group')->findByAttributes(['status' => 1, 'city_code' => $city_code, 'link_to' => $group_id]);
        if(empty($article)) {
            return '';
        }
        $article = Converter::convertModelToArray($article);

        $article['link_url'] = Yii::app()->createUrl('column/index', ['column_id' => $article['article_id']]);

        Yii::app()->cache->set($key, $article, 5 * 60);

        return $article;
    }

    public function getArticle($article_id)
    {
        $result = $this->with('sections.items')->findByPk($article_id);

        return Converter::convertModelToArray($result);
    }
}
