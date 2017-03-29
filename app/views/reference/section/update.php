<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 23.03.17
 * Time: 18:51
 */

/**
 * @var $this yii\web\View
 * @var $reference app\models\reference\Reference
 * @var $section app\models\reference\ReferenceSection
 */

$this->title = $reference->name . ': ' . $section->name . ': ' . Yii::t('app/reference', 'Update');
$this->params['breadcrumbs'][] = ['label' => $reference->referenceType->name, 'url' => ['index', 'type' => $reference->referenceType->id]];
$this->params['breadcrumbs'][] = ['label' => $reference->name, 'url' => ['section', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id]];
foreach ($section->parents()->all() as $parent) {
    $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => ['section', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id, 'reference_section_id' => $parent->id]];
}
$this->params['breadcrumbs'][] = ['label' => $section->name];
?>
<div class="reference-section-update">
    <?= $this->render('_form', [
        'model'     => $section,
        'reference' => $reference,
    ]); ?>
</div>
