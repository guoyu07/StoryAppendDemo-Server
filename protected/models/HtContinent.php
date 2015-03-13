<?php

/**
 * This is the model class for table "ht_continent".
 *
 * The followings are the available columns in table 'ht_continent':
 * @property integer $continent_id
 * @property string $cn_name
 * @property string $en_name
 * @property string $description
 */
class HtContinent extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_continent';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('cn_name, en_name, description', 'required'),
            array('cn_name, en_name', 'length', 'max' => 64),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('continent_id, cn_name, en_name, description', 'safe', 'on' => 'search'),
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
            'countries' => array(self::HAS_MANY, 'HtCountry', 'continent_id'),
        );
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'ctt',
            'order' => 'ctt.continent_id ASC',
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'continent_id' => 'Continent',
            'cn_name' => 'Name',
            'en_name' => 'Enname',
            'description' => 'Description',
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

        $criteria->compare('continent_id', $this->continent_id);
        $criteria->compare('cn_name', $this->cn_name, true);
        $criteria->compare('en_name', $this->en_name, true);
        $criteria->compare('description', $this->description, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtContinent the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function findAllWithContriesCities()
    {
        $key = 'continents_all_with_countries_cities';
        $continents = Yii::app()->cache->get($key);
        if (empty($continents)) {
            $raw = HtContinent::model()->with('countries.cities')->findAll();
            $continents = Converter::convertModelToArray($raw);

            Yii::app()->cache->set($key, $continents, 2*60*60);
        }

        return $continents;
    }
}
