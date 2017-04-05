<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;

/**
 * @var $this yii\web\View
 * @var $model app\models\distribution\Distribution
 * @var $form yii\widgets\ActiveForm
 * @var $reference app\models\reference\Reference
 * @var $parts app\models\distribution\DistributionPart[]
 */
?>

<div class="distribution-form">
    <?php $form = ActiveForm::begin([
        'id' => 'distribution-' . Yii::$app->controller->action->id . '-form',
    ]); ?>
    <?= Tabs::widget([
        'items' => [
            [
                'label'   => 'Основное',
                'active'  => true,
                'content' => $this->render('_form_fields_part', ['form' => $form, 'model' => $model]),
                'options' => [
                    'id' => 'home',
                ],
            ],
            [
                'label'   => 'Итерации',
                'content' => $this->render('_form_data_part', ['form' => $form, 'model' => $model, 'reference' => $reference, 'parts' => $parts]),
                'options' => [
                    'id' => 'data',
                ],
            ],
        ],
    ]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create Btn') : Yii::t('app', 'Update Btn'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Cancell Btn'), ['index', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id], ['class' => 'btn btn-default']); ?>
        <?
        if (!$model->isNewRecord) {
            echo Html::a(Yii::t('app', 'Delete Btn'), ['delete', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id, 'id' => $model->id], ['class' => 'btn btn-danger pull-right', 'data-method' => 'post']);
        }
        ?>
    </div>
    <?php ActiveForm::end(); ?>
    <p class="bg-warning text-warning small notify"><?= Yii::t('app', 'Sure fields') ?></p>
</div>
