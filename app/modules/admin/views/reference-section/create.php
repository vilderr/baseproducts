<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 10.03.17
 * Time: 12:13
 * @var $this yii\web\View
 * @var $model app\modules\admin\models\ReferenceSection
 * @var $reference app\modules\admin\models\Reference
 * @var $section app\modules\admin\models\ReferenceSection
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$reference = Yii::$app->controller->reference;

$this->title = $reference->name.': Добавление раздела';

$this->params['breadcrumbs'][] = ['label' => $reference->name, 'url' => ['index', 'reference_id' => $reference->id]];
if (!$section->isNewRecord)
{
    foreach ($section->parents()->all() as $parent)
    {
        $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => ['index', 'reference_id' => $reference->id, 'reference_section_id' => $parent->id]];
    }

    $this->params['breadcrumbs'][] = ['label' => $section->name, 'url' => ['index', 'reference_id' => $section->id]];
}
$this->params['breadcrumbs'][] = ['label' => 'Новый раздел'];
?>
<div class="reference-section-create">
    <? $form = ActiveForm::begin();?>

    <?= $form->field($model, 'reference_section_id')->hiddenInput()->label(false);?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]);?>
    <?= $form->field($model, 'sort')->textInput();?>
    <?= $form->field($model, 'active')->checkbox();?>

    <div class="form-group">
        <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app/reference', 'cancell-btn'), ['section/index', 'reference_id' => $reference->id], ['class' => 'btn btn-default']);?>
    </div>
    <? ActiveForm::end();?>
</div>
