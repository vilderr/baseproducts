<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 10.03.17
 * Time: 12:40
 */

namespace app\modules\admin\models\query;


use app\modules\admin\models\ReferenceSection;
use creocoder\nestedsets\NestedSetsBehavior;

class ReferenceSectionQuery extends \yii\db\ActiveQuery
{
    public function behaviors()
    {
        return [
            NestedSetsBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     * @return ReferenceSection[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ReferenceSection|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}