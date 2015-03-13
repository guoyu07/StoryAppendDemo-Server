<?php

/**
 * This is the model class for table "gta_auto_import".
 *
 * The followings are the available columns in table 'gta_auto_import':
 * @property integer $auto_id
 * @property string $city_code
 * @property string $item_id
 * @property integer $status
 * @property string $insert_time
 * @property string $update_time
 */
class GtaAutoImport extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'gta_auto_import';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_code, item_id, insert_time', 'required'),
            array('status', 'numerical', 'integerOnly' => true),
            array('city_code', 'length', 'max' => 8),
            array('item_id', 'length', 'max' => 16),
            array('update_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('auto_id, city_code, item_id, status, insert_time, update_time', 'safe', 'on' => 'search'),
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
            'auto_id' => 'Auto',
            'city_code' => 'City Code',
            'item_id' => 'Item',
            'status' => 'Status',
            'insert_time' => 'Insert Time',
            'update_time' => 'Update Time',
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

        $criteria->compare('auto_id', $this->auto_id);
        $criteria->compare('city_code', $this->city_code, true);
        $criteria->compare('item_id', $this->item_id, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('insert_time', $this->insert_time, true);
        $criteria->compare('update_time', $this->update_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return GtaAutoImport the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getProductImport($product_id)
    {
        $one_product = HtProduct::model()->findByPk($product_id);
        $import_item = $this->findByAttributes(array('city_code' => $one_product['city_code'], 'item_id' => $one_product['supplier_product_id']));

        return Converter::convertModelToArray($import_item);
    }
}
