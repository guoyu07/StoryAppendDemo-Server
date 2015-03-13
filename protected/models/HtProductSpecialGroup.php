<?php

/**
 * This is the model class for table "ht_product_special_group".
 *
 * The followings are the available columns in table 'ht_product_special_group':
 * @property integer $$group_id
 * @property integer $product_id
 * @property string $cn_title
 * @property string $en_title
 * @property integer $display_order
 * @property integer $status
 */
class HtProductSpecialGroup extends CActiveRecord
{
    const CACHE_KEY_PRODUCT_SPECIAL_GROUP_ALL = 'cache_key_product_special_group_all_';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_special_group';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, cn_title, en_title', 'required'),
            array('product_id, display_order, status', 'numerical', 'integerOnly' => true),
            array('cn_title, en_title', 'length', 'max' => 32),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('$group_id, product_id, cn_title, en_title, display_order, status', 'safe', 'on' => 'search'),
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
            'special_items' => array(self::HAS_MANY, 'HtProductSpecialItem', 'group_id'),
            'special_items_valid' => array(self::HAS_MANY, 'HtProductSpecialItem', 'group_id','condition'=>'psi.status = 1')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            '$group_id'     => 'Special Group',
            'product_id'    => 'Product',
            'cn_title'      => '中文 title',
            'en_title'      => '英文 Title',
            'display_order' => 'special group 排序字段',
            'status'        => '有效/无效',
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

        $criteria->compare('$group_id', $this->$group_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('cn_title', $this->cn_title, true);
        $criteria->compare('en_title', $this->en_title, true);
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
     * @return HtProductSpecialGroup the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'psg',
            'order' => 'psg.display_order ASC',
        );
    }

    protected function beforeSave()
    {
        HtProductSpecialGroup::clearCache($this->product_id);

        return parent::beforeSave();
    }

    protected function beforeDelete()
    {
        HtProductSpecialGroup::clearCache($this->product_id);

        return parent::beforeDelete();
    }

    public static function clearCache($product_id)
    {
        $key = HtProductSpecialGroup::CACHE_KEY_PRODUCT_SPECIAL_GROUP_ALL . $product_id;
        Yii::app()->cache->delete($key);
    }

    public static function getAllGroups($product_id, $use_cache = false)
    {
        $key = HtProductSpecialGroup::CACHE_KEY_PRODUCT_SPECIAL_GROUP_ALL . $product_id;
        if ($use_cache) {
            $result = Yii::app()->cache->get($key);
            if (!empty($result)) {
                return $result;
            }
        }

        $result = HtProductSpecialGroup::model()->with('special_items.item_limit')->findAllByAttributes(['product_id' => $product_id]);
        $result = Converter::convertModelToArray($result);

        Yii::app()->cache->set($key, $result, 5 * 60);

        return $result;
    }
}
