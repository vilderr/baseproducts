<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 23.03.17
 * Time: 13:13
 */

/**
 * @var $this yii\web\View
 * @var $reference app\models\reference\Reference
 * @var $model app\models\reference\ReferenceSection
 * @var $section app\models\reference\ReferenceSection
 */

$this->title = $reference->name . ': ' . $section->name . ': ' . Yii::t('app/reference', 'New section');
$this->params['breadcrumbs'][] = ['label' => $reference->referenceType->name, 'url' => ['index', 'type' => $reference->referenceType->id]];
$this->params['breadcrumbs'][] = ['label' => $reference->name, 'url' => ['section', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id]];
if (!$section->isNewRecord) {
    foreach ($section->parents()->all() as $parent) {
        $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => ['section', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id, 'reference_section_id' => $parent->id]];
    }
}
$this->params['breadcrumbs'][] = ['label' => $section->name];
?>
<div class="reference-section-create">
    <?= $this->render('_form', [
        'model'     => $model,
        'reference' => $reference,
    ]); ?>
</div>

