<?php

use yii\db\Migration;

/**
 * Handles the creation of table `yml_import_file`.
 */
class m170319_125731_create_yml_import_file_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%yml_import_file}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(11),
            'lft' => $this->integer(11),
            'rgt' => $this->integer(11),
            'depth' => $this->integer(11),
            'name' => $this->string(255),
            'value' => $this->text(),
            'attributes' => $this->text(),
            'group_id' => $this->integer(11),
            'offer_id' => $this->string(255),
            'offer_name'=> $this->string(255),
        ]);

        $this->createIndex('idx-yml_import_file-parent', '{{%yml_import_file}}', 'parent_id');
        $this->createIndex('idx-yml_import_file-lft', '{{%yml_import_file}}', 'lft');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%yml_import_file}}');
    }
}
