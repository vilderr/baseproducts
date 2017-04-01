<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 24.03.17
 * Time: 0:33
 */

/**
 * @var $this yii\web\View
 * @var $model app\models\reference\search\ReferenceSectionSearch
 * @var $reference app\models\reference\Reference
 * @var $section app\models\reference\ReferenceSection
 * @var $form yii\widgets\ActiveForm
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="row filter reference-element-filter">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Фильтр</h3>
            </div>
            <div class="panel-body">
                <? $form = ActiveForm::begin([
                    'id'     => 'reference-element-search-form',
                    'action' => ['/reference/element', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id, 'reference_section_id' => $section->id],
                    'method' => 'get',

                ]); ?>
                <?= $form->field($model, 'name'); ?>
                <?= $form->field($model, 'section_id')->dropDownList($section->getTree()); ?>
                <?= $form->field($model, 'subsections')->checkbox(); ?>
                <?= $form->field($model, 'active')->checkbox(); ?>
                <?
                if ($reference->catalog) {
                    echo $form->field($model, 'shop')->textInput(['maxlength' => true]);
                }
                ?>
                <? foreach ($reference->properties as $PID => $property): ?>
                    <? if (!$property->service): ?>
                        <?= $form->field($model, 'property[' . $PID . ']')->label($property->name); ?>
                    <? endif; ?>
                <? endforeach; ?>
            </div>
            <div class="panel-footer">
                <?= Html::submitButton(Yii::t('app/reference', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('app/reference', 'Reset'), ['element', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id, 'reference_section_id' => $section->id], ['class' => 'btn btn-default']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
