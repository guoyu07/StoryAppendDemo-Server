<?php

/**
 * This is the model class for table "ht_product_voucher_rule_item".
 *
 * The followings are the available columns in table 'ht_product_voucher_rule_item':
 * @property integer $product_id
 * @property integer $ticket_id
 * @property string $fields
 */
class HtProductVoucherRuleItem extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtProductVoucherRuleItem the static model class
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
        return 'ht_product_voucher_rule_item';
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
            array('product_id, ticket_id', 'numerical', 'integerOnly' => true),
            array('fields', 'length', 'max' => 128),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('product_id, ticket_id, fields', 'safe', 'on' => 'search'),
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
            'ticket_type' => array(self::HAS_ONE, 'HtTicketType', '', 'on' => ('tt.ticket_id = pvri.ticket_id')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'product_id' => 'Product',
            'ticket_id'  => 'Ticket',
            'fields'     => 'Fields',
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
        $criteria->compare('ticket_id', $this->ticket_id);
        $criteria->compare('fields', $this->fields, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array('alias' => 'pvri');
    }

    public static function addNew($product_id, $ticket_id, $fields)
    {
        $new_item = new HtProductVoucherRuleItem();
        $new_item["product_id"] = $product_id;
        $new_item["ticket_id"] = $ticket_id;
        $new_item["fields"] = $fields;

        return $new_item->save();

    }
}