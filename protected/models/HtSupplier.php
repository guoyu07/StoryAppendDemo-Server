<?php

/**
 * This is the model class for table "ht_supplier".
 *
 * The followings are the available columns in table 'ht_supplier':
 * @property integer $supplier_id
 * @property string $name
 * @property string $cn_name
 * @property string $zip_code
 * @property string $fax
 * @property string $telephone
 * @property string $website
 * @property string $address
 * @property string $image_url
 * @property integer $sort_order
 * @property string $payable_by
 */
class HtSupplier extends CActiveRecord
{
    const S_GTA = 11;
    const S_HUAPANG = 96;
    const S_CPIC = 89;
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtSupplier the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_supplier';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('sort_order', 'required'),
            array('sort_order', 'numerical', 'integerOnly' => true),
            array('name, cn_name', 'length', 'max' => 64),
            array('zip_code', 'length', 'max' => 10),
            array('fax, telephone', 'length', 'max' => 32),
            array('image_url, payable_by', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('supplier_id, name, cn_name, zip_code, fax, telephone, website, address, image_url, sort_order, payable_by', 'safe', 'on' => 'search'),
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
            'supplier_id' => 'Supplier',
            'name' => 'Name',
            'cn_name' => 'Cn Name',
            'zip_code' => 'Zip Code',
            'fax' => 'Fax',
            'telephone' => 'Telephone',
            'website' => 'Website',
            'address' => 'Address',
            'image_url' => 'Image Url',
            'sort_order' => 'Sort Order',
            'payable_by' => 'Payable By',
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

        $criteria = new CDbCriteria;

        $criteria->compare('supplier_id', $this->supplier_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('cn_name', $this->cn_name, true);
        $criteria->compare('zip_code', $this->zip_code, true);
        $criteria->compare('fax', $this->fax, true);
        $criteria->compare('telephone', $this->telephone, true);
        $criteria->compare('website', $this->website, true);
        $criteria->compare('address', $this->address, true);
        $criteria->compare('image_url', $this->image_url, true);
        $criteria->compare('sort_order', $this->sort_order);
        $criteria->compare('payable_by', $this->payable_by, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array('alias' => 's');
    }
}