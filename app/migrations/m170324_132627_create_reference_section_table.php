<?php

use yii\db\Migration;

/**
 * Handles the creation of table `reference_section`.
 */
class m170324_132627_create_reference_section_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%reference_section}}', [
            'id'                   => $this->primaryKey(),
            'name'                 => $this->string(255)->notNull(),
            'reference_id'         => $this->integer(),
            'reference_section_id' => $this->integer()->notNull()->defaultValue(0),
            'created_at'           => $this->integer()->notNull(),
            'updated_at'           => $this->integer()->notNull(),
            'code'                 => $this->string(255),
            'xml_id'               => $this->string(255),
            'active'               => $this->smallInteger(1)->notNull()->defaultValue(1),
            'sort'                 => $this->integer(18)->notNull()->defaultValue(100),
            'lft'                  => $this->integer()->notNull(),
            'rgt'                  => $this->integer()->notNull(),
            'depth'                => $this->integer()->notNull(),
            'tree'                 => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_reference_section_1', '{{%reference_section}}', ['reference_id', 'reference_section_id']);
        $this->createIndex('idx_reference_section_depth', '{{%reference_section}}', ['reference_id', 'depth']);
        $this->createIndex('idx_reference_section_lft', '{{%reference_section}}', ['reference_id', 'lft', 'rgt']);
        $this->createIndex('idx_reference_section_rgt', '{{%reference_section}}', ['reference_id', 'rgt', 'lft']);
        $this->createIndex('idx_reference_section_xml_id', '{{%reference_section}}', ['reference_id', 'xml_id']);

        $this->addForeignKey('fk_reference_section_reference', '{{%reference_section}}', 'reference_id', '{{%reference}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%reference_section}}');
    }
}
