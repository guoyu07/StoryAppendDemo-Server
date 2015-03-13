<?php

/**
 * This is the model class for table "ht_country_group_ref".
 *
 * The followings are the available columns in table 'ht_country_group_ref':
 * @property integer $group_id
 * @property string $id
 * @property string $name
 * @property string $image_url
 * @property integer $display_order
 * @property integer $status
 */
class HtCountryGroupRef extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_country_group_ref';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('group_id, id, name, link_url, display_order', 'required'),
            array('group_id, display_order, status', 'numerical', 'integerOnly' => true),
            array('id', 'length', 'max' => 10),
            array('name, image_url, link_url', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('group_id, id, name, image_url, link_url, display_order, status', 'safe', 'on' => 'search'),
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
            'product'             => array(self::HAS_ONE, 'HtProduct', '', 'on' => 'cgr.id = p.product_id'),
            'product_description' => array(self::HAS_ONE, 'HtProductDescription', '', 'on' => 'pd.product_id = cgr.id', 'select' => 'name'), //, 'condition' => 'language_id=2'
            'article'             => array(self::HAS_ONE, 'HtArticle', '', 'on' => 'cgr.id = a.article_id', 'select' => 'title'),
            'city'                => array(self::HAS_ONE, 'HtCity', '', 'on' => 'cgr.id = city.city_code', 'select' => 'cn_name'),
            'group'               => array(self::HAS_ONE, 'HtCountryGroup', '', 'on' => 'cgr.id = cg.group_id', 'select' => 'name'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'group_id'      => 'Group',
            'id'            => 'ID',
            'name'          => 'Name',
            'image_url'     => 'Image Url',
            'link_url'      => 'Link Url',
            'display_order' => 'Display Order',
            'status'        => 'Status',
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

        $criteria->compare('group_id', $this->group_id);
        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('image_url', $this->image_url, true);
        $criteria->compare('link_url', $this->link_url, true);
        $criteria->compare('display_order', $this->display_order);
        $criteria->compare('status', $this->status);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtCountryGroupRef the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'cgr',
            'order' => 'cgr.display_order ASC',
        );
    }

    protected function beforeSave()
    {
        $this->clearCache($this->group_id);

        return parent::beforeSave();
    }

    public static function  clearCache($group_id)
    {
        $group = HtCountryGroup::model()->findByPk($group_id);
        if ($group) {
            HtCountryTab::clearCache($group['country_code']);
        }
    }

    public function getRefOfGroup($group_id, $type)
    {
        $c = new CDbCriteria();
        $c->addCondition('cgr.group_id ="' . $group_id . '"');

        if ($type == HtCountryGroup::TYPE_PRODUCT || $type == HtCountryGroup::TYPE_LINE) {
            $result = $this->with(array('product', 'product_description' => array('condition' => 'language_id=2')))->findAll($c);
            $result = Converter::convertModelToArray($result);
            foreach ($result as &$cgr) {
                $cgr['product_name'] = $cgr['product_description']['name'];
                $cgr['online'] = $cgr['product']['status'] == 3 ? 1 : 0;
            }
        } else {
            if ($type == HtCountryGroup::TYPE_ARTICLE) {
                $result = $this->with('article')->findAll($c);
                $result = Converter::convertModelToArray($result);
                foreach ($result as &$cgr) {
                    $cgr['article_name'] = $cgr['article']['title'];
                }

            } else {
                if ($type == HtCountryGroup::TYPE_CITY) {
                    $result = $this->with('city')->findAll($c);
                    $result = Converter::convertModelToArray($result);
                    foreach ($result as &$cgr) {
                        $cgr['city_name'] = $cgr['city']['cn_name'];
                    }

                } else {
                    if ($type == HtCountryGroup::TYPE_GROUP) {
                        $result = $this->with('group')->findAll($c);
                        $result = Converter::convertModelToArray($result);
                        foreach ($result as &$cgr) {
                            $cgr['group_name'] = $cgr['group']['name'];
                        }
                    } else {
                        $result = $this->findAll($c);
                    }
                }
            }
        }

        return $result;
    }

    // used by front end
    public static function getRefDetail($group_type, $ref_id)
    {
        $result = [];
        switch ($group_type) {
            case HtCountryGroup::TYPE_PRODUCT: {
                $product_id = (int)$ref_id;
                if (HtProduct::model()->isProductVisible($product_id)) {
                    $product = Converter::convertModelToArray(HtProduct::model()->with('cover_image',
                                                                                       'description')->findByPk($product_id));
                    if (!empty($product)) {
                        $product['activity_info'] = Yii::app()->activity->getActivityInfo($product_id);
                        $product['cover_image'] = $product['cover_image']['image_url'];
                        $price = HtProductPricePlan::model()->getShowPrices($product_id);

                        $product['price'] = $price;

                        $result = $product;
                    }
                }
            }
                break;
            case HtCountryGroup::TYPE_LINE: {
                $product_id = (int)$ref_id;
                if (HtProduct::model()->isProductVisible($product_id)) {
                    $product['activity_info'] = Yii::app()->activity->getActivityInfo($product_id);
                    $product = HtProduct::model()->with('cover_image', 'description')->findByPk($product_id);
                    $result['product_name'] = $product['description']['name'];
                    $result['cover_image'] = $product['cover_image']['image_url'];

                    $line_product_info = HtProductGroup::getLineProductInfo($product_id);
                    $result = array_merge($result, $line_product_info);
                    $result['link_url'] = $product['link_url'];
                    $result['link_url_m'] = $product['link_url_m'];

                    $result['price'] = HtProductPricePlan::model()->getShowPrices($product_id);
                }
            }
                break;
            case HtCountryGroup::TYPE_ARTICLE: {
                $article = HtArticle::model()->findByPk((int)$ref_id);
                if (!empty($article)) {
                    $article = Converter::convertModelToArray($article);
                    $article['product_count'] = HtArticle::model()->getProductCount($article['article_id']);
                    $article['link_url'] = Yii::app()->createUrl('column/index',
                                                                 ['column_id' => $article['article_id']]);
                    $result = $article;
                }
            }
                break;
            case HtCountryGroup::TYPE_CITY: {
                $city = HtCity::model()->getCityWithCityImage($ref_id);

                return $city;
            }
                break;
            case HtCountryGroup::TYPE_GROUP: {
                return HtCountryGroup::model()->getGroupDetail($ref_id);
            }
                break;
            case HtCountryGroup::TYPE_AD: {
                // do nothing
            }
                break;
        }

        return $result;
    }

}
