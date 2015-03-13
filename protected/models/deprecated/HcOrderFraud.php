<?php

/**
 * This is the model class for table "hc_order_fraud".
 *
 * The followings are the available columns in table 'hc_order_fraud':
 * @property integer $order_id
 * @property integer $customer_id
 * @property string $country_match
 * @property string $country_code
 * @property string $high_risk_country
 * @property integer $distance
 * @property string $ip_region
 * @property string $ip_city
 * @property string $ip_latitude
 * @property string $ip_longitude
 * @property string $ip_isp
 * @property string $ip_org
 * @property integer $ip_asnum
 * @property string $ip_user_type
 * @property string $ip_country_confidence
 * @property string $ip_region_confidence
 * @property string $ip_city_confidence
 * @property string $ip_postal_confidence
 * @property string $ip_postal_code
 * @property integer $ip_accuracy_radius
 * @property string $ip_net_speed_cell
 * @property integer $ip_metro_code
 * @property integer $ip_area_code
 * @property string $ip_time_zone
 * @property string $ip_region_name
 * @property string $ip_domain
 * @property string $ip_country_name
 * @property string $ip_continent_code
 * @property string $ip_corporate_proxy
 * @property string $anonymous_proxy
 * @property integer $proxy_score
 * @property string $is_trans_proxy
 * @property string $free_mail
 * @property string $carder_email
 * @property string $high_risk_username
 * @property string $high_risk_password
 * @property string $bin_match
 * @property string $bin_country
 * @property string $bin_name_match
 * @property string $bin_name
 * @property string $bin_phone_match
 * @property string $bin_phone
 * @property string $customer_phone_in_billing_location
 * @property string $ship_forward
 * @property string $city_postal_match
 * @property string $ship_city_postal_match
 * @property string $score
 * @property string $explanation
 * @property string $risk_score
 * @property integer $queries_remaining
 * @property string $maxmind_id
 * @property string $error
 * @property string $date_added
 */
