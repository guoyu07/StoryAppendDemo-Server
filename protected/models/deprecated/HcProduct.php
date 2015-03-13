<?php

/**
 * This is the model class for table "hc_product".
 *
 * The followings are the available columns in table 'hc_product':
 * @property integer $product_id
 * @property string $hicart_id
 * @property string $model
 * @property integer $type
 * @property string $sku
 * @property string $upc
 * @property string $ean
 * @property string $jan
 * @property string $isbn
 * @property string $mpn
 * @property string $location
 * @property string $city_id
 * @property integer $quantity
 * @property integer $stock_status_id
 * @property string $image
 * @property integer $manufacturer_id
 * @property string $manufacturer_product_id
 * @property integer $shipping
 * @property string $supplier_price
 * @property string $supplier_child_price
 * @property string $orig_price
 * @property string $orig_child_price
 * @property string $stock_price
 * @property string $stock_child_price
 * @property string $price
 * @property string $age_range
 * @property string $child_price
 * @property string $child_age_range
 * @property string $price_source
 * @property integer $points
 * @property integer $tax_class_id
 * @property string $date_available
 * @property string $weight
 * @property integer $weight_class_id
 * @property string $length
 * @property string $width
 * @property string $height
 * @property integer $length_class_id
 * @property integer $subtract
 * @property integer $minimum
 * @property integer $sort_order
 * @property integer $status
 * @property string $date_added
 * @property string $date_modified
 * @property integer $viewed
 * @property integer $album_id
 * @property string $landinfo_all
 * @property string $discount_all
 * @property integer $departure_album_id
 * @property integer $pickticket_album_id
 * @property string $pickticket_note
 * @property string $tour_attributes
 * @property integer $departure_point_required
 * @property string $languages
 * @property string $language_list_code
 * @property string $currency
 * @property integer $editing_state
 * @property string $price_comparer
 * @property integer $min_adult_num
 * @property integer $max_pax_num
 * @property integer $need_passenger_num
 * @property integer $need_tour_date
 */
