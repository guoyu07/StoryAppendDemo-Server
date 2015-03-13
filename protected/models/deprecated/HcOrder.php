<?php

/**
 * This is the model class for table "hc_order".
 *
 * The followings are the available columns in table 'hc_order':
 * @property integer $order_id
 * @property integer $invoice_no
 * @property string $invoice_prefix
 * @property integer $store_id
 * @property string $store_name
 * @property string $store_url
 * @property integer $customer_id
 * @property integer $customer_group_id
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $telephone
 * @property string $fax
 * @property string $payment_firstname
 * @property string $payment_lastname
 * @property string $payment_company
 * @property string $payment_company_id
 * @property string $payment_tax_id
 * @property string $payment_address_1
 * @property string $payment_address_2
 * @property string $payment_city
 * @property string $payment_postcode
 * @property string $payment_country
 * @property integer $payment_country_id
 * @property string $payment_zone
 * @property integer $payment_zone_id
 * @property string $payment_address_format
 * @property string $payment_method
 * @property string $payment_code
 * @property string $shipping_firstname
 * @property string $shipping_lastname
 * @property string $shipping_company
 * @property string $shipping_address_1
 * @property string $shipping_address_2
 * @property string $shipping_city
 * @property string $shipping_postcode
 * @property string $shipping_country
 * @property integer $shipping_country_id
 * @property string $shipping_zone
 * @property integer $shipping_zone_id
 * @property string $shipping_address_format
 * @property string $shipping_method
 * @property string $shipping_code
 * @property string $payment_passport_number
 * @property string $payment_telephone
 * @property string $payment_email
 * @property string $comment
 * @property string $total
 * @property integer $order_status_id
 * @property integer $affiliate_id
 * @property string $commission
 * @property integer $language_id
 * @property integer $currency_id
 * @property string $currency_code
 * @property string $currency_value
 * @property string $ip
 * @property string $forwarded_ip
 * @property string $user_agent
 * @property string $accept_language
 * @property string $date_added
 * @property string $date_modified
 */
