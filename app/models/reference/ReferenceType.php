<?php

namespace app\models\reference;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "reference_type".
 *
 * @property string $id
 * @property string $name
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $sort
 *
 * @property Reference[] $references
 */
class ReferenceType extends \yii\db\ActiveRecord
{
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
        return 'reference_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['created_at', 'updated_at', 'sort'], 'integer'],
            [['id'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app/reference', 'ID'),
            'name'       => Yii::t('app/reference', 'Name'),
            'created_at' => Yii::t('app/reference', 'Created At'),
            'updated_at' => Yii::t('app/reference', 'Updated At'),
            'sort'       => Yii::t('app/reference', 'Sort'),
        ];
    }

    public static function getReferenceMenu()
    {
        $items = [];
        $tree = self::find()->with('reference')->all();

        foreach ($tree as $type) {
            $items[] = [
                'label' => $type->name,
                'url'   => ['reference/index', 'type' => $type->id],
            ];
        }

        return $items;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReference()
    {
        return $this->hasMany(Reference::className(), ['reference_type_id' => 'id']);
    }

    /**
     * @return bool|false|int
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete())
            return false;

        $result = true;
        foreach ($this->references as $reference) {
            $result = $reference->delete();
            if (!$result)
                break;
        }
        return $result;
    }
}
