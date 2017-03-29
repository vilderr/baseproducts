<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 21.03.17
 * Time: 21:53
 *
 * @var $this yii\web\View
 * @var $reference app\models\reference\Reference
 * @var $properties app\models\reference\ReferenceProperty[]
 */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\helpers\Json;
use app\models\reference\ReferenceProperty;
use app\widgets\reference\ReferenceChooser;

$model = new ReferenceProperty;
$labels = $model->attributeLabels();
$additionalTypes = Json::encode(ReferenceProperty::getAdditionalTypes());
//echo'<pre>';print_r($model->attributeLabels());echo '</pre>';
?>
<div class="row">
    <div class="col-xs-4 required"><?= Html::label($model->getAttributeLabel('name'))?></div>
    <div class="col-xs-3 required"><?= Html::label($model->getAttributeLabel('type'))?></div>
    <div class="col-xs-2"><?= Html::label($model->getAttributeLabel('sort'))?></div>
    <div class="col-xs-1"><?= Html::label($model->getAttributeLabel('multiple'))?></div>
    <div class="col-xs-1"><?= Html::label($model->getAttributeLabel('active'))?></div>
    <div class="col-xs-1"></div>
</div>
<hr class="xs" />
<div id="property-list">
<?
foreach ($properties as $uid => $property) {
    echo $this->render('_property_line', [
        'model' => $property,
        'PID'   => $uid,
    ]);
}
?>
</div>
<p class="text-center" id="add-prop-box"><?= Html::a('<i class="fa fa-plus"></i> Добавить свойство', '#', ['id' => 'new-prop-add', 'class' => 'btn btn-info']);?></p>
<?
$propUrl = Url::to(['/reference/load-property-line']);

$script = <<< JS
    $("#new-prop-add").click(function(e) {
        e.preventDefault();
        
        var request = $.ajax({
            url: '$propUrl',
            type: 'post'
        });
        
        request.done(function(content) {
            $("#property-list").append(content);
        });
    });
JS;
$this->registerJs($script);
?>