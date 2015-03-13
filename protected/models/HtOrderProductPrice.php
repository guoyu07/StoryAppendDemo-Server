<?php

/**
 * This is the model class for table "ht_order_product_price".
 *
 * The followings are the available columns in table 'ht_order_product_price':
 * @property integer $order_product_id
 * @property integer $ticket_id
 * @property integer $quantity
 * @property integer $price
 * @property integer $cost_price
 */
class HtOrderProductPrice extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_order_product_price';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_product_id, ticket_id, quantity, price, cost_price', 'required'),
            array('order_product_id, ticket_id, quantity, price, cost_price', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('order_product_id, ticket_id, quantity, price, cost_price', 'safe', 'on' => 'search'),
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
            'ticket_type' => array(self::HAS_ONE, 'HtTicketType', '', 'on' => 't.ticket_id = tt.ticket_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'order_product_id' => 'Order Product',
            'ticket_id' => 'Ticket',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'cost_price' => 'Cost Price',
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

        $criteria->compare('order_product_id', $this->order_product_id);
        $criteria->compare('ticket_id', $this->ticket_id);
        $criteria->compare('quantity', $this->quantity);
        $criteria->compare('price', $this->price);
        $criteria->compare('cost_price', $this->cost_price);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtOrderProductPrice the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getQuantities($order_product_id)
    {
        $quantities = array();
        $qn = $this->findAllByAttributes(['order_product_id' => $order_product_id]);
        foreach ($qn as $qi) {
            $quantities[$qi['ticket_id']] = $qi['quantity'];
        }
        return $quantities;
    }

    public function calcRealQuantities($order_product_id, $product_id)
    {
        $quantities = array();
        $qn = $this->findAllByAttributes(['order_product_id' => $order_product_id]);
        $psr = HtProductSaleRule::model()->with('package_rules')->findByPk($product_id);

        foreach ($qn as $qi) {
            if ($qi['ticket_id'] == HtTicketType::TYPE_PACKAGE && $psr['sale_in_package']) {
                foreach ($psr['package_rules'] as $ppri) {
                    $quantities[$ppri['ticket_id']] = $qi['quantity'] * $ppri['quantity'];
                }
                break; //
            } else {
                $quantities[$qi['ticket_id']] = $qi['quantity'];
            }
        }
        return $quantities;
    }
}
