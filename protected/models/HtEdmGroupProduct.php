<?php

/**
 * This is the model class for table "ht_edm_group_product".
 *
 * The followings are the available columns in table 'ht_edm_group_product':
 * @property integer $group_id
 * @property integer $product_id
 * @property string $product_image
 * @property string $product_name
 * @property string $product_description
 * @property string $product_link
 * @property integer $display_order
 * @property integer $orig_price
 * @property integer $price
 */
class HtEdmGroupProduct extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_edm_group_product';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('group_id, product_id, product_image, product_name, product_description, product_link, display_order', 'required'),
			array('group_id, product_id, display_order, orig_price, price', 'numerical', 'integerOnly'=>true),
			array('product_image, product_name', 'length', 'max'=>255),
			array('product_link', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('group_id, product_id, product_image, product_name, product_description, product_link, display_order, orig_price, price', 'safe', 'on'=>'search'),
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
            'product' => array(self::HAS_MANY, 'HtProduct', '', 'on'=>'gp.product_id = p.product_id', 'order'=>'gp.display_order ASC')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'group_id' => 'Group',
			'product_id' => 'Product',
			'product_image' => 'Product Image',
			'product_name' => 'Product Name',
			'product_description' => 'Product Description',
			'product_link' => 'Product Link',
			'display_order' => 'Display Order',
			'orig_price' => 'Orig Price',
			'price' => 'Price',
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
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('product_image',$this->product_image,true);
		$criteria->compare('product_name',$this->product_name,true);
		$criteria->compare('product_description',$this->product_description,true);
		$criteria->compare('product_link',$this->product_link,true);
		$criteria->compare('display_order',$this->display_order);
		$criteria->compare('orig_price',$this->orig_price);
		$criteria->compare('price',$this->price);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtEdmGroupProduct the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope()
    {
        return array(
            'alias' => 'gp',
        );
    }
}
