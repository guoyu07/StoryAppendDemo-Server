<?php

/**
 * This is the model class for table "ht_promotion_group".
 *
 * The followings are the available columns in table 'ht_promotion_group':
 * @property integer $promotion_id
 * @property integer $group_id
 * @property string $name
 * @property string $description
 * @property integer $display_order
 * @property string $attach_url
 */
class HtPromotionGroup extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_promotion_group';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('promotion_id, name, description, display_order, attach_url', 'required'),
            array('promotion_id, display_order', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 50),
            array('name', 'attach_url', 'max' => 500),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('promotion_id, group_id, name, description, display_order, attach_url', 'safe', 'on' => 'search'),
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
            'promotion_product' => array(self::HAS_MANY, 'HtPromotionProduct', 'group_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'promotion_id' => 'Promotion',
            'group_id' => 'Group',
            'name' => 'Name',
            'description' => 'Description',
            'display_order' => 'Display Order',
            'attach_url' => 'Attach Url'
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('display_order', $this->display_order);
        $criteria->compare('attach_url', $this->attach_url, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtPromotionGroup the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'pg',
            'order' => 'pg.display_order ASC'
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
