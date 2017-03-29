<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 25.03.17
 * Time: 14:00
 */

/**
 * @var $this yii\web\View
 * @var $form \yii\widgets\ActiveForm
 * @var $properties app\models\reference\ReferenceProperty[]
 */

use app\widgets\reference\PropertyValue;

foreach ($properties as $PID => $property):?>
    <?= PropertyValue::widget([
        'property' => $property,
        'form'     => $form,
    ]); ?>
<? endforeach; ?>