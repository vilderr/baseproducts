<?php

namespace app\models\reference\query;

/**
 * This is the ActiveQuery class for [[\app\models\reference\ReferenceElement]].
 *
 * @see \app\models\reference\ReferenceElement
 */
class ReferenceElementQuery extends \yii\db\ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        return $this->andWhere('[[active]]=1');
    }

    public function forReference($id)
    {
        return $this->andWhere('[[reference_id]]='.$id);
    }

    /**
     * @inheritdoc
     * @return \app\models\reference\ReferenceElement[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\reference\ReferenceElement|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
