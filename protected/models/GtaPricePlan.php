<?php

/**
 * This is the model class for table "gta_price_plan".
 *
 * The followings are the available columns in table 'gta_price_plan':
 * @property string $city_code
 * @property string $item_id
 * @property string $special_code
 * @property string $languages
 * @property string $language_list_code
 * @property string $confirmation
 * @property string $tour_date
 * @property string $price
 * @property integer $child_age
 * @property string $currency
 * @property string $search_time
 * @property integer $adult_num
 * @property integer $child_num
 */
class GtaPricePlan extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gta_price_plan';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_code, tour_date, currency, search_time', 'required'),
			array('child_age, adult_num, child_num', 'numerical', 'integerOnly'=>true),
			array('city_code', 'length', 'max'=>4),
			array('item_id', 'length', 'max'=>16),
			array('special_code, languages, language_list_code', 'length', 'max'=>32),
			array('confirmation', 'length', 'max'=>6),
			array('price', 'length', 'max'=>10),
			array('currency', 'length', 'max'=>3),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('city_code, item_id, special_code, languages, language_list_code, confirmation, tour_date, price, child_age, currency, search_time, adult_num, child_num', 'safe', 'on'=>'search'),
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
			'city_code' => 'City Code',
			'item_id' => 'Item',
			'special_code' => 'Special Code',
			'languages' => 'Languages',
			'language_list_code' => 'Language List Code',
			'confirmation' => 'Confirmation',
			'tour_date' => 'Tour Date',
			'price' => 'Price',
			'child_age' => 'Child Age',
			'currency' => 'Currency',
			'search_time' => 'Search Time',
			'adult_num' => 'Adult Num',
			'child_num' => 'Child Num',
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

		$criteria->compare('city_code',$this->city_code,true);
		$criteria->compare('item_id',$this->item_id,true);
		$criteria->compare('special_code',$this->special_code,true);
		$criteria->compare('languages',$this->languages,true);
		$criteria->compare('language_list_code',$this->language_list_code,true);
		$criteria->compare('confirmation',$this->confirmation,true);
		$criteria->compare('tour_date',$this->tour_date,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('child_age',$this->child_age);
		$criteria->compare('currency',$this->currency,true);
		$criteria->compare('search_time',$this->search_time,true);
		$criteria->compare('adult_num',$this->adult_num);
		$criteria->compare('child_num',$this->child_num);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GtaPricePlan the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
