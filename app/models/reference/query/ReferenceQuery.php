<?php

namespace app\models\reference\query;

/**
 * This is the ActiveQuery class for [[\app\models\reference\Reference]].
 *
 * @see \app\models\reference\Reference
 */
class ReferenceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\models\reference\Reference[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\reference\Reference|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
