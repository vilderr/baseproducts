<?php

namespace app\models\reference;

use Yii;

/**
 * This is the model class for table "reference_property".
 *
 * @property integer $id
 * @property string $name
 * @property string $xml_id
 * @property integer $reference_id
 * @property integer $sort
 * @property integer $active
 * @property string $type
 * @property integer $link_reference_id
 * @property integer $multiple
 * @property string $code
 * @property integer $service
 *
 * @property ReferenceElementProperty[] $referenceElementProperties
 * @property Reference $reference
 */
class ReferenceProperty extends \yii\db\ActiveRecord
{
    const TYPE_STRING = 'S';
    const TYPE_INTEGER = 'N';
    const TYPE_LINK_ELEMENT = 'LE';
    const TYPE_LINK_SECTION = 'LS';

    /**
     * marker for unlink current property from reference
     * @var bool
     */
    public $delete = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reference_property';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['reference_id', 'sort', 'link_reference_id', 'active', 'multiple', 'service'], 'integer'],
            [['name', 'xml_id', 'code'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 2],
            [['xml_id', 'code'], 'unique'],
            [['reference_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reference::className(), 'targetAttribute' => ['reference_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => Yii::t('app/reference', 'ID'),
            'name'              => Yii::t('app/reference', 'Name'),
            'xml_id'            => Yii::t('app/reference', 'Xml ID'),
            'reference_id'      => Yii::t('app/reference', 'Reference ID'),
            'sort'              => Yii::t('app/reference', 'Sort'),
            'active'            => Yii::t('app/reference', 'Active'),
            'type'              => Yii::t('app/reference', 'Type'),
            'link_reference_id' => Yii::t('app/reference', 'Link Reference ID'),
            'multiple'          => Yii::t('app/reference', 'Multiple'),
            'code'              => Yii::t('app/reference', 'Code'),
            'service'           => Yii::t('app/reference', 'Service Properety'),
            'delete'            => Yii::t('app/reference', 'Delete'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getElementProperties()
    {
        return $this->hasMany(ReferenceElementProperty::className(), ['property_id' => 'id'])->indexBy('id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReference()
    {
        return $this->hasOne(Reference::className(), ['id' => 'reference_id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\reference\query\ReferencePropertyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\reference\query\ReferencePropertyQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_STRING       => 'Строка',
            self::TYPE_INTEGER      => 'Число',
            self::TYPE_LINK_ELEMENT => 'Элемент справочника',
            self::TYPE_LINK_SECTION => 'Раздел справочника',
        ];
    }

    /**
     * @return array
     */
    public static function getAdditionalTypes()
    {
        return [
            self::TYPE_LINK_ELEMENT,
            self::TYPE_LINK_SECTION,
        ];
    }
}
