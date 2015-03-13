<?php

/**
 * This is the model class for table "hc_product_description".
 *
 * The followings are the available columns in table 'hc_product_description':
 * @property integer $product_id
 * @property integer $language_id
 * @property string $name
 * @property string $origin_name
 * @property string $slogan
 * @property string $summary
 * @property string $description
 * @property string $how_it_works
 * @property string $please_note
 * @property string $service_detail
 * @property string $close_dates
 * @property string $meta_description
 * @property string $meta_keyword
 * @property string $tag
 * @property string $service_include
 * @property string $service_include_md
 * @property string $how_it_works_md
 */
class HcProductDescription extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_product_description';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, language_id, name, origin_name, slogan, summary, description, how_it_works, please_note, service_detail, close_dates, meta_description, meta_keyword, tag, service_include', 'required'),
			array('product_id, language_id', 'numerical', 'integerOnly'=>true),
			array('name, origin_name, slogan, summary, meta_description, meta_keyword', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_id, language_id, name, origin_name, slogan, summary, description, how_it_works, please_note, service_detail, close_dates, meta_description, meta_keyword, tag, service_include', 'safe', 'on'=>'search'),
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
			'language_id' => 'Language',
			'name' => 'Name',
            'origin_name' => 'Origin Name',
			'slogan' => 'Slogan',
			'summary' => 'Summary',
			'description' => 'Description',
			'how_it_works' => 'How It Works',
			'please_note' => 'Please Note',
			'service_detail' => 'Service Detail',
			'close_dates' => 'Close Dates',
			'meta_description' => 'Meta Description',
			'meta_keyword' => 'Meta Keyword',
			'tag' => 'Tag',
			'service_include' => 'Service Include',
			'service_include_md' => 'Service Include Markdown',
			'how_it_works_md' => 'How It Works Markdown',
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
		$criteria->compare('language_id',$this->language_id);
		$criteria->compare('name',$this->name,true);
        $criteria->compare('origin_name',$this->origin_name,true);
		$criteria->compare('slogan',$this->slogan,true);
		$criteria->compare('summary',$this->summary,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('how_it_works',$this->how_it_works,true);
		$criteria->compare('please_note',$this->please_note,true);
		$criteria->compare('service_detail',$this->service_detail,true);
		$criteria->compare('close_dates',$this->close_dates,true);
		$criteria->compare('meta_description',$this->meta_description,true);
		$criteria->compare('meta_keyword',$this->meta_keyword,true);
		$criteria->compare('tag',$this->tag,true);
		$criteria->compare('service_include',$this->service_include,true);
		$criteria->compare('service_include_md',$this->service_include_md,true);
		$criteria->compare('how_it_works_md',$this->how_it_works_md,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcProductDescription the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
