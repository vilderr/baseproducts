<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 09.03.17
 * Time: 14:10
 */

namespace app\modules\admin\helpers;

use Yii;

use app\modules\admin\models\Reference;

/**
 * Class Table for create, update, delete tables
 * @package app\modules\admin\helpers
 */
class Table
{
    /**
     * @param Reference $reference
     */
    public static function createTables(Reference $reference)
    {
        $connection = Yii::$app->db;
        $sectionTableName = 'reference_' . $reference->id . '_section';
        $elementTableName = 'reference_' . $reference->id . '_element';
        $elementPropertyTableName = 'reference_' . $reference->id . '_element_property';

        /**
         * create section table
         */
        Yii::$app->db->createCommand()
            ->createTable($sectionTableName, [
                'id'                   => 'pk',
                'name'                 => 'string not null',
                'reference_section_id' => 'integer not null default 0',
                'created_at'           => 'integer not null',
                'updated_at'           => 'integer not null',
                'active'               => 'smallint(1) not null default 1',
                'sort'                 => 'integer not null default 100',
                'lft'                  => 'integer not null',
                'rgt'                  => 'integer not null',
                'depth'                => 'integer not null',
                'tree'                 => 'integer not null',
            ])
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_'.$reference->id.'_section', $sectionTableName, 'reference_section_id')
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_'.$reference->id.'_section_depth', $sectionTableName, 'depth')
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_'.$reference->id.'_section_lft', $sectionTableName, ['lft', 'rgt'])
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_'.$reference->id.'_section_rgt', $sectionTableName, ['rgt', 'lft'])
            ->query();
        /* end section table create */

        /**
         * create reference element table
         */
        $connection->createCommand()
            ->createTable($elementTableName, [
                'id'                   => 'pk',
                'xml_id'               => 'string(255)',
                'reference_id'         => 'integer',
                'name'                 => 'string not null',
                'reference_section_id' => 'integer not null default 0',
                'created_at'           => 'integer not null',
                'updated_at'           => 'integer not null',
                'active'               => 'smallint(1) not null default 1',
                'sort'                 => 'integer not null default 100',
                'picture'              => 'integer',
                'price'                => 'float',
                'oldprice'             => 'float',
                'discount'             => 'integer',
                'currency'             => 'string default "RUB"',
            ])
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_'.$reference->id.'_element_xml_id', $elementTableName, 'xml_id')
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_'.$reference->id.'_element_section', $elementTableName, 'reference_section_id')
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_'.$reference->id.'_element_active', $elementTableName, 'active')
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_'.$reference->id.'_element_price', $elementTableName, 'price')
            ->query();
        /* end element table create */


        /**
         * create property table if not exist
         */
        $propTableExists = $connection->createCommand("SHOW TABLES LIKE 'reference_properties'")->query()->count();

        if(!$propTableExists)
        {
            $connection->createCommand()
                ->createTable('reference_properties', [
                    'id'                => 'pk',
                    'name'              => 'string not null',
                    'reference_id'      => 'integer',
                    'sort'              => 'integer not null default 100',
                    'active'            => 'smallint(1) not null default 1',
                    'type'              => 'string(2) not null default S',
                    'link_reference_id' => 'integer',
                    'multiple'          => 'smallint(1) not null default 0',
                    'code'              => 'string',
                    'xml_id'            => 'string',
                ])
                ->query();

            $connection->createCommand()
                ->createIndex('idx_reference_properties_reference_id', 'reference_properties', 'reference_id')
                ->query();

            $connection->createCommand()
                ->createIndex('idx_reference_properties_link_reference_id', 'reference_properties', 'link_reference_id')
                ->query();

            $connection->createCommand()
                ->createIndex('idx_reference_properties_code', 'reference_properties', 'code')
                ->query();

            $connection->createCommand()
                ->createIndex('idx_reference_properties_xml_id', 'reference_properties', 'xml_id')
                ->query();

            $connection->createCommand()
                ->addForeignKey('fk-reference_properties_reference_id', 'reference_properties', 'reference_id', 'reference', 'id')
                ->query();
        }
        /* end property table create  */

        /**
         * create reference element proprty table
         */

        $connection->createCommand()
            ->createTable($elementPropertyTableName, [
                'id'          => 'pk',
                'property_id' => 'integer',
                'element_id'  => 'integer',
                'value'       => 'text',
            ])
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_'.$reference->id.'_element_property_1', $elementPropertyTableName, ['property_id', 'element_id'])
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_'.$reference->id.'_element_property_2', $elementPropertyTableName, 'property_id')
            ->query();
    }
}