<?php

/**
 * This is the model class for table "ht_email_setting".
 *
 * The followings are the available columns in table 'ht_email_setting':
 * @property integer $setting_id
 * @property string $setting_name
 * @property string $protocol
 * @property string $parameter
 * @property string $smtp_host
 * @property string $smtp_username
 * @property string $smtp_password
 * @property string $smtp_port
 * @property string $sender_name
 * @property string $smtp_timeout
 */
class HtEmailSetting extends CActiveRecord
{
    const SUPPLIER = 'supplier';
    const OP = 'op';
    const CUSTOMER = 'customer';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_email_setting';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('setting_name, parameter, smtp_host, smtp_username, smtp_password, sender_name', 'length', 'max' => 45),
            array('protocol', 'length', 'max' => 10),
            array('smtp_port', 'length', 'max' => 6),
            array('smtp_timeout', 'length', 'max' => 2),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('setting_id, setting_name, protocol, parameter, smtp_host, smtp_username, smtp_password, smtp_port, sender_name, smtp_timeout', 'safe', 'on' => 'search'),
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
            'setting_id' => 'Setting',
            'setting_name' => 'Setting Name',
            'protocol' => 'Protocol',
            'parameter' => 'Parameter',
            'smtp_host' => 'Smtp Host',
            'smtp_username' => 'Smtp Username',
            'smtp_password' => 'Smtp Password',
            'smtp_port' => 'Smtp Port',
            'sender_name' => 'Sender Name',
            'smtp_timeout' => 'Smtp Timeout',
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

        $criteria->compare('setting_id', $this->setting_id);
        $criteria->compare('setting_name', $this->setting_name, true);
        $criteria->compare('protocol', $this->protocol, true);
        $criteria->compare('parameter', $this->parameter, true);
        $criteria->compare('smtp_host', $this->smtp_host, true);
        $criteria->compare('smtp_username', $this->smtp_username, true);
        $criteria->compare('smtp_password', $this->smtp_password, true);
        $criteria->compare('smtp_port', $this->smtp_port, true);
        $criteria->compare('sender_name', $this->sender_name, true);
        $criteria->compare('smtp_timeout', $this->smtp_timeout, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtEmailSetting the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope(){
        return array(
            'alias' => 'es',
        );
    }
}