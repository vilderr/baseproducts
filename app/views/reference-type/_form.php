<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\reference\ReferenceType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reference-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sort')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/reference', 'create-btn') : Yii::t('app/reference', 'update-btn'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app/reference', 'cancell-btn'), ['index'], ['class' => 'btn btn-default']); ?>
    </div>

    <?php ActiveForm::end(); ?>
    <p class="bg-warning text-warning small notify"><?= Yii::t('app/reference', 'Sure fields') ?></p>
</div>
