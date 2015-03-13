<?php

/**
 * This is the model class for table "ht_city_column_ref".
 *
 * The followings are the available columns in table 'ht_city_column_ref':
 * @property integer $column_id
 * @property integer $article_id
 * @property string $article_image_url
 * @property integer $display_order
 */
class HtCityColumnRef extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_city_column_ref';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('column_id, article_id, display_order', 'required'),
            array('column_id, article_id, display_order', 'numerical', 'integerOnly' => true),
            array('article_image_url', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('column_id, article_id, article_image_url, display_order', 'safe', 'on' => 'search'),
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
            'article' => array(self::HAS_ONE, 'HtArticle', '', 'on' => 'a.article_id=ccr.article_id'),
            'article_online' => array(self::HAS_ONE, 'HtArticle', '', 'on' => 'a.article_id=ccr.article_id and a.status=1'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'column_id' => 'Column',
            'article_id' => 'Article',
            'article_image_url' => 'Product Image Url',
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

        $criteria->compare('column_id', $this->column_id);
        $criteria->compare('article_id', $this->article_id);
        $criteria->compare('article_image_url', $this->article_image_url, true);
        $criteria->compare('display_order', $this->display_order);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtCityColumnRef the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'ccr',
            'order' => 'ccr.display_order ASC',
        );
    }
}
