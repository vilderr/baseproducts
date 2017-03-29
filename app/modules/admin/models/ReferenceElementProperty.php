<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 16.03.17
 * Time: 16:13
 */

namespace app\modules\admin\models;


use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "reference_{{$reference_id}}_element_property".
 * @package app\modules\admin\models
 *
 * @property integer $id
 * @property integer $property_id
 * @property integer $element_id
 * @property string $value
 */
class ReferenceElementProperty extends ActiveRecord
{
    /**
     * @var integer
     */
    public $reference_id;
    /**
     * @var integer
     */
    static $_reference_id;
    /**
     * @var string
     */
    static $_table_name;

    /**
     * @deprecated tableName for dynamicModel
     */
    public function init()
    {
        parent::init();

        if ($this->reference_id !== null) {
            self::$_reference_id = $this->reference_id;
            self::$_table_name = 'reference_' . self::$_reference_id . '_element_property';
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
     * @return array
     */
    public function rules()
    {
        $rules = [
            [['property_id', 'value'], 'required'],
            [['property_id'], 'exist', 'skipOnError' => false, 'targetClass' => ReferenceProp::className(), 'targetAttribute' => ['property_id' => 'id']],
            [['value'], 'string'],
        ];

        if (ArrayHelper::isIn($this->referenceProperty->type, ReferenceProp::getAdditionalTypes())) {
            /**
             * @var $message string
             */
            switch ($this->referenceProperty->type) {
                case ReferenceProp::TYPE_LINK_ELEMENT:
                    $model = new ReferenceElement(['reference_id' => $this->referenceProperty->link_reference_id]);
                    $message = 'Выбран несуществующий элемент для привязки';
                    break;
                case ReferenceProp::TYPE_LINK_SECTION:
                    $model = new ReferenceSection(['reference_id' => $this->referenceProperty->link_reference_id]);
                    $message = 'Выбран несуществующий раздел для привязки';
                    break;
            }

            $rules [] = [['value'], 'exist', 'targetClass' => $model::className(), 'targetAttribute' => ['value' => 'id'], 'message' => $message];
        }

        return $rules;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'property_id' => 'Property ID',
            'element_id'  => 'Element ID',
            'value'       => 'Значение',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferenceProperty()
    {
        return $this->hasOne(ReferenceProp::className(), ['id' => 'property_id']);
    }
}