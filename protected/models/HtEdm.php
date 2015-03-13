<?php

/**
 * This is the model class for table "ht_edm".
 *
 * The followings are the available columns in table 'ht_edm':
 * @property integer $edm_id
 * @property string $title
 * @property string $description
 * @property string $banner_image
 * @property string $small_title
 * @property string $title_link
 * @property string $name
 * @property string $date_update
 */
class HtEdm extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_edm';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, description, banner_image, date_update', 'required'),
			array('title', 'length', 'max'=>100),
			array('banner_image, name', 'length', 'max'=>255),
			array('small_title, title_link', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('edm_id, title, description, banner_image, small_title, title_link, name, date_update', 'safe', 'on'=>'search'),
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
            'groups' => array(self::HAS_MANY, 'HtEdmGroup', '' ,'on'=>'e.edm_id = g.edm_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'edm_id' => 'Edm',
			'title' => 'Title',
			'description' => 'Description',
			'banner_image' => 'Banner Image',
			'small_title' => 'Small Title',
			'title_link' => 'Title Link',
			'name' => 'Name',
			'date_update' => 'Date Update',
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

		$criteria->compare('edm_id',$this->edm_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('banner_image',$this->banner_image,true);
		$criteria->compare('small_title',$this->small_title,true);
		$criteria->compare('title_link',$this->title_link,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('date_update',$this->date_update,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtEdm the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope()
    {
        return array(
            'alias' => 'e',
        );
    }
}
