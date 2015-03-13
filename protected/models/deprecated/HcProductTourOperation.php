<?php

/**
 * This is the model class for table "hc_product_tour_operation".
 *
 * The followings are the available columns in table 'hc_product_tour_operation':
 * @property integer $operation_id
 * @property integer $product_id
 * @property string $languages
 * @property string $commentary
 * @property string $from_date
 * @property string $to_date
 * @property string $sale_start
 * @property string $sale_range
 * @property integer $sale_range_type
 * @property string $frequency
 * @property string $override_text
 * @property integer $language_id
 */
class HcProductTourOperation extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_product_tour_operation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, languages, commentary, from_date, to_date, sale_range_type, frequency, override_text, language_id', 'required'),
			array('product_id, sale_range_type, language_id', 'numerical', 'integerOnly'=>true),
			array('commentary, frequency', 'length', 'max'=>32),
			array('sale_start', 'length', 'max'=>16),
			array('sale_range', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('operation_id, product_id, languages, commentary, from_date, to_date, sale_start, sale_range, sale_range_type, frequency, override_text, language_id', 'safe', 'on'=>'search'),
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
			'operation_id' => 'Operation',
			'product_id' => 'Product',
			'languages' => 'Languages',
			'commentary' => 'Commentary',
			'from_date' => 'From Date',
			'to_date' => 'To Date',
			'sale_start' => 'Sale Start',
			'sale_range' => 'Sale Range',
			'sale_range_type' => 'Sale Range Type',
			'frequency' => 'Frequency',
			'override_text' => 'Override Text',
			'language_id' => 'Language',
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

		$criteria->compare('operation_id',$this->operation_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('languages',$this->languages,true);
		$criteria->compare('commentary',$this->commentary,true);
		$criteria->compare('from_date',$this->from_date,true);
		$criteria->compare('to_date',$this->to_date,true);
		$criteria->compare('sale_start',$this->sale_start,true);
		$criteria->compare('sale_range',$this->sale_range,true);
		$criteria->compare('sale_range_type',$this->sale_range_type);
		$criteria->compare('frequency',$this->frequency,true);
		$criteria->compare('override_text',$this->override_text,true);
		$criteria->compare('language_id',$this->language_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcProductTourOperation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getGTAs() {
		$products = HcProduct::model()->findAll('manufacturer_id=11');
		$product_ids = array();
		foreach($products as $p) {
			array_push($product_ids, $p['product_id']);
		}

		$c = new CDbCriteria();
		$c->addInCondition('product_id', $product_ids);
		$c->order = 'product_id, from_date';
		return $this->findAll($c);
	}
}
