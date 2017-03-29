<?php

namespace app\modules\admin\models;

use yii\helpers\ArrayHelper;
use app\modules\admin\models\query\ReferenceTypeQuery;


/**
 * This is the model class for table "reference_type".
 *
 * @property string $id
 * @property string $name
 * @property integer $sections
 * @property integer $sort
 */
class ReferenceType extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
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
        return '{{%reference_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'string', 'max' => 50],
            [['id'], 'match', 'pattern' => '#^[\w_-]+$#i', 'message' => 'ID должен состоять из латинских букв, "-" или "_"'],
            [['id'],'unique', 'targetClass' => self::className(), 'message' => 'Cправочник с таким ID уже существует'],
            [['name'], 'string', 'max' => 255],
            [['sections', 'sort'], 'integer'],
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
            'sections' => 'Sections',
            'sort' => 'Сортировка',
        ];
    }

    /**
     * @inheritdoc
     * @return ReferenceTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ReferenceTypeQuery(get_called_class());
    }

    /**
     * 
     * @return ActiveQuery
     */
    public function getReference() {
        return $this->hasMany(Reference::className(), ['reference_type_id' => 'id']);
    }

    /**
     * @return bool|false|int
     */
    public function beforeDelete() {
        $result     = true;
        $references = Reference::find()->where(['reference_type_id' => $this->id])->all();

        foreach ($references as $reference) {
            $result = $reference->delete();

            if (!$result)
                break;
        }

        return $result;
    }

}
