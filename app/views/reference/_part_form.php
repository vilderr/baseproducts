<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 21.03.17
 * Time: 21:38
 *
 * @var $model app\models\reference\Reference
 * @var $referenceType app\models\reference\ReferenceType
 */
use yii\helpers\Html;
?>
<?= Html::activeHiddenInput($model, 'reference_type_id', ['value' => $referenceType->id]);?>
<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'xml_id')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'active')->checkbox() ?>
<?= $form->field($model, 'sort')->textInput() ?>
<?= $form->field($model, 'catalog')->checkbox() ?>
