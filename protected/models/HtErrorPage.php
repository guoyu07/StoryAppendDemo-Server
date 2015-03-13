<?php

/**
 * This is the model class for table "ht_error_page".
 *
 * The followings are the available columns in table 'ht_error_page':
 * @property integer $error_page_id
 * @property integer $product_id
 * @property string $product_name
 * @property string $product_description
 * @property string $error_description
 * @property string $bg_image_url
 * @property string $mobile_image_url
 * @property string $city_code
 * @property string $country_code
 * @property integer $status
 */
class HtErrorPage extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_error_page';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('error_page_id, product_id, product_name, product_description, error_description, bg_image_url, mobile_image_url, city_code, country_code, status', 'required'),
			array('error_page_id, product_id, status', 'numerical', 'integerOnly'=>true),
			array('product_name, city_code, country_code', 'length', 'max'=>45),
			array('product_description, error_description, bg_image_url, mobile_image_url', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('error_page_id, product_id, product_name, product_description, error_description, bg_image_url, mobile_image_url, city_code, country_code, status', 'safe', 'on'=>'search'),
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
            'city' => array(self::HAS_ONE, 'HtCity', '', 'on' => 'errorPage.city_code = city.city_code'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'error_page_id' => 'Error Page',
			'product_id' => 'Product',
			'product_name' => 'Product Name',
			'product_description' => 'Product Description',
			'error_description' => 'Error Description',
			'bg_image_url' => 'Bg Image Url',
			'mobile_image_url' => 'Mobile Image Url',
			'city_code' => 'City Code',
			'country_code' => 'Country Code',
			'status' => '0表示未启用，1表示启用',
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

		$criteria->compare('error_page_id',$this->error_page_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('product_name',$this->product_name,true);
		$criteria->compare('product_description',$this->product_description,true);
		$criteria->compare('error_description',$this->error_description,true);
		$criteria->compare('bg_image_url',$this->bg_image_url,true);
		$criteria->compare('mobile_image_url',$this->mobile_image_url,true);
		$criteria->compare('city_code',$this->city_code,true);
		$criteria->compare('country_code',$this->country_code,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtErrorPage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope()
    {
        return array(
            'alias' => 'errorPage',
        );
    }

    public function getRandomErrorPage($error_page_id = 0)
    {
        if(!$error_page_id){
            $page_count = $this->count('status = 1');
            $error_pages = $this->with('city')->findAll('status = 1');
            $error_pages = Converter::convertModelToArray($error_pages);
            $rand = rand(0,$page_count-1);
            $error_page = $error_pages[$rand];
        }else{
            $error_page = $this->with('city')->findByPk($error_page_id);
            $error_page = Converter::convertModelToArray($error_page);
        }

        $error_page['target_name'] = $error_page['city']['cn_name'];
        $error_page['error_slogan'] = $error_page['error_description'];
        $error_page['image_url'] = $error_page['bg_image_url'];
        $error_page['target_product'] = array(
            'name' => $error_page['product_name'],
            'location' => $error_page['city']['country_cn_name'].$error_page['city']['cn_name'],
            'description' => $error_page['product_description'],
        );
        return $error_page;
    }
}
