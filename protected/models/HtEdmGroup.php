<?php

/**
 * This is the model class for table "ht_edm_group".
 *
 * The followings are the available columns in table 'ht_edm_group':
 * @property integer $group_id
 * @property integer $edm_id
 * @property string $title
 * @property string $title_link
 * @property integer $display_order
 */
class HtEdmGroup extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_edm_group';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('edm_id, title, title_link, display_order', 'required'),
			array('edm_id, display_order', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>100),
			array('title_link', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('group_id, edm_id, title, title_link, display_order', 'safe', 'on'=>'search'),
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
            'group_products' => array(self::HAS_MANY, 'HtEdmGroupProduct', '', 'on'=>'g.group_id = gp.group_id','order'=>'g.display_order ASC')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'group_id' => 'Group',
			'edm_id' => 'Edm',
			'title' => 'Title',
			'title_link' => 'Title Link',
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

		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('edm_id',$this->edm_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('title_link',$this->title_link,true);
		$criteria->compare('display_order',$this->display_order);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtEdmGroup the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function defaultScope()
    {
        return array(
            'alias' => 'g',
        );
    }
}
