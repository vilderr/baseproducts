<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 09.03.17
 * Time: 14:01
 * @var $this yii\web\View
 * @var $reference app\modules\admin\models\Reference
 * @var $referenceType app\modules\admin\models\ReferenceType
 * @var $arProperty array
 * @var $errors array
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\helpers\Json;
use app\modules\admin\models\ReferenceProp;
use app\modules\admin\widgets\reference\ReferenceChooser;
use app\modules\admin\assets\reference\ReferenceAsset;

ReferenceAsset::register($this);

$this->title = $referenceType->name.': Новый справочник';

$additionalTypes = Json::encode(ReferenceProp::getAdditionalTypes());
$propertyTabStr = '
    <div class="row">
        <div class="col-xs-4 required"><label>Название</label></div>
        <div class="col-xs-3 required"><label>Тип</label></div>
        <div class="col-xs-2"><label>Сортировка</label></div>
        <div class="col-xs-1"><label>Множ.</label></div>
        <div class="col-xs-1"><label>Актив.</label></div>
        <div class="col-xs-1 required"><label>Действия</label></div>
    </div>
    <hr class="xs" />
    <div id="props-list">';

foreach ($arProperty as $uid => $property)
{
    /**
     * start buffer render property modal
     * @var $property app\modules\admin\models\ReferenceProp
     */
    ob_start();
    Modal::begin([
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
        'footer'        => '<a href="#" class="btn btn-primary" data-dismiss="modal">Сохранить</a>',
    ]); ?>

    <div class="form-group<?= ($property->isAttributeRequired('name')) ? ' required' : ''; ?>">
        <label>Название</label>
        <?= Html::textInput('property[' . $uid . '][name]', $property->name, ['class' => 'form-control mod-setting', 'id' => 'property-' . $uid . '-name', 'data-id' => 'pseudo-' . $uid . '-name']); ?>
    </div>
    <div class="form-group<?= ($property->isAttributeRequired('type')) ? ' required' : ''; ?>">
        <label>Тип</label>
        <?= Html::dropDownList('property[' . $uid . '][type]', $property->type, $property::getTypes(), ['class' => 'form-control mod-setting', 'id' => 'property-' . $uid . '-type', 'data-id' => 'pseudo-' . $uid . '-type', 'data-type' => 'property-type']); ?>
    </div>
    <div class="form-group<?= ($property->isAttributeRequired('code')) ? ' required' : ''; ?>">
        <label>Символьный код</label>
        <?= Html::textInput('property[' . $uid . '][code]', $property->code, ['class' => 'form-control']); ?>
    </div>
    <div class="form-group<?= ($property->isAttributeRequired('xml_id')) ? ' required' : '' ?>">
        <label>Внешний код</label>
        <?= Html::textInput('property[' . $uid . '][xml_id]', $property->xml_id, ['class' => 'form-control']); ?>
    </div>
    <div class="form-group<?= ($property->isAttributeRequired('sort')) ? ' required' : ''; ?>">
        <label>Сортировка</label>
        <?= Html::textInput('property[' . $uid . '][sort]', $property->sort, ['class' => 'form-control mod-setting', 'id' => 'property-' . $uid . '-sort', 'data-id' => 'pseudo-' . $uid . '-sort']); ?>
    </div>
    <div class="form-group<?= ($property->isAttributeRequired('multiple')) ? ' required' : ''; ?>">
        <label><?= Html::checkbox('property[' . $uid . '][multiple]', $property->multiple, ['class' => 'mod-setting', 'id' => 'property-' . $uid . '-multiple', 'data-id' => 'pseudo-' . $uid . '-multiple']); ?>
            Множественное</label>
    </div>
    <div class="form-group<?= ($property->isAttributeRequired('active')) ? ' required' : ''; ?>">
        <label><?= Html::checkbox('property[' . $uid . '][active]', $property->active, ['class' => 'mod-setting', 'id' => 'property-' . $uid . '-active', 'data-id' => 'pseudo-' . $uid . '-active']); ?>
            Активность</label>
    </div>
    <div id="advanced-setting-box-<?=$uid?>">
        <?
        if(ArrayHelper::isIn($property->type, ReferenceProp::getAdditionalTypes()))
        {
            echo ReferenceChooser::widget([
                'reference_type_id_name' => 'property[' . $uid . '][reference_type_id]',
                'reference_id_name'      => 'property[' . $uid . '][link_reference_id]',
                'reference_id'           => $property->link_reference_id,
                'request_url'            => '/admin/reference/reference-chooser',
                'header'                 => 'Выбор справочника',
                'options'                => [
                    'class' => 'required',
                ],
            ]);
        }
        ?>
    </div>

    <? Modal::end();

    $modalStr = ob_get_contents();
    ob_end_clean();
    /* end render property modal */

    /**
     * @var $property app\modules\admin\models\ReferenceProp
     */
    $propertyTabStr .= '<div class="row" id="property-'.$uid.'-settings-line">';

    $propertyTabStr .= '<div class="col-xs-4">'.Html::textInput(null, $property->name, ['class' => 'form-control mod-setting', 'id' => 'pseudo-'.$uid.'-name', 'data-id' => 'property-'.$uid.'-name']).'</div>';
    $propertyTabStr .= '<div class="col-xs-3">'.Html::dropDownList(null, $property->type, $property->getTypes(), ['class' => 'form-control mod-setting', 'id' => 'pseudo-'.$uid.'-type', 'data-id' => 'property-'.$uid.'-type', 'data-type' => 'property-type']).'</div>';
    $propertyTabStr .= '<div class="col-xs-2">'.Html::textInput(null, $property->sort, ['class' => 'form-control mod-setting', 'id' => 'pseudo-'.$uid.'-sort', 'data-id' => 'property-'.$uid.'-sort']).'</div>';
    $propertyTabStr .= '<div class="col-xs-1">'.Html::checkbox(null, $property->multiple, ['class' => 'mod-setting', 'id' => 'pseudo-'.$uid.'-multiple', 'data-id' => 'property-'.$uid.'-multiple']).'</div>';
    $propertyTabStr .= '<div class="col-xs-1">'.Html::checkbox(null, $property->active, ['class' => 'mod-setting', 'id' => 'pseudo-'.$uid.'-active', 'data-id' => 'property-'.$uid.'-active']).'</div>';
    $propertyTabStr .= '<div class="col-xs-1">'.$modalStr.'</div>';
    $propertyTabStr .= '</div>';
    $propertyTabStr .= '<hr class="xs" />';

$script = <<< JS
 var obProperty_$uid = new ReferenceProp({PROP: {ID: '$uid'}, ADDITIONAL: $additionalTypes, VISUAL: {ADVANCED_SETTINGS_BOX: 'advanced-setting-box-$uid'}, URL: '/admin/reference/prop-setting'}); 
JS;
$this->registerJs($script);
}
$propertyTabStr .= '</div>';
?>
<div class="reference-create">
    <?php $form = ActiveForm::begin(); ?>

    <?= Html::activeHiddenInput($reference, 'reference_type_id', ['value' => $referenceType->id]);?>

    <?= Tabs::widget([
        'items' => [
            [
                'label'   => 'Главная',
                'content' =>
                    $form->field($reference, 'name')->textInput(['maxlength' => true])
                    . $form->field($reference, 'code')->textInput(['maxlength' => true])
                    . $form->field($reference, 'xml_id')->textInput(['maxlength' => true])
                    . $form->field($reference, 'sort')->textInput()
                    . $form->field($reference, 'active')->checkbox()
                ,
                'active'  => true,
                'options' => [
                        'id' => 'home'
                ],
            ],
            [
                'label'   => 'Свойства',
                'content' =>
                    $propertyTabStr
                    .'<p class="text-center" id="add-prop-box">'.Html::a('<i class="fa fa-plus"></i> Добавить свойство', '#', ['id' => 'new-prop-add', 'class' => 'btn btn-info']).'</p>',
                'options' => [
                    'id' => 'props'
                ],
            ],
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', ['/admin/reference', 'type' => $referenceType->id], ['class' => 'btn btn-default']);?>
    </div>

    <?php ActiveForm::end(); ?>
    <p class="bg-warning text-warning small notify">Поля выделенные жирным, обязательны для заполнения</p>
</div>
<?
$propUrl = Url::to(['prop']);

$script = <<< JS
    $("#new-prop-add").click(function(e) {
        e.preventDefault();
        
        var request = $.ajax({
            url: '$propUrl',
            type: 'POST',
        });
        
        request.done(function(content) {
            $("#props-list").append(content);
        });
    });
JS;
$this->registerJs($script);
?>
