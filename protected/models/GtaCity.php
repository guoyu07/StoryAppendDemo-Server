<?php

/**
 * This is the model class for table "gta_city".
 *
 * The followings are the available columns in table 'gta_city':
 * @property string $country_code
 * @property string $city_code
 * @property string $name
 * @property string $cn_name
 * @property string $en_name
 * @property string $pinyin
 */
class GtaCity extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gta_city';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('country_code, city_code, name, cn_name, en_name', 'required'),
				array('country_code', 'length', 'max' => 2),
				array('city_code', 'length', 'max' => 4),
				array('name, cn_name, en_name, pinyin', 'length', 'max' => 128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
				array('country_code, city_code, name, cn_name, en_name, pinyin', 'safe', 'on' => 'search'),
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
				'country_code' => 'Country Code',
				'city_code' => 'City Code',
				'name' => 'Name',
				'cn_name' => 'Cn Name',
				'en_name' => 'En Name',
				'pinyin' => 'Pinyin',
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

		$criteria->compare('country_code', $this->country_code, true);
		$criteria->compare('city_code', $this->city_code, true);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('cn_name', $this->cn_name, true);
		$criteria->compare('en_name', $this->en_name, true);
		$criteria->compare('pinyin', $this->pinyin, true);

		return new CActiveDataProvider($this, array(
				'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GtaCity the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function getCountryCityInfo($cityIDs)
	{
		$city_ids = "'" . implode("','", $cityIDs) . "'";

		$sql = "SELECT DISTINCT continent_id, country.name as country_name, country.code as country_code,
				city_code, city.cn_name AS city_name, city.en_name AS city_en_name, city.pinyin AS city_pinyin
				FROM gta_city AS city LEFT JOIN gta_country as country on city.country_code=country.code
				WHERE city_code
				IN (" . $city_ids . ")
				ORDER BY city.pinyin";
//				ORDER BY continent_id, country_name, city_name";


		$connection=Yii::app()->db;
		$command=$connection->createCommand($sql);

		$rows=$command->queryAll();

		foreach ($rows as $result) {
			$data[] = array(
					'continent_id' => $result['continent_id'],
					'country_name' => $result['country_name'],
					'country_code' => $result['country_code'],
					'city_code' => $result['city_code'],
					'city_name' => $result['city_name'],
					'city_en_name' => $result['city_en_name'],
					'city_pinyin' => $result['city_pinyin']
			);
		}
		return $data;
	}

	public function getManufacturersOfCity($city_id)
	{
		// TODO filter supplier by city_id
		$c = new CDbCriteria();
		$c->select = array('manufacturer_id', 'name');
		$result = HtManufacturer::model()->findAll($c);

		$ret = array();
		foreach ($result as $row) {
			$ret[] = array($row['manufacturer_id'], $row['name']);
		}

		echo CJSON::encode(array('code' => 200, 'msg' => '', 'data' => $ret));
	}
}
