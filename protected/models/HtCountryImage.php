<?php

/**
 * This is the model class for table "ht_country_image".
 *
 * The followings are the available columns in table 'ht_country_image':
 * @property string $country_code
 * @property string $cover_url
 * @property string $mobile_url
 */
class HtCountryImage extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtCountrySe the static model class
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
        return 'ht_country_image';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('country_code, cover_url', 'required'),
            array('country_code', 'length', 'max'=>4),
            array('cover_url, mobile_url', 'length', 'max'=>255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('country_code, cover_url', 'safe', 'on'=>'search'),
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
            'country_code' => 'Country Code',
            'cover_url' => 'Cover Url',
            'mobile_url' => 'Mobile Url',
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

        $criteria->compare('country_code',$this->country_code,true);
        $criteria->compare('cover_url',$this->cover_url,true);
        $criteria->compare('mobile_url',$this->mobile_url,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    protected function beforeSave()
    {
        HtCountry::clearCache($this->country_code);

        return parent::beforeSave();
    }
}