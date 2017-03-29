<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\reference\search\ReferenceTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row filter reference-type-filter">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Фильтр</h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'action' => ['index'],
                    'method' => 'get',
                ]); ?>
                <?= $form->field($model, 'id') ?>

                <?= $form->field($model, 'name') ?>

                <?= $form->field($model, 'sort') ?>
            </div>
            <div class="panel-footer">
                <?= Html::submitButton(Yii::t('app/reference', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('app/reference', 'Reset'), ['index'], ['class' => 'btn btn-default']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
