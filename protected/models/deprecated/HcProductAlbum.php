<?php

/**
 * This is the model class for table "hc_product_album".
 *
 * The followings are the available columns in table 'hc_product_album':
 * @property integer $product_id
 * @property integer $album_id
 * @property integer $need_album
 * @property integer $pick_ticket_album_id
 * @property integer $need_pick_ticket_album
 * @property string $pt_group_info
 */
class HcProductAlbum extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_product_album';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, album_id', 'required'),
			array('product_id, album_id, pick_ticket_album_id, need_album, need_pick_ticket_album', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('product_id, album_id, pick_ticket_album_id', 'safe', 'on'=>'search'),
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
			'product_id' => 'Product',
			'need_album' => 'Need Album',
			'album_id' => 'Album',
			'need_pick_ticket_album' => 'Need Pick Ticket Album',
			'pick_ticket_album_id' => 'Pick Ticket Album',
			'pt_group_info' => 'Pick Ticket Group Info',
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

		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('album_id',$this->album_id);
		$criteria->compare('need_album',$this->need_album);
		$criteria->compare('need_pick_ticket_album',$this->need_pick_ticket_album);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcProductAlbum the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
