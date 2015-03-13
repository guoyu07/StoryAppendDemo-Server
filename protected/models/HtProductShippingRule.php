<?php

/**
 * This is the model class for table "ht_product_shipping_rule".
 *
 * The followings are the available columns in table 'ht_product_shipping_rule':
 * @property integer $product_id
 * @property string $booking_type
 * @property integer $language_id
 * @property integer $supplier_feedback_type
 * @property integer $need_supplier_booking_ref
 * @property integer $confirmation_type
 * @property integer $need_hitour_booking_ref
 * @property integer $confirmation_display_type
 * @property integer $display_additional_info
 * @property integer $need_notify_supplier
 * @property string $supplier_email
 */
class HtProductShippingRule extends HActiveRecord
{
    const BT_EMAIL = 'EMAIL';
    const BT_HITOUR = 'HITOUR';
    const BT_B2B = 'B2B';
    const BT_GTA = 'GTA';
    const BT_STOCK = 'STOCK';
    const BT_EXCEL = 'EXCEL';
    const BT_CPIC = 'CPIC';

    const FT_CODE = 1;
    const FT_PDF = 2;
    const FT_OK = 3;

    const CT_NONE = 0;
    const CT_ONE = 1;
    const CT_EVERYONE = 2;
    const CT_NOLIMIT = 3;

    const DT_STR = 1;
    const DT_BARCODE = 2;

    public $need_hitour_voucher;
    public $supplier_need_attachment_voucher;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_shipping_rule';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, supplier_feedback_type, confirmation_type', 'required'),
            array('product_id, language_id, supplier_feedback_type, need_supplier_booking_ref, confirmation_type, need_hitour_booking_ref, confirmation_display_type, display_additional_info, need_notify_supplier', 'numerical', 'integerOnly' => true),
            array('booking_type', 'length', 'max' => 32),
            array('supplier_email', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('product_id, booking_type, language_id, supplier_feedback_type, need_supplier_booking_ref, confirmation_type, need_hitour_booking_ref, confirmation_display_type, display_additional_info, need_notify_supplier, supplier_email', 'safe', 'on' => 'search'),
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
            'product' => array(self::HAS_ONE, 'HtProduct', 'product_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'product_id' => 'Product',
            'booking_type' => 'Booking Type',
            'language_id' => 'Language',
            'supplier_feedback_type' => 'Supplier Feedback Type',
            'need_supplier_booking_ref' => 'Need Supplier Booking Ref',
            'confirmation_type' => 'Confirmation Type',
            'need_hitour_booking_ref' => 'Need Hitour Booking',
            'confirmation_display_type' => 'Confirmation Display Type',
            'display_additional_info' => 'Display Additional Info',
            'need_notify_supplier' => 'Need Notify Supplier',
            'supplier_email' => 'Supplier Email',
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
        $criteria->compare('booking_type', $this->booking_type, true);
        $criteria->compare('language_id', $this->language_id);
        $criteria->compare('supplier_feedback_type', $this->supplier_feedback_type);
        $criteria->compare('need_supplier_booking_ref', $this->need_supplier_booking_ref);
        $criteria->compare('confirmation_type', $this->confirmation_type);
        $criteria->compare('need_hitour_booking_ref', $this->need_hitour_booking_ref);
        $criteria->compare('confirmation_display_type', $this->confirmation_display_type);
        $criteria->compare('display_additional_info', $this->display_additional_info);
        $criteria->compare('need_notify_supplier', $this->need_notify_supplier);
        $criteria->compare('supplier_email', $this->supplier_email, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductShippingRule the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function afterFind()
    {
        if ($this->booking_type == self::BT_STOCK || $this->supplier_feedback_type == self::FT_PDF) {
            $this->need_hitour_voucher = 0;
        } else {
            $product = HtProduct::model()->findByPk($this->product_id);
            if ($product['type'] == HtProduct::T_COUPON) {
                $this->need_hitour_voucher = 0;
            } else {
                $this->need_hitour_voucher = 1;
            }
        }

        //TODO:wenzi 需要通过配置来解决，目前只有两个商品由此需求，即给客人发 voucher 时，需要给供应商也发一份。
        $this->supplier_need_attachment_voucher = in_array($this->product_id, [1358, 1334]);

    }

    public static function initShippingRule($product_id)
    {
        $shippingConfig = new HtProductShippingRule();
        $shippingConfig["product_id"] = $product_id;
        $shippingConfig["booking_type"] = "EMAIL";
        $shippingConfig["language_id"] = "1"; //英文
        $shippingConfig["supplier_feedback_type"] = "2"; //供应商返回类型，默认PDF Voucher
        $shippingConfig["need_supplier_booking_ref"] = "0";
        $shippingConfig["confirmation_type"] = "1";
        $shippingConfig["need_hitour_booking_ref"] = "0";
        $shippingConfig["confirmation_display_type"] = "1"; //打印字符串code
        $shippingConfig["display_additional_info"] = "0";
        $shippingConfig["need_notify_supplier"] = "0";
        $shippingConfig["supplier_email"] = " ";

        $result = $shippingConfig->save();
        if ($result) {
            return $shippingConfig;
        }

        return [];
    }

    public static function regulateShippingRule(&$data)
    {
        if ($data['booking_type'] == 'STOCK') {
            $data['need_supplier_booking_ref'] = 0;
            $data['confirmation_type'] = 0;
            $data['need_hitour_booking_ref'] = 0;
            $data['need_notify_supplier'] = 0;
            $data['display_additional_info'] = 0;

        } else if ($data['booking_type'] == 'HITOUR') {
            $data['need_supplier_booking_ref'] = 0;
            $data['confirmation_type'] = 0;
            $data['need_hitour_booking_ref'] = 1;
            $data['display_additional_info'] = 0;

        } else if ($data['booking_type'] == 'B2B' || $data['booking_type'] == 'EMAIL' || $data['booking_type'] == 'EXCEL') {
            if ($data['supplier_feedback_type'] == 3) {
                // Supplier directly feedback booking result.
                $data['need_supplier_booking_ref'] = 0;
                $data['confirmation_type'] = 0;
                $data['need_hitour_booking_ref'] = 1;
            } else if ($data['supplier_feedback_type'] == 2) {
                // Supplier feedback their own voucher pdf.
                $data['need_supplier_booking_ref'] = 0;
                $data['need_hitour_booking_ref'] = 0;
            } else if ($data['supplier_feedback_type'] == 1) {
                // Supplier feedback their own codes.
                $data['need_hitour_booking_ref'] = 0;
            }
        }

        if ($data['need_hitour_booking_ref'] == 1) {
            $data['confirmation_display_type'] = 1;
        }
    }
}
