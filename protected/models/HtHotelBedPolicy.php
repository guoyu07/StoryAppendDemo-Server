<?php

/**
 * This is the model class for table "ht_hotel_bed_policy".
 *
 * The followings are the available columns in table 'ht_hotel_bed_policy':
 * @property integer $policy_id
 * @property string $age_range
 * @property string $policy
 * @property integer $room_type_id
 */
class HtHotelBedPolicy extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_hotel_bed_policy';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('age_range, policy, room_type_id', 'required'),
			array('room_type_id', 'numerical', 'integerOnly'=>true),
			array('age_range', 'length', 'max'=>16),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('policy_id, age_range, policy, room_type_id', 'safe', 'on'=>'search'),
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
			'policy_id' => 'Policy',
			'age_range' => 'Age Range',
			'policy' => 'Policy',
			'room_type_id' => 'Room Type',
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

		$criteria->compare('policy_id',$this->policy_id);
		$criteria->compare('age_range',$this->age_range,true);
		$criteria->compare('policy',$this->policy,true);
		$criteria->compare('room_type_id',$this->room_type_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtHotelBedPolicy the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope()
    {
        return array('alias' => 'bp',
        'order'=>'bp.age_range DESC');
    }
}
