<?php

/**
 * This is the model class for table "ht_product_stock_pdf".
 *
 * The followings are the available columns in table 'ht_product_stock_pdf':
 * @property string $file_md5
 * @property integer $batch_id
 * @property integer $product_id
 * @property integer $ticket_id
 * @property string $filename
 * @property string $directory
 * @property string $upload_time
 * @property integer $order_id
 * @property string $filename_in_order
 * @property string $dir_in_order
 * @property integer $status
 * @property string $comments
 */
class HtProductStockPdf extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ht_product_stock_pdf';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('file_md5, batch_id, product_id, ticket_id, filename, directory, upload_time', 'required'),
			array('batch_id, product_id, ticket_id, order_id, status', 'numerical', 'integerOnly'=>true),
			array('file_md5', 'length', 'max'=>32),
			array('filename, filename_in_order', 'length', 'max'=>128),
			array('directory, dir_in_order', 'length', 'max'=>255),
			array('comments', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('file_md5, batch_id, product_id, ticket_id, filename, directory, upload_time, order_id, filename_in_order, dir_in_order, status, comments', 'safe', 'on'=>'search'),
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
            'pdfHistory' => array(self::HAS_ONE, 'HtProductStockPDfHistory', '', 'on' => 't.batch_id=pdfHistory.batch_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'file_md5' => 'File Md5',
			'batch_id' => 'Batch',
			'product_id' => 'Product',
			'ticket_id' => 'Ticket',
			'filename' => 'Filename',
			'directory' => 'Directory',
			'upload_time' => 'Upload Time',
			'order_id' => 'Order',
			'filename_in_order' => 'Filename In Order',
			'dir_in_order' => 'Dir In Order',
			'status' => 'Status',
			'comments' => 'Comments',
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

		$criteria->compare('file_md5',$this->file_md5,true);
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('ticket_id',$this->ticket_id);
		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('directory',$this->directory,true);
		$criteria->compare('upload_time',$this->upload_time,true);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('filename_in_order',$this->filename_in_order,true);
		$criteria->compare('dir_in_order',$this->dir_in_order,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('comments',$this->comments,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HtProductStockPdf the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
