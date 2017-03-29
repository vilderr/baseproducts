<?php

namespace app\models\reference\query;

/**
 * This is the ActiveQuery class for [[\app\models\reference\ReferenceElementProperty]].
 *
 * @see \app\models\reference\ReferenceElementProperty
 */
class ReferenceElementPropertyQuery extends \yii\db\ActiveQuery
{
    public function forElement($id)
    {
        return $this->andWhere('[[element_id]]='.$id);
    }

    /**
     * @inheritdoc
     * @return \app\models\reference\ReferenceElementProperty[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\reference\ReferenceElementProperty|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
