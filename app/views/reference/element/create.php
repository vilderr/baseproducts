<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 23.03.17
 * Time: 23:23
 */

/**
 * @var $this yii\web\View
 * @var $reference app\models\reference\Reference
 * @var $section app\models\reference\ReferenceSection
 * @var $element app\models\reference\ReferenceElement
 * @var $properties app\models\reference\ReferenceElementProperty[]
 */

$this->title = $reference->name . ': ' . $section->name . ': ' . Yii::t('app/reference', 'New element');
$this->params['breadcrumbs'][] = ['label' => $reference->referenceType->name, 'url' => ['index', 'type' => $reference->referenceType->id]];
$this->params['breadcrumbs'][] = ['label' => $reference->name, 'url' => ['element', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id]];
if (!$section->isNewRecord) {
    foreach ($section->parents()->all() as $parent) {
        $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => ['element', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id, 'reference_section_id' => $parent->id]];
    }
}
$this->params['breadcrumbs'][] = ['label' => $section->name];
?>
<div class="reference-element-create">

    <?= $this->render('_form', [
        'reference'  => $reference,
        'element'    => $element,
        'section'    => $section,
        'properties' => $properties,
    ]) ?>

</div>
