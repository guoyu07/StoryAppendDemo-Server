<?php

/**
 * This is the model class for table "cityarea".
 *
 * The followings are the available columns in table 'cityarea':
 * @property integer $cityarea_id
 * @property integer $city_id
 * @property integer $type
 * @property string $create_time
 * @property string $name
 * @property string $enname
 * @property string $cover_url
 * @property string $cover_src_url
 * @property string $description
 * @property string $shape
 * @property string $shape_points
 * @property integer $shape_type
 */
class Cityarea extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cityarea';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_id, type, create_time, name, enname, description, shape, shape_points, shape_type', 'required'),
			array('city_id, type, shape_type', 'numerical', 'integerOnly'=>true),
			array('name, enname', 'length', 'max'=>64),
			array('cover_url, cover_src_url', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('cityarea_id, city_id, type, create_time, name, enname, cover_url, cover_src_url, description, shape, shape_points, shape_type', 'safe', 'on'=>'search'),
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
			'cityarea_id' => 'Cityarea',
			'city_id' => 'City',
			'type' => 'Type',
			'create_time' => 'Create Time',
			'name' => 'Name',
			'enname' => 'Enname',
			'cover_url' => 'Cover Url',
			'cover_src_url' => 'Cover Src Url',
			'description' => 'Description',
			'shape' => 'Shape',
			'shape_points' => 'Shape Points',
			'shape_type' => 'Shape Type',
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

		$criteria->compare('cityarea_id',$this->cityarea_id);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('enname',$this->enname,true);
		$criteria->compare('cover_url',$this->cover_url,true);
		$criteria->compare('cover_src_url',$this->cover_src_url,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('shape',$this->shape,true);
		$criteria->compare('shape_points',$this->shape_points,true);
		$criteria->compare('shape_type',$this->shape_type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Cityarea the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
