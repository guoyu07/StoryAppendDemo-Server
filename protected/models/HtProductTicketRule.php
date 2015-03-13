<?php

/**
 * This is the model class for table "ht_product_ticket_rule".
 *
 * The followings are the available columns in table 'ht_product_ticket_rule':
 * @property integer $product_id
 * @property integer $ticket_id
 * @property string $age_range
 * @property string $description
 * @property integer $is_independent
 * @property integer $min_num
 * @property string $cn_name
 * @property string $en_name
 */
class HtProductTicketRule extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductTicketRule the static model class
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
        return 'ht_product_ticket_rule';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, ticket_id', 'required'),
            array('product_id, ticket_id, is_independent, min_num', 'numerical', 'integerOnly' => true),
            array('age_range', 'length', 'max' => 16),
            array('description', 'length', 'max' => 64),
            array('cn_name, en_name', 'length', 'max' => 32),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('product_id, ticket_id, age_range, description, is_independent, min_num, cn_name, en_name', 'safe', 'on' => 'search'),
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
            'ticket_type' => array(self::HAS_ONE, 'HtTicketType', '', 'on' => 'ptr.ticket_id = tt.ticket_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'product_id' => 'Product',
            'ticket_id' => 'Ticket',
            'age_range' => 'Age Range',
            'description' => 'Description',
            'is_independent' => 'Is Independent',
            'min_num' => 'Min Num',
            'cn_name' => 'Cn Name',
            'en_name' => 'En Name',
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
        $criteria->compare('ticket_id', $this->ticket_id);
        $criteria->compare('age_range', $this->age_range, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('is_independent', $this->is_independent);
        $criteria->compare('min_num', $this->min_num);
        $criteria->compare('cn_name', $this->cn_name, true);
        $criteria->compare('en_name', $this->en_name, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array('alias' => 'ptr', 'order' => 'ptr.ticket_id');
    }

    public function getTicketRuleMap($product_id)
    {
        $ticket_types = array();

        $raw_ticket_types = $this->with('ticket_type')->findAllByAttributes(['product_id' => $product_id]);
        $raw_ticket_types = Converter::convertModelToArray($raw_ticket_types);
        foreach ($raw_ticket_types as $ti) {
            if (!empty($ti['cn_name'])) {
                $ti['ticket_type']['cn_name'] = $ti['cn_name'];
            }
            if (!empty($ti['en_name'])) {
                $ti['ticket_type']['en_name'] = $ti['en_name'];
            }
            $ticket_types[$ti['ticket_id']] = $ti;
        }

        return $ticket_types;
    }

    public function getTicketRuleMapForOrder($product_id)
    {
        $ticket_types = array();

        $p_types = array();
        $p_raw_types = $this->findAllByAttributes(['product_id' => $product_id]);
        $p_raw_types = Converter::convertModelToArray($p_raw_types);
        foreach ($p_raw_types as $pti) {
            $p_types[$pti['ticket_id']] = $pti;
        }


        $raw_ticket_types = HtTicketType::model()->findAll();
        $raw_ticket_types = Converter::convertModelToArray($raw_ticket_types);
        foreach ($raw_ticket_types as $ti) {
            if (!empty($p_types[$ti['ticket_id']]['cn_name'])) {
                $ti['cn_name'] = $p_types[$ti['ticket_id']]['cn_name'];
            }
            if (!empty($p_types[$ti['ticket_id']]['en_name'])) {
                $ti['en_name'] = $p_types[$ti['ticket_id']]['en_name'];
            }
            $ticket_types[$ti['ticket_id']]['ticket_type'] = $ti;
        }

        return $ticket_types;
    }
}
