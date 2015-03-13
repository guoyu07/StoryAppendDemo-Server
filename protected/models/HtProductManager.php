<?php

/**
 * This is the model class for table "ht_product_manager".
 *
 * The followings are the available columns in table 'ht_product_manager':
 * @property integer $product_id
 * @property string $manager_name
 */
class HtProductManager extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_manager';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, manager_name', 'required'),
            array('product_id', 'numerical', 'integerOnly' => true),
            array('manager_name', 'length', 'max' => 50),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('product_id, manager_name', 'safe', 'on' => 'search'),
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
            'product_id' => 'Product',
            'manager_name' => 'Manager Name',
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
        $criteria->compare('manager_name', $this->manager_name, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductManager the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function addOrUpdate($product_id, $manager_name)
    {
        $product_manager = $this->findByAttributes(['product_id' => $product_id]);
        if (!empty($product_manager)) {
            $product_manager['manager_name'] = $manager_name;
            $result = $product_manager->update() ? 1 : 0;
        } else {
            $product_manager = new HtProductManager();
            $product_manager['manager_name'] = $manager_name;
            $product_manager['product_id'] = $product_id;
            $result = $product_manager->insert() ? 1 : 0;
        }

        return $result;
    }
}
