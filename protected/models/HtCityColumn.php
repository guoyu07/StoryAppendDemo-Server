<?php

/**
 * This is the model class for table "ht_city_column".
 *
 * The followings are the available columns in table 'ht_city_column':
 * @property integer $column_id
 * @property integer $type
 * @property string $city_code
 * @property string $name
 * @property string $description
 * @property string $cover_image_url
 * @property integer $status
 * @property integer $display_order
 */
class HtCityColumn extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_city_column';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_code, name, description, cover_image_url, status, display_order', 'required'),
            array('type, status, display_order', 'numerical', 'integerOnly' => true),
            array('city_code', 'length', 'max' => 4),
            array('name', 'length', 'max' => 64),
            array('description, cover_image_url', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('column_id, type, city_code, name, description, cover_image_url, status, display_order', 'safe', 'on' => 'search'),
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
            'columns' => array(self::HAS_MANY, 'HtCityColumnRef', '', 'on' => 'cc.column_id=ccr.column_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'column_id' => 'Column',
            'type' => '1:体验分组  2:行程分组',
            'city_code' => 'City Code',
            'name' => 'Name',
            'description' => 'Description',
            'cover_image_url' => 'Cover Image Url',
            'status' => '1:编辑中; 2.已生效',
            'display_order' => 'Display Order',
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

        $criteria->compare('column_id', $this->column_id);
        $criteria->compare('type', $this->type);
        $criteria->compare('city_code', $this->city_code, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('cover_image_url', $this->cover_image_url, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('display_order', $this->display_order);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtCityColumn the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'cc',
            'order' => 'cc.display_order ASC',
        );
    }
}
