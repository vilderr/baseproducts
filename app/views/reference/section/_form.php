<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 25.03.17
 * Time: 11:35
 */

/**
 * @var $this yii\web\View
 * @var $reference app\models\reference\Reference
 * @var $model app\models\reference\ReferenceSection
 */
use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>
<? $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
<?= $form->field($model, 'reference_section_id')->dropDownList($model->getTree()); ?>
<?= $form->field($model, 'code')->textInput(['maxlength' => true]); ?>
<?= $form->field($model, 'xml_id')->textInput(['maxlength' => true]); ?>
<?= $form->field($model, 'sort')->textInput(); ?>
<?= $form->field($model, 'active')->checkbox(); ?>

<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? Yii::t('app/reference', 'create-btn') : Yii::t('app/reference', 'update-btn'), ['class' => 'btn btn-success']) ?>
    <?= Html::submitButton(Yii::t('app/reference', 'apply-btn'), ['name' => 'apply', 'class' => 'btn btn-default', 'value' => 'Y']); ?>
    <?= Html::a(Yii::t('app/reference', 'cancell-btn'), ['section', 'type' => $reference->reference_type_id, 'reference_id' => $reference->id, 'reference_section_id' => $model->reference_section_id], ['class' => 'btn btn-default']); ?>
</div>

<? ActiveForm::end(); ?>
<p class="bg-warning text-warning small notify"><?= Yii::t('app/reference', 'Sure fields') ?></p>
