<?php

/**
 * This is the model class for table "ht_product_hotel".
 *
 * The followings are the available columns in table 'ht_product_hotel':
 * @property integer $product_id
 * @property string $location
 * @property string $address_zh
 * @property string $address_en
 * @property string $latlng
 * @property string $star_level
 * @property string $highlight
 * @property string $facilities
 * @property string $food_service
 * @property string $parking_lot
 * @property string $check_in_time
 * @property string $check_out_time
 */
class HtProductHotel extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_product_hotel';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, location, address_zh, address_en, highlight, facilities, food_service, parking_lot, check_in_time, check_out_time', 'required'),
			array('product_id, star_level', 'numerical', 'integerOnly'=>true),
			array('location, check_in_time, check_out_time', 'length', 'max'=>50),
			array('address_zh, address_en', 'length', 'max'=>500),
			array('latlng, facilities, food_service, parking_lot', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_id, location, address_zh, address_en, latlng, star_level, highlight, facilities, food_service, parking_lot, check_in_time, check_out_time', 'safe', 'on'=>'search'),
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
            'rates' => array(self::HAS_MANY, 'HtProductHotelRate', '', 'on' => 'phr.product_id= ph.product_id'),
            'bankcards' => array(self::HAS_MANY, 'HtProductHotelBankcard', '', 'on' => 'phb.product_id= ph.product_id'),
            'room_types' => array(self::HAS_MANY, 'HtHotelRoomType', '', 'on' => 'rt.product_id= ph.product_id'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'product_id' => 'Product',
			'location' => 'Location',
			'address_zh' => 'Address Zh',
            'address_en' => 'Address En',
			'latlng' => 'Latlng',
            'star_level' => 'Star Level',
			'highlight' => 'Highlight',
			'facilities' => 'Facilities',
			'food_service' => 'Food Service',
			'parking_lot' => 'Parking Lot',
			'check_in_time' => 'Check In Time',
			'check_out_time' => 'Check Out Time',
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

		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('location',$this->location,true);
		$criteria->compare('address_zh',$this->address_zh,true);
        $criteria->compare('address_en',$this->address_en,true);
		$criteria->compare('latlng',$this->latlng,true);
        $criteria->compare('star_level',$this->star_level,true);
		$criteria->compare('highlight',$this->highlight,true);
		$criteria->compare('facilities',$this->facilities,true);
		$criteria->compare('food_service',$this->food_service,true);
		$criteria->compare('parking_lot',$this->parking_lot,true);
		$criteria->compare('check_in_time',$this->check_in_time,true);
		$criteria->compare('check_out_time',$this->check_out_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtProductHotel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope()
    {
        return array('alias' => 'ph');
    }
}
