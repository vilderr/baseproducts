<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 10.03.17
 * Time: 11:52
 */

namespace app\modules\admin\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\modules\admin\models\query\ReferenceSectionQuery;
use creocoder\nestedsets\NestedSetsBehavior;

/**
 * Class ReferenceSection
 * @package app\modules\admin\models
 */
class ReferenceSection extends ActiveRecord
{
    /**
     * @var integer
     */
    public $reference_id;

    /**
     * @var string
     */
    static $tableName;

    /**
     * @var integer
     */
    static $_reference_id;

    static $treeCacheid;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            'tree'          => [
                'class'         => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
            ],
            'CacheBehavior' => [
                'class'    => \app\modules\admin\behaviors\CacheBehavior::className(),
                'cache_id' => [
                    'reference_' . self::$_reference_id . '_section_tree',
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     * @deprecated tableName for dynamicModel
     */
    public function init()
    {
        parent::init();

        self::$_reference_id = $this->reference_id;
        self::$tableName = 'reference_' . self::$_reference_id . '_section';
        self::$treeCacheid = 'reference_' . self::$_reference_id . '_section_tree';
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%' . self::$tableName . '}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['reference_id', 'name'], 'required'],
            [['reference_id', 'reference_section_id', 'created_at', 'updated_at', 'active', 'sort'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'name'                 => 'Название',
            'sort'                 => 'Сортировка',
            'active'               => 'Активность',
            'reference_section_id' => 'Родительский раздел',
        ];
    }

    /**
     * @return array
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @return null|integer
     */
    public function getParentId()
    {
        $parent = $this->parent;

        return $parent ? $parent->id : null;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parents(1)->one();
    }

    public static function find()
    {
        return new ReferenceSectionQuery(get_called_class());
    }

    /**
     * @deprecated method of \yii\db\BaseActiveRecord class
     * @param array $row
     * @return static
     */
    public static function instantiate($row)
    {
        return new static(['reference_id' => self::$_reference_id]);
    }

    /**
     * @param int $node_id
     * @return array
     */
    public static function getTree()
    {

        $cache = Yii::$app->cache;
        $key = self::$treeCacheid;

        $result = $cache->getOrSet($key, function () {
            $rows = self::find()
                ->select(['id', 'name', 'depth'])
                ->orderBy(['tree' => 'SORT_DESC', 'lft' => 'SORT_ASC'])
                ->all();

            $res = [];

            foreach ($rows as $row) {
                $res[$row->id] = str_repeat('-- ', $row->depth) . $row->name;
            }

            return $res;
        }, 3600);

        return $result;
    }

    /**
     * @param bool $include_parent
     * @return array|null
     */
    public function getChildrenIDs($include_parent = true)
    {
        $childrens = null;

        if (!$this->getIsNewRecord()) {
            $childrens = ArrayHelper::getColumn($this->children()->select(['id'])->asArray()->all(), 'id');

            if($include_parent)
            {
                $childrens = ArrayHelper::merge([$this->id], $childrens);
            }
        }

        return $childrens;
    }

    /**
     * @param type $insert
     * @param type $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        Yii::$app->cache->delete(self::$treeCacheid);
    }

    /**
     * delete all children sections and elements
     * @return bool
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        $elementModel = new ReferenceElement(['reference_id' => self::$_reference_id]);
        $childrens = ArrayHelper::merge([$this->id], ArrayHelper::getColumn($this->children()->select(['id'])->asArray()->all(), 'id'));

        foreach ($elementModel::find()->where(['reference_section_id' => $childrens])->batch(100) as $elements) {
            foreach ($elements as $element) {
                /**
                 * @var $element \app\modules\admin\models\ReferenceElement
                 */
                if (!$element->delete()) {
                    return false;
                }
            }
        }

        return true;
    }
}