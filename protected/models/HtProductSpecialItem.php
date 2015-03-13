<?php

/**
 * This is the model class for table "ht_product_special_item".
 *
 * The followings are the available columns in table 'ht_product_special_item':
 * @property integer $group_id
 * @property string $special_code
 * @property string $cn_name
 * @property string $en_name
 * @property string $description
 * @property string $product_origin_name
 * @property integer $mapping_product_id
 * @property string $mapping_special_code
 * @property integer $status
 * @property integer $display_order
 */
class HtProductSpecialItem extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_special_item';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('group_id, special_code, cn_name, mapping_special_code', 'required'),
            array('group_id, mapping_product_id, status, display_order', 'numerical', 'integerOnly' => true),
            array('special_code, mapping_special_code', 'length', 'max' => 8),
            array('cn_name, en_name', 'length', 'max' => 100),
            array('description', 'length', 'max' => 1024),
            array('product_origin_name', 'length', 'max' => 128),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('group_id, special_code, cn_name, en_name, description, product_origin_name, mapping_product_id, mapping_special_code, status, display_order', 'safe', 'on' => 'search'),
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
            'item_limit' => array(self::HAS_ONE, 'HtProductSpecialItemLimit', '', 'on' =>'psil.group_id = psi.group_id AND psil.special_code = psi.special_code'),
            'special_group' => array(self::BELONGS_TO, 'HtProductSpecialGroup', '', 'on' =>'psg.group_id = psi.group_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'group_id'     => 'Special Group',
            'special_code'         => 'Special Code',
            'cn_name'              => 'Cn Name',
            'en_name'              => 'En Name',
            'description'          => 'Description',
            'product_origin_name'  => '对应商品原始名称',
            'mapping_product_id'   => '该 Special 对应的 product_id',
            'mapping_special_code' => 'Mapping Special Code',
            'status'               => '状态，1：启用；0：禁用。',
            'display_order'        => '显示顺序',
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
        $criteria->compare('special_code', $this->special_code, true);
        $criteria->compare('cn_name', $this->cn_name, true);
        $criteria->compare('en_name', $this->en_name, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('product_origin_name', $this->product_origin_name, true);
        $criteria->compare('mapping_product_id', $this->mapping_product_id);
        $criteria->compare('mapping_special_code', $this->mapping_special_code, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('display_order', $this->display_order);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductSpecialItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'psi',
            'order' => 'psi.display_order ASC',
        );
    }

    protected function beforeSave()
    {
        $group = HtProductSpecialGroup::model()->findByPk($this->group_id);
        if($group){
            HtProductSpecialGroup::clearCache($group['product_id']);
        }
        return parent::beforeSave();
    }

    protected function beforeDelete()
    {
        $group = HtProductSpecialGroup::model()->findByPk($this->group_id);
        if($group){
            HtProductSpecialGroup::clearCache($group['product_id']);
        }
        return parent::beforeDelete();
    }
}
