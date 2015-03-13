<?php

/**
 * This is the model class for table "ht_trip_highlight_ref".
 *
 * The followings are the available columns in table 'ht_trip_highlight_ref':
 * @property integer $id
 * @property integer $highlight_id
 * @property string $date
 * @property string $location
 * @property string $local_highlight
 * @property string $lodging
 */
class HtTripHighlightRef extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_trip_highlight_ref';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('highlight_id, local_highlight', 'required'),
			array('highlight_id', 'numerical', 'integerOnly'=>true),
			array('date', 'length', 'max'=>45),
			array('location, lodging', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, highlight_id, date, location, local_highlight, lodging', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'highlight_id' => 'Highlight',
			'date' => 'Date',
			'location' => 'Location',
			'local_highlight' => 'Local Highlight',
			'lodging' => 'Lodging',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('highlight_id',$this->highlight_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('location',$this->location,true);
		$criteria->compare('local_highlight',$this->local_highlight,true);
		$criteria->compare('lodging',$this->lodging,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtTripHighlightRef the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
