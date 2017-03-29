<?php

use yii\db\Migration;

/**
 * Handles the creation of table `reference`.
 */
class m170306_064629_create_reference_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%reference}}', [
            'id'                => $this->primaryKey(),
            'reference_type_id' => $this->string(50),
            'name'              => $this->string(255)->notNull(),
            'code'              => $this->string(255),
            'xml_id'            => $this->string(255),
            'created_at'        => $this->integer()->notNull(),
            'updated_at'        => $this->integer()->notNull(),
            'active'            => $this->smallInteger(1)->notNull()->defaultValue(1),
            'sort'              => $this->integer(18)->notNull()->defaultValue(100),
            'catalog'           => $this->smallInteger(1)->notNull()->defaultValue(0),
        ]);

        $this->createIndex('idx_reference', '{{%reference}}', ['reference_type_id', 'active']);

        $this->addForeignKey('fk_reference_type', '{{%reference}}', 'reference_type_id', '{{%reference_type}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%reference}}');
    }
}
