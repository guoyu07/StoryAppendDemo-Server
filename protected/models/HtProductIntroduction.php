<?php

/**
 * This is the model class for table "ht_product_introduction".
 *
 * The followings are the available columns in table 'ht_product_introduction':
 * @property integer $product_id
 * @property string $buy_note
 * @property string $redeem_note
 * @property string $tips
 * @property string $usage
 * @property integer $status
 */
class HtProductIntroduction extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_introduction';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, buy_note, redeem_note, tips, usage', 'required'),
            array('product_id, status', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('product_id, status, buy_note, redeem_note, tips, usage', 'safe', 'on' => 'search'),
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
            'buy_note' => '购买须知',
            'redeem_note' => '兑换方法',
            'tips' => '注意事项',
            'usage' => '使用方法',
            'status' => '0: 未启用；1：已启用',
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
        $criteria->compare('buy_note', $this->buy_note, true);
        $criteria->compare('redeem_note', $this->redeem_note, true);
        $criteria->compare('tips', $this->tips, true);
        $criteria->compare('usage', $this->usage, true);
        $criteria->compare('status', $this->status);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductIntroduction the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'pi',
        );
    }
}
