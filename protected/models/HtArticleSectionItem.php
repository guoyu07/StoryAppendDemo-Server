<?php

/**
 * This is the model class for table "ht_article_section_item".
 *
 * The followings are the available columns in table 'ht_article_section_item':
 * @property integer $item_id
 * @property integer $section_id
 * @property integer $type
 * @property string $text_content
 * @property string $image_url
 * @property string $image_title
 * @property string $image_description
 * @property integer $product_id
 * @property string $product_title
 * @property string $product_description
 * @property integer $display_order
 */
class HtArticleSectionItem extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_article_section_item';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('section_id, text_content, image_url, image_title, image_description, product_id, product_title, product_description', 'required'),
            array('section_id, type, product_id, display_order', 'numerical', 'integerOnly' => true),
            array('text_content', 'length', 'max' => 2000),
            array('image_url', 'length', 'max' => 255),
            array('image_title, product_title', 'length', 'max' => 100),
            array('image_description, product_description', 'length', 'max' => 1000),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('item_id, section_id, type, text_content, image_url, image_title, image_description, product_id, product_title, product_description, display_order', 'safe', 'on' => 'search'),
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
            'product_detail' => array(self::HAS_ONE, 'HtProduct', '', 'on' => 'asi.product_id = p.product_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'item_id' => 'Item',
            'section_id' => 'Section',
            'type' => 'Type',
            'text_content' => 'Text Content',
            'image_url' => 'Image Url',
            'image_title' => 'Image Title',
            'image_description' => 'Image Description',
            'product_id' => 'Product',
            'product_title' => 'Product Title',
            'product_description' => 'Product Description',
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

        $criteria->compare('item_id', $this->item_id);
        $criteria->compare('section_id', $this->section_id);
        $criteria->compare('type', $this->type);
        $criteria->compare('text_content', $this->text_content, true);
        $criteria->compare('image_url', $this->image_url, true);
        $criteria->compare('image_title', $this->image_title, true);
        $criteria->compare('image_description', $this->image_description, true);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('product_title', $this->product_title, true);
        $criteria->compare('product_description', $this->product_description, true);
        $criteria->compare('display_order', $this->display_order);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtArticleSectionItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'asi',
            'order' => 'asi.display_order'
        );
    }
}
