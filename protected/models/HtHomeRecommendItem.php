<?php

/**
 * This is the model class for table "ht_home_recommend_item".
 *
 * The followings are the available columns in table 'ht_home_recommend_item':
 * @property integer $id
 * @property integer $group_id
 * @property string $city_code
 * @property integer $product_id
 * @property string $cover_url
 * @property string $product_name
 * @property string $product_desc
 * @property integer $display_order
 */
class HtHomeRecommendItem extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_home_recommend_item';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('group_id, city_code, product_id, cover_url, product_name, product_desc, display_order', 'required'),
            array('group_id, product_id, display_order', 'numerical', 'integerOnly' => true),
            array('city_code', 'length', 'max' => 6),
            array('cover_url, product_name, product_desc', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, group_id, city_code, product_id, cover_url, product_name, product_desc, display_order', 'safe', 'on' => 'search'),
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
            'group' => array(self::BELONGS_TO, 'HtHomeRecommend', 'group_id'),
            'city' => array(self::HAS_ONE, 'HtCity', '', 'on' => 'hri.city_code=city.city_code'),
            'product' => array(self::HAS_ONE, 'HtProduct', '', 'on' => 'hri.product_id = p.product_id'),
        );
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'hri',
            'order' => 'hri.display_order ASC',
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'group_id' => '推荐分组id',
            'city_code' => 'City Code',
            'product_id' => 'Product',
            'cover_url' => 'Cover Url',
            'product_name' => 'Product Name',
            'product_desc' => 'Product Desc',
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

        $criteria->compare('id', $this->id);
        $criteria->compare('group_id', $this->group_id);
        $criteria->compare('city_code', $this->city_code, true);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('cover_url', $this->cover_url, true);
        $criteria->compare('product_name', $this->product_name, true);
        $criteria->compare('product_desc', $this->product_desc, true);
        $criteria->compare('display_order', $this->display_order);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtHomeRecommendItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function beforeDelete() {
        HtHomeRecommend::clearCache($this->group_id);
        return parent::beforeDelete();
    }

    protected function beforeSave() {
        HtHomeRecommend::clearCache($this->group_id);
        return parent::beforeSave();
    }

    public function isExists($group_id, $type = 1, $product_id = 0, $city_code = '')
    {
        $c = new CDbCriteria();
        $c->addCondition('group_id = ' . (int)$group_id);
        if ($type == 1) {
            $c->addCondition('product_id = ' . (int)$product_id);
        } else if ($type == 2) {
            $c->addCondition('city_code = "' . $city_code . '"');
        }

        $data = HtHomeRecommendItem::model()->find($c);

        return !empty($data);
    }

    public function getByGroupID($group_id)
    {
        $c = new CDbCriteria();
        $c->addCondition('group_id=' . $group_id);

        return HtHomeRecommendItem::model()->findAll($c);
    }

    public function deleteByGroupID($group_id)
    {
        $c = new CDbCriteria();
        $c->addCondition('group_id=' . $group_id);
        HtHomeRecommendItem::model()->deleteAll($c);
        HtHomeRecommend::clearCache($group_id);
    }
}
