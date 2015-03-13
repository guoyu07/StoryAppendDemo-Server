<?php
/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/15/14
 * Time: 6:17 PM
 */

/**
 * This is the model class for table "ht_product_sale_rule".
 *
 * The followings are the available columns in table 'ht_product_sale_rule':
 * @property integer $product_id
 * @property integer $sale_in_package
 * @property integer $min_num
 * @property integer $max_num
 */
class HtProductSaleRule extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtProductSaleRule the static model class
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
        return 'ht_product_sale_rule';
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
            array('product_id, sale_in_package, min_num, max_num', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('product_id, sale_in_package, min_num, max_num', 'safe', 'on' => 'search'),
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
            'package_rules' => array(self::HAS_MANY, 'HtProductPackageRule', '', 'on' => 'psr.product_id=ppr.product_id and ppr.quantity>0'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'product_id' => 'Product',
            'sale_in_package' => 'sale_in_package',
            'min_num' => 'Min Num',
            'max_num' => 'Max Num',
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
        $criteria->compare('sale_in_package', $this->sale_in_package);
        $criteria->compare('min_num', $this->min_num);
        $criteria->compare('max_num', $this->max_num);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array('alias' => 'psr');
    }
}