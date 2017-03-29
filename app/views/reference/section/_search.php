<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 23.03.17
 * Time: 17:46
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
<div class="row filter reference-section-filter">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Фильтр</h3>
            </div>
            <div class="panel-body">
            <? $form = ActiveForm::begin([
                'id'     => 'reference-section-search-form',
                'action' => ['section', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id, 'reference_section_id' => $section->id],
                'method' => 'get',
            ]); ?>

            <?= $form->field($model, 'name'); ?>
            <?= $form->field($model, 'active')->checkbox(); ?>
            </div>
            <div class="panel-footer">
                <?= Html::submitButton(Yii::t('app/reference', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('app/reference', 'Reset'), ['section', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id, 'reference_section_id' => $section->id], ['class' => 'btn btn-default']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
