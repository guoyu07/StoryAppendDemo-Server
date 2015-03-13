<?php

/**
 * This is the model class for table "landinfo".
 *
 * The followings are the available columns in table 'landinfo':
 * @property integer $landinfo_id
 * @property integer $landmark_id
 * @property integer $city_id
 * @property string $title
 * @property string $reason
 * @property string $description
 * @property integer $tag_id
 * @property integer $uid
 * @property string $source_url
 * @property string $image_url
 * @property string $image_local_url
 * @property string $image_src_url
 * @property string $location_map
 * @property string $location_latlng
 * @property integer $insert_time
 * @property integer $update_time
 * @property integer $need_time
 * @property integer $order
 * @property integer $is_del
 * @property string $price
 * @property string $child_price
 * @property string $age_range
 * @property string $child_age_range
 * @property string $currency
 * @property string $pass_benefit
 */
class Landinfo extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'landinfo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('landmark_id, city_id, title, reason, description, tag_id, uid, source_url, image_url, image_src_url, insert_time, update_time', 'required'),
				array('landmark_id, city_id, tag_id, uid, insert_time, update_time, need_time, order, is_del', 'numerical', 'integerOnly' => true),
				array('title, reason, location_map, pass_benefit', 'length', 'max' => 255),
				array('location_latlng', 'length', 'max' => 60),
				array('price, child_price', 'length', 'max' => 10),
				array('age_range, child_age_range', 'length', 'max' => 64),
				array('currency', 'length', 'max' => 3),
				array('image_local_url', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
				array('landinfo_id, landmark_id, city_id, title, reason, description, tag_id, uid, source_url, image_url, image_local_url, image_src_url, location_map, location_latlng, insert_time, update_time, need_time, order, is_del, price, child_price, age_range, child_age_range, currency, pass_benefit', 'safe', 'on' => 'search'),
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
				'landmark' => array(self::BELONGS_TO, 'Landmark', 'landmark_id','select'=>'name,en_name,address,open_time,close_time,communication,phone,price,website,description,highlight,location', 'joinType' => 'RIGHT JOIN'),
				'mark_image' => array(self::BELONGS_TO, 'Landmark', 'landmark_id','select'=>'name,en_name,address,open_time,phone,price,website,description,highlight,location'),
				'user' => array(self::BELONGS_TO, 'User', 'uid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
				'landinfo_id' => 'Landinfo',
				'landmark_id' => 'Landmark',
				'city_id' => 'City',
				'title' => 'Title',
				'reason' => 'Reason',
				'description' => 'Description',
				'tag_id' => 'Tag',
				'uid' => 'Uid',
				'source_url' => 'Source Url',
				'image_url' => 'Image Url',
				'image_local_url' => 'Image Local Url',
				'image_src_url' => 'Image Src Url',
				'location_map' => 'Location Map',
				'location_latlng' => 'Location Latlng',
				'insert_time' => 'Insert Time',
				'update_time' => 'Update Time',
				'need_time' => 'Need Time',
				'order' => 'Order',
				'is_del' => 'Is Del',
				'price' => 'Price',
				'child_price' => 'Child Price',
				'age_range' => 'Age Range',
				'child_age_range' => 'Child Age Range',
				'currency' => 'Currency',
				'pass_benefit' => 'Pass Benefit',
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

		$criteria->compare('landinfo_id', $this->landinfo_id);
		$criteria->compare('landmark_id', $this->landmark_id);
		$criteria->compare('city_id', $this->city_id);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('reason', $this->reason, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('tag_id', $this->tag_id);
		$criteria->compare('uid', $this->uid);
		$criteria->compare('source_url', $this->source_url, true);
		$criteria->compare('image_url', $this->image_url, true);
		$criteria->compare('image_local_url', $this->image_local_url, true);
		$criteria->compare('image_src_url', $this->image_src_url, true);
		$criteria->compare('location_map', $this->location_map, true);
		$criteria->compare('location_latlng', $this->location_latlng, true);
		$criteria->compare('insert_time', $this->insert_time);
		$criteria->compare('update_time', $this->update_time);
		$criteria->compare('need_time', $this->need_time);
		$criteria->compare('order', $this->order);
		$criteria->compare('is_del', $this->is_del);
		$criteria->compare('price', $this->price, true);
		$criteria->compare('child_price', $this->child_price, true);
		$criteria->compare('age_range', $this->age_range, true);
		$criteria->compare('child_age_range', $this->child_age_range, true);
		$criteria->compare('currency', $this->currency, true);
		$criteria->compare('pass_benefit', $this->pass_benefit, true);

		return new CActiveDataProvider($this, array(
				'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Landinfo the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function getLandinfo($landinfo_id)
	{
		$c = new CDbCriteria();
		$c->addCondition('landinfo_id=' . $landinfo_id);
		$info = Landinfo::model()->with('landmark')->find($c);

		$result = array(
				'landinfo_id' => $info['landinfo_id'],
				'name' => $info->landmark['name'],
				'en_name' => $info->landmark['en_name'],
				'address' => $info->landmark['address'],
				'reason' => $info['reason'],
				'image_url' => $info['image_url']
		);

		return $result;
	}

	public function getLandinfos($album_id)
	{
		$album_info_list = AlbumInfoRef::model()->findAll('album_id=' . $album_id);

		$info_ids = array();
		foreach ($album_info_list as $album_info) {
			array_push($info_ids, $album_info['info_id']);
		}

		$c = new CDbCriteria();
		$c->addInCondition('landinfo_id', $info_ids);
		$data = Landinfo::model()->with('landmark')->findAll($c);

		$result = array();
		foreach ($data as $info) {
			$result[] = array(
					'landinfo_id' => $info['landinfo_id'],
					'name' => $info->landmark['name'],
					'en_name' => $info->landmark['en_name'],
					'address' => $info->landmark['address'],
					'reason' => $info['reason'],
					'image_url' => $info['image_url'],
                    'price' => $info['price'],
                    'child_price' => $info['child_price'],
                    'description' => $info->landmark['description'],
                    'phone' => $info->landmark['phone'],
                    'website' => $info->landmark['website'],
                    'open_time' => $info->landmark['open_time'],
                    'highlight' => $info->landmark['highlight'],
                    'location' => $info->landmark['location'],
                    'pass_benefit' => $info['pass_benefit'],
                    'communication' => $info->landmark['communication'],
			);
		}

		return $result;
	}
}
