<?php

/**
 * This is the model class for table "ht_product_hotel_bankcard_item".
 *
 * The followings are the available columns in table 'ht_product_hotel_bankcard_item':
 * @property integer $bankcard_id
 * @property string $bankcard_name
 * @property string $logo_url
 * @property integer $display_order
 */
class HtProductHotelBankcardItem extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_product_hotel_bankcard_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('bankcard_name, logo_url, display_order', 'required'),
			array('display_order', 'numerical', 'integerOnly'=>true),
			array('bankcard_name', 'length', 'max'=>50),
			array('logo_url', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('bankcard_id, bankcard_name, logo_url, display_order', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'bankcard_id' => 'Bankcard',
			'bankcard_name' => 'Bankcard Name',
			'logo_url' => 'Logo Url',
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

		$criteria=new CDbCriteria;

		$criteria->compare('bankcard_id',$this->bankcard_id);
		$criteria->compare('bankcard_name',$this->bankcard_name,true);
		$criteria->compare('logo_url',$this->logo_url,true);
		$criteria->compare('display_order',$this->display_order);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtProductHotelBankcardItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope()
    {
        return array('alias' => 'phbi',
            'order' => 'phbi.display_order');
    }
}
