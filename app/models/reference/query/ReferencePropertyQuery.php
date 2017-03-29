<?php

namespace app\models\reference\query;

/**
 * This is the ActiveQuery class for [[\app\models\reference\ReferenceProperty]].
 *
 * @see \app\models\reference\ReferenceProperty
 */
class ReferencePropertyQuery extends \yii\db\ActiveQuery
{
    public function forElement($id)
    {
        return $this->andWhere(['[[element_id]]=' . $id]);
    }

    /**
     * @param $id
     * @return $this
     */
    public function forReference($id)
    {
        return $this->andWhere('[[reference_id]]=' . $id);
    }

    /**
     * @inheritdoc
     * @return \app\models\reference\ReferenceProperty[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\reference\ReferenceProperty|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
