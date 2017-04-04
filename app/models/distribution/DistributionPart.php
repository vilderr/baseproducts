<?php

namespace app\models\distribution;

use Yii;

/**
 * This is the model class for table "distribution_part".
 *
 * @property integer $id
 * @property integer $distribution_id
 * @property string $data
 * @property integer $active
 */
class DistributionPart extends \yii\db\ActiveRecord
{
    const ACTIVE = 1;
    const IN_ACTIVE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'distribution_part';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['distribution_id'], 'required'],
            [['distribution_id', 'active'], 'integer'],
            [['data'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'              => Yii::t('app/distribution', 'ID'),
            'distribution_id' => Yii::t('app/distribution', 'Distribution ID'),
            'data'            => Yii::t('app/distribution', 'Data'),
            'active'          => Yii::t('app/distribution', 'Active'),
        ];
    }

    public function getDistribution()
    {
        return $this->hasOne(Distribution::className(), ['id' => 'distribution_id']);
    }

    public static function getActiveArray()
    {
        return [
            self::ACTIVE    => 'Активировать',
            self::IN_ACTIVE => 'Деактивировать',
        ];
    }
}
