<?php

/**
 * This is the model class for table "hc_product_return_condition".
 *
 * The followings are the available columns in table 'hc_product_return_condition':
 * @property integer $product_id
 * @property integer $return_type
 * @property string $expire_date
 * @property string $offset
 * @property string $formula
 */
class HcProductReturnCondition extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_product_return_condition';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id', 'required'),
			array('product_id, return_type', 'numerical', 'integerOnly'=>true),
			array('offset', 'length', 'max'=>32),
			array('formula', 'length', 'max'=>128),
			array('expire_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_id, return_type, expire_date, offset, formula', 'safe', 'on'=>'search'),
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
			'product_id' => 'Product',
			'return_type' => 'Return Type',
			'expire_date' => 'Expire Date',
			'offset' => 'Offset',
			'formula' => 'Formula',
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

		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('return_type',$this->return_type);
		$criteria->compare('expire_date',$this->expire_date,true);
		$criteria->compare('offset',$this->offset,true);
		$criteria->compare('formula',$this->formula,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcProductReturnCondition the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
