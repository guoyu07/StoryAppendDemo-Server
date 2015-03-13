<?php

/**
 * This is the model class for table "ht_product_bundle_item".
 *
 * The followings are the available columns in table 'ht_product_bundle_item':
 * @property integer $bundle_id
 * @property integer $binding_product_id
 * @property string $discount_type
 * @property integer $discount_amount
 * @property integer $count_type
 * @property integer $count
 * @property integer $display_order
 */
class HtProductBundleItem extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_bundle_item';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('bundle_id, binding_product_id', 'required'),
            array('bundle_id, binding_product_id, discount_amount, count_type, count, display_order', 'numerical', 'integerOnly' => true),
            array('discount_type', 'length', 'max' => 1),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('bundle_id, binding_product_id, discount_type, discount_amount, count_type, count, display_order', 'safe', 'on' => 'search'),
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
            'bundle' => array(self::BELONGS_TO, 'HtProductBundle', '', 'on' => 'pbi.bundle_id = pb.bundle_id'),
            'product' => array(self::HAS_ONE, 'HtProduct', '', 'on' => 'pbi.binding_product_id = p.product_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'bundle_id' => 'Bundle',
            'binding_product_id' => 'Binding Product',
            'discount_type' => 'F：金额；P：折扣；',
            'discount_amount' => '优惠金额或折扣数',
            'count_type' => '发货数量类型；1：按套数，2：按人数；3：固定套数',
            'count' => '固定数量',
            'display_order' => '显示顺序',
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

        $criteria->compare('bundle_id', $this->bundle_id);
        $criteria->compare('binding_product_id', $this->binding_product_id);
        $criteria->compare('discount_type', $this->discount_type, true);
        $criteria->compare('discount_amount', $this->discount_amount);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductBundleItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'pbi',
            'order' => 'pbi.display_order ASC'
        );
    }

    public function getBundleInfo($product_id, $bundle_ids = [])
    {
        $criteria = new CDbCriteria;
        $criteria->addCondition('binding_product_id=' . $product_id);
        if (!empty($bundle_ids)) {
            $criteria->addInCondition('pb.bundle_id', $bundle_ids);
        }

        $bundle_info = $this->with('bundle')->find($criteria);

        return $bundle_info;
    }

}
