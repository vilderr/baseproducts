<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\reference\ReferenceAsset;

/**
 * @var $this yii\web\View
 * @var $model app\models\reference\Reference
 * @var $form yii\widgets\ActiveForm
 * @var $referenceType app\models\reference\ReferenceType
 * @var $properties app\models\reference\ReferenceProperty[]
 */

ReferenceAsset::register($this);
?>

<div class="reference-form">

    <?php $form = ActiveForm::begin([
        'id'          => 'rereference-form',
        'fieldConfig' => [
            'hintOptions' => [
                'class' => 'small text-muted',
            ],
        ],
    ]); ?>

    <?= \yii\bootstrap\Tabs::widget([
        'id'    => 'reference-form-tabs',
        'items' => [
            [
                'label'   => Yii::t('app/reference', 'Reference tab title'),
                'content' => $this->render('_part_form', ['form' => $form, 'model' => $model, 'referenceType' => $referenceType]),
                'active'  => true,
                'options' => [
                    'id' => 'home',
                ],
            ],
            [
                'label'   => Yii::t('app/reference', 'Properties tab title'),
                'content' => $this->render('_part_properties', ['reference' => $model, 'properties' => $properties]),
                'options' => [
                    'id' => 'props',
                ],
            ],
        ]]); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/reference', 'create-btn') : Yii::t('app/reference', 'update-btn'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::submitButton(Yii::t('app/reference', 'apply-btn'), ['name' => 'apply', 'class' => 'btn btn-default', 'value' => 'Y']); ?>
        <?= Html::a('Отмена', ['view', 'id' => $referenceType->id], ['class' => 'btn btn-default']); ?>
    </div>

    <?php ActiveForm::end(); ?>
    <p class="bg-warning text-warning small notify"><?= Yii::t('app/reference', 'Sure fields') ?></p>
</div>
