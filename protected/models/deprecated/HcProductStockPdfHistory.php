<?php

/**
 * This is the model class for table "hc_product_stock_pdf_history".
 *
 * The followings are the available columns in table 'hc_product_stock_pdf_history':
 * @property integer $batch_id
 * @property integer $product_id
 * @property integer $type
 * @property string $source_filename
 * @property string $source_comment
 * @property string $target_filename
 * @property string $target_dir
 * @property string $duplication_info
 * @property integer $confirmed_count
 * @property string $upload_time
 * @property integer $status
 */
class HcProductStockPdfHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_product_stock_pdf_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, type, source_filename, source_comment, target_filename, target_dir, duplication_info, upload_time, status', 'required'),
			array('product_id, type, confirmed_count, status', 'numerical', 'integerOnly'=>true),
			array('source_filename, target_filename, target_dir', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('batch_id, product_id, type, source_filename, source_comment, target_filename, target_dir, duplication_info, confirmed_count, upload_time, status', 'safe', 'on'=>'search'),
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
			'batch_id' => 'Batch',
			'product_id' => 'Product',
			'type' => 'Type',
			'source_filename' => 'Source Filename',
			'source_comment' => 'Source Comment',
			'target_filename' => 'Target Filename',
			'target_dir' => 'Target Dir',
			'duplication_info' => 'Duplication Info',
			'confirmed_count' => 'Confirmed Count',
			'upload_time' => 'Upload Time',
			'status' => 'Status',
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

		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('source_filename',$this->source_filename,true);
		$criteria->compare('source_comment',$this->source_comment,true);
		$criteria->compare('target_filename',$this->target_filename,true);
		$criteria->compare('target_dir',$this->target_dir,true);
		$criteria->compare('duplication_info',$this->duplication_info,true);
		$criteria->compare('confirmed_count',$this->confirmed_count);
		$criteria->compare('upload_time',$this->upload_time,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcProductStockPdfHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
