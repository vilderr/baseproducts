<?php

namespace app\models\distribution;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "distribution".
 *
 * @property integer $id
 * @property integer $reference_id
 * @property string $name
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $sort
 * @property integer $active
 */
class Distribution extends \yii\db\ActiveRecord
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%distribution}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reference_id', 'name'], 'required'],
            [['reference_id', 'created_at', 'updated_at', 'sort', 'active'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => Yii::t('app/reference', 'ID'),
            'reference_id' => Yii::t('app/reference', 'Reference ID'),
            'name'         => Yii::t('app/reference', 'Name'),
            'created_at'   => Yii::t('app/reference', 'Created At'),
            'updated_at'   => Yii::t('app/reference', 'Updated At'),
            'sort'         => Yii::t('app/reference', 'Sort'),
            'active'       => Yii::t('app/reference', 'Active'),
        ];
    }

    /**
     * @inheritdoc
     * @return \app\models\distribution\DistributionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\distribution\DistributionQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParts()
    {
        return $this->hasMany(DistributionPart::className(), ['distribution_id' => 'id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);


    }
}
