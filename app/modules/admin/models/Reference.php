<?php

namespace app\modules\admin\models;

use Yii;
use yii\behaviors\TimestampBehavior;

use app\modules\admin\models\query\ReferenceQuery;

/**
 * This is the model class for table "reference".
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
 */
class Reference extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            'CacheBehavior' => [
                'class' => \app\modules\admin\behaviors\CacheBehavior::className(),
                'cache_id' => [
                    'admin_menu_reference'
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reference}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'reference_type_id'], 'required'],
            ['name', 'string', 'max' => 255],

            [['created_at', 'updated_at', 'active', 'sort'], 'integer'],
            [['reference_type_id'], 'string', 'max' => 50],
            [['code', 'xml_id'], 'string', 'max' => 255],
            [['code', 'xml_id'], 'match', 'pattern' => '#^[\w_-]+$#i'],
            ['code', 'unique', 'targetClass' => self::className(), 'message' => 'This code address has already been taken.'],
            ['xml_id', 'unique', 'targetClass' => self::className(), 'message' => 'This code address has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reference_type_id' => 'Reference Type ID',
            'name' => 'Название',
            'code' => 'Символьный код',
            'xml_id' => 'Внешний код',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
            'active' => 'Активность',
            'sort' => 'Сортировка',
        ];
    }

    /**
     * @inheritdoc
     * @return ReferenceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ReferenceQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperties()
    {
        return $this->hasMany(ReferenceProp::className(), ['reference_id' => 'id'])->indexBy('id')->orderBy('sort');
    }

    /**
     * @return array
     */
    public static function menuItems()
    {
        $cache = Yii::$app->cache;
        $key = 'admin_menu_reference';

        $items = $cache->getOrSet($key, function () {

            $newItems = [];
            $types = ReferenceType::find()->with('reference')->all();
            foreach ($types as $type) {
                $newItems [] = [
                    'label' => $type->name,
                    'url'   => '#',
                    'items' => self::getChildItems($type),
                ];
            }

            return $newItems;
        });

        return $items;
    }

    /**
     * @param ReferenceType $type
     * @return array
     */
    protected static function getChildItems(ReferenceType $type)
    {
        $items = [];

        foreach ($type->reference as $reference) {
            $items[] = [
                'label' => $reference->name,
                'url'   => ['/admin/reference-section/index', 'reference_id' => $reference->id, 'reference_section_id' => 0],
            ];
        }

        return $items;
    }

    /**
     * delete all linked tables
     * delete all sections ()
     * delete all elenents where reference_section_id = 0 but others will be deleted with sections
     * delete all properties
     * @return bool
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        /* delete sections */
        $sectionModel = new ReferenceSection(['reference_id' => $this->id]);
        $sections = $sectionModel::find()->where(['depth' => 0])->all();
        foreach ($sections as $section) {
            if (!$section->deleteWithChildren()) {
                return false;
            }
        }
        /* end delete */

        /* delete elements where reference_section_id = 0 */
        $elementModel = new ReferenceElement(['reference_id' => $this->id]);
        foreach ($elementModel::find()->batch(1000) as $elements) {
            foreach ($elements as $element) {
                /**
                 * @var $element \app\modules\admin\models\ReferenceElement
                 */
                if (!$element->delete()) {
                    return false;
                }
            }
        }
        /* end delete */

        /* delete reference properties */
        $properties = $this->properties;
        foreach ($properties as $property) {
            /**
             * @var $property \app\modules\admin\models\ReferenceProp
             */
            if (!$property->delete()) {
                return false;
            }
        }
        /* end delete */

        /* delete tables */

        Yii::$app->db->createCommand()
            ->dropTable('reference_' . $this->id . '_element_property')
            ->query();

        Yii::$app->db->createCommand()
            ->dropTable('reference_' . $this->id . '_element')
            ->query();

        Yii::$app->db->createCommand()
            ->dropTable('reference_' . $this->id . '_section')
            ->query();

        /* end delete */

        return true;
    }
}
