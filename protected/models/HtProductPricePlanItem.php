<?php

/**
 * This is the model class for table "ht_product_price_plan_item".
 *
 * The followings are the available columns in table 'ht_product_price_plan_item':
 * @property integer $item_id
 * @property integer $price_plan_id
 * @property integer $is_special
 * @property string $frequency
 * @property integer $ticket_id
 * @property string $special_code
 * @property integer $quantity
 * @property integer $supplier_price
 * @property integer $cost_price
 * @property integer $orig_price
 * @property integer $price
 */
class HtProductPricePlanItem extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_product_price_plan_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('price_plan_id, frequency, ticket_id, special_code, quantity, supplier_price, cost_price, orig_price, price', 'required'),
			array('price_plan_id, ticket_id, quantity, supplier_price, cost_price, orig_price, price, is_special', 'numerical', 'integerOnly'=>true),
			array('frequency', 'length', 'max'=>255),
			array('special_code', 'length', 'max'=>8),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('item_id, price_plan_id, frequency, ticket_id, special_code, quantity, supplier_price, cost_price, orig_price, price, is_special', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'item_id' => 'Item',
			'price_plan_id' => 'Price Plan',
            'is_special' => 'is_special', // 0：普通；1：特价
			'frequency' => '在相应的频次类型下,以数字列表表示相同价格的某一天',
			'ticket_id' => 'Ticket',
			'special_code' => 'Special Code',
			'quantity' => 'Quantity',
			'supplier_price' => 'Supplier Price',
			'cost_price' => '成本价',
			'orig_price' => '原价（门市价）',
			'price' => 'Price',
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

		$criteria=new CDbCriteria;

		$criteria->compare('item_id',$this->item_id);
		$criteria->compare('price_plan_id',$this->price_plan_id);
		$criteria->compare('frequency',$this->frequency,true);
		$criteria->compare('ticket_id',$this->ticket_id);
		$criteria->compare('special_code',$this->special_code,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('supplier_price',$this->supplier_price);
		$criteria->compare('cost_price',$this->cost_price);
		$criteria->compare('orig_price',$this->orig_price);
		$criteria->compare('price',$this->price);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtProductPricePlanItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope()
    {
        return array(
            'alias' => 'price_plan_item',
            'order' => 'special_code ASC, ticket_id ASC, quantity ASC'
        );
    }
}
