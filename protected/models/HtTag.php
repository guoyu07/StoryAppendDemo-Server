<?php

/**
 * This is the model class for table "ht_tag".
 *
 * The followings are the available columns in table 'ht_tag':
 * @property integer $tag_id
 * @property integer $parent_tag_id
 * @property string $name
 * @property string $en_name
 * @property integer $display_order
 */
class HtTag extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_tag';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, en_name', 'required'),
            array('parent_tag_id, display_order', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 32),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('tag_id, parent_tag_id, name, en_name, display_order', 'safe', 'on' => 'search'),
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
            'tag_id' => 'Tag',
            'parent_tag_id' => 'Parent Tag',
            'name' => 'Name',
            'en_name' => 'English Name',
            'display_order' => 'Display Order',
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

        $criteria->compare('tag_id', $this->tag_id);
        $criteria->compare('parent_tag_id', $this->parent_tag_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('en_name', $this->en_name, true);
        $criteria->compare('display_order', $this->display_order);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtTag the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 't',
            'order' => 't.display_order'
        );
    }

}
