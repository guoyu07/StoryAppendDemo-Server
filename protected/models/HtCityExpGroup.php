<?php

/**
 * This is the model class for table "ht_city_exp_group".
 *
 * The followings are the available columns in table 'ht_city_exp_group':
 * @property integer $group_id
 * @property string $city_code
 * @property string $name
 * @property string $cover_image_url
 * @property integer $status
 * @property integer $display_order
 * @property integer $rel_article_id
 */
class HtCityExpGroup extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_city_exp_group';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_code, name, cover_image_url, status, display_order, rel_article_id', 'required'),
			array('status, display_order, rel_article_id', 'numerical', 'integerOnly'=>true),
			array('city_code', 'length', 'max'=>4),
			array('name', 'length', 'max'=>64),
			array('cover_image_url', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('group_id, city_code, name, cover_image_url, status, display_order, rel_article_id', 'safe', 'on'=>'search'),
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
			'group_id' => 'Group',
			'city_code' => 'City Code',
			'name' => 'Name',
			'cover_image_url' => 'Cover Image Url',
			'status' => 'Status',
			'display_order' => 'Display Order',
			'rel_article_id' => 'Rel Article',
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

		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('city_code',$this->city_code,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('cover_image_url',$this->cover_image_url,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('display_order',$this->display_order);
		$criteria->compare('rel_article_id',$this->rel_article_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtCityExpGroup the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope()
    {
        return array(
            'alias' => 'eg',
            'order' => 'eg.display_order ASC',
        );
    }

    public function readyToOnline()
    {
        if (trim($this->name) == '' || $this->cover_image_url == '' || $this->rel_article_id == '') {
            return array('code' => 400, 'msg' => '体验分组信息不完整。');
        }else{
            return array('code' => 200, 'msg' => 'OK');
        }
        //TODO:检测关联文章状态
    }
}
