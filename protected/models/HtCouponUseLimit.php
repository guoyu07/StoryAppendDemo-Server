<?php

/**
 * This is the model class for table "ht_coupon_use_limit".
 *
 * The followings are the available columns in table 'ht_coupon_use_limit':
 * @property integer $coupon_limit_id
 * @property integer $coupon_id
 * @property integer $id
 * @property integer $valid_type
 * @property integer $limit_type
 */
class HtCouponUseLimit extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_coupon_use_limit';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('coupon_limit_id, coupon_id, id, valid_type, limit_type', 'required'),
            array('coupon_limit_id, coupon_id, id, valid_type, limit_type', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('coupon_limit_id, coupon_id, id, valid_type, limit_type', 'safe', 'on' => 'search'),
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
            'description' => array(self::HAS_ONE, 'HtProductDescription', '', 'on' => 'pd.product_id = ul.id', 'condition' => 'pd.language_id=2'),
            'country'     => array(self::HAS_ONE, 'HtCountry', '', 'on' => 'cnt.country_code = ul.id'),
            'city'        => array(self::HAS_ONE, 'HtCity', '', 'on' => 'city.city_code = ul.id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'coupon_limit_id' => 'Coupon Limit',
            'coupon_id'       => 'Coupon',
            'id'              => 'ID',
            'valid_type'      => 'Valid Type',
            'limit_type'      => 'Limit Type',
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

        $criteria->compare('coupon_limit_id', $this->coupon_limit_id);
        $criteria->compare('coupon_id', $this->coupon_id);
        $criteria->compare('id', $this->id);
        $criteria->compare('valid_type', $this->valid_type);
        $criteria->compare('limit_type', $this->limit_type);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'ul',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtCouponUseLimit the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function addNew($data)
    {
        $limit = new HtCouponUseLimit();
        ModelHelper::fillItem($limit, $data);
        $result = false;
        try {
            $result = $limit->insert();
        } catch (CException $e) {
            Yii::log('Failed to add new recode in HtCouponUseLimit : ' . $e . getMessage());
        }

        return $result;
    }

    public function getLimitIds($coupon_id, $valid_type = 0)
    {
        $data = array();
        $data['limit_ids'] = array();
        switch ($valid_type) {
            case 1 :
                $with = 'description';
                break;
            case 2 :
                $with = 'city';
                break;
            case 3 :
                $with = 'country';
                break;
            default :
                $with = '';
        }
        $item_tmp = array();
        if ($with) {
            $limit_ids = $this->with($with)->findAllByAttributes(array('coupon_id' => $coupon_id));
            foreach ($limit_ids as $item) {
                if ($valid_type == 1) {
                    $item_tmp = array('coupon_limit_id' => $item['coupon_limit_id'],
                                      'id'              => $item['id'],
                                      'name'            => $item['description']['name']
                    );
                }
                if ($valid_type == 2) {
                    $item_tmp = array('coupon_limit_id' => $item['coupon_limit_id'],
                                      'id'              => $item['id'],
                                      'name'            => $item['city']['cn_name']
                    );
                }
                if ($valid_type == 3) {
                    $item_tmp = array('coupon_limit_id' => $item['coupon_limit_id'],
                                      'id'              => $item['id'],
                                      'name'            => $item['country']['cn_name']
                    );
                }

                $data['limit_ids'][] = $item_tmp;
            }
        }

        return $data;
    }

    public function getLimitInfo($coupon_id)
    {
        $use_limit = $this->findAllByAttributes(['coupon_id' => $coupon_id]);
        $limit_ids = [];
        if ($use_limit) {
            foreach ($use_limit as $limit) {
                if ($limit['valid_type'] == 1) {
                    $product = HtProduct::model()->with('description')->findByPk($limit['id']);
                    $limit_ids[] = ['id' => $product['product_id'], 'link_url' => $product['link_url'], 'name' => $product['description']['name']];
                }
                if ($limit['valid_type'] == 2) {
                    $city = HtCity::model()->findByPk($limit['id']);
                    $limit_ids[] = ['id' => $city['city_code'], 'link_url' => $city['link_url'], 'name' => $city['cn_name']];
                }
                if ($limit['valid_type'] == 3) {
                    $country = HtCountry::model()->findByPk($limit['id']);
                    $limit_ids[] = ['id' => $country['country_code'], 'link_url' => $country['link_url'], 'name' => $country['cn_name']];
                }

            }
        }

        return [$use_limit, $limit_ids];
    }

}
