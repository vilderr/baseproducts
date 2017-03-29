<?php

namespace app\models\reference;

use Yii;

/**
 * This is the model class for table "reference_element_property".
 *
 * @property integer $id
 * @property integer $property_id
 * @property integer $element_id
 * @property string $value
 * @property string $marker
 *
 * @property ReferenceProperty $property
 */
class ReferenceElementProperty extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reference_element_property';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['property_id', 'element_id'], 'integer'],
            [['value', 'marker'], 'string'],
            [['property_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReferenceProperty::className(), 'targetAttribute' => ['property_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('app/reference', 'ID'),
            'property_id' => Yii::t('app/reference', 'Property ID'),
            'element_id'  => Yii::t('app/reference', 'Element ID'),
            'value'       => Yii::t('app/reference', 'Value'),
            'marker'      => Yii::t('app/reference', 'Import Marker'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperty()
    {
        return $this->hasOne(ReferenceProperty::className(), ['id' => 'property_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getElement()
    {
        return $this->hasOne(ReferenceElement::className(), ['id' => 'element_id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\reference\query\ReferenceElementPropertyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\reference\query\ReferenceElementPropertyQuery(get_called_class());
    }
}