class HcProduct extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hc_product';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('hicart_id, model, type, sku, upc, ean, jan, isbn, mpn, location, city_id, stock_status_id, manufacturer_id, manufacturer_product_id, orig_child_price, stock_price, stock_child_price, price_source, tax_class_id, date_available, album_id, landinfo_all, discount_all, departure_album_id, pickticket_album_id, pickticket_note, tour_attributes, price_comparer', 'required'),
				array('type, quantity, stock_status_id, manufacturer_id, shipping, points, tax_class_id, weight_class_id, length_class_id, subtract, minimum, sort_order, status, viewed, album_id, departure_album_id, pickticket_album_id, departure_point_required, editing_state, min_adult_num, max_pax_num, need_passenger_num, need_tour_date', 'numerical', 'integerOnly' => true),
				array('hicart_id, image, manufacturer_product_id, price_source, price_comparer', 'length', 'max' => 255),
				array('model, sku, mpn', 'length', 'max' => 64),
				array('upc', 'length', 'max' => 12),
				array('ean', 'length', 'max' => 14),
				array('jan, isbn', 'length', 'max' => 13),
				array('location, tour_attributes', 'length', 'max' => 128),
				array('city_id', 'length', 'max' => 4),
				array('supplier_price, supplier_child_price, orig_price, orig_child_price, stock_price, stock_child_price', 'length', 'max' => 11),
				array('price, weight, length, width, height', 'length', 'max' => 15),
				array('age_range, child_age_range, languages, language_list_code', 'length', 'max' => 32),
				array('child_price', 'length', 'max' => 10),
				array('currency', 'length', 'max' => 3),
				array('date_added, date_modified', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
				array('product_id, hicart_id, model, type, sku, upc, ean, jan, isbn, mpn, location, city_id, quantity, stock_status_id, image, manufacturer_id, manufacturer_product_id, shipping, supplier_price, supplier_child_price, orig_price, orig_child_price, stock_price, stock_child_price, price, age_range, child_price, child_age_range, price_source, points, tax_class_id, date_available, weight, weight_class_id, length, width, height, length_class_id, subtract, minimum, sort_order, status, date_added, date_modified, viewed, album_id, landinfo_all, discount_all, departure_album_id, pickticket_album_id, pickticket_note, tour_attributes, departure_point_required, languages, language_list_code, currency, editing_state, price_comparer, min_adult_num, max_pax_num, need_passenger_num, need_tour_date', 'safe', 'on' => 'search'),
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
				'product_description' => array(self::HAS_ONE, 'HcProductDescription', 'product_id', 'condition' => 'language_id=2')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
				'product_id' => 'Product',
				'hicart_id' => 'Hicart',
				'model' => 'Model',
				'type' => 'Type',
				'sku' => 'Sku',
				'upc' => 'Upc',
				'ean' => 'Ean',
				'jan' => 'Jan',
				'isbn' => 'Isbn',
				'mpn' => 'Mpn',
				'location' => 'Location',
				'city_id' => 'City',
				'quantity' => 'Quantity',
				'stock_status_id' => 'Stock Status',
				'image' => 'Image',
				'manufacturer_id' => 'Manufacturer',
				'manufacturer_product_id' => 'Manufacturer Product',
				'shipping' => 'Shipping',
				'supplier_price' => 'Supplier Price',
				'supplier_child_price' => 'Supplier Child Price',
				'orig_price' => 'Orig Price',
				'orig_child_price' => 'Orig Child Price',
				'stock_price' => 'Stock Price',
				'stock_child_price' => 'Stock Child Price',
				'price' => 'Price',
				'age_range' => 'Age Range',
				'child_price' => 'Child Price',
				'child_age_range' => 'Child Age Range',
				'price_source' => 'Price Source',
				'points' => 'Points',
				'tax_class_id' => 'Tax Class',
				'date_available' => 'Date Available',
				'weight' => 'Weight',
				'weight_class_id' => 'Weight Class',
				'length' => 'Length',
				'width' => 'Width',
				'height' => 'Height',
				'length_class_id' => 'Length Class',
				'subtract' => 'Subtract',
				'minimum' => 'Minimum',
				'sort_order' => 'Sort Order',
				'status' => 'Status',
				'date_added' => 'Date Added',
				'date_modified' => 'Date Modified',
				'viewed' => 'Viewed',
				'album_id' => 'Album',
				'landinfo_all' => 'Landinfo All',
				'discount_all' => 'Discount All',
				'departure_album_id' => 'Departure Album',
				'pickticket_album_id' => 'Pickticket Album',
				'pickticket_note' => 'Pickticket Note',
				'tour_attributes' => 'Tour Attributes',
				'departure_point_required' => 'Departure Point Required',
				'languages' => 'Languages',
				'language_list_code' => 'Language List Code',
				'currency' => 'Currency',
				'editing_state' => 'Editing State',
				'price_comparer' => 'Price Comparer',
				'min_adult_num' => 'Min Adult Num',
				'max_pax_num' => 'Max Pax Num',
				'need_passenger_num' => 'Need Passenger Num',
				'need_tour_date' => 'Need Tour Date',
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
		$criteria->compare('hicart_id', $this->hicart_id, true);
		$criteria->compare('model', $this->model, true);
		$criteria->compare('type', $this->type);
		$criteria->compare('sku', $this->sku, true);
		$criteria->compare('upc', $this->upc, true);
		$criteria->compare('ean', $this->ean, true);
		$criteria->compare('jan', $this->jan, true);
		$criteria->compare('isbn', $this->isbn, true);
		$criteria->compare('mpn', $this->mpn, true);
		$criteria->compare('location', $this->location, true);
		$criteria->compare('city_id', $this->city_id, true);
		$criteria->compare('quantity', $this->quantity);
		$criteria->compare('stock_status_id', $this->stock_status_id);
		$criteria->compare('image', $this->image, true);
		$criteria->compare('manufacturer_id', $this->manufacturer_id);
		$criteria->compare('manufacturer_product_id', $this->manufacturer_product_id, true);
		$criteria->compare('shipping', $this->shipping);
		$criteria->compare('supplier_price', $this->supplier_price, true);
		$criteria->compare('supplier_child_price', $this->supplier_child_price, true);
		$criteria->compare('orig_price', $this->orig_price, true);
		$criteria->compare('orig_child_price', $this->orig_child_price, true);
		$criteria->compare('stock_price', $this->stock_price, true);
		$criteria->compare('stock_child_price', $this->stock_child_price, true);
		$criteria->compare('price', $this->price, true);
		$criteria->compare('age_range', $this->age_range, true);
		$criteria->compare('child_price', $this->child_price, true);
		$criteria->compare('child_age_range', $this->child_age_range, true);
		$criteria->compare('price_source', $this->price_source, true);
		$criteria->compare('points', $this->points);
		$criteria->compare('tax_class_id', $this->tax_class_id);
		$criteria->compare('date_available', $this->date_available, true);
		$criteria->compare('weight', $this->weight, true);
		$criteria->compare('weight_class_id', $this->weight_class_id);
		$criteria->compare('length', $this->length, true);
		$criteria->compare('width', $this->width, true);
		$criteria->compare('height', $this->height, true);
		$criteria->compare('length_class_id', $this->length_class_id);
		$criteria->compare('subtract', $this->subtract);
		$criteria->compare('minimum', $this->minimum);
		$criteria->compare('sort_order', $this->sort_order);
		$criteria->compare('status', $this->status);
		$criteria->compare('date_added', $this->date_added, true);
		$criteria->compare('date_modified', $this->date_modified, true);
		$criteria->compare('viewed', $this->viewed);
		$criteria->compare('album_id', $this->album_id);
		$criteria->compare('landinfo_all', $this->landinfo_all, true);
		$criteria->compare('discount_all', $this->discount_all, true);
		$criteria->compare('departure_album_id', $this->departure_album_id);
		$criteria->compare('pickticket_album_id', $this->pickticket_album_id);
		$criteria->compare('pickticket_note', $this->pickticket_note, true);
		$criteria->compare('tour_attributes', $this->tour_attributes, true);
		$criteria->compare('departure_point_required', $this->departure_point_required);
		$criteria->compare('languages', $this->languages, true);
		$criteria->compare('language_list_code', $this->language_list_code, true);
		$criteria->compare('currency', $this->currency, true);
		$criteria->compare('editing_state', $this->editing_state);
		$criteria->compare('price_comparer', $this->price_comparer, true);
		$criteria->compare('min_adult_num', $this->min_adult_num);
		$criteria->compare('max_pax_num', $this->max_pax_num);
		$criteria->compare('need_passenger_num', $this->need_passenger_num);
		$criteria->compare('need_tour_date', $this->need_tour_date);

		return new CActiveDataProvider($this, array(
				'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HcProduct the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function getProductBasic($product_id)
	{
		$sql = "SELECT p.product_id, pd.name" .
				" FROM hc_product p LEFT JOIN hc_product_description pd ON pd.product_id=p.product_id and pd.language_id=2 ";
		if (is_array($product_id)) {
			$sql .= " WHERE p.product_id in (" . implode(",", $product_id) . ")";
		} else {
			$sql .= " WHERE p.product_id=" . $product_id;
		}

		$connection = Yii::app()->db;
		$command = $connection->createCommand($sql);

		if (is_array($product_id)) {
			$result = $command->queryAll();
		} else {
			$result = $command->queryRow();
		}

		return $result;
	}

	public function getProductDetail($product_id)
	{
		$sql = "SELECT p.*, pd.*, " .
				" (select image_url FROM `hc_product_image_ext` pi where pi.product_id=p.product_id and pi.as_cover = 1 limit 1) as pi_image_url, " .
				" (select name from hc_manufacturer m where m.manufacturer_id=p.manufacturer_id) as m_name, " .
				" (select cn_name from gta_city c where c.city_code = p.city_id ) as city_cn_name " .
				" FROM hc_product p LEFT JOIN hc_product_description pd ON pd.product_id=p.product_id and pd.language_id=2 ";

		if (is_array($product_id)) {
			$sql .= " WHERE p.product_id in (" . implode(",", $product_id) . ")";
		} else {
			$sql .= " WHERE p.product_id=" . $product_id;
		}

		$connection = Yii::app()->db;
		$command = $connection->createCommand($sql);

		if (is_array($product_id)) {
			$result = $command->queryAll();
		} else {
			$result = $command->queryRow();
		}

		return $result;
	}

	public function getProductDetailAll($condition = '', $sortDir = 'ASC', $sortedBy = 'product_id', $pageNumber = 1)
	{
		$page_count = 20;
		$sql = "SELECT p.*, pd.name, " .
				" (select image_url FROM `hc_product_image` pi where pi.product_id=p.product_id order by pi.sort_order limit 1) as pi_image_url, " .
				" (select name from hc_manufacturer m where m.manufacturer_id=p.manufacturer_id) as m_name, " .
				" (select cn_name from gta_city c where c.city_code = p.city_id ) as city_cn_name " .
				" FROM hc_product p LEFT JOIN hc_product_description pd ON pd.product_id=p.product_id and pd.language_id=2 " .
				" WHERE 1=1 " . $condition;

		if (!empty($sortedBy)) {
			$sql .= ' ORDER BY ' . $sortedBy . ' ' . $sortDir;
		}

		$sql .= ' LIMIT ' . $page_count * ($pageNumber - 1) . ", " . $page_count;

		$connection = Yii::app()->db;
		$command = $connection->createCommand($sql);

		return $command->queryAll();
	}

	public function getProductTotal($condition = '')
	{
		$sql = "SELECT count(*) as total " .
				" FROM hc_product p LEFT JOIN hc_product_description pd ON pd.product_id=p.product_id and pd.language_id=2 " .
				" WHERE 1=1 " . $condition;

		$connection = Yii::app()->db;
		$command = $connection->createCommand($sql);

		$result = $command->queryRow();

		return $result['total'];
	}

	public function getProducts($reqData)
	{
		$sortDir = $this->getParam($reqData, 'sortDir', 'ASC');
		$sortedBy = $this->getParam($reqData, 'sortedBy', 'product_id');
		$pageNumber = $this->getParam($reqData, 'pageNumber', 1);

		$product_id = $this->getParam($reqData, 'product_id', 0);
		$city_id = $this->getParam($reqData['city'], 'city_code');
		$manufacturer_id = $this->getParam($reqData['manufacturer'], 'manufacturer_id', 0);
		$product_name = $this->getParam($reqData, 'product_name', '');
		$editing_state = $this->getParam($reqData, 'editing_state', -1);
		$product = $this->getParam($reqData, 'product');

		$condition = $this->getQueryCondition($product_id, $city_id, $manufacturer_id, $product_name, $editing_state, $product);
		$total = $this->getProductTotal($condition);
		$result = $this->getProductDetailAll($condition, $sortDir, $sortedBy, $pageNumber);

		$data = array();
		foreach ($result as $row) {
			$data[] = array('product_id' => $row['product_id'],
					'name' => $row['name'],
					'image_url' => $row['pi_image_url'],
					'manufacturer_id' => $row['manufacturer_id'],
					'm_name' => $row['m_name'],
					'city_cn_name' => $row['city_cn_name'],
					'city_id' => $row['city_id'],
					'editing_state' => $row['editing_state'],
					'price' => $row['price'],
					'child_price' => $row['child_price'],
					'product_id' => $row['product_id']
			);
		}

		return array('data' => $data, 'total' => $total);
	}

	private function getQueryCondition($product_id = 0, $city_id = 0, $manufacturer_id = 0, $product_name = '', $editing_state = -1, $product = '')
	{
		$sql = '';
		if ($product_id > 0) {
			$sql .= " AND p.product_id = " . (int)$product_id;
		} else if (!empty($product) && is_numeric($product)) {
			$sql .= ' AND p.product_id = ' . (int)$product;
		} else {
			if (!empty($city_id)) {
				$sql .= ' AND p.city_id = "' . $city_id . '"';
			}
			if ($manufacturer_id > 0) {
				$sql .= ' AND p.manufacturer_id = ' . $manufacturer_id;
			}

			if (!empty($product_name)) {
				$sql .= ' AND pd.name like "%' . $product_name . '%"';
			}

			if ($editing_state > 0) {
				$sql .= ' AND p.editing_state = ' . (int)$editing_state;
			}

			if (!empty($product)) {
				$sql .= ' AND pd.name like "%' . $product . '%"';
			}
		}

		return $sql;
	}

	public function getProductIDs($city_id, $manufacturer_id, $search_str, $page_num = 1, $page_count = 20, $sortDir = 'ASC', $sortedBy = 'product_id')
	{
		$sql = "SELECT p.product_id FROM hc_product p " .
				"LEFT JOIN hc_product_description pd ON pd.product_id=p.product_id and pd.language_id=2 WHERE 1=1 ";
		if ($city_id > 0) {
			$sql .= ' AND p.city_id = ' . $city_id;
		}
		if ($manufacturer_id > 0) {
			$sql .= ' AND p.manufacturer_id = ' . $manufacturer_id;
		}

		if (!empty($search_str) && str_len($search_str) > 0) {
			//			$c->addCondition('')
			$sql .= ' AND pd.name like "%' . $search_str . '%"';
			if (is_numeric($search_str)) {
				$sql .= ' OR p.product_id = ' . (int)$search_str;
			}
		}
		if (!empty($sortedBy)) {
			$sql .= ' ORDER BY ' . $sortedBy . ' ' . $sortDir;
		}

		$sql .= ' LIMIT ' . $page_count * $page_num . ", " . $page_count;

		$connection = Yii::app()->db;
		$command = $connection->createCommand($sql);

		$rows = $command->queryAll();

		$data = array();
		foreach ($rows as $row) {
			array_push($data, $row['product_id']);
		}

		return $data;
	}

	public function getCityIDsHaveProduct()
	{
		$criteria = new CDbCriteria;
		$criteria->distinct = true;
		$criteria->select = 'city_id';

		$result = $this->findAll($criteria);
		$data = array();
		foreach ($result as $row) {
			array_push($data, $row['city_id']);
		}

		return $data;
	}

	public function getOnlineProductCityIDs()
	{
		$criteria = new CDbCriteria;
		$criteria->addInCondition('editing_state', array(3, 5));
		$criteria->distinct = true;
		$criteria->select = 'city_id';

		$result = $this->findAll($criteria);
		$data = array();
		foreach ($result as $row) {
			array_push($data, $row['city_id']);
		}

		return $data;
	}

	private function getParam($reqData, $key, $default = null)
	{
		if (isset($reqData[$key])) {
			return $reqData[$key];
		}
		return $default;
	}
}
