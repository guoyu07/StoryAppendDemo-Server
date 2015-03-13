<?php
/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/24/14
 * Time: 5:09 PM
 */


/**
 * This is the model class for table "ht_product_related".
 *
 * The followings are the available columns in table 'ht_product_related':
 * @property integer $product_id
 * @property integer $sub_product_id
 */
class HtProductCombo extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_combo';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, sub_product_id', 'required'),
            array('product_id, sub_product_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('product_id, sub_product_id', 'safe', 'on' => 'search'),
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
            'product' => array(self::HAS_ONE, 'HtProduct', array('product_id' => 'sub_product_id'), 'alias' => 'pp'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'product_id'     => 'Product',
            'sub_product_id' => 'Sub Product',
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
        $criteria->compare('sub_product_id', $this->sub_product_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductRelated the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'pc',
        );
    }

    public static function addNew($product_id, $sub_product_id)
    {
        $ps = new HtProductCombo();
        $ps['product_id'] = $product_id;
        $ps['sub_product_id'] = $sub_product_id;

        return $ps->insert();
    }
}
