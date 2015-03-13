<?php

/**
 * This is the model class for table "ht_address".
 *
 * The followings are the available columns in table 'ht_address':
 * @property integer $address_id
 * @property integer $customer_id
 * @property string $firstname
 * @property string $passport_number
 * @property string $telephone
 * @property string $email
 */
class HtAddress extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_address';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('customer_id, firstname', 'required'),
            array('customer_id', 'numerical', 'integerOnly' => true),
            array('firstname, passport_number, telephone', 'length', 'max' => 32),
            array('email', 'length', 'max' => 96),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('address_id, customer_id, firstname,  passport_number, telephone, email', 'safe', 'on' => 'search'),
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
            'address_id' => 'Address',
            'customer_id' => 'Customer',
            'firstname' => 'Firstname',
            'passport_number' => '护照号码',
            'telephone' => '联系电话',
            'email' => '联系Email',
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

        $criteria->compare('address_id', $this->address_id);
        $criteria->compare('customer_id', $this->customer_id);
        $criteria->compare('firstname', $this->firstname, true);
        $criteria->compare('passport_number', $this->passport_number, true);
        $criteria->compare('telephone', $this->telephone, true);
        $criteria->compare('email', $this->email, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtAddress the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'address',
        );
    }

    public function updateAddress($customer_id, $address_id, $email, $telephone, $firstname)
    {
        $item = $this->findByPk($address_id);
        if (!empty($item)) {
            $item['email'] = $email;
            $item['telephone'] = $telephone;
            $item['firstname'] = $firstname;
            $item['customer_id'] = $customer_id;

            return $item->update();
        }

        return false;
    }

    public function addAddress($customer_id, $email, $telephone, $firstname)
    {
        $item = new HtAddress();
        $item->fillDefaultValue();
        $item['email'] = $email;
        $item['telephone'] = $telephone;
        $item['firstname'] = $firstname;
        $item['customer_id'] = $customer_id;

        $result = $item->insert();
        if($result) return $item['address_id'];
        return 0;
    }

    private function fillDefaultValue()
    {
        $this->email = '';
        $this->telephone = '';
        $this->firstname = '';
        $this->passport_number = '';
    }
}
