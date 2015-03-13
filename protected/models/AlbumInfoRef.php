<?php

/**
 * This is the model class for table "album_info_ref".
 *
 * The followings are the available columns in table 'album_info_ref':
 * @property integer $album_id
 * @property integer $info_id
 * @property integer $land_order
 * @property integer $st_id
 * @property string $offer
 * @property string $pass_benefit
 */
class AlbumInfoRef extends CActiveRecord
{
    const UNSORT = 99;

    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'album_info_ref';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('album_id, info_id, st_id, offer, pass_benefit', 'required'),
			array('album_id, info_id, land_order, st_id', 'numerical', 'integerOnly'=>true),
			array('offer, pass_benefit', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('album_id, info_id, land_order, st_id, offer, pass_benefit', 'safe', 'on'=>'search'),
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
            'landinfo'=>array(self::HAS_ONE,'Landinfo','','on'=>'info_id=landinfo_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'album_id' => 'Album',
			'info_id' => 'Info',
			'land_order' => 'Land Order',
			'st_id' => 'St',
			'offer' => 'Offer',
			'pass_benefit' => 'Pass Benefit',
		);
	}

    public function defaultScope()
    {
        return array(
            'alias' => 'air',
            'order' => 'land_order',
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

		$criteria->compare('album_id',$this->album_id);
		$criteria->compare('info_id',$this->info_id);
		$criteria->compare('land_order',$this->land_order);
		$criteria->compare('st_id',$this->st_id);
		$criteria->compare('offer',$this->offer,true);
		$criteria->compare('pass_benefit',$this->pass_benefit,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AlbumInfoRef the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
