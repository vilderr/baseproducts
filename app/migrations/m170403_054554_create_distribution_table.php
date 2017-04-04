<?php

use yii\db\Migration;

/**
 * Handles the creation of table `distribution`.
 */
class m170403_054554_create_distribution_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%distribution}}', [
            'id'           => $this->primaryKey(),
            'reference_id' => $this->integer()->notNull(),
            'name'         => $this->string(255)->notNull(),
            'created_at'   => $this->integer()->notNull(),
            'updated_at'   => $this->integer()->notNull(),
            'sort'         => $this->integer(11)->notNull()->defaultValue(100),
            'active'       => $this->integer(1)->notNull()->defaultValue(1),
        ]);

        $this->createIndex('idx_distribution_reference', '{{%distribution}}', 'reference_id');
        $this->createIndex('idx_distribution_active', '{{%distribution}}', ['reference_id', 'active']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%distribution}}');
    }
}
