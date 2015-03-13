<?php

/**
 * This is the model class for table "hc_order_product".
 *
 * The followings are the available columns in table 'hc_order_product':
 * @property integer $order_product_id
 * @property integer $order_id
 * @property integer $product_id
 * @property integer $manufacturer_id
 * @property string $name
 * @property string $model
 * @property integer $quantity
 * @property string $price
 * @property integer $child_quantity
 * @property string $child_price
 * @property string $total
 * @property string $tax
 * @property integer $reward
 * @property string $tour_date
 * @property string $departure_id
 * @property string $departure_time
 * @property string $departure_point
 * @property string $language
 * @property string $language_list_code
 * @property string $supplier_order_id
 * @property string $attachment
 * @property integer $status_id
 * @property string $redeem_expire_date
 * @property string $return_expire_date
 * @property integer $stock_limited
 * @property string $update_time
 */
class HcOrderProduct extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_order_product';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, product_id, manufacturer_id, name, model, quantity, child_quantity, child_price, reward, departure_time, attachment, redeem_expire_date, return_expire_date, update_time', 'required'),
			array('order_id, product_id, manufacturer_id, quantity, child_quantity, reward, status_id, stock_limited', 'numerical', 'integerOnly'=>true),
			array('name, attachment', 'length', 'max'=>255),
			array('model', 'length', 'max'=>64),
			array('price, total, tax', 'length', 'max'=>15),
			array('child_price', 'length', 'max'=>10),
			array('departure_id', 'length', 'max'=>16),
			array('departure_point, supplier_order_id', 'length', 'max'=>128),
			array('language', 'length', 'max'=>3),
			array('language_list_code', 'length', 'max'=>4),
			array('tour_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('order_product_id, order_id, product_id, manufacturer_id, name, model, quantity, price, child_quantity, child_price, total, tax, reward, tour_date, departure_id, departure_time, departure_point, language, language_list_code, supplier_order_id, attachment, status_id, redeem_expire_date, return_expire_date, stock_limited, update_time', 'safe', 'on'=>'search'),
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
			'order_product_id' => 'Order Product',
			'order_id' => 'Order',
			'product_id' => 'Product',
			'manufacturer_id' => 'Manufacturer',
			'name' => 'Name',
			'model' => 'Model',
			'quantity' => 'Quantity',
			'price' => 'Price',
			'child_quantity' => 'Child Quantity',
			'child_price' => 'Child Price',
			'total' => 'Total',
			'tax' => 'Tax',
			'reward' => 'Reward',
			'tour_date' => 'Tour Date',
			'departure_id' => 'Departure',
			'departure_time' => 'Departure Time',
			'departure_point' => 'Departure Point',
			'language' => 'Language',
			'language_list_code' => 'Language List Code',
			'supplier_order_id' => 'Supplier Order',
			'attachment' => 'Attachment',
			'status_id' => 'Status',
			'redeem_expire_date' => 'Redeem Expire Date',
			'return_expire_date' => 'Return Expire Date',
			'stock_limited' => 'Stock Limited',
			'update_time' => 'Update Time',
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

		$criteria->compare('order_product_id',$this->order_product_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('manufacturer_id',$this->manufacturer_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('child_quantity',$this->child_quantity);
		$criteria->compare('child_price',$this->child_price,true);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('tax',$this->tax,true);
		$criteria->compare('reward',$this->reward);
		$criteria->compare('tour_date',$this->tour_date,true);
		$criteria->compare('departure_id',$this->departure_id,true);
		$criteria->compare('departure_time',$this->departure_time,true);
		$criteria->compare('departure_point',$this->departure_point,true);
		$criteria->compare('language',$this->language,true);
		$criteria->compare('language_list_code',$this->language_list_code,true);
		$criteria->compare('supplier_order_id',$this->supplier_order_id,true);
		$criteria->compare('attachment',$this->attachment,true);
		$criteria->compare('status_id',$this->status_id);
		$criteria->compare('redeem_expire_date',$this->redeem_expire_date,true);
		$criteria->compare('return_expire_date',$this->return_expire_date,true);
		$criteria->compare('stock_limited',$this->stock_limited);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcOrderProduct the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
