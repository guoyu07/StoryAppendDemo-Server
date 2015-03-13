<?php

/**
 * This is the model class for table "ht_country_group".
 *
 * The followings are the available columns in table 'ht_country_group':
 * @property integer $group_id
 * @property string $country_code
 * @property integer $tab_id
 * @property integer $type
 * @property string $name
 * @property string $brief
 * @property string $summary
 * @property string $description
 * @property string $cover_image_url
 * @property string $link_url
 * @property string $city_code
 * @property integer $status
 * @property integer $display_order
 */
class HtCountryGroup extends CActiveRecord
{
    const TYPE_PRODUCT = 1;
    const TYPE_LINE = 2;
    const TYPE_ARTICLE = 3;
    const TYPE_CITY = 4;
    const TYPE_GROUP = 5;
    const TYPE_AD = 6;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_country_group';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('country_code, tab_id, name, brief, summary, description, cover_image_url, status, display_order', 'required'),
            array('tab_id, type, status, display_order', 'numerical', 'integerOnly' => true),
            array('country_code, city_code', 'length', 'max' => 4),
            array('name', 'length', 'max' => 64),
            array('brief', 'length', 'max' => 100),
            array('summary, description, cover_image_url, link_url', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('group_id, country_code, tab_id, type, name, brief, summary, description, cover_image_url, link_url, city_code, status, display_order', 'safe', 'on' => 'search'),
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
            'refs' => array(self::HAS_MANY, 'HtCountryGroupRef', 'group_id'),
            'refs_online' => array(self::HAS_MANY, 'HtCountryGroupRef', 'group_id', 'condition' => 'cgr.status=2'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'group_id'        => 'Group',
            'country_code'    => 'Country Code',
            'tab_id'          => 'Tab',
            'type'            => 'Type',
            'name'            => 'Name',
            'brief'           => 'Brief',
            'summary'         => 'Summary',
            'description'     => 'Description',
            'cover_image_url' => 'Cover Image Url',
            'link_url'        => 'Link Url',
            'city_code'       => 'City Code',
            'status'          => 'Status',
            'display_order'   => 'Display Order',
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

        $criteria->compare('group_id', $this->group_id);
        $criteria->compare('country_code', $this->country_code, true);
        $criteria->compare('tab_id', $this->tab_id);
        $criteria->compare('type', $this->type);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('brief', $this->brief, true);
        $criteria->compare('summary', $this->summary, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('cover_image_url', $this->cover_image_url, true);
        $criteria->compare('link_url', $this->link_url, true);
        $criteria->compare('city_code', $this->city_code, true);
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
     * @return HtCountryGroup the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'cg',
            'order' => 'cg.display_order ASC',
        );
    }

    protected function beforeSave()
    {
        HtCountryTab::clearCache($this->country_code);
        return parent::beforeSave();
    }

    public function getGroupDetail($group_id)
    {
        $group = $this->with('refs_online')->findByPk($group_id);
        if(empty($group)) {
           return [];
        }

        $group = Converter::convertModelToArray($group);
        if (!empty($group['city_code'])) {
            $city = HtCity::model()->findByPk($group['city_code']);
            $group['city_cn_name'] = $city['cn_name'];
            $group['city_link_url'] = $city['link_url'];
            $group['city_link_url_m'] = $city['link_url_m'];
        }

        foreach($group['refs_online'] as $key => &$ref) {
            $ref['detail'] = HtCountryGroupRef::getRefDetail($group['type'], $ref['id']);
            if (empty($ref['detail'])) {
                unset($group['refs_online'][$key]);
                $group['refs_online'] = array_values($group['refs_online']);
            }
        }
        $group['refs'] = $group['refs_online'];
        unset($group['refs_online']);

        return $group;
    }

}
