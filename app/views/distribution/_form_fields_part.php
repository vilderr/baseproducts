<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 03.04.17
 * Time: 9:44
 */

/**
 * @var $this yii\web\View
 * @var $form \yii\widgets\ActiveForm
 * @var $model app\models\distribution\Distribution
 */
?>
<?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
<?= $form->field($model, 'sort')->textInput(); ?>
<?= $form->field($model, 'active')->checkbox(); ?>
