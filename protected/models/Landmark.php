<?php

/**
 * This is the model class for table "landmark".
 *
 * The followings are the available columns in table 'landmark':
 * @property integer $landmark_id
 * @property integer $city_id
 * @property string $name
 * @property string $en_name
 * @property string $location
 * @property string $price
 * @property string $phone
 * @property string $baike_url
 * @property integer $uid
 * @property integer $source
 * @property string $description
 * @property string $address
 * @property integer $insert_time
 * @property integer $images_insert_time
 * @property string $place_detail
 * @property string $open_time
 * @property string $close_time
 * @property string $website
 * @property string $video_link
 * @property string $reminders
 * @property string $communication
 * @property string $highlight
 */
class Landmark extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'landmark';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_id, name, en_name, location, price, phone, baike_url, uid, source, description, address, insert_time, open_time, close_time, website, video_link, reminders, communication, highlight', 'required'),
			array('city_id, uid, source, insert_time, images_insert_time', 'numerical', 'integerOnly'=>true),
			array('name, en_name', 'length', 'max'=>128),
			array('location', 'length', 'max'=>32),
			array('price, phone', 'length', 'max'=>64),
			array('baike_url, open_time, close_time, website, video_link, communication', 'length', 'max'=>255),
			array('reminders, highlight', 'length', 'max'=>1024),
			array('place_detail', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('landmark_id, city_id, name, en_name, location, price, phone, baike_url, uid, source, description, address, insert_time, images_insert_time, place_detail, open_time, close_time, website, video_link, reminders, communication, highlight', 'safe', 'on'=>'search'),
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
			'landmark_id' => 'Landmark',
			'city_id' => 'City',
			'name' => 'Name',
			'en_name' => 'En Name',
			'location' => 'Location',
			'price' => 'Price',
			'phone' => 'Phone',
			'baike_url' => 'Baike Url',
			'uid' => 'Uid',
			'source' => 'Source',
			'description' => 'Description',
			'address' => 'Address',
			'insert_time' => 'Insert Time',
			'images_insert_time' => 'Images Insert Time',
			'place_detail' => 'Place Detail',
			'open_time' => 'Open Time',
			'close_time' => 'Close Time',
			'website' => 'Website',
			'video_link' => 'Video Link',
			'reminders' => 'Reminders',
			'communication' => 'Communication',
			'highlight' => 'Highlight',
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

		$criteria->compare('landmark_id',$this->landmark_id);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('en_name',$this->en_name,true);
		$criteria->compare('location',$this->location,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('baike_url',$this->baike_url,true);
		$criteria->compare('uid',$this->uid);
		$criteria->compare('source',$this->source);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('insert_time',$this->insert_time);
		$criteria->compare('images_insert_time',$this->images_insert_time);
		$criteria->compare('place_detail',$this->place_detail,true);
		$criteria->compare('open_time',$this->open_time,true);
		$criteria->compare('close_time',$this->close_time,true);
		$criteria->compare('website',$this->website,true);
		$criteria->compare('video_link',$this->video_link,true);
		$criteria->compare('reminders',$this->reminders,true);
		$criteria->compare('communication',$this->communication,true);
		$criteria->compare('highlight',$this->highlight,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Landmark the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
