<?php

/**
 * This is the model class for table "ht_order_passenger".
 *
 * The followings are the available columns in table 'ht_order_passenger':
 * @property integer $order_id
 * @property integer $order_product_id
 * @property integer $ticket_id
 * @property integer $passenger_id
 */
class HtOrderPassenger extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_order_passenger';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_id, order_product_id, ticket_id, passenger_id', 'required'),
            array('order_id, order_product_id, ticket_id, passenger_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('order_id, order_product_id, ticket_id, passenger_id', 'safe', 'on' => 'search'),
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
            'passenger' =>array(self::HAS_ONE, 'HtPassenger', '', 'on' => 'px.passenger_id = opx.passenger_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'order_id' => 'Order',
            'order_product_id' => 'Order Product',
            'ticket_id' => 'Ticket',
            'passenger_id' => 'Passenger',
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

        $criteria->compare('order_id', $this->order_id);
        $criteria->compare('order_product_id', $this->order_product_id);
        $criteria->compare('ticket_id', $this->ticket_id);
        $criteria->compare('passenger_id', $this->passenger_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtOrderPassenger the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'opx',
            'order' => 'opx.passenger_id ASC');
    }

    public function findAllByOrder($order_id,$order_product_id = 0)
    {
        if(empty($order_product_id)){
            $data = $this->with('passenger')->findAllByAttributes(['order_id' => $order_id]);
        }else{
            $data = $this->with('passenger')->findAllByAttributes(['order_id' => $order_id,'order_product_id' => $order_product_id]);
        }
        $result = [];
        foreach($data as $order_passenger) {
            $passenger = Converter::convertModelToArray($order_passenger['passenger']);

            $passenger['order_id'] = $order_passenger['order_id'];
            $passenger['order_product_id'] = $order_passenger['order_product_id'];
            $passenger['ticket_id'] = $order_passenger['ticket_id'];

            array_push($result, $passenger);
        }

        return $result;
    }
}
