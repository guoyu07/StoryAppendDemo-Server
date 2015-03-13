<?php

/**
 * This is the model class for table "ht_product_tour_plan".
 *
 * The followings are the available columns in table 'ht_product_tour_plan':
 * @property integer $plan_id
 * @property integer $product_id
 * @property integer $total_days
 * @property integer $the_day
 * @property integer $is_online
 * @property string $title
 */
class HtProductTourPlan extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_tour_plan';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('product_id', 'required'),
            array('product_id, total_days, the_day, is_online', 'numerical', 'integerOnly' => true),
            array('title', 'length', 'max' => 50),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('plan_id, product_id, total_days, the_day, title', 'safe', 'on' => 'search'),
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
            'groups' => array(self::HAS_MANY, 'HtProductTourPlanGroup', 'plan_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'plan_id' => 'Plan',
            'product_id' => 'Product',
            'total_days' => 'Total Days',
            'the_day' => 'The Day',
            'is_online' => 'Is Online',
            'title' => 'Title',
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

        $criteria->compare('plan_id', $this->plan_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('total_days', $this->total_days);
        $criteria->compare('the_day', $this->the_day);
        $criteria->compare('is_online', $this->is_online);
        $criteria->compare('title', $this->title, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtProductTourPlan the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'ptp',
            'order' => 'ptp.the_day ASC',
        );
    }

    public static function copyTourPlan($product_id, $new_product_id)
    {
        $data = HtProductTourPlan::model()->with('groups.items')->findAll('product_id=' . $product_id);
        foreach ($data as $tour_plan) {
            $new_tour_plan = new HtProductTourPlan();
            ModelHelper::fillItem($new_tour_plan, $tour_plan, array('total_days', 'the_day', 'title', 'is_online'));
            $new_tour_plan['product_id'] = $new_product_id;
            $result = $new_tour_plan->insert();
            if ($result) {
                $new_tour_plan_id = $new_tour_plan['plan_id'];
                //  copy groups
                $groups = $tour_plan['groups'];
                foreach ($groups as $group) {
                    $new_tour_plan_group = new HtProductTourPlanGroup();
                    ModelHelper::fillItem($new_tour_plan_group, $group,
                                          array('title', 'time', 'display_order'));
                    $new_tour_plan_group['plan_id'] = $new_tour_plan_id;
                    $result = $new_tour_plan_group->insert();
                    if ($result) {
                        //  copy items
                        $new_group_id = $new_tour_plan_group['group_id'];
                        $items = $group['items'];
                        foreach ($items as $item) {
                            $new_tour_plan_item = new HtProductTourPlanItem();
                            ModelHelper::fillItem($new_tour_plan_item, $item,
                                                  array('image_url', 'title', 'description', 'display_order'));
                            $new_tour_plan_item['group_id'] = $new_group_id;
                            $new_tour_plan_item->insert();
                        }
                    }
                }
            }
        }
    }

    public static function hasTourPlan($product_id) {
        $result = false;

        $tour_plan = Converter::convertModelToArray(HtProductTourPlan::model()->findAllByAttributes(['product_id' => $product_id]));

        if(count($tour_plan) > 0) {
            foreach($tour_plan as $plan) {
                if($plan['is_online'] == 1) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    public static function getProductTourPlan($product_id) {
        $result = [];
        $product = HtProduct::model()->findByPk($product_id);
        $tour_plan = Converter::convertModelToArray(HtProductTourPlan::model()->with('groups.items')->findAllByAttributes(['product_id' => $product_id], ['order' => 'ptp.the_day ASC, groups.display_order ASC, ptpi.display_order ASC']));

        if(isset($tour_plan[0]) && $tour_plan[0]['is_online']) {
            $result = $tour_plan;
            if($product->type == HtProduct::T_MULTI_DAY) {
                foreach($tour_plan as &$plan) {
                    $plan['local_highlight'] = HtTripHighlight::model()->getLocalHighlightFromTourPlan($plan['product_id'], $plan['the_day']);
                }
            }
        }

        return $result;
    }

    public static function getProductTourPlanTitle($product_id) {
        $result = Converter::convertModelToArray(HtProductDescription::model()->findByAttributes(['product_id' => $product_id, 'language_id' => 2]));

        return $result['schedule'];
    }
}
