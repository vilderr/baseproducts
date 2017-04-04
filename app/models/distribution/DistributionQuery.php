<?php

namespace app\models\distribution;

/**
 * This is the ActiveQuery class for [[Distribution]].
 *
 * @see Distribution
 */
class DistributionQuery extends \yii\db\ActiveQuery
{
    public function forReference($id)
    {
        return $this->andWhere('[[reference_id]]=' . $id);
    }

    /**
     * @return $this
     */
    public function active()
    {
        return $this->andWhere('[[active]]=1');
    }

    /**
     * @inheritdoc
     * @return Distribution[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Distribution|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
