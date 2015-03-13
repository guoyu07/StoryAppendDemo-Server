<?php

/**
 * This is the model class for table "ht_product_hotel_service".
 *
 * The followings are the available columns in table 'ht_product_hotel_service':
 * @property integer $product_id
 * @property integer $room_type_id
 * @property integer $service_id
 * @property string $service_info
 */
class HtProductHotelService extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_hotel_service';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, room_type_id', 'required'),
            array('product_id, room_type_id, service_id', 'numerical', 'integerOnly' => true),
            array('service_info', 'length', 'max' => 50),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('product_id, room_type_id, service_id, service_info', 'safe', 'on' => 'search'),
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
            'item'=>array(self::BELONGS_TO,'HtProductHotelServiceItem','service_id','order'=>'display_order'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'product_id' => 'Product',
            'room_type_id' => 'Room Type ID',
            'service_id' => '服务号',
            'service_info' => '补充信息（比如收费情况）',
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
        $criteria->compare('room_type_id', $this->room_type_id);
        $criteria->compare('service_id', $this->service_id);
        $criteria->compare('service_info', $this->service_info, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductHotelService the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array('alias' => 'phs');
    }
}
