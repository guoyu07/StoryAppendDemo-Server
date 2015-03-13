<?php

/**
 * This is the model class for table "ht_product_trip_introduction".
 *
 * The followings are the available columns in table 'ht_product_trip_introduction':
 * @property integer $product_id
 * @property string $brief_author
 * @property string $brief_avatar
 * @property string $brief_title
 * @property string $brief_description
 * @property string $brief_image
 * @property string $brief_image_mobile
 * @property string $line_image
 * @property string $trip_intro_image
 * @property integer $status
 */
class HtProductTripIntroduction extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_trip_introduction';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id', 'required'),
            array('product_id, status', 'numerical', 'integerOnly' => true),
            array('brief_author', 'length', 'max' => 45),
            array('brief_avatar, brief_title, brief_image, brief_image_mobile, line_image, trip_intro_image', 'length', 'max' => 255),
            array('brief_description', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('product_id, brief_author, brief_avatar, brief_title, brief_description, brief_image, brief_image_mobile, line_image, trip_intro_image, status', 'safe', 'on' => 'search'),
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
            'product_id'         => 'Product',
            'brief_author'       => 'Brief Author',
            'brief_avatar'       => 'Brief Avatar',
            'brief_title'        => 'Brief Title',
            'brief_description'  => 'Brief Description',
            'brief_image'        => 'Brief Image',
            'brief_image_mobile' => 'Brief Image Mobile',
            'line_image'         => 'Line Image',
            'trip_intro_image'   => 'Trip Intro Image',
            'status'             => '状态；0：未启用；1：已启用',
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

        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('brief_author', $this->brief_author, true);
        $criteria->compare('brief_avatar', $this->brief_avatar, true);
        $criteria->compare('brief_title', $this->brief_title, true);
        $criteria->compare('brief_description', $this->brief_description, true);
        $criteria->compare('brief_image', $this->brief_image, true);
        $criteria->compare('brief_image_mobile', $this->brief_image_mobile, true);
        $criteria->compare('line_image', $this->line_image, true);
        $criteria->compare('trip_intro_image', $this->trip_intro_image, true);
        $criteria->compare('status', $this->status, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getTripIntroductionByProductId($product_id)
    {
        $result = HtProductTripIntroduction::model()->findByPk($product_id);
        $result = Converter::convertModelToArray($result);

        return $result;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductTripIntroduction the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
