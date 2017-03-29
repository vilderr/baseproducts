<?php

use yii\db\Migration;

/**
 * Handles the creation of table `reference_element_property`.
 */
class m170324_135441_create_reference_element_property_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%reference_element_property}}', [
            'id'          => $this->primaryKey(),
            'property_id' => $this->integer(),
            'element_id'  => $this->integer(),
            'value'       => $this->text(),
            'marker'      => $this->string(255),
        ]);

        $this->createIndex('idx_reference_element_property_1', '{{%reference_element_property}}', ['element_id', 'property_id']);
        $this->createIndex('idx_reference_element_property_2', '{{%reference_element_property}}', 'property_id');
        $this->createIndex('idx_reference_element_marker', '{{%reference_element_property}}', 'marker');

        $this->addForeignKey('fk_reference_element_property_id', '{{%reference_element_property}}', 'property_id', '{{%reference_property}}', 'id');
        $this->addForeignKey('fk_reference_element_element_id', '{{%reference_element_property}}', 'element_id', '{{%reference_element}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%reference_element_property}}');
    }
}
