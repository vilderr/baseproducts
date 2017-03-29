<?php

namespace app\models\reference;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use creocoder\nestedsets\NestedSetsBehavior;

/**
 * This is the model class for table "reference_section".
 *
 * @property integer $id
 * @property string $name
 * @property integer $reference_id
 * @property integer $reference_section_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $code
 * @property string $xml_id
 * @property integer $active
 * @property integer $sort
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property integer $tree
 *
 * @property ReferenceElement[] $referenceElements
 * @property Reference $reference
 */
class ReferenceSection extends \yii\db\ActiveRecord
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            'tree' => [
                'class'         => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reference_section';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['reference_id', 'reference_section_id', 'created_at', 'updated_at', 'active', 'sort', 'lft', 'rgt', 'depth', 'tree'], 'integer'],
            [['name', 'code', 'xml_id'], 'string', 'max' => 255],
            [['reference_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reference::className(), 'targetAttribute' => ['reference_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                   => Yii::t('app/reference', 'ID'),
            'name'                 => Yii::t('app/reference', 'Name'),
            'reference_id'         => Yii::t('app/reference', 'Reference ID'),
            'reference_section_id' => Yii::t('app/reference', 'Reference Section ID'),
            'created_at'           => Yii::t('app/reference', 'Created At'),
            'updated_at'           => Yii::t('app/reference', 'Updated At'),
            'code'                 => Yii::t('app/reference', 'Code'),
            'xml_id'               => Yii::t('app/reference', 'Xml ID'),
            'active'               => Yii::t('app/reference', 'Active'),
            'sort'                 => Yii::t('app/reference', 'Sort'),
            'lft'                  => Yii::t('app/reference', 'Lft'),
            'rgt'                  => Yii::t('app/reference', 'Rgt'),
            'depth'                => Yii::t('app/reference', 'Depth'),
            'tree'                 => Yii::t('app/reference', 'Tree'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferenceElements()
    {
        return $this->hasMany(ReferenceElement::className(), ['reference_section_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReference()
    {
        return $this->hasOne(Reference::className(), ['id' => 'reference_id']);
    }

    public function getChildSections()
    {
        return $this->hasMany(ReferenceSection::className(), ['reference_section_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\reference\query\ReferenceSectionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\reference\query\ReferenceSectionQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function getTree()
    {
        $res = [
            0 => 'Верхний уровень',
        ];

        $rows = $this->find()
            ->forReference($this->reference_id)
            ->orderBy(['tree' => 'SORT_ASC', 'lft' => 'SORT_ASC'])
            ->indexBy('id')
            ->all();

        foreach ($rows as $id => $row) {
            $res[$id] = str_repeat('-- ', $row->depth) . $row->name;
        }

        return $res;
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

            if ($include_parent) {
                $childrens = ArrayHelper::merge([$this->id], $childrens);
            }
        }

        return $childrens;
    }

    /**
     * @return bool|false|int
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete())
            return false;

        $result = true;

        foreach ($this->referenceElements as $element) {
            if (!$result = $element->delete())
                break;
        }

        return $result;
    }
}
