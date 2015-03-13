<?php

/**
 * This is the model class for table "ht_product_passenger_rule_item".
 *
 * The followings are the available columns in table 'ht_product_passenger_rule_item':
 * @property integer $product_id
 * @property integer $ticket_id
 * @property string $fields
 * @property string $hidden_fields
 */
class HtProductPassengerRuleItem extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_passenger_rule_item';
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
            array('fields, hidden_fields', 'length', 'max' => 128),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
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
            'ticket_type' => array(self::HAS_ONE, 'HtTicketType', '', 'on' => ('tt.ticket_id = ppri.ticket_id')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'product_id' => 'Product',
            'ticket_id' => 'Ticket',
            'fields' => 'Fields',
            'hidden_fields' => '对前台隐藏字段',
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
        $criteria->compare('ticket_id', $this->ticket_id);
        $criteria->compare('fields', $this->fields, true);
        $criteria->compare('hidden_fields', $this->hidden_fields, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductPassengerRuleItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array('alias' => 'ppri');
    }

    public function updateByTicketRule($product_id, $ticket_ids = array())
    {
        $existing_ticket_ids = array();
        $items = $this->findAll('product_id = ' . $product_id);
        if (!empty($items)) {
            $existing_ticket_ids = ModelHelper::getList($items, 'ticket_id');
        }

        foreach ($existing_ticket_ids as $ticket_id) {
            if (!in_array($ticket_id, $ticket_ids)) {
                $this->deleteByPk(array('product_id' => $product_id, 'ticket_id' => $ticket_id));
                HtProductVoucherRuleItem::model()->deleteByPk(array('product_id' => $product_id, 'ticket_id' => $ticket_id));
            }
        }

        foreach ($ticket_ids as $ticket_id) {
            if (!in_array($ticket_id, $existing_ticket_ids)) {
                $item = new HtProductPassengerRuleItem();
                $item['product_id'] = $product_id;
                $item['ticket_id'] = $ticket_id;
                $item->insert();
            }
        }
    }
}
