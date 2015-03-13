<?php

/**
 * This is the model class for table "ht_product".
 *
 * The followings are the available columns in table 'ht_product':
 * @property integer $product_id
 * @property string $city_code
 * @property integer $supplier_id
 * @property string $supplier_product_id
 * @property string $model
 * @property integer $type
 * @property integer $is_combo
 * @property integer $status
 * @property string  $source_url
 * @property string $date_added
 * @property string $date_modified
 */
class HtProduct extends HActiveRecord
{
    const IN_EDITING = 1;
    const IN_REVIEW = 2;
    const IN_SALE = 3;
    const SOLD_OUT = 4;

    //0：未分类；1：单票；2：组合票；3：通票；4：Hop On Hop Off；5：Tour; 6: coupon; 7: 酒店；8: 酒店套餐; 9:多日行程; 10:包车
    const T_UNDEFINED = 0;
    const T_TICKET = 1;
    const T_COMBO = 2;
    const T_PASS = 3;
    const T_HOPON_HOPOFF = 4;
    const T_TOUR = 5;
    const T_COUPON = 6;
    const T_HOTEL = 7;
    const T_HOTEL_BUNDLE = 8;
    const T_MULTI_DAY = 9;
    const T_CHARTER_BUS = 10;

    public $link_url;
    public $link_url_m;
    public $available;
    public $buy_label;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtProduct the static model class
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
        return 'ht_product';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_code, supplier_id, supplier_product_id, model, type', 'required'),
            array('supplier_id, type, is_combo, status', 'numerical', 'integerOnly' => true),
            array('city_code', 'length', 'max' => 4),
            array('supplier_product_id', 'length', 'max' => 255),
            array('model', 'length', 'max' => 64),
            array('source_url', 'length', 'max' => 255),
            array('date_added, date_modified', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('product_id, city_code, supplier_id, supplier_product_id, model, type, is_combo, status, source_url, date_added, date_modified', 'safe', 'on' => 'search'),
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
            'description'                => array(self::HAS_ONE, 'HtProductDescription', '', 'on' => 'p.product_id=pd.product_id AND pd.language_id=2'),
            'introduction'                => array(self::HAS_ONE, 'HtProductIntroduction', 'product_id'),
            'descriptions'               => array(self::HAS_MANY, 'HtProductDescription', 'product_id'),
            'cover_image'                => array(self::HAS_ONE, 'HtProductImage', '', 'on' => 'p.product_id=pi.product_id AND pi.as_cover=1'),
            'images'                     => array(self::HAS_MANY, 'HtProductImage', '', 'on' => 'p.product_id=pi.product_id'),
            'album_info'                 => array(self::HAS_ONE, 'HtProductAlbum', 'product_id'),
            'city'                       => array(self::BELONGS_TO, 'HtCity', 'city_code'),
            'qa'                         => array(self::HAS_ONE, 'HtProductQa', 'product_id'),
            'subs'                       => array(self::HAS_MANY, 'HtProductCombo', 'product_id'),
            'supplier'                   => array(self::HAS_ONE, 'HtSupplier', '', 'on' => 'p.supplier_id = s.supplier_id'),
            'tags'                       => array(self::HAS_MANY, 'HtProductTag', 'product_id'),
            'special_codes'              => array(self::HAS_MANY, 'HtProductSpecialCode', 'product_id'),

            //rules
            'date_rule'                  => array(self::HAS_ONE, 'HtProductDateRule', 'product_id'),
            'redeem_rule'                => array(self::HAS_ONE, 'HtProductRedeemRule', 'product_id'),
            'return_rule'                => array(self::HAS_ONE, 'HtProductReturnRule', 'product_id'),
            'ticket_rule'                => array(self::HAS_MANY, 'HtProductTicketRule', 'product_id'),
            'sale_rule'                  => array(self::HAS_ONE, 'HtProductSaleRule', 'product_id'),
            'package_rule'               => array(self::HAS_ONE, 'HtProductPackageRule', 'product_id'),
            'shipping_rule'              => array(self::HAS_ONE, 'HtProductShippingRule', 'product_id'),
            'local_support'              => array(self::HAS_MANY, 'HtSupplierLocalSupport', '', 'on' => 'p.supplier_id=sls.supplier_id AND sls.destination = cnt.country_code AND sls.language_id = 2', 'through' => 'city.country'),
            'count_product_comment'      => array(self::STAT, 'HtProductComment', 'product_id', 'condition' => 'pc.approved = 1'),
            'avg_hitour_service_level'   => array(self::STAT, 'HtProductComment', 'product_id', 'select' => 'avg(hitour_service_level)', 'condition' => 'pc.approved = 1'),
            'avg_supplier_service_level' => array(self::STAT, 'HtProductComment', 'product_id', 'select' => 'avg(supplier_service_level)', 'condition' => 'pc.approved = 1'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'product_id'          => 'Product',
            'city_code'           => 'City Code',
            'supplier_id'         => 'Supplier',
            'supplier_product_id' => 'Supplier Product',
            'model'               => 'Model',
            'type'                => 'Type',
            'is_combo'            => 'Is Combo',
            'status'              => 'Status',
            'source_url'          => 'Source Url',
            'date_added'          => 'Date Added',
            'date_modified'       => 'Date Modified',
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

        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('city_code', $this->city_code, true);
        $criteria->compare('supplier_id', $this->supplier_id);
        $criteria->compare('supplier_product_id', $this->supplier_product_id, true);
        $criteria->compare('model', $this->model, true);
        $criteria->compare('type', $this->type);
        $criteria->compare('is_combo', $this->is_combo);
        $criteria->compare('status', $this->status);
        $criteria->compare('source_url', $this->source_url, true);
        $criteria->compare('date_added', $this->date_added, true);
        $criteria->compare('date_modified', $this->date_modified, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'p',
        );
    }

    public function scopes()
    {
        return array(
            'published' => ['condition' => 'p.status IN(3,5) '],
        );
    }

    protected function beforeSave()
    {
        HtProduct::clearCache($this->product_id);

        return parent::beforeSave();
    }

    protected function afterFind()
    {
        $this->link_url = Yii::app()->urlManager->createUrl('product/index', array('product_id' => $this->product_id));
        $this->link_url_m = Yii::app()->urlManager->createUrl('mobile#/product/' . $this->product_id);
        $this->available = $this->status == self::IN_SALE ? 1 : 0;
        switch ($this->status) {
            case self::IN_SALE:
                $this->buy_label = '购买';
                break;
            case self::IN_EDITING:
                $this->buy_label = '敬请期待';
                break;
            case self::IN_REVIEW:
                $this->buy_label = '敬请期待';
                break;
            case self::SOLD_OUT:
                $this->buy_label = '已下架';
                break;
            default:
                $this->buy_label = '敬请期待';
                break;
        }
    }

    public function isProductVisible($product_id)
    {
        $p = Converter::convertModelToArray(HtProduct::model()->findByPk($product_id));
        $aty = Yii::app()->activity->getActivityInfo($product_id);
        //商品状态不可售卖而且该商品参加活动并且不在城市页显示
        if (!empty($p) && $p['status'] != HtProduct::IN_SALE || (!empty($aty) && ($aty['display_in_city'] == 0))) {
            return false;
        }

        return true;
    }

    public static function clearCache($product_id)
    {

    }

    public function copyProduct($product_id)
    {
        $product = HtProduct::model()->findByPk($product_id);
        if (empty($product)) {
            return [false, '要复制的商品不存在。', 0];
        }

        // Copy product
        $new_product = new HtProduct();
        $new_product->attributes = $product->attributes;
        unset($new_product['product_id']);
        $new_product['status'] = 1;
        $date_added = date('Y-m-d H:i:s');
        $new_product['date_added'] = $date_added;
        $new_product['date_modified'] = $date_added;

        if (!$product['supplier_id'] == 11) {
            // for GTA products, do not copy it
            // TODO replace magic number 11 with some method?
            $new_product['supplier_product_id'] = substr(md5(date("Y-m-d H:i:s") . microtime(true)), 0, 16);
        }

        $result = $new_product->insert();
        if (!$result) {
            return [false, '复制失败。', 0];
        }

        $new_product_id = $new_product['product_id'];

        $this->recordCopy(HtProductDescription::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductQa::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductTourOperation::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductPassengerRule::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductPassengerRuleItem::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductRedeemRule::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductReturnRule::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductDateRule::model(), $product_id, $new_product_id);
//        $this->recordCopy(HtProductSpecialCode::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductShippingRule::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductIntroduction::model(), $product_id, $new_product_id);

        $this->recordCopy(HtProductAlbum::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductImage::model(), $product_id, $new_product_id);

        $this->recordCopy(HtProductVoucherRule::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductVoucherRuleItem::model(), $product_id, $new_product_id);

        $this->recordCopy(HtProductDeparture::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductDeparturePlan::model(), $product_id, $new_product_id);

        $this->recordCopy(HtProductPackageRule::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductSaleRule::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductTicketRule::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductRelated::model(), $product_id, $new_product_id);

        $this->recordCopy(HtProductManager::model(), $product_id, $new_product_id);

        HtProductSpecialCombo::copySpecialInfo($product_id, $new_product_id);

        HtProductPricePlan::copyPricePlan($product_id, $new_product_id);

        HtProductTourPlan::copyTourPlan($product_id, $new_product_id);

        // copy hotel
        $this->recordCopy(HtProductHotel::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductHotelRate::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductHotelService::model(), $product_id, $new_product_id);
        $this->recordCopy(HtProductHotelBankcard::model(), $product_id, $new_product_id);
        $this->recordCopy(HtHotelRoomType::model(), $product_id, $new_product_id);

        return [true, "复制成功！\n新商品ID：" . $new_product['product_id'], $new_product_id];
    }

    private function recordCopy($model, $product_id, $new_product_id)
    {
        $auto_increment_field = '';
        $table = $model->getMetaData()->tableSchema;
        foreach ($table->columns as $key => $column) {
            if ($column->autoIncrement == true) {
                $auto_increment_field = $key;
                break;
            }
        }

        $old = $model->findAll('product_id=' . $product_id);
        foreach ($old as $o) {
            $new = new $model;

            foreach ($o->attributes as $key => $value) {
                if ($auto_increment_field == $key) {
                    continue;
                }
                $new[$key] = $value;
            }

            $new['product_id'] = $new_product_id;
            $new->insert();
        }
    }

    public static function changeStatus($product_id, $new_status)
    {
        $product = HtProduct::model()->findByPk($product_id);
        if (empty($product)) {
            return [false, '商品ID为"' . $product_id . '"的商品不存在。'];
        } else {
            if ($new_status < 1 || $new_status > 4) {
                return [false, '状态参数非法。'];
            } else {
                if (3 == $product['status'] && 3 != $new_status) { // 由上架改为其它状态
                    // 更改上架商品的状态，需要处理首页推荐
                    // get affected home groups
                    $items = HtHomeRecommendItem::model()->findAll('product_id=' . $product_id);
                    if (count($items) > 0) {
                        $group_ids = array();
                        foreach ($items as $item) {
                            array_push($group_ids, $item['group_id']);
                        }
                        // HtHomeRecommendItem::model()->deleteAll('product_id=' . $product_id);
                        // count online products only
                        foreach ($group_ids as $group_id) {
                            $count = HtHomeRecommendItem::model()->count('group_id=' . $group_id) - 1;
                            $hr_items = HtHomeRecommendItem::model()->with('product')->findAll('group_id=' . $group_id);
                            foreach ($hr_items as $hri) {
                                if ($hri['product']['status'] != 3) {
                                    $count--;
                                }
                            }

                            if ($count < 6) {
                                $group = HtHomeRecommend::model()->findByPk($group_id);
                                $group['status'] = 1;
                                $group->update();
                            }
                        }
                    }

                    HtProductGroup::clearCacheByProduct($product_id);
                    HtProductTag::clearCacheByProduct($product_id);

                    //                HtProductRelated::model()->deleteAllByAttributes(['related_id' => $product_id]);
                    //
                    //                $this->updateProductGroupRef($product_id, false);
                } else {
                    if (3 == $new_status && 3 != $product['status']) {
                        // DO NOTHING! Since we do not use product group of type 1
                        HtProductGroupRef::updateProductGroupOfType6($product_id);
                    }
                }

                $product['status'] = $new_status;
                $result = $product->update();
                if ($result) {
                    HtCity::updateCityHasOnlineProduct($product['city_code']);
                }

                return [$result, $result ? '状态更新成功！' : '状态更新失败！'];
            }
        }
    }

    public function getProductBasic($product_id)
    {
        $sql = "SELECT p.product_id, pd.name" .
            " FROM ht_product p LEFT JOIN ht_product_description pd ON pd.product_id=p.product_id and pd.language_id=2 ";
        if (is_array($product_id)) {
            $sql .= " WHERE p.product_id in (" . implode(",", $product_id) . ")";
        } else {
            $sql .= " WHERE p.product_id=" . $product_id;
        }

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);

        if (is_array($product_id)) {
            $result = $command->queryAll();
        } else {
            $result = $command->queryRow();
        }

        return $result;
    }

