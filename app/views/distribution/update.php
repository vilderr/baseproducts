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
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/distribution', 'Distributions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/distribution', 'Update');
?>
<div class="distribution-update">

    <?= $this->render('_form', [
        'model'     => $model,
        'reference' => $reference,
        'parts'     => $parts,
    ]) ?>

</div>
