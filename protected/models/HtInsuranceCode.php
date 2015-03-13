<?php

/**
 * This is the model class for table "ht_insurance_code".
 *
 * The followings are the available columns in table 'ht_insurance_code':
 * @property integer $id
 * @property integer $company_id
 * @property string $partner_code
 * @property string $product_code
 * @property string $redeem_code
 * @property integer $redeem_status
 * @property string $redeem_start_date
 * @property string $redeem_expire_date
 * @property integer $order_id
 * @property integer $refunded
 */
class HtInsuranceCode extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_insurance_code';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('redeem_start_date, redeem_expire_date', 'required'),
            array('company_id, redeem_status, order_id, refunded', 'numerical', 'integerOnly' => true),
            array('partner_code', 'length', 'max' => 16),
            array('redeem_code', 'length', 'max' => 64),
            array('product_code', 'length', 'max' => 32),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, company_id, partner_code, product_code, redeem_code, redeem_status, redeem_start_date, redeem_expire_date, order_id, refunded', 'safe', 'on' => 'search'),
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
            'company' => array(self::BELONGS_TO, 'HtInsuranceCompany', 'company_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'company_id' => 'Company',
            'partner_code' => 'Partner Code',
            'product_code' => 'Product Code',
            'redeem_code' => 'Redeem Code',
            'redeem_status' => 'Redeem Status',
            'redeem_start_date' => 'Redeem Start Date',
            'redeem_expire_date' => 'Redeem Expire Date',
            'order_id' => 'Order',
            'refunded' => 'Refunded',
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

        $criteria->compare('id', $this->id);
        $criteria->compare('company_id', $this->company_id);
        $criteria->compare('partner_code', $this->partner_code, true);
        $criteria->compare('product_code', $this->product_code, true);
        $criteria->compare('redeem_code', $this->redeem_code, true);
        $criteria->compare('redeem_status', $this->redeem_status);
        $criteria->compare('redeem_start_date', $this->redeem_start_date, true);
        $criteria->compare('redeem_expire_date', $this->redeem_expire_date, true);
        $criteria->compare('order_id', $this->order_id);
        $criteria->compare('refunded', $this->refunded);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtInsuranceCode the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array('alias' => 'ic');
    }

    public function rationInsuranceCode($order_id)
    {
        $result = ['code' => 200, 'msg' => 'OK'];
        $need_insurance_num = $this->calcNeedInsuranceNum($order_id);

        if ($need_insurance_num>0) {
            $rationed_num = HtInsuranceCode::model()->countByAttributes(['order_id' => $order_id]);
            if ($need_insurance_num > $rationed_num) {
                $need_insurance_num -= $rationed_num;

                $c = new CDbCriteria();
                $c->addCondition(['order_id=0']);
                $c->limit = $need_insurance_num;
                $affected_rows = HtInsuranceCode::model()->updateAll(['order_id' => $order_id], $c);
                if ($affected_rows < $need_insurance_num) {
                    HtInsuranceCode::model()->updateAll(['order_id' => 0], 'order_id=' . $order_id);
                    $result['code'] = 400;
                    $result['msg'] = '保险码不足!';
                    Yii::log('保险码不足,Order_id=' . $order_id, CLogger::LEVEL_ERROR, 'biz.shipping.insurance');
                }
            } else {
                Yii::log('该订单已经分配了足够的保险码，本次无需再次分配。Order_id=' . $order_id, CLogger::LEVEL_INFO, 'biz.shipping.insurance');
            }
        } else {
            Yii::log('该订单不需要保险码,Order_id=' . $order_id, CLogger::LEVEL_WARNING, 'biz.shipping.insurance');
        }

        return $result;
    }

    public function calcNeedInsuranceNum($order_id){
        //只查订单中第一个商品进行保险码数量检查（如果是套餐商品，Combo 商品，计算的都是主商品）
        $order_product = Converter::convertModelToArray(HtOrderProduct::model()->with('product')->findByAttributes(['order_id' => $order_id]));
        $order_product_id = $order_product['order_product_id'];
        $product_id = $order_product['product_id'];

        //秒杀的抵用券不用发保险码
        if ($order_product['product']['type'] == HtProduct::T_COUPON) {
            return 0;
        }

        //太保商品不用附赠保险码
        if ($order_product['product']['supplier_id'] == HtSupplier::S_CPIC) {
            return 0;
        }

        //根据 quantities 判断需要的保险码数量
        $need_num_1 = 0;
        $ticket_types = HtProductTicketRule::model()->getTicketRuleMapForOrder($product_id);
        $quantities = HtOrderProductPrice::model()->calcRealQuantities($order_product_id, $product_id);
        foreach ($quantities as $ticket_id => $qn) {
            if ($ticket_types[$ticket_id]['ticket_type']['need_ration_insurance']) {
                $need_num_1 += $qn;
            }
        }

        //根据Passenger判断需要的保险码数量
        $need_num_2 = 0;
        $passengers = HtOrderPassenger::model()->findAllByAttributes(['order_id'=>$order_id,'order_product_id'=>$order_product_id]);
        foreach ($passengers as $p) {
            if ($ticket_types[$p['ticket_id']]['ticket_type']['need_ration_insurance']) {
                $need_num_2 += 1;
            }
        }

        return max($need_num_1,$need_num_2) ;
    }
}
