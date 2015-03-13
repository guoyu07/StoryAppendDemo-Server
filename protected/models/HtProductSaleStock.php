<?php

/**
 * This is the model class for table "ht_product_sale_stock".
 *
 * The followings are the available columns in table 'ht_product_sale_stock':
 * @property integer $activity_id
 * @property integer $product_id
 * @property string $sale_date
 * @property integer $all_stock_num
 * @property integer $current_stock_num
 * @property integer $payment_reservation_duration
 * @property integer $status
 */
class HtProductSaleStock extends CActiveRecord
{
    const VALID = 1;
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('activity_id, product_id, sale_date, all_stock_num, current_stock_num', 'required'),
            array('activity_id, product_id, all_stock_num, current_stock_num, payment_reservation_duration, status', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('activity_id, product_id, sale_date, all_stock_num, current_stock_num, payment_reservation_duration, status', 'safe', 'on' => 'search'),
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
            'activity_id' => 'Activity',
            'product_id' => 'Product',
            'sale_date' => 'Sale Date',
            'all_stock_num' => 'All Stock Num',
            'current_stock_num' => 'Current Stock Num',
            'payment_reservation_duration' => 'Payment Reservation Duration',
            'status' => 'Is Stock Limited',
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

        $criteria->compare('activity_id', $this->activity_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('sale_date', $this->sale_date, true);
        $criteria->compare('all_stock_num', $this->all_stock_num);
        $criteria->compare('current_stock_num', $this->current_stock_num);
        $criteria->compare('payment_reservation_duration', $this->payment_reservation_duration);
        $criteria->compare('status', $this->status);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope(){
        return array(
            'alias'=>'pss',
        );
    }

    public function getProductSaleStock( $product_id, $sale_date, $activity_id = 0)
    {
        $result = [];
        $stocks = HtProductSaleStock::model()->findAllByAttributes(['activity_id'=>$activity_id,'product_id' => $product_id,'status'=>self::VALID], ['order' => 'sale_date ASC']);
        $stocks = Converter::convertModelToArray($stocks);
        if (!empty($stocks)) {
            //hack
            if($product_id == 3619 && $stocks[0]['current_stock_num'] == 2){
                $stocks[0]['current_stock_num'] = 5;
            }

            if($product_id == 3588 && $stocks[0]['current_stock_num'] == 5){
                $stocks[0]['current_stock_num'] = 20;
            }

            if ($stocks[0]['sale_date'] == '0000-00-00') {
                $result['all_stock_num'] = $stocks[0]['all_stock_num'];
                $result['current_stock_num'] = $stocks[0]['current_stock_num'];
                $result['other_stock_num'] = 0;
            } else {
                $other_stock_num = 0;
                $result['current_stock_num'] = 0;
                $result['other_stock_num'] = 0;
                foreach($stocks as $s){
                    if($s['sale_date']==$sale_date){
                        $result['current_stock_num'] = $s['current_stock_num'];
                        $result['all_stock_num'] = $s['current_stock_num'];
                    }else{
                        if($s['sale_date']>date('Y-m-d')){
                            $other_stock_num += $s['current_stock_num'];
                        }
                    }
                }
                $result['other_stock_num'] = $other_stock_num;
            }
        }

        return $result;
    }

    public function checkSaleStock($product_id, $sale_date, $quantity, &$result, $activity_id = 0){
        $stock_info = $this->getProductSaleStock($product_id, $sale_date, $activity_id);

        //无库存限制
        if (empty($stock_info)) {
            return $stock_info;
        }

        //全部售罄
        if ($stock_info['current_stock_num'] <= 0 ) {
            if($stock_info['other_stock_num'] <=0 ){
                $result['code'] = 301;
                $result['msg'] = '抱歉，该商品已经售罄！';
            }else{
                $result['code'] = 302;
                $result['msg'] = '抱歉，该商品今天已经售罄，请后续继续关注！';
            }
            return false;
        } else{
            if($stock_info['current_stock_num']<$quantity){
                $result['code'] = 304;
                $result['msg'] = '抱歉，该商品库存不足，仅有' . $stock_info['current_stock_num'] . '份！';
                return false;
            }
        }

        return $stock_info;
    }

//    public function checkStock($product_id, $sale_date, $quantity, &$result)
//    {
//        $stock_info = $this->getProductStock($product_id, true);
//
//        //无库存限制
//        if ($stock_info['stock_limited'] == 0) {
//            return $stock_info;
//        }
//
//        //全部售罄
//        if ($stock_info['sold_out'] == 1) {
//            $result['code'] = 304;
//            $result['msg'] = '抱歉，该商品已经售罄！';
//            return false;
//        }
//
//        if (empty($sale_date)) {
//            $sale_date = '0000-00-00';
//            $stock_detail = $stock_info['stock_details'][$sale_date];
//            if ($stock_detail['current_stock_num'] == 0) {
//                $result['code'] = 304;
//                $result['msg'] = '抱歉，该商品已经售罄！';
//                return false;
//            } else if ($stock_detail['current_stock_num'] < $quantity) {
//                $result['code'] = 305;
//                $result['msg'] = '抱歉，该商品库存不足，仅有' . $stock_detail['current_stock_num'] . '份！';
//                return false;
//            }
//        } else {
//            if (isset($stock_info['stock_details'][$sale_date])) {
//                $stock_detail = $stock_info['stock_details'][$sale_date];
//            } else if (isset($stock_info['stock_details']['0000-00-00'])) {
//                $stock_detail = $stock_info['stock_details']['0000-00-00'];
//            } else {
//                $result['code'] = 304;
//                $result['msg'] = '抱歉，该商品已经售罄！';
//                return false;
//            }
//
//            if ($stock_detail['current_stock_num'] == 0) {
//                $result['code'] = 305;
//                $result['msg'] = '抱歉，您选择的日期已经售罄，请尝试选择其他日期购买！';
//                return false;
//            } else if ($stock_detail['current_stock_num'] < $quantity) {
//                $result['code'] = 305;
//                $result['msg'] = '抱歉，您选择的日期库存不足，仅有' . $stock_detail['current_stock_num'] . '份，请尝试选择其他日期购买！';
//                return false;
//            }
//        }
//
//        return $stock_info;
//    }



    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductSaleStock the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function reduceStock($product_id, $tour_date = '', $num = 1, $activity_id = 0)
    {
        if (empty($tour_date)) {
            $tour_date = '0000-00-00';
        }

//        $sql = 'UPDATE ' . $this->tableName() . ' SET current_stock_num = (current_stock_num-' . (int)$num . ') WHERE product_id = "' . (int)$product_id . '" AND current_stock_num >="' . (int)$num . '" AND  sale_date = "' . $tour_date . '"';
        $sql = 'UPDATE ' . $this->tableName() . ' SET current_stock_num = (current_stock_num-' . (int)$num . ') WHERE activity_id = "' . (int)$activity_id . '" AND product_id = "' . (int)$product_id . '" AND current_stock_num >="' . (int)$num . '"';
        $update_num = Yii::app()->db->createCommand($sql)->execute();

        return ($update_num == 1);
    }

//    public function getProductStock($product_id,$with_details = false)
//    {
//        $stock_result = array('stock_limited' => 0, 'sold_out' => 0, 'stock_details' => array(), 'sold_out_dates' => '');
//        $all_stock = 0;
//        $current_stock = 0;
//
//        $stocks = HtProductSaleStock::model()->findAllByAttributes(['product_id'=>$product_id]);
//        $stocks = Converter::convertModelToArray($stocks);
//
//        foreach ($stocks as $stock) {
//            $all_stock += $stock['all_stock_num'];
//            $current_stock += $stock['current_stock_num'];
//            $stock_result['stock_details'][$stock['sale_date']] = $stock;
//            if ($stock['all_stock_num'] > 0 && $stock['current_stock_num'] <= 0) {
//                $stock_result['sold_out_dates'][] = $stock['sale_date'];
//            }
//            $stock_result['payment_reservation_duration'] = $stock['payment_reservation_duration'];
//        }
//
//        if ($all_stock > 0 && $current_stock <= 0) {
//            $stock_result['sold_out'] = 1;
//        }
//        $stock_result['stock_limited'] = count($stocks) > 0 ? 1 : 0;
//
//        if(!$with_details){
//            unset($stock_result['stock_details']);
//        }
//
//        return $stock_result;
//    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_sale_stock';
    }

    public function recycleStockByOrder($order_id, $activity_id = 0)
    {
        $sql = 'SELECT o.order_id,op.product_id,op.tour_date,SUM(opp.quantity) qty_total FROM ht_order o LEFT JOIN ht_order_product op ON o.order_id = op.order_id LEFT JOIN ht_order_product_price opp ON op.order_product_id = opp.order_product_id WHERE o.order_id = "' . (int)$order_id . '" AND o.status_id IN (7,25) GROUP BY o.order_id';
        $result = Yii::app()->db->createCommand($sql)->queryRow();

        if ($result) {
            $product_id = $result['product_id'];
            $tour_date = $result['tour_date'];
            $quantity = $result['qty_total'];
            Yii::log('Recyle Order:' . $order_id . ',tour_date:' . $tour_date . ',qty:' . $quantity, CLogger::LEVEL_WARNING);
            if ($this->recycleStock($product_id, $tour_date, $quantity, $activity_id)) {
                return true;
            } else {
                return $this->recycleStock($product_id, '0000-00-00', $quantity, $activity_id);
            }
        } else {
            Yii::log('Order:' . $order_id . 'Dont recycle sale stock', CLogger::LEVEL_WARNING);
            return true;
        }
    }

    public function recycleStock($product_id, $tour_date = '', $num = 1, $activity_id = 0)
    {
        if (empty($tour_date)) {
            $tour_date = '0000-00-00';
        }
        $sql = 'UPDATE ' . $this->tableName() . ' SET current_stock_num = (current_stock_num +' . (int)$num . ') WHERE activity_id = "' . (int)$activity_id . '" AND product_id = "' . (int)$product_id . '" AND  sale_date = "' . $tour_date . '"';
        $update_num = Yii::app()->db->createCommand($sql)->execute();

        return ($update_num == 1);
    }
}
