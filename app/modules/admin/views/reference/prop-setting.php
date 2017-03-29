<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 14.03.17
 * Time: 12:05
 * @var $type string
 * @var $property_id string
 */

use yii\helpers\ArrayHelper;
use app\modules\admin\models\ReferenceProp;
use app\modules\admin\widgets\reference\ReferenceChooser;

if (ArrayHelper::isIn($type, ReferenceProp::getAdditionalTypes())) {
    echo ReferenceChooser::widget([
        'reference_type_id_name' => 'property[' . $property_id . '][reference_type_id]',
        'reference_id_name' => 'property[' . $property_id . '][link_reference_id]',
        'request_url'       => '/admin/reference/reference-chooser',
        'header'            => 'Выбор справочника',
        'options'           => [
            'class' => 'required',
        ],
    ]);
} else {
    echo '';
}
