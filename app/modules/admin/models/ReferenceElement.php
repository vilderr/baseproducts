<?php

namespace app\modules\admin\models;

use Yii;
use app\modules\admin\models\query\ReferenceElementQuery;
use app\modules\admin\models\ReferenceSection;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "reference_{{$reference_id}}_element".
 *
 * @property integer $id
 * @property string $xml_id
 * @property string $tmp_id
 * @property integer $reference_id
 * @property string $name
 * @property integer $reference_section_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $active
 * @property integer $sort
 * @property integer $picture
 * @property double $price
 * @property double $oldprice
 * @property integer $discount
 * @property string $currency
 *
 * @property ReferenceSection $referenceSection
 */
class ReferenceElement extends \yii\db\ActiveRecord
{
    /**
     * @var null
     */
    public $reference_id = null;
    /**
     * @var \app\modules\admin\models\Reference
     */
    public $reference;
    /**
     * @var integer
     */
    static $_reference_id;
    /**
     * @var string
     */
    static $_table_name;

    static $_oldProperties;
    public $_properties;

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
     * @deprecated tableName for dynamicModel
     */
    public function init()
    {
        parent::init();

        if ($this->reference_id !== null) {
            self::$_reference_id = $this->reference_id;
            self::$_table_name = 'reference_' . self::$_reference_id . '_element';
        }
    }

    /**
     * @inheritdoc
     * @return string
     */
    public static function tableName()
    {
        return '{{%' . self::$_table_name . '}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['reference_section_id', 'created_at', 'updated_at', 'active', 'sort', 'picture', 'discount'], 'integer'],
            [['price', 'oldprice'], 'number'],
            [['name', 'currency', 'xml_id', 'tmp_id', 'shop'], 'string', 'max' => 255],
            [['xml_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'xml_id' => 'XML ID',
            'tmp_id' => 'TMP ID',
            'reference_id' => 'Reference ID',
            'name' => 'Название',
            'reference_section_id' => 'Раздел',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
            'active' => 'Активность',
            'sort' => 'Сортировка',
            'picture' => 'Картинка',
            'price' => 'Цена',
            'oldprice' => 'Старая цена',
            'shop' => 'Магазин',
            'discount' => 'Скидка',
            'currency' => 'Валюта',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferenceSection()
    {
        return $this->hasOne(ReferenceSection::className(), ['id' => 'reference_section_id']);
    }

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\query\ReferenceElementQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ReferenceElementQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        $propertyModel = $this->getPropertyModel();
        $properties = ArrayHelper::index($this->hasMany($propertyModel::className(), ['element_id' => 'id'])->all(), 'id', 'property_id');
        $arProperty = [];

        foreach ($this->reference->properties as $PID => $prop) {
            if (ArrayHelper::keyExists($PID, $properties)) {
                $arProperty[$PID] = $properties[$PID];
            } else {
                $arProperty[$PID] = null;
            }
        }

        if (self::$_oldProperties === null) {
            self::$_oldProperties = $arProperty;
        }

        return $arProperty;
    }

    /**
     * @return ReferenceElementProperty
     */
    public function getPropertyModel()
    {
        return new ReferenceElementProperty(['reference_id' => $this->reference_id]);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->setAttribute('reference_id', $this->reference_id);

        if (self::$_oldProperties !== null) {
            foreach (self::$_oldProperties as $PID => $oldProperty) {
                /**
                 * @var $oldProperty \app\modules\admin\models\ReferenceElementProperty[]
                 */
                if (!$oldProperty)
                    continue;

                foreach ($oldProperty as $id => $value) {
                    if (!isset($this->_properties[$PID][$id]))
                        $value->delete();
                }
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->_properties !== null) {
            foreach ($this->_properties as $property) {
                /**
                 * @var $property \app\modules\admin\models\ReferenceElementProperty[]
                 */
                if (!$property)
                    continue;

                foreach ($property as $value) {
                    $value->element_id = $this->id;
                    $value->save();
                }
            }
        }
    }

    public function afterFind()
    {
        $this->reference_id = $this->getAttribute('reference_id');
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete())
            return false;

        /* delete all element properties */
        $propertyModel = new ReferenceElementProperty(['reference_id' => $this->reference_id]);
        $properties = $propertyModel::find()->where(['element_id' => $this->id])->all();

        $result = true;
        foreach ($properties as $property) {
            if (!$result = $property->delete())
                return $result;
        }

        return $result;
    }
}
