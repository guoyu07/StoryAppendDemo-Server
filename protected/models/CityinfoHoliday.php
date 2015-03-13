<?php

/**
 * This is the model class for table "cityinfo_holiday".
 *
 * The followings are the available columns in table 'cityinfo_holiday':
 * @property integer $cityinfo_id
 * @property integer $month
 * @property string $title
 * @property string $description
 * @property string $hdate
 * @property string $url
 */
class CityinfoHoliday extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cityinfo_holiday';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cityinfo_id, month, title, description, hdate, url', 'required'),
			array('cityinfo_id, month', 'numerical', 'integerOnly'=>true),
			array('title, url', 'length', 'max'=>255),
			array('hdate', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('cityinfo_id, month, title, description, hdate, url', 'safe', 'on'=>'search'),
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
			'cityinfo_id' => 'Cityinfo',
			'month' => 'Month',
			'title' => 'Title',
			'description' => 'Description',
			'hdate' => 'Hdate',
			'url' => 'Url',
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

		$criteria->compare('cityinfo_id',$this->cityinfo_id);
		$criteria->compare('month',$this->month);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('hdate',$this->hdate,true);
		$criteria->compare('url',$this->url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CityinfoHoliday the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
