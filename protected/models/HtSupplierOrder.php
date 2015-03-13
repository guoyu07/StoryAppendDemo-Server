<?php

/**
 * This is the model class for table "ht_supplier_order".
 *
 * The followings are the available columns in table 'ht_supplier_order':
 * @property integer $supplier_order_id
 * @property string $hitour_booking_ref
 * @property string $supplier_booking_ref
 * @property string $supplier_product_id
 * @property string $confirmation_ref
 * @property string $voucher_ref
 * @property string $additional_info
 * @property string $payable_by
 * @property string $tour_supplier
 * @property string $tour_supplier_code
 * @property integer $current_status
 */
class HtSupplierOrder extends CActiveRecord
{
    const PENDING = 1;
    const CONFIRMED = 2;
    const CANCELED = 3;
    const RETURN_REQUEST = 4;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtSupplierOrder the static model class
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
        return 'ht_supplier_order';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('current_status', 'required'),
            array('current_status', 'numerical', 'integerOnly' => true),
            array('hitour_booking_ref, supplier_booking_ref, tour_supplier_code', 'length', 'max' => 32),
            array('supplier_product_id, payable_by, tour_supplier', 'length', 'max' => 128),
            array('confirmation_ref', 'length', 'max' => 255),
            array('voucher_ref', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('supplier_order_id, hitour_booking_ref, supplier_booking_ref, supplier_product_id, confirmation_ref, voucher_ref, additional_info, payable_by, tour_supplier, tour_supplier_code, current_status', 'safe', 'on' => 'search'),
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
            'supplier_order_id' => 'Supplier Order',
            'hitour_booking_ref' => 'Hitour Booking Ref',
            'supplier_booking_ref' => 'Supplier Booking Ref',
            'supplier_product_id' => 'Supplier Product',
            'confirmation_ref' => 'Confirmation Ref',
            'voucher_ref' => 'Voucher Ref',
            'additional_info' => 'Additional Info',
            'payable_by' => 'Payable By',
            'tour_supplier' => 'Tour Supplier',
            'tour_supplier_code' => 'Tour Supplier Code',
            'current_status' => 'Current Status',
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

        $criteria->compare('supplier_order_id', $this->supplier_order_id);
        $criteria->compare('hitour_booking_ref', $this->hitour_booking_ref, true);
        $criteria->compare('supplier_booking_ref', $this->supplier_booking_ref, true);
        $criteria->compare('supplier_product_id', $this->supplier_product_id, true);
        $criteria->compare('confirmation_ref', $this->confirmation_ref, true);
        $criteria->compare('voucher_ref', $this->voucher_ref, true);
        $criteria->compare('additional_info', $this->additional_info, true);
        $criteria->compare('payable_by', $this->payable_by, true);
        $criteria->compare('tour_supplier', $this->tour_supplier, true);
        $criteria->compare('tour_supplier_code', $this->tour_supplier_code, true);
        $criteria->compare('current_status', $this->current_status);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array('alias' => 'so');
    }
}