class HcOrderFraud extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_order_fraud';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, customer_id, country_match, country_code, high_risk_country, distance, ip_region, ip_city, ip_latitude, ip_longitude, ip_isp, ip_org, ip_asnum, ip_user_type, ip_country_confidence, ip_region_confidence, ip_city_confidence, ip_postal_confidence, ip_postal_code, ip_accuracy_radius, ip_net_speed_cell, ip_metro_code, ip_area_code, ip_time_zone, ip_region_name, ip_domain, ip_country_name, ip_continent_code, ip_corporate_proxy, anonymous_proxy, proxy_score, is_trans_proxy, free_mail, carder_email, high_risk_username, high_risk_password, bin_match, bin_country, bin_name_match, bin_name, bin_phone_match, bin_phone, customer_phone_in_billing_location, ship_forward, city_postal_match, ship_city_postal_match, score, explanation, risk_score, queries_remaining, maxmind_id, error', 'required'),
			array('order_id, customer_id, distance, ip_asnum, ip_accuracy_radius, ip_metro_code, ip_area_code, proxy_score, queries_remaining', 'numerical', 'integerOnly'=>true),
			array('country_match, high_risk_country, ip_country_confidence, ip_region_confidence, ip_city_confidence, ip_postal_confidence, ip_corporate_proxy, anonymous_proxy, is_trans_proxy, free_mail, carder_email, high_risk_username, high_risk_password, bin_name_match, bin_phone_match, ship_forward, city_postal_match, ship_city_postal_match', 'length', 'max'=>3),
			array('country_code, ip_continent_code, bin_country', 'length', 'max'=>2),
			array('ip_region, ip_city, ip_isp, ip_org, ip_user_type, ip_net_speed_cell, ip_time_zone, ip_region_name, ip_domain, ip_country_name, bin_name', 'length', 'max'=>255),
			array('ip_latitude, ip_longitude, ip_postal_code, bin_match, score, risk_score', 'length', 'max'=>10),
			array('bin_phone', 'length', 'max'=>32),
			array('customer_phone_in_billing_location, maxmind_id', 'length', 'max'=>8),
			array('date_added', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('order_id, customer_id, country_match, country_code, high_risk_country, distance, ip_region, ip_city, ip_latitude, ip_longitude, ip_isp, ip_org, ip_asnum, ip_user_type, ip_country_confidence, ip_region_confidence, ip_city_confidence, ip_postal_confidence, ip_postal_code, ip_accuracy_radius, ip_net_speed_cell, ip_metro_code, ip_area_code, ip_time_zone, ip_region_name, ip_domain, ip_country_name, ip_continent_code, ip_corporate_proxy, anonymous_proxy, proxy_score, is_trans_proxy, free_mail, carder_email, high_risk_username, high_risk_password, bin_match, bin_country, bin_name_match, bin_name, bin_phone_match, bin_phone, customer_phone_in_billing_location, ship_forward, city_postal_match, ship_city_postal_match, score, explanation, risk_score, queries_remaining, maxmind_id, error, date_added', 'safe', 'on'=>'search'),
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
			'order_id' => 'Order',
			'customer_id' => 'Customer',
			'country_match' => 'Country Match',
			'country_code' => 'Country Code',
			'high_risk_country' => 'High Risk Country',
			'distance' => 'Distance',
			'ip_region' => 'Ip Region',
			'ip_city' => 'Ip City',
			'ip_latitude' => 'Ip Latitude',
			'ip_longitude' => 'Ip Longitude',
			'ip_isp' => 'Ip Isp',
			'ip_org' => 'Ip Org',
			'ip_asnum' => 'Ip Asnum',
			'ip_user_type' => 'Ip User Type',
			'ip_country_confidence' => 'Ip Country Confidence',
			'ip_region_confidence' => 'Ip Region Confidence',
			'ip_city_confidence' => 'Ip City Confidence',
			'ip_postal_confidence' => 'Ip Postal Confidence',
			'ip_postal_code' => 'Ip Postal Code',
			'ip_accuracy_radius' => 'Ip Accuracy Radius',
			'ip_net_speed_cell' => 'Ip Net Speed Cell',
			'ip_metro_code' => 'Ip Metro Code',
			'ip_area_code' => 'Ip Area Code',
			'ip_time_zone' => 'Ip Time Zone',
			'ip_region_name' => 'Ip Region Name',
			'ip_domain' => 'Ip Domain',
			'ip_country_name' => 'Ip Country Name',
			'ip_continent_code' => 'Ip Continent Code',
			'ip_corporate_proxy' => 'Ip Corporate Proxy',
			'anonymous_proxy' => 'Anonymous Proxy',
			'proxy_score' => 'Proxy Score',
			'is_trans_proxy' => 'Is Trans Proxy',
			'free_mail' => 'Free Mail',
			'carder_email' => 'Carder Email',
			'high_risk_username' => 'High Risk Username',
			'high_risk_password' => 'High Risk Password',
			'bin_match' => 'Bin Match',
			'bin_country' => 'Bin Country',
			'bin_name_match' => 'Bin Name Match',
			'bin_name' => 'Bin Name',
			'bin_phone_match' => 'Bin Phone Match',
			'bin_phone' => 'Bin Phone',
			'customer_phone_in_billing_location' => 'Customer Phone In Billing Location',
			'ship_forward' => 'Ship Forward',
			'city_postal_match' => 'City Postal Match',
			'ship_city_postal_match' => 'Ship City Postal Match',
			'score' => 'Score',
			'explanation' => 'Explanation',
			'risk_score' => 'Risk Score',
			'queries_remaining' => 'Queries Remaining',
			'maxmind_id' => 'Maxmind',
			'error' => 'Error',
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

		$criteria=new CDbCriteria;

		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('customer_id',$this->customer_id);
		$criteria->compare('country_match',$this->country_match,true);
		$criteria->compare('country_code',$this->country_code,true);
		$criteria->compare('high_risk_country',$this->high_risk_country,true);
		$criteria->compare('distance',$this->distance);
		$criteria->compare('ip_region',$this->ip_region,true);
		$criteria->compare('ip_city',$this->ip_city,true);
		$criteria->compare('ip_latitude',$this->ip_latitude,true);
		$criteria->compare('ip_longitude',$this->ip_longitude,true);
		$criteria->compare('ip_isp',$this->ip_isp,true);
		$criteria->compare('ip_org',$this->ip_org,true);
		$criteria->compare('ip_asnum',$this->ip_asnum);
		$criteria->compare('ip_user_type',$this->ip_user_type,true);
		$criteria->compare('ip_country_confidence',$this->ip_country_confidence,true);
		$criteria->compare('ip_region_confidence',$this->ip_region_confidence,true);
		$criteria->compare('ip_city_confidence',$this->ip_city_confidence,true);
		$criteria->compare('ip_postal_confidence',$this->ip_postal_confidence,true);
		$criteria->compare('ip_postal_code',$this->ip_postal_code,true);
		$criteria->compare('ip_accuracy_radius',$this->ip_accuracy_radius);
		$criteria->compare('ip_net_speed_cell',$this->ip_net_speed_cell,true);
		$criteria->compare('ip_metro_code',$this->ip_metro_code);
		$criteria->compare('ip_area_code',$this->ip_area_code);
		$criteria->compare('ip_time_zone',$this->ip_time_zone,true);
		$criteria->compare('ip_region_name',$this->ip_region_name,true);
		$criteria->compare('ip_domain',$this->ip_domain,true);
		$criteria->compare('ip_country_name',$this->ip_country_name,true);
		$criteria->compare('ip_continent_code',$this->ip_continent_code,true);
		$criteria->compare('ip_corporate_proxy',$this->ip_corporate_proxy,true);
		$criteria->compare('anonymous_proxy',$this->anonymous_proxy,true);
		$criteria->compare('proxy_score',$this->proxy_score);
		$criteria->compare('is_trans_proxy',$this->is_trans_proxy,true);
		$criteria->compare('free_mail',$this->free_mail,true);
		$criteria->compare('carder_email',$this->carder_email,true);
		$criteria->compare('high_risk_username',$this->high_risk_username,true);
		$criteria->compare('high_risk_password',$this->high_risk_password,true);
		$criteria->compare('bin_match',$this->bin_match,true);
		$criteria->compare('bin_country',$this->bin_country,true);
		$criteria->compare('bin_name_match',$this->bin_name_match,true);
		$criteria->compare('bin_name',$this->bin_name,true);
		$criteria->compare('bin_phone_match',$this->bin_phone_match,true);
		$criteria->compare('bin_phone',$this->bin_phone,true);
		$criteria->compare('customer_phone_in_billing_location',$this->customer_phone_in_billing_location,true);
		$criteria->compare('ship_forward',$this->ship_forward,true);
		$criteria->compare('city_postal_match',$this->city_postal_match,true);
		$criteria->compare('ship_city_postal_match',$this->ship_city_postal_match,true);
		$criteria->compare('score',$this->score,true);
		$criteria->compare('explanation',$this->explanation,true);
		$criteria->compare('risk_score',$this->risk_score,true);
		$criteria->compare('queries_remaining',$this->queries_remaining);
		$criteria->compare('maxmind_id',$this->maxmind_id,true);
		$criteria->compare('error',$this->error,true);
		$criteria->compare('date_added',$this->date_added,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcOrderFraud the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
