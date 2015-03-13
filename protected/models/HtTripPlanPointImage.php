<?php

/**
 * This is the model class for table "ht_trip_plan_point_image".
 *
 * The followings are the available columns in table 'ht_trip_plan_point_image':
 * @property integer $image_id
 * @property integer $point_id
 * @property string $image_url
 * @property string $title
 * @property string $description
 * @property integer $display_order
 */
class HtTripPlanPointImage extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_trip_plan_point_image';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('image_id, point_id', 'required'),
            array('image_id, point_id, display_order', 'numerical', 'integerOnly' => true),
            array('image_url', 'length', 'max' => 255),
            array('title', 'length', 'max' => 100),
            array('description', 'length', 'max' => 1000),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('image_id, point_id, image_url, title, description, display_order', 'safe', 'on' => 'search'),
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
            'image_id' => 'Image ID',
            'point_id' => 'Point ID',
            'image_url' => '图片',
            'title' => '标题',
            'description' => '描述',
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

        $criteria->compare('image_id', $this->image_id);
        $criteria->compare('point_id', $this->point_id);
        $criteria->compare('image_url', $this->image_url, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('display_order', $this->display_order, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtTripPlanPointImage the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array('alias' => 'tppi',
            'order' => 'tppi.display_order');
    }
}
