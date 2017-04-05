<?php

use yii\db\Migration;

/**
 * Handles the creation of table `option`.
 */
class m170405_101014_create_option_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%option}}', [
            'id'          => $this->primaryKey(),
            'name'        => $this->string(255)->notNull(),
            'value'       => $this->text()->notNull(),
            'description' => $this->string(255),
        ]);

        $this->createIndex('idx_option_name', '{{%option}}', 'name');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%option}}');
    }
}
