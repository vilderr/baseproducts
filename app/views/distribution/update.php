<?php
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $model app\models\distribution\Distribution
 * @var $reference app\models\reference\Reference
 * @var $parts app\models\distribution\DistributionPart[]
 */

$this->title = Yii::t('app/distribution', 'Update {modelClass}: ', [
        'modelClass' => 'распределения',
    ]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => $reference->referenceType->name, 'url' => ['/reference', 'type' => $reference->referenceType->id]];
$this->params['breadcrumbs'][] = ['label' => $reference->name, 'url' => ['/reference/section', 'type' => $reference->reference_type_id, 'reference_id' => $reference->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/distribution', 'Distributions'), 'url' => ['index', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id]];
$this->params['breadcrumbs'][] = Yii::t('app/distribution', 'Update');
?>
<div class="distribution-update">

    <?= $this->render('_form', [
        'model'     => $model,
        'reference' => $reference,
        'parts'     => $parts,
    ]) ?>

</div>
