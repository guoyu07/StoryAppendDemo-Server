<?php
/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/13/14
 * Time: 9:46 AM
 */

/**
 * This is the model class for table "ht_product_image".
 *
 * The followings are the available columns in table 'ht_product_image':
 * @property integer $product_image_id
 * @property integer $product_id
 * @property string $image
 * @property string $image_url
 * @property integer $landinfo_id
 * @property integer $image_usage
 * @property integer $as_cover
 * @property integer $sort_order
 * @property string $name
 * @property string $short_desc
 * @property integer $changed
 */
class HtProductImage extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HtProductImage the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ht_product_image';
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
            array('product_id, landinfo_id, image_usage, as_cover, sort_order, changed', 'numerical', 'integerOnly' => true),
            array('image, image_url', 'length', 'max' => 255),
            array('name', 'length', 'max' => 64),
            array('short_desc', 'length', 'max' => 128),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('product_image_id, product_id, image, image_url, landinfo_id, image_usage, as_cover, sort_order, name, short_desc, changed', 'safe', 'on' => 'search'),
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
            'landinfo_image' => array(self::HAS_ONE, 'Landinfo', array('landinfo_id' => 'landinfo_id'), 'select' => 'reason,image_url'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'product_image_id' => 'Product Image',
            'product_id'       => 'Product',
            'image'            => 'Image',
            'image_url'        => 'Image Url',
            'landinfo_id'      => 'Landinfo',
            'image_usage'      => 'Image Usage',
            'as_cover'         => 'As Cover',
            'sort_order'       => 'Sort Order',
            'name'             => 'Name',
            'short_desc'       => 'Short Desc',
            'changed'          => 'Changed',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('product_image_id', $this->product_image_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('image', $this->image, true);
        $criteria->compare('image_url', $this->image_url, true);
        $criteria->compare('landinfo_id', $this->landinfo_id);
        $criteria->compare('image_usage', $this->image_usage);
        $criteria->compare('as_cover', $this->as_cover);
        $criteria->compare('sort_order', $this->sort_order);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('short_desc', $this->short_desc, true);
        $criteria->compare('changed', $this->changed);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'pi',
            'order' => 'sort_order ASC',
        );
    }

    public function afterFind()
    {
        if ($this->image_usage == 2) {
            // get image by landinfo_id
            $landinfo = Landinfo::model()->findByPk($this->landinfo_id);
            if (!empty($landinfo)) {
                $this->image_url = $landinfo['image_url'];
            }
        }
    }

    public function getProductCover($product_id)
    {
        $c = new CDbCriteria();
        $c->addCondition('product_id=' . $product_id);
        $c->addCondition('as_cover=1');
        $item = $this->find($c);
        if (!empty($item)) {
            $image_url = $item['image_url'];
            if ($item['image_usage'] == 2) {
                // get image by landinfo_id
                $landinfo = Landinfo::model()->findByPk($item['landinfo_id']);
                if (!empty($landinfo)) {
                    $image_url = $landinfo['image_url'];
                }
            }

            return $image_url;
        }

        return '';
    }

    public function isSample()
    {
        return $this->image_usage == 0;
    }

    public function isCover()
    {
        return $this->as_cover;
    }

    public static function addProductImage($data)
    {
        $pi = new HtProductImage();
        ModelHelper::fillItem($pi, $data,
                              ['product_id', 'changed', 'image', 'image_url', 'image_usage', 'landinfo_id']);

        $result = $pi->save();

        return $result ? $pi : null;
    }
}