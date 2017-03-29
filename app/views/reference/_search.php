<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\reference\search\ReferenceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reference-search">

    <?php $form = ActiveForm::begin([
        'action' => ['view'],
        'method' => 'get',
    ]); ?>
    <?= Html::hiddenInput('id', $model->id);?>

    <?= $form->field($searchModel, 'id') ?>

    <?= $form->field($searchModel, 'name') ?>

    <?= $form->field($searchModel, 'active')->checkbox(); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/reference', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app/reference', 'Reset'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
