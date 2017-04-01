<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 21.03.17
 * Time: 23:38
 *
 * @var $model app\models\reference\ReferenceProperty
 * @var $type string
 * @var $property_id string
 */

use yii\helpers\ArrayHelper;
use app\widgets\reference\ReferenceChooser;

if (ArrayHelper::isIn($type, $model::getAdditionalTypes())) {
    echo ReferenceChooser::widget([
        'reference_type_id_name' => $model->formName() . '[' . $property_id . '][reference_type_id]',
        'reference_id_name'      => $model->formName() . '[' . $property_id . '][link_reference_id]',
        'reference_id'           => $model->link_reference_id,
        'request_url'            => '/reference/reference-chooser',
        'header'                 => 'Выбор справочника',
        'options'                => [
            'class' => 'required',
        ],
    ]);
} else {
    echo '';
}
