<?php

/**
 * This is the model class for table "citycover".
 *
 * The followings are the available columns in table 'citycover':
 * @property integer $citycover_id
 * @property integer $city_id
 * @property string $cover_title
 * @property string $cover_url
 * @property string $cover_src_url
 * @property integer $cover_order
 */
class Citycover extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'citycover';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_id, cover_url, cover_src_url', 'required'),
			array('city_id, cover_order', 'numerical', 'integerOnly'=>true),
			array('cover_title', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('citycover_id, city_id, cover_title, cover_url, cover_src_url, cover_order', 'safe', 'on'=>'search'),
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
			'citycover_id' => 'Citycover',
			'city_id' => 'City',
			'cover_title' => 'Cover Title',
			'cover_url' => 'Cover Url',
			'cover_src_url' => 'Cover Src Url',
			'cover_order' => 'Cover Order',
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

		$criteria->compare('citycover_id',$this->citycover_id);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('cover_title',$this->cover_title,true);
		$criteria->compare('cover_url',$this->cover_url,true);
		$criteria->compare('cover_src_url',$this->cover_src_url,true);
		$criteria->compare('cover_order',$this->cover_order);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Citycover the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
