<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 21.03.17
 * Time: 20:41
 */

namespace app\components\helpers;

use Yii;
use app\models\reference\Reference;

class Table
{
    public static function createReferenceTables(Reference $reference)
    {
        $connection = Yii::$app->db;
        $sectionTableName = 'reference_' . $reference->id . '_section';
        $elementTableName = 'reference_' . $reference->id . '_element';
        $elementPropertyTableName = 'reference_' . $reference->id . '_element_property';

        /**
         * create reference section table
         */
        $connection->createCommand()
            ->createTable($sectionTableName, [
                'id'                   => 'pk',
                'name'                 => 'string not null',
                'reference_section_id' => 'integer not null default 0',
                'created_at'           => 'integer not null',
                'updated_at'           => 'integer not null',
                'code'                 => 'string(255)',
                'xml_id'               => 'string(255)',
                'active'               => 'smallint(1) not null default 1',
                'sort'                 => 'integer not null default 100',
                'lft'                  => 'integer not null',
                'rgt'                  => 'integer not null',
                'depth'                => 'integer not null',
                'tree'                 => 'integer not null',
            ])
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_' . $reference->id . '_section', $sectionTableName, 'reference_section_id')
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_' . $reference->id . '_section_xml_id', $sectionTableName, 'xml_id')
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_' . $reference->id . '_section_depth', $sectionTableName, 'depth')
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_' . $reference->id . '_section_lft', $sectionTableName, ['lft', 'rgt'])
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_' . $reference->id . '_section_rgt', $sectionTableName, ['rgt', 'lft'])
            ->query();
        /* end section table create */

        /**
         * create reference element table
         */
        $connection->createCommand()
            ->createTable($elementTableName, [
                'id'                   => 'pk',
                'name'                 => 'string not null',
                'reference_section_id' => 'integer not null default 0',
                'created_at'           => 'integer not null',
                'updated_at'           => 'integer not null',
                'code'                 => 'string(255)',
                'xml_id'               => 'string(255)',
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
            ->createIndex('idx_reference_' . $reference->id . '_element_xml_id', $elementTableName, 'xml_id')
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_' . $reference->id . '_element_section', $elementTableName, 'reference_section_id')
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_' . $reference->id . '_element_active', $elementTableName, 'active')
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_' . $reference->id . '_element_price', $elementTableName, 'price')
            ->query();
        /* end element table create */

        /**
         * create reference element property table
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
            ->createIndex('idx_reference_' . $reference->id . '_element_property_1', $elementPropertyTableName, ['property_id', 'element_id'])
            ->query();

        $connection->createCommand()
            ->createIndex('idx_reference_' . $reference->id . '_element_property_2', $elementPropertyTableName, 'property_id')
            ->query();

        /* end reference element property create */
    }
}