<?php

/**
 * This is the model class for table "ht_product_hotel_service_item".
 *
 * The followings are the available columns in table 'ht_product_hotel_service_item':
 * @property integer $service_id
 * @property string $name
 * @property integer $is_free
 * @property integer $need_additional_info
 * @property string $comment
 * @property integer $display_order
 */
class HtProductHotelServiceItem extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_hotel_service_item';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array('is_free, need_additional_info, display_order', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 60),
            array('comment', 'length', 'max' => 100),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('service_id, name, is_free, need_additional_info, comment, display_order', 'safe', 'on' => 'search'),
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
            'service_id' => '服务',
            'name' => '服务名称',
            'is_free' => '1：免费；0：付费',
            'need_additional_info' => '是否需要额外信息，0：不需要，1：需要',
            'comment' => '备注',
            'display_order' => '显示顺序',
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

        $criteria->compare('service_id', $this->service_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('is_free', $this->is_free);
        $criteria->compare('need_additional_info', $this->need_additional_info);
        $criteria->compare('comment', $this->comment, true);
        $criteria->compare('display_order', $this->display_order);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductHotelServiceItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array('alias' => 'phsi',
            'order' => 'phsi.display_order');
    }

}
