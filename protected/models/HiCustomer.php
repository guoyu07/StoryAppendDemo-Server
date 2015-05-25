<?php

/**
 * This is the model class for table "hi_customer".
 *
 * The followings are the available columns in table 'hi_customer':
 * @property string $customer_id
 * @property string $screen_name
 * @property string $email
 * @property string $avatar_url
 * @property integer $sex
 * @property string $city
 * @property string $birthday
 * @property string $ouid
 * @property string $token
 * @property integer $bind_third
 */
class HiCustomer extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hi_customer';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sex, bind_third', 'numerical', 'integerOnly'=>true),
			array('screen_name, city, birthday', 'length', 'max'=>32),
			array('email', 'length', 'max'=>96),
			array('avatar_url, ouid, token', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('customer_id, screen_name, email, avatar_url, sex, city, birthday, ouid, token, bind_third', 'safe', 'on'=>'search'),
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
			'customer_id' => 'Customer',
			'screen_name' => 'Screen Name',
			'email' => 'Email',
			'avatar_url' => 'Avatar Url',
			'sex' => 'Sex',
			'city' => 'City',
			'birthday' => 'Birthday',
			'ouid' => 'Ouid',
			'token' => 'Token',
			'bind_third' => 'Bind Third',
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

		$criteria->compare('customer_id',$this->customer_id,true);
		$criteria->compare('screen_name',$this->screen_name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('avatar_url',$this->avatar_url,true);
		$criteria->compare('sex',$this->sex);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('birthday',$this->birthday,true);
		$criteria->compare('ouid',$this->ouid,true);
		$criteria->compare('token',$this->token,true);
		$criteria->compare('bind_third',$this->bind_third);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HiCustomer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope()
    {
        return array(
            'alias' => 'c',
            'order' => 'c.customer_id DESC'
        );
    }
}
