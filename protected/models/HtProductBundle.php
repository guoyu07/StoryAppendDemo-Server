<?php

/**
 * This is the model class for table "ht_product_bundle".
 *
 * The followings are the available columns in table 'ht_product_bundle':
 * @property integer $bundle_id
 * @property integer $product_id
 * @property string $top_group_title
 * @property string $top_group_alias
 * @property integer $group_id
 * @property string $group_title
 * @property integer $group_type
 */
class HtProductBundle extends CActiveRecord
{
    const GT_SELECTION = 1;
    const GT_REQUIRED = 2;
    const GT_OPTIONAL = 3;

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductBundle the static model class
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
        return 'ht_product_bundle';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id', 'required'),
            array('product_id, group_id, group_type', 'numerical', 'integerOnly' => true),
            array('top_group_title, top_group_alias', 'length', 'max' => 32),
            array('group_title', 'length', 'max' => 100),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('bundle_id, product_id, top_group_title, top_group_alias, group_id, group_title, group_type', 'safe', 'on' => 'search'),
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
            'items' => array(self::HAS_MANY, 'HtProductBundleItem', '', 'on' => 'pb.bundle_id=pbi.bundle_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'bundle_id' => 'Bundle',
            'product_id' => 'Product',
            'top_group_title' => '服务包含，可选',
            'top_group_alias' => '指定别名',
            'group_id' => '分组编号',
            'group_title' => '分组名称',
            'group_type' => '1: N选1；2：必选；3：可选；',
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
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('top_group_title', $this->top_group_title, true);
        $criteria->compare('top_group_alias', $this->top_group_alias, true);
        $criteria->compare('group_id', $this->group_id);
        $criteria->compare('group_title', $this->group_title, true);
        $criteria->compare('group_type', $this->group_type);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'pb',
            'order' => 'pb.group_id ASC'
        );
    }

    public function getPackageIncludedProductCount($package_product_id) {
        $rows = HtProductBundle::model()->with('items')->findByAttributes(['product_id' => $package_product_id, 'group_type' => '2']);
        $count = 0;

        if(!empty($rows['items'])) {
            foreach($rows['items'] as $r) {
                $count += $r['count'];
            }
        }

        return $count;
    }
}
