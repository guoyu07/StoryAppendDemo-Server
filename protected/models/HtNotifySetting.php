<?php

/**
 * This is the model class for table "ht_notify_setting".
 *
 * The followings are the available columns in table 'ht_notify_setting':
 * @property integer $id
 * @property string $notify_obj_name
 * @property string $notify_type
 * @property integer $order_status_id
 * @property integer $template_id
 */
class HtNotifySetting extends CActiveRecord
{
    const SUPPLIER = 'supplier';
    const OP = 'op';
    const CUSTOMER = 'customer';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_notify_setting';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('notify_obj_name, order_status_id, template_id', 'required'),
            array('order_status_id, template_id', 'numerical', 'integerOnly' => true),
            array('notify_obj_name', 'length', 'max' => 64),
            array('notify_type', 'length', 'max' => 32),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, notify_obj_name, notify_type, order_status_id, template_id', 'safe', 'on' => 'search'),
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
            'template' => array(self::HAS_ONE, 'HtNotifyTemplate', '','on'=>'ns.template_id = nt.notify_template_id'),
            'email_setting' => array(self::HAS_ONE, 'HtEmailSetting', '', 'on' => 'ns.notify_obj_name = es.setting_name'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'notify_obj_name' => 'Notify Obj Name',
            'notify_type' => 'Notify Type',
            'order_status_id' => 'Order Status',
            'template_id' => 'Template',
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
        $criteria->compare('notify_obj_name', $this->notify_obj_name, true);
        $criteria->compare('notify_type', $this->notify_type, true);
        $criteria->compare('order_status_id', $this->order_status_id);
        $criteria->compare('template_id', $this->template_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtNotifySetting the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope(){
        return array(
            'alias' => 'ns',
        );
    }
}