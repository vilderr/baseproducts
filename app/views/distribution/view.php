<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\distribution\Distribution */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/distribution', 'Distributions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app/distribution', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app/distribution', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app/distribution', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'reference_id',
            'name',
            'created_at',
            'updated_at',
            'sort',
            'active',
        ],
    ]) ?>

</div>
