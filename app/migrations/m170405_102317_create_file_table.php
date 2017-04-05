<?php

use yii\db\Migration;

/**
 * Handles the creation of table `file`.
 */
class m170405_102317_create_file_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%file}}', [
            'id'            => $this->primaryKey(),
            'name'          => $this->string('255')->notNull(),
            'original_name' => $this->string(255),
            'external_id'   => $this->string(50),
            'height'        => $this->integer(),
            'width'         => $this->integer(),
            'size'          => $this->integer(),
            'type'          => $this->string(255),
            'subdir'        => $this->string(255),
        ]);

        $this->createIndex('idx_file_ixternal_id', '{{%file}}', 'external_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%file}}');
    }
}
