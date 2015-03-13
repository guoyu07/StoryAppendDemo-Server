<?php

/**
 * This is the model class for table "ht_customer_favorite_product".
 *
 * The followings are the available columns in table 'ht_customer_favorite_product':
 * @property integer $customer_id
 * @property integer $product_id
 * @property string $date_added
 */
class HtCustomerFavoriteProduct extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtCustomerFavoriteProduct the static model class
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
        return 'ht_customer_favorite_product';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('customer_id, product_id, date_added', 'required'),
            array('customer_id, product_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('customer_id, product_id, date_added', 'safe', 'on' => 'search'),
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
            'product' => array(self::HAS_ONE, 'HtProduct', '', 'on' => 'p.product_id = favorite_product.product_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'customer_id' => 'Customer',
            'product_id' => 'Product',
            'date_added' => 'Date Added',
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

        $criteria->compare('customer_id', $this->customer_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('date_added', $this->date_added, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'favorite_product',
        );
    }

    public function isFavorite($product_id)
    {
        if (Yii::app()->customer->isLogged()) {
            $fav_num = HtCustomerFavoriteProduct::model()->findByAttributes(['customer_id'=>Yii::app()->customer->customerId,'product_id'=>$product_id]);
            return empty($fav_num)?0:1;
        } else {
            return 0;
        }
    }
}
