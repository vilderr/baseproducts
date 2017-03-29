<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 24.03.17
 * Time: 13:21
 */

/**
 * @var $form \yii\widgets\ActiveForm
 * @var $element app\models\reference\ReferenceElement
 * @var $section app\models\reference\ReferenceSection
 */
?>
<?= $form->field($element, 'name')->textInput(['maxlength' => true]); ?>
<?= $form->field($element, 'reference_section_id')->dropDownList($section->getTree()); ?>
<?= $form->field($element, 'xml_id')->textInput(['maxlength' => true]); ?>
<?= $form->field($element, 'code')->textInput(['maxlength' => true]); ?>
<?= $form->field($element, 'sort')->textInput(); ?>
<?= $form->field($element, 'active')->checkbox(); ?>
