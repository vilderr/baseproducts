<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 13.03.17
 * Time: 10:15
 * @var $this yii\web\View
 * @var $reference app\modules\admin\models\Reference
 * @var $element app\modules\admin\models\ReferenceElement
 * @var $section app\modules\admin\models\ReferenceSection
 * @var $properties array
 */

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\modules\admin\widgets\reference\PropertyValueInput;

$reference = Yii::$app->controller->reference;

$this->title = $reference->name.': '.$element->name.' - редактирование';
$this->params['breadcrumbs'][] = ['label' => $reference->name, 'url' => ['index', 'reference_id' => $reference->id]];
if(!$section->isNewRecord)
{
    foreach ($section->parents()->all() as $parent)
    {
        $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => ['index', 'reference_id' => $reference->id, 'reference_section_id' => $parent->id]];
    }

    $this->params['breadcrumbs'][] = ['label' => $section->name, 'url' => ['index', 'reference_id' => $reference->id, 'reference_section_id' => $section->id]];
}
$this->params['breadcrumbs'][] = ['label' => 'Редактирование элемента']
?>
<div class="reference-element-update">
    <? $form = ActiveForm::begin(['id' => 'reference-element-update']);?>

    <?= $form->field($element, 'name')->textInput(['maxlength' => true]);?>
    <?= $form->field($element, 'sort')->textInput();?>
    <?= $form->field($element, 'active')->checkbox();?>
    <?= $form->field($element, 'reference_section_id')->dropDownList(ArrayHelper::merge([0 => '-- Верхний уровень --'], $section::getTree()));?>

    <?= $form->field($element, 'price')->textInput();?>
    <?= $form->field($element, 'oldprice')->textInput();?>
    <?= $form->field($element, 'discount')->textInput();?>
    <?= $form->field($element, 'currency')->textInput();?>

    <?
    if (!empty($properties)) {
        ?><h3>Свойства</h3><?

        foreach ($properties as $PID => $property) {
            echo PropertyValueInput::widget([
                'property' => $element->reference->properties[$PID],
                'model'       => $property,
            ]);
        }
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);?>
        <?= Html::a('Отмена', ['index', 'reference_id' => $reference->id, 'reference_section_id' => $section->id], ['class' => 'btn btn-default']);?>
    </div>
    <? ActiveForm::end();?>
</div>

