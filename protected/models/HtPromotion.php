<?php

/**
 * This is the model class for table "ht_promotion".
 *
 * The followings are the available columns in table 'ht_promotion':
 * @property integer $promotion_id
 * @property string $name
 * @property string $route
 * @property string $title
 * @property string $description
 * @property string $image
 * @property string $mobile_image
 * @property integer $status
 */
class HtPromotion extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_promotion';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, route, title, description, image, mobile_image, status', 'required'),
            array('status', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 50),
            array('route', 'length', 'max' => 50),
            array('title', 'length', 'max' => 100),
            array('image', 'length', 'max' => 500),
            array('mobile_image', 'length', 'max' => 500),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('promotion_id, name, title, description, image, mobile_image, status', 'safe', 'on' => 'search'),
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
            'promotion_rule'  => array(self::HAS_MANY, 'HtPromotionRule', 'promotion_id'),
            'promotion_group' => array(self::HAS_MANY, 'HtPromotionGroup', 'promotion_id'),
            'seo'             => array(self::HAS_ONE, 'HtSeoSetting', '', 'on' => 'promotion.promotion_id = seo.id and seo.type = 6'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'promotion_id' => 'Promotion',
            'name'         => 'Name',
            'route'        => 'Route',
            'title'        => 'Title',
            'description'  => 'Description',
            'image'        => 'Image',
            'mobile_image' => 'Mobile Image',
            'status'       => 'Status',
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

        $criteria->compare('promotion_id', $this->promotion_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('route', $this->route, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('image', $this->image, true);
        $criteria->compare('mobile_image', $this->mobile_image, true);
        $criteria->compare('status', $this->status);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtPromotion the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'promotion',
        );
    }

    protected function beforeSave()
    {
        HtPromotion::clearCache($this->promotion_id);

        return parent::beforeSave();
    }

    protected function beforeDelete()
    {
        HtPromotion::clearCache($this->promotion_id);

        return parent::beforeDelete();
    }

    public static function clearCache($promotion_id)
    {
        $key = 'PromotionController_detail_' . $promotion_id;
        Yii::app()->cache->delete($key);
    }

    public function fetchPromotionDetail($promotion_id)
    {
        $key = 'PromotionController_detail_' . $promotion_id;
        $result = Yii::app()->cache->get($key);
        if (empty($result)) {
            $data = HtPromotion::model()->with('promotion_rule', 'promotion_group.promotion_product',
                                               'seo')->findByPk($promotion_id);
            $result = Converter::convertModelToArray($data);
            $result['is_hotelplus'] = HtCityHotelPlus::model()->isPromotionHotelplus($promotion_id);
            foreach ($result['promotion_group'] as &$promotion_group) {
                foreach ($promotion_group['promotion_product'] as &$product) {
                    $date = $result['promotion_rule'][0]['start_date'];
                    if (empty($date) || $date < date('Y-m-d')) {
                        $date = date('Y-m-d');
                    }
                    $product = Yii::app()->product->getSimplifiedData($product['product_id'], $date);
                }
            }

            Yii::app()->cache->set($key, $result, 60 * 60);
        }

        return $result;
    }

    public function fetchHotelplusDetail($promotion_id)
    {
        $result = Converter::convertModelToArray(HtPromotion::model()->with('promotion_rule',
                                                                            'promotion_group.promotion_product')->findByPk($promotion_id));
        if (empty($result)) {
            return [];
        }

        foreach ($result['promotion_group'] as $key => &$promotion_group) {
            $promotion_product = [];
            foreach ($promotion_group['promotion_product'] as &$product) {
                $date = $result['promotion_rule'][0]['start_date'];
                if (empty($date) || $date < date('Y-m-d')) {
                    $date = date('Y-m-d');
                }
                $product_id = $product['product_id'];
                $product_result = Yii::app()->product->getSimplifiedData($product_id, $date);

                if ($product_result['status'] != 3) // 挂接商品是否要过滤上架？是
                {
                    continue;
                }

                $names = HtProductDescription::model()->getFieldValues($product_result['product_id'], 'name');
                $product = array(
                    'product_id'   => $product_result['product_id'],
                    'link_url'     => $product_result['link_url'],
                    'description'  => array(
                        'name' => $names['cn_name'],
                        'en_name' => $names['en_name'],
                        'benefit' => $product_result['description']['benefit'],
                        'summary' => explode("\n",$product_result['description']['summary'])
                    ),
                    'cover_image'  => array(
                        'image_url' => $product_result['cover_image']['image_url']
                    ),
                    'show_prices'  => $product_result['show_prices'],
                    'comment_stat' => HtProductComment::getStatInfo($product_id)
                );

                $product['hotel'] = array();
                $product['complimentary'] = array();
                $bundles = Converter::convertModelToArray(HtProductBundle::model()->with('items')->findAllByAttributes(['product_id' => $product['product_id']]));
                foreach ($bundles as &$bundle) {
                    foreach ($bundle['items'] as $item) {
//                        $product_brief = Converter::convertModelToArray(HtProduct::model()->findByPk($item['binding_product_id']));
//                        if ($product_brief['status'] != 3) {
//                            continue;
//                        }

                        if ($bundle['group_type'] == HtProductBundle::GT_SELECTION) { //酒店
                            $hotel_info = Yii::app()->product->getHotelInfo($item['binding_product_id'], $product_id);
                            unset($hotel_info['rates']);
                            unset($hotel_info['highlight']);
                            unset($hotel_info['bankcards']);
//                            array_push($product['hotel'], $hotel_info);
                            if(empty($product['hotel'])) {
                                $product['hotel'] = $hotel_info;
                            }
                        } else {
                            if ($bundle['group_type'] == HtProductBundle::GT_REQUIRED) { //附赠
                                $product_info = Converter::convertModelToArray(HtProduct::model()->with('description')->findByPk($item['binding_product_id']));
                                array_push($product['complimentary'], array(
                                    'product_id'   => $product_info['product_id'],
                                    'descriptions' => array(
                                        'name' => $product_info['description']['name']
                                    )
                                ));
                            }
                        }
                    }
                }

                $promotion_product[] = $product;
            }

            if(!empty($promotion_product)) {
                $promotion_group['promotion_product'] = $promotion_product;
            } else {
                unset($result['promotion_group'][$key]);
            }
        }

        return $result;
    }

    public static function getHotelCount($promotion_id)
    {
        $result = Converter::convertModelToArray(HtPromotion::model()->with('promotion_group.promotion_product')->findByPk($promotion_id));
        if(empty($result)) {
            return 0;
        } else {
            $count = 0;
            foreach ($result['promotion_group'] as $promotion_group) {
                $count += count($promotion_group['promotion_product']);
            }
        }

        return $count;
    }
}
