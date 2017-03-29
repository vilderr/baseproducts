<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "reference_props".
 *
 * @property integer $id
 * @property string $name
 * @property integer $reference_id
 * @property integer $sort
 * @property integer $active
 * @property string $type
 * @property integer $link_reference_id
 * @property integer $multiple
 * @property string $code
 * @property string $xml_id
 *
 * @property Reference $reference
 */
class ReferenceProp extends \yii\db\ActiveRecord
{
    const TYPE_STRING = 'S';
    const TYPE_INTEGER = 'N';
    const TYPE_LINK_ELEMENT = 'LE';
    const TYPE_LINK_SECTION = 'LS';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reference_props}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['reference_id', 'sort', 'active', 'link_reference_id', 'multiple'], 'integer'],
            [['name', 'type', 'xml_id', 'multiple'], 'required'],
            [['type'], 'string', 'max' => 2],
            [['code', 'xml_id'], 'string', 'max' => 255],
            [['code', 'xml_id'], 'unique'],
            [['reference_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reference::className(), 'targetAttribute' => ['reference_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'reference_id' => 'Reference ID',
            'sort' => 'Сортировка',
            'active' => 'Active',
            'type' => 'Type',
            'link_reference_id' => 'Link Reference ID',
            'multiple' => 'Multiple',
            'code' => 'Символьный код',
            'xml_id' => 'Внешний код',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReference()
    {
        return $this->hasOne(Reference::className(), ['id' => 'reference_id']);
    }

    public static function getTypes()
    {
        return [
            self::TYPE_STRING       => 'Строка',
            self::TYPE_INTEGER      => 'Число',
            self::TYPE_LINK_ELEMENT => 'Элемент справочника',
            self::TYPE_LINK_SECTION => 'Раздел справочника',
        ];
    }

    public static function getAdditionalTypes()
    {
        return [
            self::TYPE_LINK_ELEMENT,
            self::TYPE_LINK_SECTION,
        ];
    }
}
