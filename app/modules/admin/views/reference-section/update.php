<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 11.03.17
 * Time: 12:09
 * @var $this yii\web\View
 * @var $reference app\modules\admin\models\Reference
 * @var $section app\modules\admin\models\ReferenceSection
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$reference = Yii::$app->controller->reference;
$this->title = $reference->name.': Редактирование: '.$section->name;

$this->params['breadcrumbs'][] = ['label' => $reference->name, 'url' => ['index', 'reference_id' => $reference->id]];
foreach ($section->parents()->all() as $parent)
{
    $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => ['index', 'reference_id' => $reference->id, 'reference_section_id' => $parent->id]];
}
$this->params['breadcrumbs'][] = ['label' => $section->name.' - редактирование'];
?>
<div class="reference-section-update">
    <? $form = ActiveForm::begin(['id' => 'reference-section-update']); ?>

    <?= $form->field($section, 'name')->textInput(['maxlength' => true]);?>
    <?= $form->field($section, 'sort')->textInput();?>
    <?= $form->field($section, 'active')->checkbox();?>
    <?= $form->field($section, 'reference_section_id')->dropDownList(ArrayHelper::merge([0 => '-- Верхний уровень --'], $section::getTree()));?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', ['index', 'reference_id' => $reference->id, 'reference_section_id' => $section->reference_section_id], ['class' => 'btn btn-default']);?>
    </div>
    <? ActiveForm::end();?>
</div>
