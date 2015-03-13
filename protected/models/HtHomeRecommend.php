<?php

/**
 * This is the model class for table "ht_home_recommend".
 *
 * The followings are the available columns in table 'ht_home_recommend':
 * @property integer $group_id
 * @property string $name
 * @property string $title
 * @property string $brief
 * @property integer $type
 * @property integer $status
 * @property integer $display_order
 * @property string $cover_url
 */
class HtHomeRecommend extends CActiveRecord
{
    const CACHE_KEY_ALL_WITH_ITEMS_CITY = 'home_recommend_all_with_items_city';
    const CACHE_KEY_BY_PK_WITH_ITEMS_CITY = 'home_recommend_by_pk_with_items_city_';

    const TYPE_PRODUCT = '1';
    const TYPE_CITY = '2';
    const TYPE_ACTIVITY = '3';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_home_recommend';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, title, brief, type, status, display_order', 'required'),
            array('type, status, display_order', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 16),
            array('title', 'length', 'max' => 32),
            array('brief', 'length', 'max' => 128),
            array('cover_url', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('group_id, name, title, brief, type, status, display_order, cover_url', 'safe', 'on' => 'search'),
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
            'items' => array(self::HAS_MANY, 'HtHomeRecommendItem', 'group_id'),
            'items_count' => array(self::STAT, 'HtHomeRecommendItem', 'group_id'),
        );
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'hr',
            'order' => 'hr.status DESC, hr.display_order ASC',
        );
    }

    public function scopes()
    {
        $scopes = array('published' => array('condition' => 'status=2'));

        return $scopes;
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'group_id' => 'Group',
            'name' => 'Name',
            'title' => 'Title',
            'brief' => 'Brief',
            'type' => '1-product;2-city',
            'status' => '2-生效;1-编辑中',
            'display_order' => '显示顺序',
            'cover_url' => 'Cover URL',
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('brief', $this->brief, true);
        $criteria->compare('type', $this->type);
        $criteria->compare('status', $this->status);
        $criteria->compare('display_order', $this->display_order);
        $criteria->compare('cover_url', $this->cover_url);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HtHomeRecommend the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function beforeDelete()
    {
        HtHomeRecommend::clearCache($this->group_id);

        return parent::beforeDelete();
    }

    public function beforeSave()
    {
        HtHomeRecommend::clearCache($this->group_id);

        return parent::beforeSave();
    }

    public static function clearCache($group_id = 0)
    {
        $key = HtHomeRecommend::CACHE_KEY_ALL_WITH_ITEMS_CITY;
        Yii::app()->cache->delete($key);

        $key = HtHomeRecommend::CACHE_KEY_BY_PK_WITH_ITEMS_CITY . $group_id;
        Yii::app()->cache->delete($key);
    }

    public function getAll()
    {
        $c = new CDbCriteria();
        $c->order = 'display_order ASC'; // 排序
        return HtHomeRecommend::model()->findAll($c);
    }

    public function findAllWithItemsCityCached()
    {
        $key = HtHomeRecommend::CACHE_KEY_ALL_WITH_ITEMS_CITY;
        $recommend_groups = Yii::app()->cache->get($key);
        if (empty($recommend_groups)) {
            $recommend = $this->with('items.city')->published()->findAll();
            $recommend_groups = Converter::convertModelToArray($recommend);

            Yii::app()->cache->set($key, $recommend_groups, 1 * 60 * 60);
        }

        return $recommend_groups;
    }

    public function findByPkWithItemsCityCached($group_id)
    {
        $key = HtHomeRecommend::CACHE_KEY_BY_PK_WITH_ITEMS_CITY . $group_id;
        $recommend_groups = Yii::app()->cache->get($key);
        if (empty($recommend_groups)) {
            $recommend = $this->with('items.city')->published()->findByPk($group_id);
            $recommend_groups = Converter::convertModelToArray($recommend);

            Yii::app()->cache->set($key, $recommend_groups, 1 * 60 * 60);
        }

        return $recommend_groups;
    }
}
