<?php

/**
 * This is the model class for table "ht_manufacturer_contacts".
 *
 * The followings are the available columns in table 'ht_manufacturer_contacts':
 * @property integer $contact_id
 * @property integer $manufacturer_id
 * @property string $en_name
 * @property string $cn_name
 * @property string $email
 * @property string $position
 * @property string $telephone
 * @property string $mobilephone
 * @property string $qq
 * @property string $wechat
 * @property string $skype
 * @property string $work_time
 * @property string $comments
 */
class HtManufacturerContacts extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_manufacturer_contacts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('manufacturer_id, en_name, cn_name', 'required'),
			array('manufacturer_id', 'numerical', 'integerOnly'=>true),
			array('en_name, cn_name, position, qq, wechat, skype', 'length', 'max'=>64),
			array('email', 'length', 'max'=>96),
			array('telephone, mobilephone, work_time', 'length', 'max'=>32),
			array('comments', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('contact_id, manufacturer_id, en_name, cn_name, email, position, telephone, mobilephone, qq, wechat, skype, work_time, comments', 'safe', 'on'=>'search'),
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
			'contact_id' => 'Contact',
			'manufacturer_id' => 'Manufacturer',
			'en_name' => 'En Name',
			'cn_name' => 'Cn Name',
			'email' => 'Email',
			'position' => 'Position',
			'telephone' => 'Telephone',
			'mobilephone' => 'Mobilephone',
			'qq' => 'Qq',
			'wechat' => 'Wechat',
			'skype' => 'Skype',
			'work_time' => 'Work Time',
			'comments' => 'Comments',
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

		$criteria->compare('contact_id',$this->contact_id);
		$criteria->compare('manufacturer_id',$this->manufacturer_id);
		$criteria->compare('en_name',$this->en_name,true);
		$criteria->compare('cn_name',$this->cn_name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('position',$this->position,true);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('mobilephone',$this->mobilephone,true);
		$criteria->compare('qq',$this->qq,true);
		$criteria->compare('wechat',$this->wechat,true);
		$criteria->compare('skype',$this->skype,true);
		$criteria->compare('work_time',$this->work_time,true);
		$criteria->compare('comments',$this->comments,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtManufacturerContacts the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
