<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 27.03.17
 * Time: 9:53
 */

namespace app\models\reference\import;

use Yii;
use yii\db\ActiveRecord;

class YmlTree extends ActiveRecord
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
            'id'        => 'ID',
            'parent_id' => 'Parent ID',
            'lft'       => 'Lft',
            'rgt'       => 'Rgt',
            'depth'     => 'Depth',
            'name'      => 'Name',
            'value'     => 'Value',
            'attrs'     => 'Attributes',
        ];
    }

    /**
     * truncate current table
     */
    public static function truncateTable()
    {
        Yii::$app->db->createCommand()->truncateTable(self::tableName())->query();
    }
}