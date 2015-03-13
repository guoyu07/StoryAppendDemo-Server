<?php

/**
 * This is the model class for table "ht_product_group_ref".
 *
 * The followings are the available columns in table 'ht_product_group_ref':
 * @property integer $group_id
 * @property integer $product_id
 * @property string $product_image_url
 * @property integer $display_order
 */
class HtProductGroupRef extends CActiveRecord
{
    const CACHE_KEY_PRODUCT_GROUP_REF_ALL_BY_GROUP_ID = 'product_group_ref_all_by_group_id_';

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtProductGroupRef the static model class
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
        return 'ht_product_group_ref';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('group_id, product_id, display_order', 'required'),
            array('group_id, product_id, display_order', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('group_id, product_id, display_order', 'safe', 'on' => 'search'),
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
            'product'             => array(self::HAS_ONE, 'HtProduct', '', 'on' => 'pgr.product_id=p.product_id'),
            'product_description' => array(self::HAS_ONE, 'HtProductDescription', '', 'on' => 'pd.product_id=pgr.product_id', 'select' => 'name'), //, 'condition' => 'language_id=2'
            'app_group'           => array(self::HAS_ONE, 'HtProductGroup', '', 'on' => 'pgr.group_id=pg.group_id and pg.type = 8'),

        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'group_id'          => 'Group',
            'product_id'        => 'Product',
            'product_image_url' => 'Product Image Url',
            'display_order'     => 'Display Order',
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
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('product_image_url', $this->product_image_url);
        $criteria->compare('display_order', $this->display_order);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'pgr',
            'order' => 'pgr.display_order ASC',
        );
    }

    protected function beforeSave()
    {
        HtProductGroupRef::clearCache($this->group_id);
        HtProductGroup::clearCache($this->group_id);

        return parent::beforeSave();
    }

    protected function beforeDelete()
    {
        HtProductGroupRef::clearCache($this->group_id);
        HtProductGroup::clearCache($this->group_id);

        return parent::beforeDelete();
    }

    public static function clearCache($group_id)
    {
        $key = HtProductGroupRef::CACHE_KEY_PRODUCT_GROUP_REF_ALL_BY_GROUP_ID . $group_id;
        Yii::app()->cache->delete($key);

    }

    public function getAllByGroupId($group_id)
    {
        $key = HtProductGroupRef::CACHE_KEY_PRODUCT_GROUP_REF_ALL_BY_GROUP_ID . $group_id;
        $group_ref_all = Yii::app()->cache->get($key);
        if (empty($group_ref_all)) {
            $group_ref_all = Converter::convertModelToArray($this->findAllByAttributes(['group_id' => $group_id]));

            Yii::app()->cache->set($key, $group_ref_all, 60 * 60);
        }

        return $group_ref_all;
    }

    public function getProductsOfGroup($group_id,$type = '')
    {
        $c = new CDbCriteria();
        $c->addCondition('pgr.group_id ="' . $group_id . '"');
        $c->order = 'pgr.display_order ASC';

        $result = $this->with(array('product', 'product_description' => array('condition' => 'language_id=2')))->findAll($c);

        $data = array();
        foreach ($result as $pgr) {
            $cities = array();
            if($type == 7){//线路商品数据
                $highlight = HtTripHighlight::model()->findByAttributes(['product_id' => $pgr['product_id']]);
                $tour_cities = explode(';',$highlight['tour_cities']);
                $cities = array();
                if(is_array($tour_cities)){
                    foreach($tour_cities as $city_code){
                        $city = HtCity::model()->findByPk($city_code);
                        if($city){
                            array_push($cities,array('city_code'=>$city_code,'city_name'=>$city['cn_name']));
                        }
                    }
                }
            }

            $data[] = array(
                'product_id'        => $pgr['product_id'],
                'product_name'      => $pgr['product_name'],
                'product_image_url' => $pgr['product_image_url'],
                'tour_cities'       => $cities,
                'display_order'     => $pgr['display_order'],
                'status'            => $pgr['status'],
                'name'              => $pgr['product_description']['name'],
                'online'            => $pgr['product']['status'] == 3 ? 1 : 0
            );
        }

        return $data;
    }

    public function haveProductsOnline($group_id)
    {
        $c = new CDbCriteria();
        $c->addCondition('group_id ="' . $group_id . '"');
        $c->order = 'display_order ASC';

        $products = $this->with('product', array('select' => 'product_id'))->findAll($c);

        $result = false;
        foreach ($products as $row) {
            if (HtProduct::model()->isProductVisible($row['product']['product_id'])) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    public function productGrouped($product_id)
    {
        $data = $this->find('product_id=' . $product_id);

        return !empty($data);
    }

    public static function updateProductGroupOfType6($product_id)
    {
        $changed = false;
        $price_special = HtProductPricePlanSpecial::model()->getPricePlanSpecial($product_id);

        $product = HtProduct::model()->findByPk($product_id);
        $city_code[] = $product['city_code'];

        $other_cities = HtProductCity::model()->findAllByAttributes(['product_id' => $product_id]);
        foreach ($other_cities as $city) {
            $city_code[] = $city['city_code'];
        }

        $c = new CDbCriteria();
        $c->addCondition('type=6');
        $c->addInCondition('city_code', $city_code);

        $product_groups = HtProductGroup::model()->findAll($c);

        if (!empty($price_special)) {
            foreach ($product_groups as $pg) {
                $item = HtProductGroupRef::model()->findByAttributes(['group_id' => $pg['group_id'], 'product_id' => $product_id]);
                if (empty($item)) {
                    // add an record
                    $item = new HtProductGroupRef();
                    $item['group_id'] = $pg['group_id'];
                    $item['product_id'] = $product_id;
                    $item['product_image_url'] = '';
                    $item['display_order'] = 1;
                    $item->insert();
                    $changed = true;
                }
            }
        } else {
            foreach ($product_groups as $pg) {
                $changed = $changed || HtProductGroupRef::model()->deleteAllByAttributes(['group_id' => $pg['group_id'], 'product_id' => $product_id]) > 0;
            }
        }

        if ($changed) {
            HtProductGroup::clearCacheByProduct($product_id);
        }
    }
}