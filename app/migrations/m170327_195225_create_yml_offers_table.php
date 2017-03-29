<?php

use yii\db\Migration;

/**
 * Handles the creation of table `yml_offers`.
 */
class m170327_195225_create_yml_offers_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%yml_offers}}', [
            'id'       => $this->primaryKey(),
            'offer_id' => $this->string(255),
            'sku_id'   => $this->integer(11),
        ]);

        $this->createIndex('idx_yml_offer_id', '{{%yml_offers}}', 'offer_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%yml_offers}}');
    }
}
