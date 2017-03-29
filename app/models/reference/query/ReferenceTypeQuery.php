<?php

namespace app\models\reference\query;

/**
 * This is the ActiveQuery class for [[\app\models\reference\ReferenceType]].
 *
 * @see \app\models\reference\ReferenceType
 */
class ReferenceTypeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\models\reference\ReferenceType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\reference\ReferenceType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
