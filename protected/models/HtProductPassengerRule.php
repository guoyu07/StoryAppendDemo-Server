<?php

/**
 * This is the model class for table "ht_product_passenger_rule".
 *
 * The followings are the available columns in table 'ht_product_passenger_rule':
 * @property integer $product_id
 * @property string $lead_fields
 * @property string $lead_hidden_fields
 * @property integer $need_passenger_num
 * @property integer $need_lead
 */
class HtProductPassengerRule extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_passenger_rule';
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
            array('product_id, need_passenger_num, need_lead', 'numerical', 'integerOnly' => true),
            array('lead_fields, lead_hidden_fields', 'length', 'max' => 64),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('product_id, lead_fields, need_passenger_num, need_lead', 'safe', 'on' => 'search'),
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
            'items'=>array(self::HAS_MANY,'HtProductPassengerRuleItem','','on'=>'ppr.product_id=ppri.product_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'product_id' => 'Product',
            'lead_fields' => 'Lead Fields',
            'need_passenger_num' => '订单中需要填写的多少个旅客的信息,0：所有；1：只一个；',
            'need_lead' => '0：不需要；1：需要',
            'lead_hidden_fields' => '领队对前台隐藏字段',
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
        $criteria->compare('lead_fields', $this->lead_fields, true);
        $criteria->compare('lead_hidden_fields', $this->lead_hidden_fields, true);
        $criteria->compare('need_passenger_num', $this->need_passenger_num);
        $criteria->compare('need_lead', $this->need_lead);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductPassengerRule the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array('alias' => 'ppr');
    }

    public function getPassengerRule($product_id){
        $pax_meta_map = array();

        $pax_meta = Converter::convertModelToArray(HtPassengerMetaData::model()->findAll());
        $pax_rule = Converter::convertModelToArray($this->with('items')->findByPk($product_id));

        if ($pax_rule['need_lead']) {
            $fields_array = array_filter(explode(',', $pax_rule['lead_fields']));
//            $hidden_array = array_filter(explode(',', $pax_rule['lead_hidden_fields']));
//            $new_array = array();
            foreach ($pax_meta as $pxm) {
                if (in_array($pxm['id'], $fields_array)) {
//                    $new_array[] = $pxm['id'];
                    $pax_meta_map[$pxm['id']] = $pxm;
                }
            }
            $pax_rule['lead_ids'] = $fields_array;
        }

        $id_map = array();
        $hidden_id_map = array();
        if (!empty($pax_rule['items']) && count($pax_rule['items']) > 0) {
            foreach ($pax_rule['items'] as &$ti) {
                $fields_array = array_filter(explode(',', $ti['fields']));
                $hidden_array = array_filter(explode(',', $ti['hidden_fields']));
//                $new_array = array();
                foreach ($pax_meta as $pxm) {
                    if (in_array($pxm['id'], $fields_array) ) {
//                        $new_array[] = $pxm['id'];
                        $pax_meta_map[$pxm['id']] = $pxm;
                    }
                }
                $id_map[$ti['ticket_id']] = $fields_array;
                $hidden_id_map[$ti['ticket_id']] = $hidden_array;
            }
        }

        //硬编码处理历史订单问题。TODO:
        if (count($id_map) == 1 && isset($id_map[1])) {
            $id_map[2] = $id_map[1];
            $id_map[3] = $id_map[1];
        }//wenzi

        $pax_rule['id_map'] = $id_map;
        $pax_rule['hidden_id_map'] = $hidden_id_map;
        unset($pax_rule['items']);
        $data['pax_rule'] = $pax_rule;
        $data['pax_meta'] = $pax_meta_map;

        return $data;
    }

    //更新酒店套餐产品出行人信息
    public function updateHotelPassengerRule($product_id)
    {
        //取绑定商品
        $result = HtProductBundle::model()->with('items')->findAll("product_id = " . $product_id);
        $result = Converter::convertModelToArray($result);
        $need_lead = 0;
        $need_passenger_num = 0;
        $lead_fields = array();
        $fields = array();
        $no_lead_fields = array();
        if (!empty($result)) {
            $flag = 0;
            $count = 0;
            $lead_count = 0;
            foreach ($result as $bundle) {
                foreach ($bundle['items'] as $item) {
                    $passenger_rule = $this->findByPk($item['binding_product_id']);
                    if($passenger_rule['need_lead'] == 1){
                        $lead_count++;//需要领队信息商品数
                        $need_lead = 1;
                        $lead_fields_array = explode(',', $passenger_rule['lead_fields']);
                        foreach($lead_fields_array as $id){
                            $lead_fields[] = $id;
                        }
                    }
                    $passenger_rule_item = HtProductPassengerRuleItem::model()->findAll('product_id = '.$item['binding_product_id']);
                    $passenger_rule_item = Converter::convertModelToArray($passenger_rule_item);
                    if(is_array($passenger_rule_item)){
                        foreach($passenger_rule_item as $item){
                            $fields_array = explode(',', $item['fields']);
                            foreach($fields_array as $id){
                                if(!(($passenger_rule['need_lead']==1) && ($passenger_rule['need_passenger_num']==1))){
                                    //排除只有领队
                                    $fields[] = $id;
                                }

                                if($passenger_rule['need_lead'] != 1){
                                    $no_lead_fields[] = $id;
                                }
                            }
                        }
                    }
                    $flag += $passenger_rule['need_passenger_num'];
                    $count++;
                }
            }
            $lead_fields = array_merge($lead_fields,$no_lead_fields);
            if($flag >= $count){//所有商品都只需要填一个人信息则套餐商品只填一个人信息
                $need_passenger_num = 1;
            }
            //$lead_fields[] = 4;//固定出生日期
            $lead_fields = array_filter(array_unique($lead_fields));
            //$fields[] = 4;
            $fields = array_filter(array_unique($fields));
        }
        $lead_fields = implode(',',$lead_fields);
        $fields = implode(',',$fields);
        $passenger_rule = $this->findByPk($product_id);
        $passenger_rule['need_lead'] = $need_lead;
        $passenger_rule['need_passenger_num'] = $need_passenger_num;
        $passenger_rule['lead_fields'] = $lead_fields;
        $result = $passenger_rule->update();

        $passenger_rule_item = HtProductPassengerRuleItem::model()->findByAttributes(array('product_id' => $product_id, 'ticket_id' => 1));
        if($passenger_rule_item){
            $passenger_rule_item['fields'] = $fields;
            $result = $passenger_rule_item->update();
        }else{
            HtProductPassengerRuleItem::model()->deleteAll('product_id = '.$product_id);
            $passenger_rule_item = new HtProductPassengerRuleItem();
            $passenger_rule_item['product_id'] = $product_id;
            $passenger_rule_item['ticket_id'] = 1;
            $passenger_rule_item['fields'] = $fields;
            $result = $passenger_rule_item->insert();
        }
        return $result;
    }

    public function mergePassengerRule($order_id) {
        //取绑定商品
        $order_products = HtOrder::model()->with('order_products.product')->find("o.order_id = " . $order_id);
        $order_products = Converter::convertModelToArray($order_products);
        $need_lead = 0;
        $need_passenger_num = 0;
        $lead_fields = array();
        $fields = array();
        $no_lead_fields = array();
        if (!empty($order_products)) {
            $flag = 0;
            $count = 0;
            $lead_count = 0;
            foreach ($order_products['order_products'] as $order_product) {
                if($order_product['product']['type'] != HtProduct::T_HOTEL_BUNDLE){
                    $passenger_rule = $this->findByPk($order_product['product_id']);
                    if($passenger_rule['need_lead'] == 1){
                        $lead_count++;//需要领队信息商品数
                        $need_lead = 1;
                        $lead_fields_array = explode(',', $passenger_rule['lead_fields']);
                        foreach($lead_fields_array as $id){
                            $lead_fields[] = $id;
                        }
                    }
                    $passenger_rule_item = HtProductPassengerRuleItem::model()->findAll('product_id = '.$order_product['product_id']);
                    $passenger_rule_item = Converter::convertModelToArray($passenger_rule_item);
                    if(is_array($passenger_rule_item)){
                        foreach($passenger_rule_item as $item){
                            $fields_array = explode(',', $item['fields']);
                            foreach($fields_array as $id){
                                if(!(($passenger_rule['need_lead']==1) && ($passenger_rule['need_passenger_num']==1))){
                                    //排除只有领队
                                    $fields[] = $id;
                                }
                                if($passenger_rule['need_lead'] != 1){
                                    $no_lead_fields[] = $id;
                                }
                            }
                        }
                    }
                    $flag += $passenger_rule['need_passenger_num'];
                    $count++;
                }
            }
            $lead_fields = array_merge($lead_fields,$no_lead_fields);
            if($flag >= $count){//所有商品都只需要填一个人信息则套餐商品只填一个人信息
                $need_passenger_num = 1;
            }
            //$lead_fields[] = 4;//固定出生日期
            $lead_fields = array_filter(array_unique($lead_fields));
            //$fields[] = 4;
            $fields = array_filter(array_unique($fields));
        }
        $lead_fields = implode(',',$lead_fields);
        $fields = implode(',',$fields);
        return array('need_lead'=>$need_lead,'need_passenger_num'=>$need_passenger_num,'lead_fields'=>$lead_fields,'items'=>array('0'=>array('ticket_id'=>1,'fields'=>$fields)));
    }

    // Get passenger info for order edit, this method return whole rule with the hidden fields.
    public function getPassengerTotalRule($order_id){
        $pax_meta_map = array();

        $pax_meta = Converter::convertModelToArray(HtPassengerMetaData::model()->findAll());
        $pax_rule = $this->mergePassengerRule($order_id);

        if ($pax_rule['need_lead']) {
            $fields_array = array_filter(explode(',', $pax_rule['lead_fields']));
            $new_array = array();
            foreach ($pax_meta as $pxm) {
                if (in_array($pxm['id'], $fields_array)) {
                    $new_array[] = $pxm['id'];
                    $pax_meta_map[$pxm['id']] = $pxm;
                }
            }
            $pax_rule['lead_ids'] = $new_array;
        }

        $id_map = array();
        if (!empty($pax_rule['items']) && count($pax_rule['items']) > 0) {
            foreach ($pax_rule['items'] as &$ti) {
                if ($pax_rule['need_lead']) {
                    $id_map[$ti['ticket_id']] = $pax_rule['lead_ids'];

                } else {
                    $id_map[$ti['ticket_id']] = array_filter(explode(',', $ti['fields']));

                    foreach ($pax_meta as $pxm) {
                        if (in_array($pxm['id'], $id_map[$ti['ticket_id']]) ) {
                            $pax_meta_map[$pxm['id']] = $pxm;
                        }
                    }
                }
            }
        }

        //硬编码处理历史订单问题。
        if(count($id_map)==1 && isset($id_map[1])){
            $id_map[2] = $id_map[1];
            $id_map[3] = $id_map[1];
        }//wenzi

        $pax_rule['id_map'] = $id_map;
        unset($pax_rule['items']);
        $data['pax_rule'] = $pax_rule;
        $data['pax_meta'] = $pax_meta_map;

        return $data;
    }
}
