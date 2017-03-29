<?php

namespace app\modules\admin\models;

use Yii;
use app\modules\admin\models\query\YmlFileQuery;

/**
 * This is the model class for table "yml_import_file".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $name
 * @property string $value
 * @property string $attrs
 */
class YmlFile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%yml_import_file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['parent_id', 'integer', 'skipOnError' => true],
            [['lft', 'rgt', 'depth'], 'integer', 'skipOnError' => true],
            [['value'], 'string', 'skipOnError' => true],
            [['attrs'], 'string', 'skipOnError' => true],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'depth' => 'Depth',
            'name' => 'Name',
            'value' => 'Value',
            'attrs' => 'Attributes',
        ];
    }

    /**
     * @inheritdoc
     * @return YmlFileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new YmlFileQuery(get_called_class());
    }
}
