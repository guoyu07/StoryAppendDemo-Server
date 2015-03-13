<?php

/**
 * This is the model class for table "album".
 *
 * The followings are the available columns in table 'album':
 * @property integer $album_id
 * @property integer $city_id
 * @property integer $tag_id
 * @property integer $owner
 * @property integer $order
 * @property string $insert_time
 * @property integer $update_time
 * @property string $title
 * @property string $reason
 * @property string $description
 * @property string $cover
 * @property string $cover_src
 * @property string $remarks
 * @property integer $type
 */
class Album extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'album';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_id, tag_id, owner, order, insert_time, title, reason, cover, cover_src', 'required'),
			array('city_id, tag_id, owner, order, update_time, type', 'numerical', 'integerOnly'=>true),
			array('title, reason', 'length', 'max'=>255),
			array('description, remarks', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('album_id, city_id, tag_id, owner, order, insert_time, update_time, title, reason, description, cover, cover_src, remarks, type', 'safe', 'on'=>'search'),
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
            'album_info_ref'=>array(self::HAS_MANY,'AlbumInfoRef','album_id'),
            'landinfos'=>array(self::HAS_MANY,'Landinfo',array('info_id'=>'landinfo_id'),'through' => 'album_info_ref','order'=>'land_order'),
            'album_ci_ref'=>array(self::HAS_MANY,'AlbumCiRef','album_id'),
            'communications'=>array(self::HAS_MANY,'CommunicationInfo','ci_id','through' => 'album_ci_ref'),
            'additionals'=>array(self::HAS_MANY,'AlbumAdditionalInfo',array('album_id'=>'album_id')),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'album_id' => 'Album',
			'city_id' => 'City',
			'tag_id' => 'Tag',
			'owner' => 'Owner',
			'order' => 'Order',
			'insert_time' => 'Insert Time',
			'update_time' => 'Update Time',
			'title' => 'Title',
			'reason' => 'Reason',
			'description' => 'Description',
			'cover' => 'Cover',
			'cover_src' => 'Cover Src',
			'remarks' => 'Remarks',
			'type' => 'Type',
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

		$criteria->compare('album_id',$this->album_id);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('tag_id',$this->tag_id);
		$criteria->compare('owner',$this->owner);
		$criteria->compare('order',$this->order);
		$criteria->compare('insert_time',$this->insert_time,true);
		$criteria->compare('update_time',$this->update_time);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('cover',$this->cover,true);
		$criteria->compare('cover_src',$this->cover_src,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('type',$this->type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Album the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
