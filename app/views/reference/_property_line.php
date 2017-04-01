<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 21.03.17
 * Time: 22:29
 *
 * @var $this yii\web\View
 * @var $model app\models\reference\ReferenceProperty
 * @var $PID string|integer
 */

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Json;

?>
<div class="row" id="<?= $model->formName() . '-' . $PID; ?>-settings-line">
    <div class="col-xs-4"><?= Html::textInput(null, $model->name, ['class' => 'form-control mod-setting', 'id' => 'pseudo-' . $PID . '-name', 'data-id' => $model->formName() . '-' . $PID . '-name']); ?></div>
    <div class="col-xs-3"><?= Html::dropDownList(null, $model->type, $model::getTypes(), ['class' => 'form-control mod-setting', 'id' => 'pseudo-' . $PID . '-type', 'data-id' => $model->formName() . '-' . $PID . '-type', 'data-type' => 'property-type']); ?></div>
    <div class="col-xs-2"><?= Html::textInput(null, $model->sort, ['class' => 'form-control mod-setting', 'id' => 'pseudo-' . $PID . '-sort', 'data-id' => $model->formName() . '-' . $PID . '-sort']); ?></div>
    <div class="col-xs-1"><?= Html::checkbox(null, $model->multiple, ['class' => 'mod-setting', 'id' => 'pseudo-' . $PID . '-multiple', 'data-id' => $model->formName() . '-' . $PID . '-multiple']); ?></div>
    <div class="col-xs-1"><?= Html::checkbox(null, $model->active, ['class' => 'mod-setting', 'id' => 'pseudo-' . $PID . '-active', 'data-id' => $model->formName() . '-' . $PID . '-active']); ?></div>
    <div class="col-xs-1">
        <? Modal::begin([
            'header'        => '<h4 class="modal-title">' . Yii::t('app/reference', 'Property setings') . '</h4>',
            'toggleButton'  => [
                'tag'   => 'button',
                'class' => 'btn btn-default',
                'label' => '<i class="fa fa-list"></i>',
            ],
            'id'            => $model->formName() . '-' . $PID . '-settings',
            'clientOptions' => [
                'backdrop' => 'static',
            ],
            'footer'        => '<a href="#" class="btn btn-primary" data-dismiss="modal">' . Yii::t('app/reference', 'save-btn') . '</a>',
        ]); ?>
        <div class="form-group<?= ($model->isAttributeRequired('name')) ? ' required' : ''; ?>">
            <?= Html::label($model->getAttributeLabel('name')); ?>
            <?= Html::activeTextInput($model, '[' . $PID . ']name', ['class' => 'form-control mod-setting', 'id' => $model->formName() . '-' . $PID . '-name', 'data-id' => 'pseudo-' . $PID . '-name']); ?>
        </div>
        <div class="form-group<?= ($model->isAttributeRequired('type')) ? ' required' : ''; ?>">
            <?= Html::label($model->getAttributeLabel('type')); ?>
            <?= Html::activeDropDownList($model, '[' . $PID . ']type', $model::getTypes(), ['class' => 'form-control mod-setting', 'id' => $model->formName() . '-' . $PID . '-type', 'data-id' => 'pseudo-' . $PID . '-type', 'data-type' => 'property-type']); ?>
        </div>
        <div class="form-group<?= ($model->isAttributeRequired('code')) ? ' required' : ''; ?>">
            <?= Html::label($model->getAttributeLabel('code')); ?>
            <?= Html::activeTextInput($model, '[' . $PID . ']code', ['class' => 'form-control']); ?>
        </div>
        <div class="form-group<?= ($model->isAttributeRequired('xml_id')) ? ' required' : ''; ?>">
            <?= Html::label($model->getAttributeLabel('xml_id')); ?>
            <?= Html::activeTextInput($model, '[' . $PID . ']xml_id', ['class' => 'form-control']); ?>
        </div>
        <div class="form-group<?= ($model->isAttributeRequired('sort')) ? ' required' : ''; ?>">
            <?= Html::label($model->getAttributeLabel('sort')); ?>
            <?= Html::activeTextInput($model, '[' . $PID . ']sort', ['class' => 'form-control mod-setting', 'id' => $model->formName() . '-' . $PID . '-sort', 'data-id' => 'pseudo-' . $PID . '-sort']); ?>
        </div>
        <div class="form-group<?= ($model->isAttributeRequired('multiple')) ? ' required' : ''; ?>">
            <?= Html::activeCheckbox($model, '[' . $PID . ']multiple', ['class' => 'mod-setting', 'id' => $model->formName() . '-' . $PID . '-multiple', 'data-id' => 'pseudo-' . $PID . '-multiple']); ?>
        </div>
        <div class="form-group<?= ($model->isAttributeRequired('active')) ? ' required' : ''; ?>">
            <?= Html::activeCheckbox($model, '[' . $PID . ']active', ['class' => 'mod-setting', 'id' => $model->formName() . '-' . $PID . '-active', 'data-id' => 'pseudo-' . $PID . '-active']); ?>
        </div>
        <div class="form-group<?= ($model->isAttributeRequired('service')) ? ' required' : ''; ?>">
            <?= Html::activeCheckbox($model, '[' . $PID . ']service'); ?>
        </div>
        <div id="advanced-setting-box-<?= $PID ?>"></div>
        <div id="delete-property-<?= $PID ?>" class="alert alert-danger small notify">
            <label class="text-danger"
                   style="margin: 0;"><?= Html::checkbox($model->formName() . '[' . $PID . '][delete]', $model->delete) ?> <?= $model->getAttributeLabel('delete') ?></label>
        </div>
        <? Modal::end(); ?>
    </div>
</div>
<?
$additional = Json::encode($model::getAdditionalTypes());
$script = <<< JS
var obProperty_$PID = new ReferenceProp({PROP: {ID: '$PID'}, ADDITIONAL: $additional, VISUAL: {ADVANCED_SETTINGS_BOX: 'advanced-setting-box-$PID'}, URL: '/reference/load-property-setting'});
JS;
$this->registerJs($script);
?>
<hr class="xs"/>

