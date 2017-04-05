<?php

namespace app\models\file;

use Yii;

/**
 * This is the model class for table "file".
 *
 * @property integer $id
 * @property string $name
 * @property string $original_name
 * @property string $external_id
 * @property integer $height
 * @property integer $width
 * @property integer $size
 * @property string $type
 * @property string $subdir
 */
class BaseFile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['height', 'width', 'size'], 'integer'],
            [['name', 'original_name', 'type', 'subdir'], 'string', 'max' => 255],
            [['external_id'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'original_name' => Yii::t('app', 'Original Name'),
            'external_id' => Yii::t('app', 'External ID'),
            'height' => Yii::t('app', 'Height'),
            'width' => Yii::t('app', 'Width'),
            'size' => Yii::t('app', 'Size'),
            'type' => Yii::t('app', 'Type'),
            'subdir' => Yii::t('app', 'Subdir'),
        ];
    }
}
