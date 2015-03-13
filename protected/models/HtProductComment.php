<?php

/**
 * This is the model class for table "ht_product_comment".
 *
 * The followings are the available columns in table 'ht_product_comment':
 * @property integer $comment_id
 * @property integer $product_id
 * @property integer $customer_id
 * @property integer $hitour_service_level
 * @property integer $supplier_service_level
 * @property string $content
 * @property integer $approved
 * @property string $insert_time
 */
class HtProductComment extends CActiveRecord
{
    const CACHE_COMMENT_STAT_INFO_PREFIX = 'cache_comment_stat_info_';
    const CACHE_COMMENTS_INFO_PREFIX = 'cache_comments_';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_comment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('comment_id, product_id, hitour_service_level, supplier_service_level, insert_time', 'required'),
            array('comment_id, product_id, customer_id, hitour_service_level, supplier_service_level, approved', 'numerical', 'integerOnly' => true),
            array('content', 'length', 'max' => 2048),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('comment_id, product_id, customer_id, hitour_service_level, supplier_service_level, content, insert_time', 'safe', 'on' => 'search'),
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
            'customer' => array(self::HAS_ONE, 'HtCustomer', '', 'on' => 'customer.customer_id = pc.customer_id', 'select' => 'customer.customer_id, customer.firstname, customer.email, customer.avatar_url'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'comment_id' => 'Comment ID',
            'product_id' => 'Product ID',
            'customer_id' => 'Customer ID',
            'hitour_service_level' => '玩途服务星级',
            'supplier_service_level' => '供应商服务星级',
            'content' => '评论内容',
            'approved' => '0: 未审核；1：已审核；2：垃圾评论；3：标记为删除',
            'insert_time' => '评论时间',
        );
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'pc',
            'order' => 'pc.insert_time DESC'
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

        $criteria->compare('comment_id', $this->comment_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('customer_id', $this->customer_id);
        $criteria->compare('hitour_service_level', $this->hitour_service_level);
        $criteria->compare('supplier_service_level', $this->supplier_service_level);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('approved', $this->approved);
        $criteria->compare('insert_time', $this->insert_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductComment the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function clearCache($product_id)
    {
        Yii::app()->cache->delete(HtProductComment::CACHE_COMMENT_STAT_INFO_PREFIX . $product_id);
        Yii::app()->cache->delete(HtProductComment::CACHE_COMMENTS_INFO_PREFIX . $product_id);
    }

    protected function beforeSave()
    {
        HtProductComment::clearCache($this->product_id);

        return parent::beforeSave();
    }

    protected function beforeDelete()
    {
        HtProductComment::clearCache($this->product_id);

        return parent::beforeDelete();
    }

    public static function getComments($product_id, $page_index = 0, $count_per_page = 3, $approved_only = true)
    {
        $can_cache = $page_index == 0 && $count_per_page == 3 && $approved_only == true;
        $key = HtProductComment::CACHE_COMMENTS_INFO_PREFIX . $product_id;
        if($can_cache) {
            $comments = Yii::app()->cache->get($key);
        }

        if(empty($comments)) {
            $c = new CDbCriteria();
            $c->addCondition('pc.product_id = ' . $product_id);
            $c->addCondition('customer.customer_id is not null');
            if($approved_only) {
                $c->addCondition('pc.approved = 1');
            }
            $c->limit = $count_per_page;
            $c->offset = $page_index * $count_per_page;

            $comments = HtProductComment::model()->with('customer')->findAll($c);

            if($can_cache) {
                Yii::app()->cache->set($key, $comments, 24 * 60 * 60);
            }
        }

        return $comments;
    }

    public static function getProcessedComments($product_id, $page_index = 0, $counts = 3, $approve_only = true)
    {
        $comments = Converter::convertModelToArray(HtProductComment::getComments($product_id, $page_index, $counts, $approve_only));

        if(!empty($comments)) {
            foreach($comments as &$comment) {
                $comment['customer']['email'] = str_repeat('*', mt_rand(3, 10)) . '@hitour.cc';
                $first_name = $comment['customer']['firstname'];
                $length = mb_strlen($first_name, 'utf-8');
                if($length > 2) {
                    $first_name = HtProductComment::utf8Substr($first_name, 0, 1) . '***' . HtProductComment::utf8Substr($first_name, $length - 1, 1);
                }
                $comment['customer']['firstname'] = $first_name;
            }
        }

        return $comments;
    }

    public static function getStatInfo($product_id)
    {
        $key = HtProductComment::CACHE_COMMENT_STAT_INFO_PREFIX . $product_id;
        $result = Yii::app()->cache->get($key);
        if(empty($result)) {
            $product = HtProduct::model()->with('count_product_comment', 'avg_hitour_service_level',
                                                'avg_supplier_service_level')->findByPk($product_id);

            $result = array(
                'total' => 0,
                'avg_hitour_service_level' => 5.0,
                'avg_supplier_service_level' => 5.0
            );

            if(!empty($product) && $product['count_product_comment'] > 0) {
                $result['total'] = $product['count_product_comment'];
                $result['avg_hitour_service_level'] = sprintf('%.1f', HtProductComment::getStarByScore($product['avg_hitour_service_level']));
                $result['avg_supplier_service_level'] = sprintf('%.1f', HtProductComment::getStarByScore($product['avg_supplier_service_level']));
            }
            Yii::app()->cache->set($key, $result, 24 * 60 * 60);
        }

        return $result;
    }

    private static function utf8Substr($str, $from, $len)
    {
        return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' .
                            '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s',
                            '$1', $str);
    }

    private static function getStarByScore($score)
    {
        $refined_score = round($score * 2) * 5;
        if($refined_score < 10) {
            return '0.' . $refined_score;
        }

        return floor($refined_score / 10) . '.' . ($refined_score % 10);
    }
}
