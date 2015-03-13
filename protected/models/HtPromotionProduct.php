<?php

/**
 * This is the model class for table "ht_promotion_product".
 *
 * The followings are the available columns in table 'ht_promotion_product':
 * @property integer $promotion_id
 * @property integer $group_id
 * @property integer $product_id
 * @property integer $display_order
 */
class HtPromotionProduct extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_promotion_product';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('promotion_id, group_id, product_id, display_order', 'required'),
            array('promotion_id, group_id, product_id, display_order', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('promotion_id, group_id, product_id, display_order', 'safe', 'on' => 'search'),
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
            'promotion_id' => 'Promotion',
            'group_id' => 'Group',
            'product_id' => 'Product',
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

        $criteria->compare('promotion_id', $this->promotion_id);
        $criteria->compare('group_id', $this->group_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('display_order', $this->display_order);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtPromotionProduct the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'pp',
            'order' => 'pp.display_order ASC'
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
}
