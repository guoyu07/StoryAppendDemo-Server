<?php

/**
 * This is the model class for table "ht_notify_template".
 *
 * The followings are the available columns in table 'ht_notify_template':
 * @property integer $notify_template_id
 * @property string $type
 * @property string $path
 */
class HtNotifyTemplate extends CActiveRecord
{
    //const REGISTER_OK = 51;
    const REGISTER_OK = 61;
    //const RESET_PASSWORD = 52;
    const RESET_PASSWORD = 62;

    public $absolute_path;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_notify_template';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('type', 'length', 'max' => 16),
            array('path', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('notify_template_id, type, path', 'safe', 'on' => 'search'),
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
            'notify_template_id' => 'ID',
            'type' => 'Type',
            'path' => 'Path',
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

        $criteria->compare('notify_template_id', $this->notify_template_id);
        $criteria->compare('type', $this->type, true);
        $criteria->compare('path', $this->path, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtNotifyTemplate the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function afterFind()
    {
        $this->absolute_path = ''.$this->path;//TODO:
    }

    public function defaultScope(){
        return array(
            'alias' => 'nt',
        );
    }
}