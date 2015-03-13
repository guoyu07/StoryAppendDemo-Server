<?php

/**
 * This is the model class for table "hc_layout_route".
 *
 * The followings are the available columns in table 'hc_layout_route':
 * @property integer $layout_route_id
 * @property integer $layout_id
 * @property integer $store_id
 * @property string $route
 */
class HcLayoutRoute extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_layout_route';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('layout_id, store_id, route', 'required'),
			array('layout_id, store_id', 'numerical', 'integerOnly'=>true),
			array('route', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('layout_route_id, layout_id, store_id, route', 'safe', 'on'=>'search'),
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
			'layout_route_id' => 'Layout Route',
			'layout_id' => 'Layout',
			'store_id' => 'Store',
			'route' => 'Route',
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

		$criteria->compare('layout_route_id',$this->layout_route_id);
		$criteria->compare('layout_id',$this->layout_id);
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('route',$this->route,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcLayoutRoute the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
