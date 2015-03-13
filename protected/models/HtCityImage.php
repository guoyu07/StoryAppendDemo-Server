<?php

/**
 * This is the model class for table "ht_city_image".
 *
 * The followings are the available columns in table 'ht_city_image':
 * @property string $city_code
 * @property string $banner_image_url
 * @property string $grid_image_url
 */
class HtCityImage extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtCityImage the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_city_image';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_code, banner_image_url, grid_image_url, app_image_url', 'app_strip_image_url', 'required'),
            array('city_code', 'length', 'max'=>4),
            array('banner_image_url, grid_image_url, app_image_url', 'app_strip_image_url', 'length', 'max'=>255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('city_code, banner_image_url, grid_image_url, app_image_url', 'app_strip_image_url', 'safe', 'on'=>'search'),
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
            'city_code' => 'City Code',
            'banner_image_url' => 'Banner Image Url',
            'grid_image_url' => 'Grid Image Url',
            'app_image_url' => 'App Image Url',
            'app_strip_image_url' => 'App Strip Image Url',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('city_code',$this->city_code,true);
        $criteria->compare('banner_image_url',$this->banner_image_url,true);
        $criteria->compare('grid_image_url',$this->grid_image_url,true);
        $criteria->compare('app_image_url',$this->grid_image_url,true);
        $criteria->compare('app_strip_image_url',$this->grid_image_url,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    protected function beforeSave()
    {
        HtCity::clearCache('','', $this->city_code);
        return parent::beforeSave();
    }

    public function getGridImageUrl($city_code) {
        $city_image = $this->findByPk($city_code);
        if(empty($city_image)) {
            return '';
        }
        return $city_image['grid_image_url'];
    }
}