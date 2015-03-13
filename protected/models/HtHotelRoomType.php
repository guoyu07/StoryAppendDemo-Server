<?php

/**
 * This is the model class for table "ht_hotel_room_type".
 *
 * The followings are the available columns in table 'ht_hotel_room_type':
 * @property integer $room_type_id
 * @property integer $product_id
 * @property string $name
 * @property string $area
 * @property string $highlight
 * @property string $price_include
 * @property string $facilities
 * @property string $bed_type
 * @property string $bed_size
 * @property string $second_bed_type
 * @property string $second_bed_size
 * @property integer $capacity
 * @property integer $max_capacity
 * @property string $special_code
 */
class HtHotelRoomType extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_hotel_room_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, name, area, highlight, price_include, facilities, bed_type, bed_size, second_bed_type, second_bed_size, capacity, max_capacity, special_code', 'required'),
			array('product_id, capacity, max_capacity', 'numerical', 'integerOnly'=>true),
			array('name, bed_type, bed_size', 'length', 'max'=>50),
            array('price_include', 'length', 'max'=>100),
			array('area', 'length', 'max'=>10),
			array('special_code', 'length', 'max'=>8),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('room_type_id, product_id, name, area, highlight, price_include, facilities, bed_type, bed_size, second_bed_type, second_bed_size, capacity, max_capacity, special_code', 'safe', 'on'=>'search'),
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
            'services'=>array(self::HAS_MANY,'HtProductHotelService','','on'=>'rt.room_type_id = phs.room_type_id AND rt.product_id = phs.product_id'),
            'images'=>array(self::HAS_MANY,'HtHotelRoomImage','','on'=>'rt.room_type_id = ri.room_type_id'),
            'policies'=>array(self::HAS_MANY,'HtHotelBedPolicy','','on'=>'rt.room_type_id = bp.room_type_id'),
            'special_info'=>array(self::HAS_ONE,'HtProductSpecialCode','','on'=>'rt.product_id = ps.product_id and rt.special_code = ps.special_code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'room_type_id' => 'Room Type',
			'product_id' => 'Product',
			'name' => 'Name',
			'area' => 'Area',
			'highlight' => 'Highlight',
            'price_include' => 'Price Include',
			'facilities' => 'Facilities',
			'bed_type' => 'Bed Type',
			'bed_size' => 'Bed Size',
            'second_bed_type' => 'Second Bed Type',
            'second_bed_size' => 'Second Bed Size',
			'capacity' => 'Capacity',
			'max_capacity' => 'Max Capacity',
			'special_code' => 'Special Code',
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

		$criteria->compare('room_type_id',$this->room_type_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('area',$this->area,true);
		$criteria->compare('highlight',$this->highlight,true);
        $criteria->compare('highlight',$this->price_include,true);
		$criteria->compare('facilities',$this->facilities,true);
		$criteria->compare('bed_type',$this->bed_type,true);
		$criteria->compare('bed_size',$this->bed_size,true);
        $criteria->compare('second_bed_type',$this->second_bed_type,true);
        $criteria->compare('second_bed_size',$this->second_bed_size,true);
		$criteria->compare('capacity',$this->capacity);
		$criteria->compare('max_capacity',$this->max_capacity);
		$criteria->compare('special_code',$this->special_code,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtHotelRoomType the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope()
    {
        return array('alias' => 'rt');
    }
}
