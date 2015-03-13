<?php

/**
 * This is the model class for table "ht_product_voucher_rule".
 *
 * The followings are the available columns in table 'ht_product_voucher_rule':
 * @property integer $product_id
 * @property integer $language_id
 * @property string $lead_fields
 * @property integer $need_pay_cert
 * @property string $pay_cert
 * @property integer $need_origin_name
 * @property integer $need_signature
 */
class HtProductVoucherRule extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtProductVoucherRule the static model class
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
        return 'ht_product_voucher_rule';
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
            array('product_id, language_id, need_pay_cert, need_origin_name, need_signature', 'numerical', 'integerOnly' => true),
            array('lead_fields', 'length', 'max' => 128),
            array('pay_cert', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('product_id, language_id, lead_fields, need_pay_cert, pay_cert, need_origin_name, need_signature', 'safe', 'on' => 'search'),
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
            'items' => array(self::HAS_MANY, 'HtProductVoucherRuleItem', '', 'on' => 'pvr.product_id=pvri.product_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'product_id'       => 'Product',
            'language_id'      => 'Language',
            'lead_fields'      => 'Lead Fields',
            'need_pay_cert'    => 'Need Pay Cert',
            'pay_cert'         => 'Pay Cert',
            'need_origin_name' => 'Need Origin Name',
            'need_signature'   => 'Need Signature',
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

        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('language_id', $this->language_id);
        $criteria->compare('lead_fields', $this->lead_fields, true);
        $criteria->compare('need_pay_cert', $this->need_pay_cert);
        $criteria->compare('pay_cert', $this->pay_cert, true);
        $criteria->compare('need_origin_name', $this->need_origin_name);
        $criteria->compare('need_signature', $this->need_signature);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array('alias' => 'pvr');
    }

    public function getVoucherRule($product_id)
    {
        $pax_meta = HtPassengerMetaData::model()->findAll();
        $pax_meta = Converter::convertModelToArray($pax_meta);

        $pax_meta_map = array();
        $voucher_rule = $this->with('items')->findByPk($product_id);
        $voucher_rule = Converter::convertModelToArray($voucher_rule);
        $fields_array = explode(',', $voucher_rule['lead_fields']);
        $new_array = array();
        if (!empty($pax_meta)) {
            foreach ($pax_meta as $pxm) {
                if (in_array($pxm['id'], $fields_array)) {
                    $new_array[] = $pxm['id'];
                    $pax_meta_map[$pxm['id']] = $pxm;
                }
            }
        }
        $voucher_rule['lead_ids'] = $new_array;

        $id_map = array();
        if (!empty($voucher_rule) && !empty($voucher_rule['items']) && is_array($voucher_rule['items'])) {
            foreach ($voucher_rule['items'] as &$ti) {
                $fields_array = explode(',', $ti['fields']);
                $new_array = array();
                foreach ($pax_meta as $pxm) {
                    if (in_array($pxm['id'], $fields_array)) {
                        $new_array[] = $pxm['id'];
                        $pax_meta_map[$pxm['id']] = $pxm;
                    }
                }
                $id_map[$ti['ticket_id']] = $new_array;
            }
        }
        $voucher_rule['id_map'] = $id_map;
        unset($voucher_rule['items']);
        $data['voucher_rule'] = $voucher_rule;
        $data['pax_meta'] = $pax_meta_map;

        return $data;
    }

    public static function addNew($product_id)
    {
        $voucherConfigurations = new HtProductVoucherRule();
        $voucherConfigurations["product_id"] = $product_id;

        return $voucherConfigurations->save();
    }
}