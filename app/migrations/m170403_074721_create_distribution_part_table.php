<?php

use yii\db\Migration;

/**
 * Handles the creation of table `distribution_part`.
 */
class m170403_074721_create_distribution_part_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%distribution_part}}', [
            'id'              => $this->primaryKey(),
            'distribution_id' => $this->integer()->notNull(),
            'data'            => $this->text(),
            'active'          => $this->integer(1)->notNull()->defaultValue(1),
        ]);

        $this->createIndex('idx_distribution_part_parent_id', '{{%distribution_part}}', 'distribution_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%distribution_part}}');
    }
}
