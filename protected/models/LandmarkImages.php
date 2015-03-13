<?php

/**
 * This is the model class for table "landmark_images".
 *
 * The followings are the available columns in table 'landmark_images':
 * @property integer $image_id
 * @property integer $landmark_id
 * @property string $image_url
 * @property string $image_src_url
 * @property integer $insert_time
 * @property string $author
 * @property integer $created_time
 * @property string $location
 * @property string $caption
 * @property integer $distance
 */
class LandmarkImages extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'landmark_images';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('landmark_id, image_url, image_src_url, insert_time, author, created_time', 'required'),
			array('landmark_id, insert_time, created_time, distance', 'numerical', 'integerOnly'=>true),
			array('image_url, image_src_url, location, caption', 'length', 'max'=>255),
			array('author', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('image_id, landmark_id, image_url, image_src_url, insert_time, author, created_time, location, caption, distance', 'safe', 'on'=>'search'),
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
			'image_id' => 'Image',
			'landmark_id' => 'Landmark',
			'image_url' => 'Image Url',
			'image_src_url' => 'Image Src Url',
			'insert_time' => 'Insert Time',
			'author' => 'Author',
			'created_time' => 'Created Time',
			'location' => 'Location',
			'caption' => 'Caption',
			'distance' => 'Distance',
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

		$criteria->compare('image_id',$this->image_id);
		$criteria->compare('landmark_id',$this->landmark_id);
		$criteria->compare('image_url',$this->image_url,true);
		$criteria->compare('image_src_url',$this->image_src_url,true);
		$criteria->compare('insert_time',$this->insert_time);
		$criteria->compare('author',$this->author,true);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('location',$this->location,true);
		$criteria->compare('caption',$this->caption,true);
		$criteria->compare('distance',$this->distance);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return LandmarkImages the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
