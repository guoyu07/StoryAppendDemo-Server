<?php

/**
 * This is the model class for table "ht_trip_highlight".
 *
 * The followings are the available columns in table 'ht_trip_highlight':
 * @property integer $id
 * @property integer $product_id
 * @property integer $total_days
 * @property integer $distance
 * @property string $highlight_summary
 * @property string $start_location
 * @property string $finish_location
 * @property string $tour_cities
 * @property string $suitable_time
 */
class HtTripHighlight extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_trip_highlight';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id, total_days, distance, highlight_summary', 'required'),
            array('product_id, total_days, distance', 'numerical', 'integerOnly' => true),
            array('start_location, finish_location, tour_cities', 'length', 'max' => 255),
            array('suitable_time', 'length', 'max' => 45),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, product_id, total_days, distance, highlight_summary, start_location, finish_location, suitable_time', 'safe', 'on' => 'search'),
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
            'highlight_refs' => array(self::HAS_MANY, 'HtTripHighlightRef', 'highlight_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id'                => 'ID',
            'product_id'        => 'Product',
            'total_days'        => 'Total Days',
            'distance'          => 'Distance',
            'highlight_summary' => 'Highlight Summary',
            'start_location'    => 'Start Location',
            'finish_location'   => 'Finish Location',
            'tour_cities'       => 'Tour Cities',
            'suitable_time'     => 'Suitable Time',
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

        $criteria->compare('id', $this->id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('total_days', $this->total_days);
        $criteria->compare('distance', $this->distance);
        $criteria->compare('highlight_summary', $this->highlight_summary, true);
        $criteria->compare('start_location', $this->start_location, true);
        $criteria->compare('finish_location', $this->finish_location, true);
        $criteria->compare('tour_cities', $this->tour_cities, true);
        $criteria->compare('suitable_time', $this->suitable_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getProductTripHighlights($product_id)
    {
        $result = HtTripHighlight::model()->with('highlight_refs')->findByAttributes(['product_id' => $product_id]);
        $result = Converter::convertModelToArray($result);
        if (!empty($result) && isset($result['highlight_summary'])) {
            $result['highlight_summary'] = explode(';', $result['highlight_summary']);
        }

        //线路商品
        if (!empty($result) && isset($result['tour_cities'])) {
            $tour_cities = explode(';',$result['tour_cities']);
            $cities = array();
            if(is_array($tour_cities)){
                foreach($tour_cities as $city_code){
                    $city = HtCity::model()->findByPk($city_code);
                    if($city){
                        array_push($cities,array('city_code'=>$city_code,'city_name'=>$city['cn_name']));
                    }
                }
            }
            $result['tour_cities'] = $cities;
        }


        $dates_arr = array();
        if (!empty($result['highlight_refs'])) {
            foreach ($result['highlight_refs'] as $ref) {
                $dates_arr[] = $ref['date'];
            }
            array_multisort($dates_arr, SORT_ASC, SORT_NUMERIC, $result['highlight_refs']);
        }

        return $result;
    }

    public function getLocalHighlightFromTourPlan($product_id, $the_day)
    {
        $condition = array(
            'highlight_refs' => array(
                'select'    => 'local_highlight',
                'condition' => 'date=' . $the_day
            )
        );
        $one_day = HtTripHighlight::model()->with($condition)->findByAttributes(['product_id' => $product_id]);

        $result = Converter::convertModelToArray($one_day);

        return isset($result['highlight_refs']) ? $result['highlight_refs'][0]['local_highlight'] : '';
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtTripHighlight the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
