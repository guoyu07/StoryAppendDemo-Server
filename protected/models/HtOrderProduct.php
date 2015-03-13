<?php

/**
 * This is the model class for table "ht_order_product".
 *
 * The followings are the available columns in table 'ht_order_product':
 * @property integer $order_product_id
 * @property integer $order_id
 * @property integer $product_id
 * @property integer $bundle_product_id
 * @property integer $total
 * @property integer $cost_total
 * @property string $supplier_order_id
 * @property string $name
 * @property string $special_code
 * @property string $departure_code
 * @property string $language
 * @property string $language_list_code
 * @property string $departure_time
 * @property string $tour_date
 * @property integer $pax_num
 * @property string $redeem_expire_date
 * @property string $return_expire_date
 * @property integer $stock_limited
 * @property string $date_added
 * @property string $date_modified
 */
class HtOrderProduct extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_order_product';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_id, product_id, total, cost_total, supplier_order_id, name, special_code, departure_code, language, language_list_code, departure_time, tour_date, redeem_expire_date, return_expire_date, stock_limited, date_added, date_modified', 'required'),
            array('order_id, product_id, bundle_product_id, total, cost_total, pax_num, stock_limited', 'numerical', 'integerOnly' => true),
            array('supplier_order_id', 'length', 'max' => 64),
            array('name', 'length', 'max' => 255),
            array('special_code, departure_code, language, language_list_code', 'length', 'max' => 16),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('order_product_id, order_id, product_id, bundle_product_id, total, cost_total, supplier_order_id, name, special_code, departure_code, language, language_list_code, departure_time, tour_date, pax_num, redeem_expire_date, return_expire_date, stock_limited, date_added, date_modified', 'safe', 'on' => 'search'),
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
            'subs' => array(self::HAS_MANY, 'HtOrderProductSub', 'order_product_id'),
            'product' => array(self::HAS_ONE, 'HtProduct', '', 'on' => 'op.product_id = p.product_id'),
            'product_description' => array(self::HAS_ONE, 'HtProductDescription', '', 'on' => 'op.product_id = pd.product_id AND language_id = 2'),
            'product_descriptions' => array(self::HAS_MANY, 'HtProductDescription', '', 'on' => 'op.product_id = pd.product_id'),
            'special' => array(self::HAS_ONE, 'HtProductSpecialCode', '', 'on' => 'op.special_code = ps.special_code AND op.product_id = ps.product_id'),
            'special_combo' => array(self::HAS_ONE, 'HtProductSpecialCombo', '', 'on' => 'op.special_code = psc.special_id AND op.product_id = psc.product_id'),
            'departure' => array(self::HAS_ONE, 'HtProductDeparture', '', 'on' => 'op.departure_code = pdep.departure_code AND op.product_id = pdep.product_id AND pdep.language_id = 2'),
            'departures' => array(self::HAS_MANY, 'HtProductDeparture', '', 'on' => 'op.departure_code = pdep.departure_code AND op.product_id = pdep.product_id'),
            'supplier_order' => array(self::HAS_ONE, 'HtSupplierOrder', '', 'on' => 'op.supplier_order_id = so.supplier_order_id'),
            'prices' => array(self::HAS_MANY, 'HtOrderProductPrice', 'order_product_id'),
            'order' => array(self::BELONGS_TO, 'HtOrder', 'order_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'order_product_id' => 'Order Product',
            'order_id' => 'Order',
            'product_id' => 'Product',
            'bundle_product_id' => 'Bundle Parent',
            'total' => 'Total',
            'cost_total' => 'Cost Total',
            'supplier_order_id' => 'Supplier Order',
            'name' => 'Name',
            'special_code' => 'Special Code',
            'departure_code' => 'Departure Code',
            'language' => 'Language',
            'language_list_code' => 'Language List Code',
            'departure_time' => 'Departure Time',
            'tour_date' => 'Tour Date',
            'pax_num' => 'Pax Num',
            'redeem_expire_date' => 'Redeem Expire Date',
            'return_expire_date' => 'Return Expire Date',
            'stock_limited' => 'Stock Limited',
            'date_added' => 'Date Added',
            'date_modified' => 'Date Modified',
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

        $criteria->compare('order_product_id', $this->order_product_id);
        $criteria->compare('order_id', $this->order_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('bundle_product_id', $this->bundle_product_id);
        $criteria->compare('total', $this->total);
        $criteria->compare('cost_total', $this->cost_total);
        $criteria->compare('supplier_order_id', $this->supplier_order_id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('special_code', $this->special_code, true);
        $criteria->compare('departure_code', $this->departure_code, true);
        $criteria->compare('language', $this->language, true);
        $criteria->compare('language_list_code', $this->language_list_code, true);
        $criteria->compare('departure_time', $this->departure_time, true);
        $criteria->compare('tour_date', $this->tour_date, true);
        $criteria->compare('pax_num', $this->pax_num);
        $criteria->compare('redeem_expire_date', $this->redeem_expire_date, true);
        $criteria->compare('return_expire_date', $this->return_expire_date, true);
        $criteria->compare('stock_limited', $this->stock_limited);
        $criteria->compare('date_added', $this->date_added, true);
        $criteria->compare('date_modified', $this->date_modified, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array('alias' => 'op',
            'order' => 'op.order_id, op.order_product_id');
    }

    public function getLeadProduct($order_id)
    {
        return HtOrderProduct::model()->findByAttributes(array('order_id' => $order_id, 'bundle_product_id' => 0));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtOrderProduct the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getShippingDesc($order_id)
    {
        $product_info = HtOrderProduct::model()->findByAttributes(array('order_id' => $order_id));
        $date_rule = Yii::app()->product->getRuleDesc($product_info['product_id']);
        return '预计' . $date_rule['shipping_desc'];
    }
}
