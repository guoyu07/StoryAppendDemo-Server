<?php

/**
 * This is the model class for table "ht_coupon_product".
 *
 * The followings are the available columns in table 'ht_coupon_product':
 * @property integer $coupon_product_id
 * @property integer $coupon_id
 * @property integer $product_id
 * @property integer $could_use
 */
class HtCouponProduct extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_coupon_product';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('coupon_id, product_id', 'required'),
            array('coupon_id, product_id, could_use', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('coupon_product_id, coupon_id, product_id, could_use', 'safe', 'on' => 'search'),
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
            'description' => array(self::HAS_ONE, 'HtProductDescription', '', 'on' => 'pd.product_id = cp.product_id',  'condition' => 'pd.language_id=2'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'coupon_product_id' => 'Coupon Product',
            'coupon_id' => 'Coupon',
            'product_id' => 'Product',
            'could_use' => '0：不可以使用；1：可以使用',
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

        $criteria->compare('coupon_product_id', $this->coupon_product_id);
        $criteria->compare('coupon_id', $this->coupon_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('could_use', $this->could_use);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtCouponProduct the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'cp',
            'order' => 'cp.product_id ASC',
        );
    }

    public function getProducts($coupon_id)
    {
        $data = array();
        $data['products_could_use'] = array();
        $data['products_could_not_use'] = array();
        $products_could_use = $this->with('description')->findAllByAttributes(array('coupon_id' => $coupon_id));
        foreach($products_could_use as $item) {
            $item_tmp = array('coupon_product_id' => $item['coupon_product_id'],
                'product_id' => $item['product_id'],
                'name' => $item['description']['name']
            );
            if($item['could_use'] == 1) {
                $data['products_could_use'][] = $item_tmp;
            } else {
                $data['products_could_not_use'][] = $item_tmp;
            }
        }

        return $data;
    }
}
