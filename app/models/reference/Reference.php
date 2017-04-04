<?php

namespace app\models\reference;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table 'reference'.
 *
 * @property integer $id
 * @property string $reference_type_id
 * @property string $name
 * @property string $code
 * @property string $xml_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $active
 * @property integer $sort
 * @property integer $catalog
 *
 * @property ReferenceType $referenceType
 * @property ReferenceElement[] $referenceElements
 * @property ReferenceProperty[] $referenceProperties
 * @property ReferenceSection[] $referenceSections
 */
class Reference extends \yii\db\ActiveRecord
{
    const ACTIVE = 1;
    const IN_ACTIVE = 0;

    const IS_CATALOG = 1;
    const IS_NOT_CATALOG = 0;

    /**
     * @var \app\models\reference\ReferenceProperty[]
     */
    public $arProperty = [];

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
        return 'reference';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at', 'updated_at', 'active', 'sort', 'catalog'], 'integer'],
            [['reference_type_id'], 'string', 'max' => 50],
            [['name', 'code', 'xml_id'], 'string', 'max' => 255],
            [['reference_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReferenceType::className(), 'targetAttribute' => ['reference_type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => Yii::t('app/reference', 'ID'),
            'reference_type_id' => Yii::t('app/reference', 'Reference Type ID'),
            'name'              => Yii::t('app/reference', 'Name'),
            'code'              => Yii::t('app/reference', 'Code'),
            'xml_id'            => Yii::t('app/reference', 'Xml ID'),
            'created_at'        => Yii::t('app/reference', 'Created At'),
            'updated_at'        => Yii::t('app/reference', 'Updated At'),
            'active'            => Yii::t('app/reference', 'Active'),
            'sort'              => Yii::t('app/reference', 'Sort'),
            'catalog'           => Yii::t('app/reference', 'Catalog'),
        ];
    }

    /**
     * @return array
     */
    public function attributeHints()
    {
        return [
            'xml_id'  => 'Код для загрузки и доступа из внешних источников',
            'catalog' => Yii::t('app/reference', 'is-catalog-hint'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferenceType()
    {
        return $this->hasOne(ReferenceType::className(), ['id' => 'reference_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferenceElements()
    {
        return $this->hasMany(ReferenceElement::className(), ['reference_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferenceProperties()
    {
        return $this->hasMany(ReferenceProperty::className(), ['reference_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferenceSections()
    {
        return $this->hasMany(ReferenceSection::className(), ['reference_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\reference\query\ReferenceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\reference\query\ReferenceQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperties()
    {
        return $this->hasMany(ReferenceProperty::className(), ['reference_id' => 'id'])->indexBy('id');
    }

    /**
     * @return array
     */
    public static function getStatusArray()
    {
        return [
            self::IN_ACTIVE => Yii::t('app/reference', 'filter-active-no'),
            self::ACTIVE    => Yii::t('app/reference', 'filter-active-yes'),
        ];
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        foreach ($this->arProperty as $property) {
            if (!$property->delete) {
                $property->link('reference', $this);
                continue;
            }

            $property->unlink('reference', $this, true);
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
            /* @var $property \app\models\reference\ReferenceProperty */
            $result = $property->delete();
            if (!$result)
                break;
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getSectionTree()
    {
        $model = new ReferenceSection(['reference_id' => $this->id]);
        return $model->getTree();
    }
}
