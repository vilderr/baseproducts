<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\reference\ReferenceType */

$this->title = Yii::t('app/reference', 'Update Reference Types') . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/reference', 'Reference Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/reference', 'Update');
?>
<div class="reference-type-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
