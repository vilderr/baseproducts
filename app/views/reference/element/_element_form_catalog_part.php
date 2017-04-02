<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 24.03.17
 * Time: 13:25
 */

/**
 * @var $form \yii\widgets\ActiveForm
 * @var $element app\models\reference\ReferenceElement
 */
?>
<?= $form->field($element, 'price')->textInput(); ?>
<?= $form->field($element, 'oldprice')->textInput(); ?>
<?= $form->field($element, 'discount')->textInput(); ?>
<?= $form->field($element, 'currency')->textInput(); ?>
<?= $form->field($element, 'current_props')->textarea(); ?>
