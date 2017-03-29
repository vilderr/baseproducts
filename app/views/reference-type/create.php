<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\reference\ReferenceType */

$this->title = Yii::t('app/reference', 'Create Reference Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/reference', 'Reference Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reference-type-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
