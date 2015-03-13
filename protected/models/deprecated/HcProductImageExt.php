<?php

/**
 * This is the model class for table "hc_product_image_ext".
 *
 * The followings are the available columns in table 'hc_product_image_ext':
 * @property integer $product_image_id
 * @property integer $product_id
 * @property string $image
 * @property string $image_url
 * @property integer $landinfo_id
 * @property integer $image_usage
 * @property integer $as_cover
 * @property integer $sort_order
 * @property string $name
 * @property string $short_desc
 * @property integer $changed
 */
class HcProductImageExt extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_product_image_ext';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, changed', 'required'),
			array('product_id, landinfo_id, image_usage, as_cover, sort_order, changed', 'numerical', 'integerOnly'=>true),
			array('image, image_url', 'length', 'max'=>255),
			array('name', 'length', 'max'=>64),
			array('short_desc', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_image_id, product_id, image, image_url, landinfo_id, image_usage, as_cover, sort_order, name, short_desc, changed', 'safe', 'on'=>'search'),
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
			'product_image_id' => 'Product Image',
			'product_id' => 'Product',
			'image' => 'Image',
			'image_url' => 'Image Url',
			'landinfo_id' => '使用其图片的景点',
			'image_usage' => '0：样张；1：本地轮播图；2：来自景点的轮播图',
			'as_cover' => '0：不是封面；1：是封面',
			'sort_order' => 'Sort Order',
			'name' => '图片名称',
			'short_desc' => '图片短描述',
			'changed' => '0：无变化；1：有变化；',
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

		$criteria->compare('product_image_id',$this->product_image_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('image_url',$this->image_url,true);
		$criteria->compare('landinfo_id',$this->landinfo_id);
		$criteria->compare('image_usage',$this->image_usage);
		$criteria->compare('as_cover',$this->as_cover);
		$criteria->compare('sort_order',$this->sort_order);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('short_desc',$this->short_desc,true);
		$criteria->compare('changed',$this->changed);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcProductImageExt the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getProductCover($product_id) {
		$c = new CDbCriteria();
		$c->addCondition('product_id=' . $product_id);
		$c->addCondition('as_cover=1');
		$item = HcProductImageExt::model()->find($c);
		if(!empty($item)) {
			$image_url = $item['image_url'];
			if($item['image_usage'] == 2) {
				// TODO get image by landinfo_id
				$landinfo = Landinfo::model()->findByPk($item['landinfo_id']);
				if(!empty($landinfo)) {
					$image_url = $landinfo['image_url'];
				}
			}
			return $image_url;
		}

		return '';
	}
}