    public function getProductDetail($product_id)
    {
        $sql = "SELECT p.*, pd.*, " .
            " (select image_url FROM `ht_product_image` pi where pi.product_id=p.product_id and pi.as_cover = 1 limit 1) as pi_image_url, " .
            " (select name from ht_supplier m where m.supplier_id=p.supplier_id) as m_name, " .
            " (select cn_name from ht_city c where c.city_code = p.city_code ) as city_cn_name " .
            " FROM ht_product p LEFT JOIN ht_product_description pd ON pd.product_id=p.product_id and pd.language_id=2 ";

        if (is_array($product_id)) {
            $sql .= " WHERE p.product_id in (" . implode(",", $product_id) . ")";
        } else {
            $sql .= " WHERE p.product_id=" . $product_id;
        }

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);

        if (is_array($product_id)) {
            $result = $command->queryAll();
        } else {
            $result = $command->queryRow();
        }

        return $result;
    }

    public function getProductDetailAll($condition = '', $sortDir = 'ASC', $sortedBy = 'product_id', $pageNumber = 1)
    {
        $page_count = 20;
        $sql = "SELECT p.*, pd.name, " .
            " (select image_url FROM `ht_product_image` pi where pi.product_id=p.product_id order by pi.sort_order limit 1) as pi_image_url, " .
            " (select name from ht_supplier m where m.supplier_id=p.supplier_id) as m_name, " .
            " (select cn_name from ht_city c where c.city_code = p.city_code ) as city_cn_name " .
            " FROM ht_product p LEFT JOIN ht_product_description pd ON pd.product_id=p.product_id and pd.language_id=2 " .
            " WHERE 1=1 " . $condition;

        if (!empty($sortedBy) && 'price' != $sortedBy) {
            $sql .= ' ORDER BY ' . $sortedBy . ' ' . $sortDir;
        }

        $sql .= ' LIMIT ' . $page_count * ($pageNumber - 1) . ", " . $page_count;

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);

        return $command->queryAll();
    }

    public function getProductTotal($condition = '')
    {
        $sql = "SELECT count(*) as total " .
            " FROM ht_product p LEFT JOIN ht_product_description pd ON pd.product_id=p.product_id and pd.language_id=2 " .
            " WHERE 1=1 " . $condition;

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);

        $result = $command->queryRow();

        return $result['total'];
    }

    public function getProducts($reqData)
    {
        $sortDir = $this->getParam($reqData, 'sortDir', 'ASC');
        $sortedBy = $this->getParam($reqData, 'sortedBy', 'product_id');
        $pageNumber = $this->getParam($reqData, 'pageNumber', 1);

        $product_id = $this->getParam($reqData, 'product_id', 0);
        $city_code = $this->getParam($reqData['city'], 'city_code');
        $supplier_id = $this->getParam($reqData['supplier'], 'supplier_id', 0);
        $product_name = $this->getParam($reqData, 'product_name', '');
        $status = $this->getParam($reqData, 'status', -1);
        $product = $this->getParam($reqData, 'product');
        $type = $this->getParam($reqData['type'], 'value', -1);

        $condition = $this->getQueryCondition($product_id, $city_code, $supplier_id, $product_name, $status, $product, $type);
        $total = $this->getProductTotal($condition);
        $result = $this->getProductDetailAll($condition, $sortDir, $sortedBy, $pageNumber);

        $data = array();
        foreach ($result as $row) {
            $showPrices = HtProductPricePlan::model()->getShowPrices($row['product_id']);
            $data[] = array(
                'product_id'   => $row['product_id'],
                'name'         => $row['name'],
                'image_url'    => $row['pi_image_url'],
                'supplier_id'  => $row['supplier_id'],
                'm_name'       => $row['m_name'],
                'city_cn_name' => $row['city_cn_name'],
                'city_code'    => $row['city_code'],
                'status'       => $row['status'],
                'price'        => $showPrices['price'],
                'orig_price'   => $showPrices['orig_price'],
            );
        }

        return array('data' => $data, 'total' => $total);
    }

    private function getQueryCondition($product_id = 0, $city_code = 0, $supplier_id = 0, $product_name = '', $status = -1, $product = '', $type = -1)
    {
        $sql = '';
        if ($product_id > 0) {
            $sql .= " AND p.product_id = " . (int)$product_id;
        } else {
            if (!empty($product) && is_numeric($product)) {
                $sql .= ' AND p.product_id = ' . (int)$product;
            } else {
                if (!empty($city_code)) {
                    $sql .= ' AND (p.city_code = "' . $city_code .
                        '" OR p.product_id IN (SELECT product_id FROM ht_product_city WHERE ht_product_city.city_code = "' . $city_code . '")) ';
                }
                if ($supplier_id > 0) {
                    $sql .= ' AND p.supplier_id = ' . $supplier_id;
                }

                if (!empty($product_name)) {
                    $sql .= ' AND pd.name like "%' . $product_name . '%"';
                }

                if ($status > 0) {
                    $sql .= ' AND p.status = ' . (int)$status;
                } else {
                    $sql .= ' AND p.status in (1, 2, 3)';
                }

                if (!empty($product)) {
                    $sql .= ' AND pd.name like "%' . $product . '%"';
                }

                if ($type > 0) {
                    $sql .= ' AND p.type = ' . $type;
                }
            }
        }

        return $sql;
    }

    public function getProductIDs($city_code, $supplier_id, $search_str, $page_num = 1, $page_count = 20, $sortDir = 'ASC', $sortedBy = 'product_id')
    {
        $sql = "SELECT p.product_id FROM ht_product p " .
            "LEFT JOIN ht_product_description pd ON pd.product_id=p.product_id and pd.language_id=2 WHERE 1=1 ";
        if ($city_code != 0) {
            $sql .= ' AND p.city_code = "' . $city_code . '"';
        }
        if ($supplier_id > 0) {
            $sql .= ' AND p.supplier_id = ' . $supplier_id;
        }

        if (!empty($search_str) && str_len($search_str) > 0) {
            //			$c->addCondition('')
            $sql .= ' AND pd.name like "%' . $search_str . '%"';
            if (is_numeric($search_str)) {
                $sql .= ' OR p.product_id = ' . (int)$search_str;
            }
        }
        if (!empty($sortedBy)) {
            $sql .= ' ORDER BY ' . $sortedBy . ' ' . $sortDir;
        }

        $sql .= ' LIMIT ' . $page_count * $page_num . ", " . $page_count;

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);

        $rows = $command->queryAll();

        $data = array();
        foreach ($rows as $row) {
            array_push($data, $row['product_id']);
        }

        return $data;
    }

    private function getParam($reqData, $key, $default = null)
    {
        if (isset($reqData[$key])) {
            return $reqData[$key];
        }

        return $default;
    }

    public function getRuleDesc($product_id)
    {
        $key = 'product_rule_desc_' . $product_id;
        $rules = Yii::app()->cache->get($key);
        if (empty($rules)) {
            $product = HtProduct::model()->with(['redeem_rule', 'return_rule', 'date_rule'])->findByPk($product_id);

            //rule desc
            $rules = array();
            $rules['redeem_desc'] = $product['redeem_rule'] ? $product['redeem_rule']->getRuleDesc() : '';
            $rules['return_desc'] = $product['return_rule'] ? $product['return_rule']->getRuleDesc() : '';
            $rules['sale_desc'] = $product['date_rule'] ? $product['date_rule']->getBuyDesc() : '';
            $rules['shipping_desc'] = $product['date_rule'] ? $product['date_rule']->getShippingDesc() : '';
            Yii::app()->cache->set($key, $rules, 2 * 60 * 60);
        }

        return $rules;
    }

    public static function clearCachedRuleDesc($product_id)
    {
        $key = 'product_rule_desc_' . $product_id;
        Yii::app()->cache->delete($key);
    }

    public function getProductIDsOfCity($city_code)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('city_code="' . $city_code . '"');
        $criteria->addCondition('status=3');
        $criteria->select = 'product_id';
        $products = HtProduct::model()->findAll($criteria);

        $result = ModelHelper::getList($products, 'product_id');

        $additional_products = HtProductCity::model()->findAllByAttributes(['city_code' => $city_code]);
        $additional_product_ids = ModelHelper::getList($additional_products, 'product_id');

        $result = array_merge($result, $additional_product_ids);

        return $result;
    }

    // used by HtProductGroup::getProductsFromOneGroup and
    public static function getProductInfo($product_id)
    {
        $p = Converter::convertModelToArray(HtProduct::model()->with(['description' => ['select' => 'name,summary,benefit,service_include'], 'cover_image'])->published()->findByPk($product_id));
        if ($p['status'] != HtProduct::IN_SALE) {
            return [];
        }

        $aty = Yii::app()->activity->getActivityInfo($p['product_id']);
        if (!empty($aty)) {
            if ($aty['display_in_city'] == 0) { //该商品参加活动并且不在城市页显示
                return [];
            } else {
                $p['activity_info'] = $aty;
            }
        }

        $p['description']['service_include'] = Yii::app()->product->refineServiceInclude($p['description']['service_include']);
        $p['show_prices'] = HtProductPricePlan::model()->getShowPrices($p['product_id']);
        $p['is_favorite'] = HtCustomerFavoriteProduct::model()->isFavorite($p['product_id']);
        $p['rules'] = Yii::app()->product->getRuleDesc($p['product_id']);
        $p['comments_stat'] = HtProductComment::getStatInfo($product_id);
        // get sale data of product
        $p['sales_volume'] = HtOrder::getSalesVolume($product_id);

        return $p;
    }

    public static function getProductCity($product_id)
    {
        $record = HtProduct::model()->findByPk($product_id);
        if ($record) {
            return Converter::convertModelToArray(HtCity::model()->getByCode($record->city_code));
        }
    }

    public static function validateProduct($product_id)
    {
        // TODO valid product to be online.
        $error = '';

        $product = HtProduct::model()->findByPk($product_id);
//
//        if ($product['supplier_id'] == 0) {
//            $error .= "请为商品选择供应商。\n";
//        }
//        if ($product['city_code'] == '') {
//            $error .= "请为商品选择所属城市。\n";
//        }

        // 商品描述
        $product_description = HtProductDescription::model()->getFieldValues($product_id, 'name');
        if ($product_description['cn_name'] == '' || $product_description['en_name'] == '') {
            $error .= "请填写商品名称。\n";
        }

        $product_description = HtProductDescription::model()->findByAttributes(array('product_id' => $product_id, 'language_id' => 2));
//        if ($product_description['summary'] == '') {
//            $error .= "请填写商品简要描述。\n";
//        }
        if ($product_description['description'] == '') {
            $error .= "请填写商品详细介绍/推荐理由。\n";
        }
        if ($product_description['service_include'] == '') {
            $error .= "请填写商品包含服务。\n";
        }
        if ($product_description['how_it_works'] == '') {
            $error .= "请填写商品兑换及使用。\n";
        }

        $date_rule = HtProductDateRule::model()->findByPk($product_id);
        if ($date_rule['sale_range_type'] == HtProductDateRule::TYPE_RANGE && substr($date_rule['sale_range'], 0,
                                                                                     1) == '0'
        ) {
            $error .= "销售时间范围至少为1个月。\n";
        }

        // 商品图片
        $product_images = HtProductImage::model()->findAllByAttributes(array('product_id' => $product_id, 'as_cover' => 1));
        if (empty($product_images)) {
            $error .= "请编辑商品图片，指定封面图。\n";
        }

        // 关联专辑
        $product_album = HtProductAlbum::model()->findByPk($product_id);
        if (!empty($product_album)) {
            if ($product_album['need_album'] == 1 && $product_album['album_id'] == 0) {
                $error .= "请填写关联景点专辑ID。\n";
            }
            if ($product_album['need_pick_ticket_album'] == 1) {
                if ($product_album['pick_ticket_album_id'] == 0) {
                    $error .= "请填写接送点专辑ID。\n";
                }
                if (strlen($product_album['pt_group_info']) < 10) {
                    $error .= "请填写接送点专辑分组信息。\n";
                }
            }
        }

        $date_rule = HtProductDateRule::model()->findByPk($product_id);
        // 价格计划
        $price_plan = HtProductPricePlan::model()->findByAttributes(array('product_id' => $product_id));
        if ($price_plan['valid_region'] == 1 && $product['supplier_id'] != 11) {
            $min_max_date = HtProductPricePlan::model()->getPricePlanFromTo($product_id);

            if ($min_max_date['min_date'] != $date_rule->from_date) {
                $error .= "价格计划开始时间不等于售卖开始时间。\n";
            }
            if ($min_max_date['max_date'] != $date_rule->to_date) {
                $error .= "价格计划结束时间不等于售卖截止时间。\n";
            }
        }

        // departure point
        $departure_plan = HtProductDeparturePlan::model()->findByAttributes(array('product_id' => $product_id));
        if ($departure_plan['valid_region'] == 1 && $product['supplier_id'] != 11) {
            $min_max_date = HtProductDeparturePlan::model()->getFromTo($product_id);
            if ($min_max_date['min_date'] != $date_rule->from_date) {
                $error .= "Departure Point 开始时间不等于售卖开始时间。\n";
            }
            if ($min_max_date['max_date'] != $date_rule->to_date) {
                $error .= "Departure Point 结束时间不等于售卖截止时间。\n";
            }
        }

        // voucher rule
        $shipping_rule = HtProductShippingRule::model()->findByPk($product_id);
        if ($shipping_rule->need_hitour_voucher == 1 && $product['type'] != HtProduct::T_HOTEL_BUNDLE) {
            $passenger_rule = HtProductPassengerRule::model()->with('items')->findByPk($product_id);
            $voucher_rule = HtProductVoucherRule::model()->with('items')->findByPk($product_id);

            if ($passenger_rule['need_lead'] == 1) {
                if (empty($voucher_rule['lead_fields'])) {
                    $error .= "Voucher 需要出现的出行人信息中的领队信息不完整。\n";
                }
            }

            if ($passenger_rule['need_passenger_num'] == 0) {
                $items_count = count($passenger_rule['items']);
                $items = $voucher_rule['items'];
                if (count($items) < $items_count) {
                    $error .= "Voucher 需要出现的出行人信息不完整。\n";
                } else {
                    foreach ($items as $item) {
                        if (empty($item['fields'])) {
                            $error .= "Voucher 需要出现的出行人信息不完整。\n";
                            break;
                        }
                    }
                }
            }
        }

        $tag = HtProductTag::model()->findAll('product_id = ' . $product_id);
        if (!$tag) {
            $error .= "商品tag信息/商品标签未添加。\n";
        }

        return $error;
    }

    public static function validateAll()
    {
        $c = new CDbCriteria();
        $c->addCondition('status=3');
        $c->order = 'product_id ASC';
        $products = HtProduct::model()->findAll($c);
        $data = [];
        foreach ($products as $product) {
            $product_id = $product['product_id'];
            $error_msg = HtProduct::validateProduct($product_id);
            if (!empty($error_msg)) {
                $data[] = ['product_id' => $product_id, 'error_msg' => $error_msg];
            }
        }

        return $data;
    }
}
