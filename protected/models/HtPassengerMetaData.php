<?php

/**
 * This is the model class for table "ht_passenger_meta_data".
 *
 * The followings are the available columns in table 'ht_passenger_meta_data':
 * @property integer $id
 * @property string $label
 * @property string $en_label
 * @property string $hint
 * @property string $input_type
 * @property string $input_sub_type
 * @property string $range
 * @property string $default_value
 * @property string $regex
 * @property string $storage_field
 * @property string $group_title
 * @property integer $group_order
 * @property integer $display_order
 */
class HtPassengerMetaData extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_passenger_meta_data';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('label, input_type, range, regex, storage_field, group_order', 'required'),
			array('group_order, display_order', 'numerical', 'integerOnly'=>true),
			array('label, en_label, hint, group_title', 'length', 'max'=>32),
			array('input_type, input_sub_type', 'length', 'max'=>16),
			array('default_value', 'length', 'max'=>128),
			array('regex', 'length', 'max'=>255),
			array('storage_field', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, label, en_label, hint, input_type, input_sub_type, range, default_value, regex, storage_field, group_title, group_order, display_order', 'safe', 'on'=>'search'),
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
			'label' => 'Label',
			'en_label' => 'En Label',
			'hint' => 'Hint',
			'input_type' => 'Input Type',
            'input_sub_type' => 'Input Sub Type',
			'range' => 'Range',
			'default_value' => 'Default Value',
			'regex' => 'Regex',
			'storage_field' => 'Storage Field',
			'group_title' => 'Group Title',
			'group_order' => 'Group Order',
			'display_order' => 'Display Order',
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
		$criteria->compare('label',$this->label,true);
		$criteria->compare('en_label',$this->en_label,true);
		$criteria->compare('hint',$this->hint,true);
		$criteria->compare('input_type',$this->input_type,true);
        $criteria->compare('input_sub_type',$this->input_sub_type,true);
		$criteria->compare('range',$this->range,true);
		$criteria->compare('default_value',$this->default_value,true);
		$criteria->compare('regex',$this->regex,true);
		$criteria->compare('storage_field',$this->storage_field,true);
		$criteria->compare('group_title',$this->group_title,true);
		$criteria->compare('group_order',$this->group_order);
		$criteria->compare('display_order',$this->display_order);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtPassengerMetaData the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope(){
        return array('alias'=>'pmeta','order'=>'pmeta.display_order');
    }
}
