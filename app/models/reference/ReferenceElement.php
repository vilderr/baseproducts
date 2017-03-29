<?php

namespace app\models\reference;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table 'reference_element'.
 *
 * @property integer $id
 * @property string $name
 * @property integer $reference_id
 * @property integer $reference_section_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $code
 * @property string $xml_id
 * @property integer $active
 * @property integer $sort
 * @property integer $detail_picture
 * @property integer $preview_picture
 * @property double $price
 * @property double $oldprice
 * @property integer $discount
 * @property string $currency
 * @property string $shop
 * @property string $picture_src
 * @property string $item_url
 * @property string $current_section
 * @property string $current_props
 * @property string $brand
 *
 * @property Reference $reference
 * @property ReferenceSection $referenceSection
 */
class ReferenceElement extends \yii\db\ActiveRecord
{
    /**
     * @var ReferenceProperty[]
     */
    public $arProperty = [];

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reference_element';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'reference_id'], 'required'],
            [['reference_id', 'reference_section_id', 'created_at', 'updated_at', 'active', 'sort', 'detail_picture', 'preview_picture', 'discount'], 'integer'],
            [['price', 'oldprice'], 'number'],
            [['name', 'code', 'xml_id', 'shop', 'picture_src', 'brand'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 3],
            [['reference_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reference::className(), 'targetAttribute' => ['reference_id' => 'id']],
            [['item_url', 'current_section', 'current_props'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                   => Yii::t('app/reference', 'ID'),
            'name'                 => Yii::t('app/reference', 'Name'),
            'reference_id'         => Yii::t('app/reference', 'Reference ID'),
            'reference_section_id' => Yii::t('app/reference', 'Reference Section ID'),
            'created_at'           => Yii::t('app/reference', 'Created At'),
            'updated_at'           => Yii::t('app/reference', 'Updated At'),
            'code'                 => Yii::t('app/reference', 'Code'),
            'xml_id'               => Yii::t('app/reference', 'Xml ID'),
            'active'               => Yii::t('app/reference', 'Active'),
            'sort'                 => Yii::t('app/reference', 'Sort'),
            'detail_picture'       => Yii::t('app/reference', 'Detail Picture'),
            'preview_picture'      => Yii::t('app/reference', 'Preview Picture'),
            'price'                => Yii::t('app/reference', 'Price'),
            'oldprice'             => Yii::t('app/reference', 'Oldprice'),
            'discount'             => Yii::t('app/reference', 'Discount'),
            'currency'             => Yii::t('app/reference', 'Currency'),
            'subsections'          => Yii::t('app/reference', 'Include Subsections'),
            'section_id'           => Yii::t('app/reference', 'Parent Section ID'),
            'shop'                 => Yii::t('app/reference', 'Shop'),
            'picture_src'          => Yii::t('app/reference', 'Picture Src'),
            'item_url'             => Yii::t('app/reference', 'Item Url'),
            'current_section'      => Yii::t('app/reference', 'Current Section'),
            'current_props'        => Yii::t('app/reference', 'Current Props'),
            'brand'                => Yii::t('app/reference', 'Brand'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReference()
    {
        return $this->hasOne(Reference::className(), ['id' => 'reference_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferenceSection()
    {
        return $this->hasOne(ReferenceSection::className(), ['id' => 'reference_section_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperties()
    {
        return $this->hasMany(ReferenceElementProperty::className(), ['element_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\reference\query\ReferenceElementQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\reference\query\ReferenceElementQuery(get_called_class());
    }

    /**
     * @return ReferenceProperty[]|array
     */
    public function initProperties()
    {
        $properties = ReferenceProperty::find()->forReference($this->reference_id)->indexBy('id')->with([
            'elementProperties' => function ($query) {
                /* @var $query \app\models\reference\query\ReferenceElementPropertyQuery */
                $query->forElement(intval($this->id));
            },
        ])->all();

        return $properties;
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert))
            return false;

        return true;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        foreach ($this->arProperty as $PID => $props) {
            /**
             * @var $props ReferenceElementProperty[]
             */
            foreach ($props as $property) {
                $property->element_id = $this->id;

                if (!$property->value) {
                    $property->unlink('element', $this, true);
                    continue;
                }

                $property->link('element', $this);
            }
        }
    }

    /**
     * @return bool|false|int
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete())
            return false;

        $result = true;
        foreach ($this->properties as $property) {
            /**
             * @var $property ReferenceElementProperty
             */
            if (!$result = $property->delete())
                break;
        }

        return $result;
    }
}
