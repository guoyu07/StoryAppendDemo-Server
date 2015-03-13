<?php

/**
 * This is the model class for table "ht_seo_setting".
 *
 * The followings are the available columns in table 'ht_seo_setting':
 * @property integer $type
 * @property string $id
 * @property string $title
 * @property string $description
 * @property string $keywords
 */
class HtSeoSetting extends CActiveRecord
{
    const TYPE_HOME = 1;
    const TYPE_COUNTRY = 2;
    const TYPE_CITY = 3;
    const TYPE_PRODUCT = 4;
    const TYPE_GROUP = 5;
    const TYPE_PROMOTION = 6;
    const TYPE_ARTICLE = 7;

    const CACHE_KEY_PREFIX = 'HtSeoSetting_';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_seo_setting';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('type, id, title, description, keywords', 'required'),
            array('type', 'numerical', 'integerOnly' => true),
            array('id', 'length', 'max' => 8),
            array('title, keywords', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('type, id, title, description, keywords', 'safe', 'on' => 'search'),
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
            'type' => 'Type',
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'keywords' => 'Keywords',
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

        $criteria->compare('type', $this->type);
        $criteria->compare('id', $this->id, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('keywords', $this->keywords, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtSeoSetting the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'seo',
        );

    }

    protected function beforeSave()
    {
        Yii::app()->cache->delete(HtSeoSetting::CACHE_KEY_PREFIX . '_' . $this->type . '_' . $this->id);
        if ($this->type == HtSeoSetting::TYPE_HOME) {
            Yii::app()->cache->delete(HtSeoSetting::CACHE_KEY_PREFIX . '_' . $this->type);
        }

        return parent::beforeSave();
    }

    public function findByPk($pk, $condition = '', $params = array())
    {
        $key = HtSeoSetting::CACHE_KEY_PREFIX . '_' . $pk['type'] . '_' . $pk['id'];
        $result = Yii::app()->cache->get($key);
        if (empty($result)) {
            $result = parent::findByPk($pk, $condition, $params);

            Yii::app()->cache->set($key, $result, 24 * 60 * 60);
        }

        return $result;
    }

    public function findHomeSeoSetting()
    {
        $key = HtSeoSetting::CACHE_KEY_PREFIX . '_' . HtSeoSetting::TYPE_HOME;
        $result = Yii::app()->cache->get($key);
        if (empty($result)) {
            $result = $this->findByAttributes(['type' => self::TYPE_HOME]);

            Yii::app()->cache->set($key, $result, 24 * 60 * 60);
        }

        return $result;
    }

    public function findByProductId($product_id)
    {
        $product_seo = $this->findByPk(['type' => self::TYPE_PRODUCT, 'id' => $product_id]);
        $product_names = HtProductDescription::model()->getFieldValues($product_id, 'name');
        if ($product_seo == null) {
            $product_seo = new HtSeoSetting();
        }
        if (empty($product_seo['title'])) {
            $product_seo['title'] = $product_names['cn_name'] . '_优惠预订_玩途自由行';
        }
        if (empty($product_seo['keywords'])) {
            $product_seo['keywords'] = $product_names['cn_name'];
        }
        if (empty($product_seo['description'])) {
            $product_seo['description'] = '玩途提供' . $product_names['cn_name'] . '的优惠预订';
        }

        return $product_seo;
    }

    public function findByCityCode($city_code)
    {
        return $this->findByPk(['type' => self::TYPE_CITY, 'id' => $city_code]);
    }

    public function findByGroupCode($group_id)
    {
        $group_seo = $this->findByPk(['type' => self::TYPE_GROUP, 'id' => $group_id]);
        $city_info = HtProductGroup::model()->getByPkWithCityCountry($group_id);
        $city_name = $city_info['city']['cn_name'];
        if ($group_seo == null) {
            $group_seo = new HtSeoSetting();
        }
        if (empty($group_seo['title'])) {
            $group_seo['title'] = $city_name . '自由行_特价商品_优惠预订_玩途自由行';
        }
        if (empty($group_seo['keywords'])) {
            $group_seo['keywords'] = $city_name . '自由行，特价旅行，玩途自由行';
        }
        if (empty($group_seo['description'])) {
            $group_seo['description'] = '玩途提供' . $city_name . '自由行特价商品，包括景点门票、当地行程等优惠预订，玩途自由行让你的全球旅行轻松、自在！';
        }

        return $group_seo;
    }

    public function findByCountryCode($country_code)
    {
        return $this->findByPk(['type' => self::TYPE_COUNTRY, 'id' => $country_code]);
    }

    public function findByPromotionId($promotion_id)
    {
        return $this->findByPk(['type' => self::TYPE_PROMOTION, 'id' => $promotion_id]);
    }

    public function findByArticleId($article_id)
    {
        return $this->findByPk(['type' => self::TYPE_ARTICLE, 'id' => $article_id]);
    }

    public static function addOrUpdateProductSeo($product_id, $data)
    {
        $product_seo = HtSeoSetting::model()->findByProductId($product_id);
        if ($product_seo->getIsNewRecord() == true) {
            $product_seo['type'] = HtSeoSetting::TYPE_PRODUCT;
            $product_seo['id'] = $product_id;
            ModelHelper::fillItem($product_seo, $data, array('title', 'description', 'keywords'));
            $result = $product_seo->insert();
        } else {
            $result = ModelHelper::updateItem($product_seo, $data, array('title', 'description', 'keywords'));
        }

        return $result;
    }
}
