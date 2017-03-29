<?php

use yii\db\Migration;

/**
 * Handles the creation of table `reference_element`.
 */
class m170324_133937_create_reference_element_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%reference_element}}', [
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
            'detail_picture'       => $this->integer(),
            'preview_picture'      => $this->integer(),
            'price'                => $this->float(),
            'oldprice'             => $this->float(),
            'discount'             => $this->integer(),
            'currency'             => $this->string(3)->notNull()->defaultValue('RUB'),
            'shop'                 => $this->string(255),
            'picture_src'          => $this->string(255),
            'item_url'             => $this->text(),
            'current_props'        => $this->text(),
            'current_section'      => $this->text(),
            'brand'                => $this->string(255),
        ]);

        $this->createIndex('idx_reference_element_section', '{{%reference_element}}', 'reference_section_id');
        $this->createIndex('idx_reference_element_section_1', '{{%reference_element}}', ['reference_id', 'reference_section_id']);
        $this->createIndex('idx_reference_element_xml_id', '{{%reference_element}}', ['reference_id', 'xml_id']);
        $this->createIndex('idx_reference_element_active', '{{%reference_element}}', ['reference_id', 'active']);
        $this->createIndex('idx_reference_element_price', '{{%reference_element}}', ['reference_id', 'price']);
        $this->createIndex('idx_reference_element_shop', '{{%reference_element}}', ['reference_id', 'shop']);

        $this->addForeignKey('fk_reference_element_reference', '{{%reference_element}}', 'reference_id', '{{%reference}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%reference_element}}');
    }
}
