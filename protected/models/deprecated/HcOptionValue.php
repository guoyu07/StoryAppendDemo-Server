<?php

/**
 * This is the model class for table "hc_option_value".
 *
 * The followings are the available columns in table 'hc_option_value':
 * @property integer $option_value_id
 * @property integer $option_id
 * @property string $image
 * @property string $special_code
 * @property string $languages
 * @property string $language_list_code
 * @property integer $sort_order
 */
class HcOptionValue extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_option_value';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('option_id, image, sort_order', 'required'),
			array('option_id, sort_order', 'numerical', 'integerOnly'=>true),
			array('image', 'length', 'max'=>255),
			array('special_code', 'length', 'max'=>64),
			array('languages, language_list_code', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('option_value_id, option_id, image, special_code, languages, language_list_code, sort_order', 'safe', 'on'=>'search'),
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
			'option_value_id' => 'Option Value',
			'option_id' => 'Option',
			'image' => 'Image',
			'special_code' => 'Special Code',
			'languages' => 'Languages',
			'language_list_code' => 'Language List Code',
			'sort_order' => 'Sort Order',
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

		$criteria->compare('option_value_id',$this->option_value_id);
		$criteria->compare('option_id',$this->option_id);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('special_code',$this->special_code,true);
		$criteria->compare('languages',$this->languages,true);
		$criteria->compare('language_list_code',$this->language_list_code,true);
		$criteria->compare('sort_order',$this->sort_order);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcOptionValue the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
