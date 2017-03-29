<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 13.03.17
 * Time: 15:36
 * @var $this yii\web\View
 * @var $model app\modules\admin\models\ReferenceProp
 */

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Json;

$uid = substr(md5(uniqid("", true)), 0, 3);

?>
<div class="row" id="property-<?=$uid;?>-settings-line">
    <div class="col-xs-4"><?= Html::textInput(null, $model->name, ['class' => 'form-control mod-setting', 'id' => 'pseudo-'.$uid.'-name', 'data-id' => 'property-'.$uid.'-name']);?></div>
    <div class="col-xs-3"><?= Html::dropDownList(null, $model->type, $model::getTypes(), ['class' => 'form-control mod-setting', 'id' => 'pseudo-'.$uid.'-type', 'data-id' => 'property-'.$uid.'-type', 'data-type' => 'property-type']);?></div>
    <div class="col-xs-2"><?= Html::textInput(null, $model->sort, ['class' => 'form-control mod-setting', 'id' => 'pseudo-'.$uid.'-sort', 'data-id' => 'property-'.$uid.'-sort']);?></div>
    <div class="col-xs-1"><?= Html::checkbox(null, $model->multiple, ['class' => 'mod-setting', 'id' => 'pseudo-'.$uid.'-multiple', 'data-id' => 'property-'.$uid.'-multiple']);?></div>
    <div class="col-xs-1"><?= Html::checkbox(null, $model->active, ['class' => 'mod-setting', 'id' => 'pseudo-'.$uid.'-active', 'data-id' => 'property-'.$uid.'-active']);?></div>
    <div class="col-xs-1">
        <? Modal::begin([
            'header'        => '<h4 class="modal-title">Настройки свойства</h4>',
            'toggleButton'  => [
                'tag'   => 'button',
                'class' => 'btn btn-default',
                'label' => '<i class="fa fa-list"></i>',
            ],
            'id'            => 'property-' . $uid . '-settings',
            'clientOptions' => [
                'backdrop' => 'static',
            ],
            'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Сохранить</a>'
        ]); ?>
        <div class="form-group<?=($model->isAttributeRequired('name')) ? ' required' : '';?>">
            <label>Название</label>
            <?= Html::textInput('property[' . $uid . '][name]', $model->name, ['class' => 'form-control mod-setting', 'id' => 'property-' . $uid . '-name', 'data-id' => 'pseudo-' . $uid . '-name']); ?>
        </div>
        <div class="form-group<?=($model->isAttributeRequired('type')) ? ' required' : '';?>">
            <label>Тип</label>
            <?= Html::dropDownList('property[' . $uid . '][type]', $model->type, $model::getTypes(), ['class' => 'form-control mod-setting', 'id' => 'property-' . $uid . '-type', 'data-id' => 'pseudo-' . $uid . '-type', 'data-type' => 'property-type']); ?>
        </div>
        <div class="form-group<?=($model->isAttributeRequired('code')) ? ' required' : '';?>">
            <label>Символьный код</label>
            <?= Html::textInput('property[' . $uid . '][code]', $model->code, ['class' => 'form-control']);?>
        </div>
        <div class="form-group<?=($model->isAttributeRequired('xml_id')) ? ' required' : '';?>">
            <label>Внешний код</label>
            <?= Html::textInput('property[' . $uid . '][xml_id]', $model->xml_id, ['class' => 'form-control']); ?>
        </div>
        <div class="form-group<?=($model->isAttributeRequired('sort')) ? ' required' : '';?>">
            <label>Сортировка</label>
            <?= Html::textInput('property[' . $uid . '][sort]', $model->sort, ['class' => 'form-control mod-setting', 'id' => 'property-' . $uid . '-sort', 'data-id' => 'pseudo-' . $uid . '-sort']); ?>
        </div>
        <div class="form-group<?=($model->isAttributeRequired('multiple')) ? ' required' : '';?>">
            <label><?= Html::checkbox('property[' . $uid . '][multiple]', $model->multiple, ['class' => 'mod-setting', 'id' => 'property-' . $uid . '-multiple', 'data-id' => 'pseudo-' . $uid . '-multiple']); ?> Множественное</label>
        </div>
        <div class="form-group<?=($model->isAttributeRequired('active')) ? ' required' : '';?>">
            <label><?= Html::checkbox('property[' . $uid . '][active]', $model->active, ['class' => 'mod-setting', 'id' => 'property-' . $uid . '-active', 'data-id' => 'pseudo-' . $uid . '-active']); ?> Активность</label>
        </div>
        <div id="advanced-setting-box"></div>
        <? Modal::end();?>
    </div>
</div>
<script>
    var obProperty_<?=$uid;?> = new ReferenceProp({PROP: {ID: '<?=$uid;?>'}, ADDITIONAL: <?=Json::encode($model::getAdditionalTypes());?>, VISUAL: {ADVANCED_SETTINGS_BOX: 'advanced-setting-box'}, URL: '/admin/reference/prop-setting'});
</script>
<hr class="xs" />
