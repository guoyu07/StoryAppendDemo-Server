<?php

/**
 * This is the model class for table "ht_product_ask".
 *
 * The followings are the available columns in table 'ht_product_ask':
 * @property integer $ask_id
 * @property integer $user_id
 * @property integer $ask_type
 * @property integer $product_id
 * @property integer $priority
 * @property integer $status
 * @property integer $is_online
 * @property string $date_added
 * @property string $date_expected
 * @property string $date_answered
 * @property string $date_modified
 * @property string $contact_phone
 * @property string $contact_weixin
 * @property string $contact_qq
 * @property string $contact_mail
 * @property string $contact_name
 * @property string $question
 * @property string $answer
 */
class HtProductAsk extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return HtProductAsk the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_product_ask';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, ask_type, product_id, date_added, date_expected, date_answered, date_modified, contact_phone, contact_weixin, contact_qq, contact_mail, contact_name, question, answer', 'required'),
			array('user_id, ask_type, product_id, priority, status', 'numerical', 'integerOnly'=>true),
			array('contact_phone, contact_weixin, contact_mail', 'length', 'max'=>64),
			array('contact_qq', 'length', 'max'=>16),
			array('contact_name', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ask_id, user_id, ask_type, product_id, priority, status, date_added, date_expected, date_answered, date_modified, contact_phone, contact_weixin, contact_qq, contact_mail, contact_name, question, answer', 'safe', 'on'=>'search'),
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
            'user' => array(self::HAS_ONE, 'User', '', 'on' => 'user.uid = pa.user_id', 'select' => 'User.account,User.screen_name'),
            'product' => array(self::HAS_ONE, 'HtProduct', 'product_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ask_id' => 'Ask',
			'user_id' => 'User',
			'ask_type' => 'Ask Type',
			'product_id' => 'Product',
			'priority' => 'Priority',
			'status' => 'Status',
            'is_onlinne' => 'Is Online',
			'date_added' => 'Date Added',
			'date_expected' => 'Date Expected',
			'date_answered' => 'Date Answered',
			'date_modified' => 'Date Modified',
			'contact_phone' => 'Contact Phone',
			'contact_weixin' => 'Contact Weixin',
			'contact_qq' => 'Contact Qq',
			'contact_mail' => 'Contact Mail',
			'contact_name' => 'Contact Name',
			'question' => 'Question',
			'answer' => 'Answer',
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

		$criteria=new CDbCriteria;

		$criteria->compare('ask_id',$this->ask_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('ask_type',$this->ask_type);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('priority',$this->priority);
		$criteria->compare('status',$this->status);
        $criteria->compare('is_online',$this->is_online);
		$criteria->compare('date_added',$this->date_added,true);
		$criteria->compare('date_expected',$this->date_expected,true);
		$criteria->compare('date_answered',$this->date_answered,true);
		$criteria->compare('date_modified',$this->date_modified,true);
		$criteria->compare('contact_phone',$this->contact_phone,true);
		$criteria->compare('contact_weixin',$this->contact_weixin,true);
		$criteria->compare('contact_qq',$this->contact_qq,true);
		$criteria->compare('contact_mail',$this->contact_mail,true);
		$criteria->compare('contact_name',$this->contact_name,true);
		$criteria->compare('question',$this->question,true);
		$criteria->compare('answer',$this->answer,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function defaultScope()
    {
        return array(
            'alias' => 'pa',
            'order' => 'pa.date_added DESC'
        );
    }

    public static function getAsks($param)
    {
        $c = new CDbCriteria();
        if (!empty($param['product_id'])) {
            $c->addCondition('pa.product_id = ' . $param['product_id']);
            $asks = HtProductAsk::model()->with('user')->findAll($c);
            $asks = Converter::convertModelToArray($asks);
        }else if (empty($param['query_filter'])){
            $asks = self::getUnReplyAsk($param);
        }else {
            $asks = self::getAskByProduct($param);
        }
        return $asks;
    }

    public static function getUnReplyAsk($param)
    {
        $sql = 'SELECT pa.product_id,pa.date_expected,pa.ask_id,u.screen_name,pa.question,pa.answer,pa.contact_mail,pa.contact_phone,pa.contact_weixin,pa.contact_qq,pa.contact_mail,pd.name ';
        $sql .= 'FROM ht_product_ask pa ';
        $sql .= 'LEFT JOIN user u ON pa.user_id=u.uid ';
        $sql .= 'LEFT JOIN ht_product_description pd ON pa.product_id=pd.product_id ';
        $sql .= 'WHERE ask_id>0 AND pd.language_id=2 AND answer=""';

        $result = Yii::app()->db->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getAskByProduct($param)
    {
        $data = array();
        $filter = $param['query_filter'];

        $sql = 'SELECT pa.product_id,pa.answer,pd.name,city.cn_name city_name,country.cn_name country_name ';
        $sql .= 'FROM ht_product_ask pa ';
        $sql .= 'LEFT JOIN ht_product p ON pa.product_id=p.product_id ';
        $sql .= 'LEFT JOIN ht_product_description pd ON pa.product_id=pd.product_id ';
        $sql .= 'LEFT JOIN ht_city city ON p.city_code=city.city_code ';
        $sql .= 'LEFT JOIN ht_country country ON city.country_code=country.country_code ';
        $sql .= 'WHERE ask_id>0 AND pd.language_id=2 ';
        if (!empty($filter['date_start']) && !empty($filter['date_end'])) {
            $start_time = date('Y-m-d H:i:s', strtotime($filter['date_start']));
            $end_time = date('Y-m-d H:i:s', strtotime($filter['date_end']) + 86400);
            $sql .= 'AND pa.date_added >= "' . $start_time . '" ';
            $sql .= 'AND pa.date_added <= "' . $end_time . '" ';
        }
        if (!empty($filter['country_code'])) {
            $sql .= 'AND country.country_code = "' . $filter['country_code'] . '" ';
        }
        if (!empty($filter['city_code'])) {
            $sql .= 'AND p.city_code = "' . $filter['city_code'] . '" ';
        }
        if (!empty($filter['supplier_id'])) {
            $sql .= 'AND p.supplier_id = "' . $filter['supplier_id'] . '" ';
        }
        if (!empty($filter['product'])) {
            $sql .= " AND (pd.name LIKE '%" . $filter['product'] . "%'";
            $sql .= " OR pd.product_id = '" . $filter['product'] . "')";
        }
        $pagen = empty($param['paging']['limit']) ? 20 : $param['paging']['limit'];
        $offset = empty($param['paging']['start']) ? 0 : $param['paging']['start'];

        $result = Yii::app()->db->createCommand($sql)->queryAll();
        if ($result) {
            $product = array();
            foreach($result as $rkey => $row) {
                $product_id = $row['product_id'];
                $product[$product_id]['product_name'] = $row['name'];
                $product[$product_id]['city_name'] = $row['city_name'];
                $product[$product_id]['country_name'] = $row['country_name'];
                if (empty($product[$product_id]['ask_num'])) {
                    $product[$product_id]['ask_num'] = 1;
                }else{
                    $product[$product_id]['ask_num']++;
                }
                if (empty($row['answer'])) {
                    if (empty($product[$product_id]['ask_wait_num'])) {
                        $product[$product_id]['ask_wait_num'] = 1;
                    }else{
                        $product[$product_id]['ask_wait_num']++;
                    }
                } else {
                    $product[$product_id]['ask_wait_num'] = 0;
                }
            }
            foreach($product as $product_id => $p) {
                $p['product_id'] = $product_id;
                if (isset($param['sort']['ask_num'])) {
                    $p['order'] = $p['ask_num'];
                }else if (isset($param['sort']['ask_wait_num'])) {
                    $p['order'] = $p['ask_wait_num'];
                }else{
                    $p['order'] = $p['ask_num'];
                }
                array_push($data, $p);
            }
            if((isset($param['sort']['ask_num']) && $param['sort']['ask_num']>0) || (isset($param['sort']['ask_wait_num']) && $param['sort']['ask_wait_num']>0) ){
                uasort($data, array('HtProductAsk', 'orderAskProductAsc'));
            } else {
                uasort($data, array('HtProductAsk', 'orderAskProductDesc'));
            }
        }
        return array_slice($data, $offset, $pagen);
    }

    public static function orderAskProductAsc($a, $b)
    {
        $al = $a['order'];
        $bl = $b['order'];
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }

    public static function orderAskProductDesc($a, $b)
    {
        $al = $a['order'];
        $bl = $b['order'];
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? -1 : +1;
    }
}