class HcOrder extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('invoice_prefix, store_name, store_url, firstname, lastname, email, telephone, fax, payment_firstname, payment_lastname, payment_company, payment_company_id, payment_tax_id, payment_address_1, payment_address_2, payment_city, payment_postcode, payment_country, payment_country_id, payment_zone, payment_zone_id, payment_address_format, payment_method, payment_code, shipping_firstname, shipping_lastname, shipping_company, shipping_address_1, shipping_address_2, shipping_city, shipping_postcode, shipping_country, shipping_country_id, shipping_zone, shipping_zone_id, shipping_address_format, shipping_method, shipping_code, comment, affiliate_id, commission, language_id, currency_id, currency_code, ip, forwarded_ip, user_agent, accept_language, date_added, date_modified', 'required'),
			array('invoice_no, store_id, customer_id, customer_group_id, payment_country_id, payment_zone_id, shipping_country_id, shipping_zone_id, order_status_id, affiliate_id, language_id, currency_id', 'numerical', 'integerOnly'=>true),
			array('invoice_prefix', 'length', 'max'=>26),
			array('store_name', 'length', 'max'=>64),
			array('store_url, user_agent, accept_language', 'length', 'max'=>255),
			array('firstname, lastname, telephone, fax, payment_firstname, payment_lastname, payment_company, payment_company_id, payment_tax_id, shipping_firstname, shipping_lastname, shipping_company, payment_passport_number, payment_telephone', 'length', 'max'=>32),
			array('email, payment_email', 'length', 'max'=>96),
			array('payment_address_1, payment_address_2, payment_city, payment_country, payment_zone, payment_method, payment_code, shipping_address_1, shipping_address_2, shipping_city, shipping_country, shipping_zone, shipping_method, shipping_code', 'length', 'max'=>128),
			array('payment_postcode, shipping_postcode', 'length', 'max'=>10),
			array('total, commission, currency_value', 'length', 'max'=>15),
			array('currency_code', 'length', 'max'=>3),
			array('ip, forwarded_ip', 'length', 'max'=>40),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('order_id, invoice_no, invoice_prefix, store_id, store_name, store_url, customer_id, customer_group_id, firstname, lastname, email, telephone, fax, payment_firstname, payment_lastname, payment_company, payment_company_id, payment_tax_id, payment_address_1, payment_address_2, payment_city, payment_postcode, payment_country, payment_country_id, payment_zone, payment_zone_id, payment_address_format, payment_method, payment_code, shipping_firstname, shipping_lastname, shipping_company, shipping_address_1, shipping_address_2, shipping_city, shipping_postcode, shipping_country, shipping_country_id, shipping_zone, shipping_zone_id, shipping_address_format, shipping_method, shipping_code, payment_passport_number, payment_telephone, payment_email, comment, total, order_status_id, affiliate_id, commission, language_id, currency_id, currency_code, currency_value, ip, forwarded_ip, user_agent, accept_language, date_added, date_modified', 'safe', 'on'=>'search'),
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
            'product' => array(self::HAS_ONE, 'HcOrderProduct', 'order_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'order_id' => 'Order',
			'invoice_no' => 'Invoice No',
			'invoice_prefix' => 'Invoice Prefix',
			'store_id' => 'Store',
			'store_name' => 'Store Name',
			'store_url' => 'Store Url',
			'customer_id' => 'Customer',
			'customer_group_id' => 'Customer Group',
			'firstname' => 'Firstname',
			'lastname' => 'Lastname',
			'email' => 'Email',
			'telephone' => 'Telephone',
			'fax' => 'Fax',
			'payment_firstname' => 'Payment Firstname',
			'payment_lastname' => 'Payment Lastname',
			'payment_company' => 'Payment Company',
			'payment_company_id' => 'Payment Company',
			'payment_tax_id' => 'Payment Tax',
			'payment_address_1' => 'Payment Address 1',
			'payment_address_2' => 'Payment Address 2',
			'payment_city' => 'Payment City',
			'payment_postcode' => 'Payment Postcode',
			'payment_country' => 'Payment Country',
			'payment_country_id' => 'Payment Country',
			'payment_zone' => 'Payment Zone',
			'payment_zone_id' => 'Payment Zone',
			'payment_address_format' => 'Payment Address Format',
			'payment_method' => 'Payment Method',
			'payment_code' => 'Payment Code',
			'shipping_firstname' => 'Shipping Firstname',
			'shipping_lastname' => 'Shipping Lastname',
			'shipping_company' => 'Shipping Company',
			'shipping_address_1' => 'Shipping Address 1',
			'shipping_address_2' => 'Shipping Address 2',
			'shipping_city' => 'Shipping City',
			'shipping_postcode' => 'Shipping Postcode',
			'shipping_country' => 'Shipping Country',
			'shipping_country_id' => 'Shipping Country',
			'shipping_zone' => 'Shipping Zone',
			'shipping_zone_id' => 'Shipping Zone',
			'shipping_address_format' => 'Shipping Address Format',
			'shipping_method' => 'Shipping Method',
			'shipping_code' => 'Shipping Code',
			'payment_passport_number' => 'Payment Passport Number',
			'payment_telephone' => 'Payment Telephone',
			'payment_email' => 'Payment Email',
			'comment' => 'Comment',
			'total' => 'Total',
			'order_status_id' => 'Order Status',
			'affiliate_id' => 'Affiliate',
			'commission' => 'Commission',
			'language_id' => 'Language',
			'currency_id' => 'Currency',
			'currency_code' => 'Currency Code',
			'currency_value' => 'Currency Value',
			'ip' => 'Ip',
			'forwarded_ip' => 'Forwarded Ip',
			'user_agent' => 'User Agent',
			'accept_language' => 'Accept Language',
			'date_added' => 'Date Added',
			'date_modified' => 'Date Modified',
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

		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('invoice_no',$this->invoice_no);
		$criteria->compare('invoice_prefix',$this->invoice_prefix,true);
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('store_name',$this->store_name,true);
		$criteria->compare('store_url',$this->store_url,true);
		$criteria->compare('customer_id',$this->customer_id);
		$criteria->compare('customer_group_id',$this->customer_group_id);
		$criteria->compare('firstname',$this->firstname,true);
		$criteria->compare('lastname',$this->lastname,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('payment_firstname',$this->payment_firstname,true);
		$criteria->compare('payment_lastname',$this->payment_lastname,true);
		$criteria->compare('payment_company',$this->payment_company,true);
		$criteria->compare('payment_company_id',$this->payment_company_id,true);
		$criteria->compare('payment_tax_id',$this->payment_tax_id,true);
		$criteria->compare('payment_address_1',$this->payment_address_1,true);
		$criteria->compare('payment_address_2',$this->payment_address_2,true);
		$criteria->compare('payment_city',$this->payment_city,true);
		$criteria->compare('payment_postcode',$this->payment_postcode,true);
		$criteria->compare('payment_country',$this->payment_country,true);
		$criteria->compare('payment_country_id',$this->payment_country_id);
		$criteria->compare('payment_zone',$this->payment_zone,true);
		$criteria->compare('payment_zone_id',$this->payment_zone_id);
		$criteria->compare('payment_address_format',$this->payment_address_format,true);
		$criteria->compare('payment_method',$this->payment_method,true);
		$criteria->compare('payment_code',$this->payment_code,true);
		$criteria->compare('shipping_firstname',$this->shipping_firstname,true);
		$criteria->compare('shipping_lastname',$this->shipping_lastname,true);
		$criteria->compare('shipping_company',$this->shipping_company,true);
		$criteria->compare('shipping_address_1',$this->shipping_address_1,true);
		$criteria->compare('shipping_address_2',$this->shipping_address_2,true);
		$criteria->compare('shipping_city',$this->shipping_city,true);
		$criteria->compare('shipping_postcode',$this->shipping_postcode,true);
		$criteria->compare('shipping_country',$this->shipping_country,true);
		$criteria->compare('shipping_country_id',$this->shipping_country_id);
		$criteria->compare('shipping_zone',$this->shipping_zone,true);
		$criteria->compare('shipping_zone_id',$this->shipping_zone_id);
		$criteria->compare('shipping_address_format',$this->shipping_address_format,true);
		$criteria->compare('shipping_method',$this->shipping_method,true);
		$criteria->compare('shipping_code',$this->shipping_code,true);
		$criteria->compare('payment_passport_number',$this->payment_passport_number,true);
		$criteria->compare('payment_telephone',$this->payment_telephone,true);
		$criteria->compare('payment_email',$this->payment_email,true);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('order_status_id',$this->order_status_id);
		$criteria->compare('affiliate_id',$this->affiliate_id);
		$criteria->compare('commission',$this->commission,true);
		$criteria->compare('language_id',$this->language_id);
		$criteria->compare('currency_id',$this->currency_id);
		$criteria->compare('currency_code',$this->currency_code,true);
		$criteria->compare('currency_value',$this->currency_value,true);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('forwarded_ip',$this->forwarded_ip,true);
		$criteria->compare('user_agent',$this->user_agent,true);
		$criteria->compare('accept_language',$this->accept_language,true);
		$criteria->compare('date_added',$this->date_added,true);
		$criteria->compare('date_modified',$this->date_modified,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
