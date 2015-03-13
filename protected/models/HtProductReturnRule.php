<?php
/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/15/14
 * Time: 6:13 PM
 */

/**
 * This is the model class for table "ht_product_return_rule".
 *
 * The followings are the available columns in table 'ht_product_return_rule':
 * @property integer $product_id
 * @property integer $return_type
 * @property string $offset
 * @property string $formula
 */
class HtProductReturnRule extends CActiveRecord
{
    const DONT_RETURN = 0;
    const BEFORE_REDEEM = 1;
    const BEFORE_TOUR_DATE = 2;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtProductReturnRule the static model class
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
        return 'ht_product_return_rule';
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
            array('product_id, return_type', 'numerical', 'integerOnly' => true),
            array('offset', 'length', 'max' => 32),
            array('formula', 'length', 'max' => 128),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('product_id, return_type, offset, formula', 'safe', 'on' => 'search'),
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
            'return_type' => 'Return Type', // 0.不可退单;1.兑换截止日之前几天可退；２：TourDate前几天可退
            'offset' => 'Offset',
            'formula' => 'Formula',
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
        $criteria->compare('return_type', $this->return_type);
        $criteria->compare('offset', $this->offset, true);
        $criteria->compare('formula', $this->formula, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getRuleDesc()
    {
        switch ($this->return_type) {
            case self::DONT_RETURN:
                $desc = '该商品下单后将不允许退订';
                break;
            case self::BEFORE_REDEEM:
                $desc = sprintf('兑换截止日期前%s可免费退订',
                                str_ireplace(['day', 'month', 'year'], ['天', '个月', '年'], $this->offset));
                break;
            case self::BEFORE_TOUR_DATE:
                $desc = sprintf('您选择的日期前%s可免费退订', str_ireplace(['day', 'month', 'year'], ['天', '个月', '年'], $this->offset));
                break;
            default:
                $desc = '该商品下单后将不允许退订';
        }

        return $desc;
    }

    protected function beforeSave()
    {
        HtProduct::clearCachedRuleDesc($this->product_id);

        return parent::beforeSave();
    }
}