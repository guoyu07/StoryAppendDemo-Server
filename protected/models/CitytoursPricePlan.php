<?php

/**
 * This is the model class for table "citytours_price_plan".
 *
 * The followings are the available columns in table 'citytours_price_plan':
 * @property string $city_code
 * @property string $item_id
 * @property string $special_code
 * @property string $special_title_en
 * @property string $special_title_zh
 * @property string $adult_price
 * @property string $adult_age_range
 * @property string $child_price
 * @property string $child_age_range
 */
class CitytoursPricePlan extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'citytours_price_plan';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_code', 'length', 'max'=>4),
			array('item_id, adult_age_range, child_age_range', 'length', 'max'=>16),
			array('special_code', 'length', 'max'=>32),
			array('special_title_en, special_title_zh', 'length', 'max'=>128),
			array('adult_price, child_price', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('city_code, item_id, special_code, special_title_en, special_title_zh, adult_price, adult_age_range, child_price, child_age_range', 'safe', 'on'=>'search'),
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
			'special_title_en' => 'Special Title En',
			'special_title_zh' => 'Special Title Zh',
			'adult_price' => 'Adult Price',
			'adult_age_range' => 'Adult Age Range',
			'child_price' => 'Child Price',
			'child_age_range' => 'Child Age Range',
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
		$criteria->compare('special_title_en',$this->special_title_en,true);
		$criteria->compare('special_title_zh',$this->special_title_zh,true);
		$criteria->compare('adult_price',$this->adult_price,true);
		$criteria->compare('adult_age_range',$this->adult_age_range,true);
		$criteria->compare('child_price',$this->child_price,true);
		$criteria->compare('child_age_range',$this->child_age_range,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CitytoursPricePlan the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
