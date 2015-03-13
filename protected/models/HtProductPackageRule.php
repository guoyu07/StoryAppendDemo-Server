<?php

/**
 * This is the model class for table "ht_product_package_rule".
 *
 * The followings are the available columns in table 'ht_product_package_rule':
 * @property integer $product_id
 * @property integer $base_product_id
 * @property integer $ticket_id
 * @property integer $quantity
 */
class HtProductPackageRule extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_package_rule';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, base_product_id, ticket_id, quantity', 'required'),
            array('product_id, base_product_id, ticket_id, quantity', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('product_id, base_product_id, ticket_id, quantity', 'safe', 'on' => 'search'),
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
            'ticket_type' => array(self::HAS_ONE, 'HtTicketType', '', 'on' => 'ppr.ticket_id = tt.ticket_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'product_id' => 'Product',
            'base_product_id' => '由哪个product组装的套票',
            'ticket_id' => 'Ticket',
            'quantity' => 'Quantity',
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
        $criteria->compare('base_product_id', $this->base_product_id);
        $criteria->compare('ticket_id', $this->ticket_id);
        $criteria->compare('quantity', $this->quantity);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductPackageRule the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array('alias' => 'ppr');
    }
}
