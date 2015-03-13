<?php

/**
 * This is the model class for table "ht_ticket_type".
 *
 * The followings are the available columns in table 'ht_ticket_type':
 * @property integer $ticket_id
 * @property string $cn_name
 * @property string $en_name
 */
class HtTicketType extends HActiveRecord
{
    const TYPE_UNIFIED = 1;
    const TYPE_ADULT = 2;
    const TYPE_CHILD = 3;
    const TYPE_YOUTH = 4;
    const TYPE_ELDERLY = 5;
    const TYPE_INFANT = 6;
    const TYPE_STUDENT = 7;
    const TYPE_PACKAGE = 99;

    public $need_ration_insurance;

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtTicketType the static model class
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
        return 'ht_ticket_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, description', 'required'),
            array('cn_name, en_name', 'length', 'max' => 32),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('ticket_id, cn_name, en_name', 'safe', 'on' => 'search'),
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
            'ticket_id' => 'Ticket',
            'cn_name' => 'CN Name',
            'en_name' => 'EN Name',
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

        $criteria->compare('ticket_id', $this->ticket_id);
        $criteria->compare('cn_name', $this->cn_name, true);
        $criteria->compare('en_name', $this->en_name, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function afterFind()
    {
        $this->need_ration_insurance = ($this->ticket_id == self::TYPE_ADULT || $this->ticket_id == self::TYPE_ELDERLY || $this->ticket_id == self::TYPE_UNIFIED);
    }

    public function getTicketTypesOfProduct($product_id)
    {
        $result = array();
        $ticket_rules = HtProductTicketRule::model()->with('ticket_type')->findAll('product_id=' . $product_id);
        foreach ($ticket_rules as $ticket_rule) {
            array_push($result, $ticket_rule['ticket_type']);
        }

        return $result;
    }

    public function getTicketTitle($ticket_id, $product_id = '')
    {
        if (!empty($product_id)) {
            $rule = HtProductTicketRule::model()->findByAttributes(['product_id' => $product_id, 'ticket_id' => $ticket_id]);
            if (!empty($rule['cn_name'])) {
                return '每' . $rule['cn_name'];
            }
        }

        $type = $this->findByPk($ticket_id);

        if ($type['cn_name'] == '套票') {
            return '每套';
        } else if ($type['cn_name'] == '出行人') {
            return '每人';
        } else {
            return '每' . $type['cn_name'];
        }
    }

    public function defaultScope()
    {
        return array('alias' => 'tt');
    }
}
