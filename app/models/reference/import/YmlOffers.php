<?php

namespace app\models\reference\import;

use Yii;

/**
 * This is the model class for table "yml_offers".
 *
 * @property integer $id
 * @property integer $group_id
 * @property string $offer_id
 * @property string $offer_name
 */
class YmlOffers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'yml_offers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id'], 'integer'],
            [['offer_id', 'offer_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group_id' => 'Group ID',
            'offer_id' => 'Offer ID',
            'offer_name' => 'Offer Name',
        ];
    }

    /**
     * truncate current table
     */
    public static function truncateTable()
    {
        Yii::$app->db->createCommand()->truncateTable(self::tableName())->query();
    }
}
