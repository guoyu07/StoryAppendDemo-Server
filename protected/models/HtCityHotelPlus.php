<?php

/**
 * This is the model class for table "ht_city_hotel_plus".
 *
 * The followings are the available columns in table 'ht_city_hotel_plus':
 * @property string $city_code
 * @property integer $promotion_id
 * @property string $introduction_title
 * @property string $introduction_description
 * @property string $introduction_image
 * @property integer $status
 */
class HtCityHotelPlus extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_city_hotel_plus';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_code, promotion_id, introduction_title, introduction_description, introduction_image', 'required'),
            array('promotion_id, status', 'numerical', 'integerOnly' => true),
            array('city_code', 'length', 'max' => 4),
            array('introduction_title', 'length', 'max' => 45),
            array('introduction_description, introduction_image', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('city_code, promotion_id, introduction_title, introduction_description, introduction_image, status', 'safe', 'on' => 'search'),
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
            'city_code'                => 'City Code',
            'promotion_id'             => 'Promotion',
            'introduction_title'       => 'Introduction Title',
            'introduction_description' => 'Introduction Description',
            'introduction_image'       => 'Introduction Image',
            'status'                   => '状态，0：未启用；1：启用',
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

        $criteria->compare('city_code', $this->city_code, true);
        $criteria->compare('promotion_id', $this->promotion_id);
        $criteria->compare('introduction_title', $this->introduction_title, true);
        $criteria->compare('introduction_description', $this->introduction_description, true);
        $criteria->compare('introduction_image', $this->introduction_image, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtCityHotelPlus the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function isPromotionHotelplus($promotion_id)
    {
        $result = Converter::convertModelToArray(HtCityHotelPlus::model()->findAllByAttributes(['promotion_id' => $promotion_id]));

        return count($result) > 0;
    }

    public function getCityPromotion($city_code)
    {
        $result = Converter::convertModelToArray(HtCityHotelPlus::model()->findByPk($city_code));
        if (!empty($result) && $result['status'] == 1) {
            if (empty($result['introduction_image']) && !empty($result['promotion_id'])) {
                $promotion = Converter::convertModelToArray(HtPromotion::model()->findByPk($result['promotion_id']));
                if (!empty($promotion['image'])) {
                    $result['promotion_image'] = $promotion['image'];
                }
            }

            return $result;
        } else {
            return false;
        }
    }

}
