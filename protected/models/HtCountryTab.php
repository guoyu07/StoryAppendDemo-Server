<?php

/**
 * This is the model class for table "ht_country_tab".
 *
 * The followings are the available columns in table 'ht_country_tab':
 * @property integer $tab_id
 * @property string $country_code
 * @property string $name
 * @property string $title
 * @property string $brief
 * @property string $description
 * @property integer $status
 * @property integer $display_order
 */
class HtCountryTab extends CActiveRecord
{

    const CACHE_KEY_COUNTRY_TABS = 'country_tabs_';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_country_tab';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('country_code, name, title, brief, description, status, display_order', 'required'),
            array('status, display_order', 'numerical', 'integerOnly' => true),
            array('country_code', 'length', 'max' => 4),
            array('name', 'length', 'max' => 64),
            array('title, brief', 'length', 'max' => 100),
            array('description', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('tab_id, country_code, name, title, brief, description, status, display_order', 'safe', 'on' => 'search'),
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
            'groups'        => array(self::HAS_MANY, 'HtCountryGroup', 'tab_id'),
            'groups_online' => array(self::HAS_MANY, 'HtCountryGroup', 'tab_id', 'condition' => 'cg.status=2'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'tab_id'        => 'Tab',
            'country_code'  => 'Country Code',
            'name'          => 'Name',
            'title'         => 'Title',
            'brief'         => 'Brief',
            'description'   => 'Description',
            'status'        => 'Status',
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

        $criteria = new CDbCriteria;

        $criteria->compare('tab_id', $this->tab_id);
        $criteria->compare('country_code', $this->country_code, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('brief', $this->brief, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('display_order', $this->display_order);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtCountryTab the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'ct',
            'order' => 'ct.display_order ASC',
        );
    }

    protected function beforeSave()
    {
        HtCountryTab::clearCache($this->country_code);

        return parent::beforeSave();
    }

    public static function  clearCache($country_code)
    {
        $cache_key = HtCountryTab::CACHE_KEY_COUNTRY_TABS . $country_code;
        Yii::app()->cache->delete($cache_key);
    }

    public static function getTabs($country_code)
    {
        $cache_key = HtCountryTab::CACHE_KEY_COUNTRY_TABS . $country_code;

        $result = Yii::app()->cache->get($cache_key);
        if (!empty($result)) {
//            return $result; // remove comment when online
        }

        $result = HtCountryTab::model()->with('groups_online.refs')->findAllByAttributes(['country_code' => $country_code, 'status' => 2]);
        if (empty($result)) {
            return [];
        }

        $result = Converter::convertModelToArray($result);
        foreach ($result as &$tab) {
            foreach ($tab['groups_online'] as $key => &$group) {
                if (!empty($group['city_code'])) {
                    $city = HtCity::model()->findByPk($group['city_code']);
                    $group['city_cn_name'] = $city['cn_name'];
                    $group['city_link_url'] = $city['link_url'];
                    $group['city_link_url_m'] = $city['link_url_m'];
                }
                foreach ($group['refs'] as $ref_key => &$ref) {
                    if ($ref['status'] == 1) {
                        unset($group['refs'][$ref_key]);
                        $group['refs'] = array_values($group['refs']);
                        continue;
                    }

                    $detail = HtCountryGroupRef::getRefDetail($group['type'], $ref['id']);

                    $ref['detail'] = $detail;

                    if ($group['type'] == HtCountryGroup::TYPE_PRODUCT || $group['type'] == HtCountryGroup::TYPE_LINE) {
                        if (empty($detail)) {
                            unset($group['refs'][$ref_key]);
                            $group['refs'] = array_values($group['refs']);
                        }
                    }
                }
            }
            $tab['groups'] = $tab['groups_online'];
            unset($tab['groups_online']);
        }

        Yii::app()->cache->set($cache_key, $result, 5 * 60);

        return $result;
    }

    public static function hasTab($country_code)
    {
        $result = HtCountryTab::model()->findByAttributes(['country_code' => $country_code, 'status' => 2]);

        return !empty($result);
    }
}
