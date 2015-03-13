<?php

/**
 * This is the model class for table "ht_product_special_code".
 *
 * The followings are the available columns in table 'ht_product_special_code':
 * @property integer $product_id
 * @property string $special_code
 * @property string $cn_name
 * @property string $en_name
 * @property string $description
 * @property string $product_origin_name
 * @property string $mapping_product_id
 * @property string $mapping_special_code
 * @property integer $status
 * @property integer display_order
 */
class HtProductSpecialCode extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_special_code';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, special_code, cn_name', 'required'),
            array('product_id, status, mapping_product_id, display_order', 'numerical', 'integerOnly' => true),
            array('special_code, mapping_special_code', 'length', 'max' => 8),
            array('cn_name, en_name', 'length', 'max' => 64),
            array('product_origin_name', 'length', 'max' => 128),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('product_id, special_code, cn_name, en_name, product_origin_name, status, mapping_product_id, mapping_special_code, display_order', 'safe', 'on' => 'search'),
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
            'product_id' => 'Product',
            'special_code' => 'Special Code',
            'cn_name' => 'Cn Name',
            'en_name' => 'En Name',
            'description' => 'Description',
            'product_origin_name' => '对应商品原始名称',
            'mapping_product_id' => 'Mapping Product ID',
            'mapping_special_code' => 'Mapping Special Code',
            'status' => 'Status', // 状态，1：启用；0：禁用
            'display_order' => 'Display Order',
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
        $criteria->compare('special_code', $this->special_code, true);
        $criteria->compare('cn_name', $this->cn_name, true);
        $criteria->compare('en_name', $this->en_name, true);
        $criteria->compare('product_origin_name', $this->product_origin_name, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductSpecialCode the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'ps',
            'order' => 'ps.display_order ASC'
        );
    }

    public function needSpecialCode($product_id)
    {
        $special_code = HtProductSpecialCode::model()->findAllByAttributes(['product_id' => $product_id, 'status' => '1']);
        if (!empty($special_code)) {
            return true;
        }

        return false;
    }
}
