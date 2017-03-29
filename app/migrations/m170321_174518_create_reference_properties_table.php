<?php

use yii\db\Migration;

/**
 * Handles the creation of table `reference_property`.
 */
class m170321_174518_create_reference_properties_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%reference_property}}', [
            'id'                => $this->primaryKey(),
            'name'              => $this->string(255)->notNull(),
            'xml_id'            => $this->string(255),
            'reference_id'      => $this->integer(11),
            'sort'              => $this->integer(11)->notNull()->defaultValue(100),
            'active'            => $this->smallInteger(1)->notNull()->defaultValue(1),
            'type'              => $this->string(2)->notNull()->defaultValue('S'),
            'link_reference_id' => $this->integer(11),
            'multiple'          => $this->smallInteger(1)->notNull()->defaultValue(0),
            'code'              => $this->string(255),
        ]);

        $this->createIndex('idx_reference_property_reference_id', '{{%reference_property}}', 'reference_id');
        $this->createIndex('idx_reference_property_link_reference_id', '{{%reference_property}}', 'link_reference_id');
        $this->createIndex('idx_reference_property_xml_id', '{{%reference_property}}', 'xml_id');

        $this->addForeignKey('fk-reference_property-reference', '{{%reference_property}}', 'reference_id', '{{%reference}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%reference_property}}');
    }
}
