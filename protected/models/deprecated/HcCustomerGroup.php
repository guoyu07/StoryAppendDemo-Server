<?php

/**
 * This is the model class for table "hc_customer_group".
 *
 * The followings are the available columns in table 'hc_customer_group':
 * @property integer $customer_group_id
 * @property integer $approval
 * @property integer $company_id_display
 * @property integer $company_id_required
 * @property integer $tax_id_display
 * @property integer $tax_id_required
 * @property integer $sort_order
 */
class HcCustomerGroup extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_customer_group';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('approval, company_id_display, company_id_required, tax_id_display, tax_id_required, sort_order', 'required'),
			array('approval, company_id_display, company_id_required, tax_id_display, tax_id_required, sort_order', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('customer_group_id, approval, company_id_display, company_id_required, tax_id_display, tax_id_required, sort_order', 'safe', 'on'=>'search'),
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
			'customer_group_id' => 'Customer Group',
			'approval' => 'Approval',
			'company_id_display' => 'Company Id Display',
			'company_id_required' => 'Company Id Required',
			'tax_id_display' => 'Tax Id Display',
			'tax_id_required' => 'Tax Id Required',
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

		$criteria->compare('customer_group_id',$this->customer_group_id);
		$criteria->compare('approval',$this->approval);
		$criteria->compare('company_id_display',$this->company_id_display);
		$criteria->compare('company_id_required',$this->company_id_required);
		$criteria->compare('tax_id_display',$this->tax_id_display);
		$criteria->compare('tax_id_required',$this->tax_id_required);
		$criteria->compare('sort_order',$this->sort_order);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcCustomerGroup the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
