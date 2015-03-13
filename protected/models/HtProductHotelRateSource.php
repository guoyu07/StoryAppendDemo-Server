<?php

/**
 * This is the model class for table "ht_product_hotel_rate_source".
 *
 * The followings are the available columns in table 'ht_product_hotel_rate_source':
 * @property integer $source_id
 * @property string $name
 * @property string $website
 * @property string $logo
 */
class HtProductHotelRateSource extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_hotel_rate_source';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'length', 'max' => 60),
            array('website, logo', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('source_id, name, website, logo', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'source_id' => 'Source',
            'name' => '评价来源名称',
            'website' => '来源网站',
            'logo' => 'logo url',
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

        $criteria = new CDbCriteria;

        $criteria->compare('source_id', $this->source_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('website', $this->website, true);
        $criteria->compare('logo', $this->logo, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductHotelRateSource the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function defaultScope()
    {
        return array('alias' => 'phrs',
        'order' => 'phrs.source_id');
    }

}
