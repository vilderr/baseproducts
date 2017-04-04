<?php
/**
 * @var $this yii\web\View
 * @var $reference app\models\reference\Reference
 * @var $searchModel app\models\distribution\DistributionSearch
 * @var $dataProvider \yii\data\ActiveDataProvider
 */

use yii\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Distribution Elements');
$this->params['breadcrumbs'][] = ['label' => $reference->referenceType->name, 'url' => ['/reference', 'type' => $reference->referenceType->id]];
$this->params['breadcrumbs'][] = ['label' => $reference->name, 'url' => ['/reference/section', 'type' => $reference->reference_type_id, 'reference_id' => $reference->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/reference', 'Reference Distribution')];
?>
<div class="distribution-index">
    <p>
        <?= Html::a(Yii::t('app', 'Create New {model}', ['model' => strtolower(Yii::t('app/distribution', 'Distributions'))]), ['create', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id], ['class' => 'btn btn-success']); ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'       => '{items}{summary}{pager}',
        'columns'      => [
            [
                'attribute' => 'id',
                'options'        => [
                    'width' => '50px',
                ],
                'contentOptions' => [
                    'class' => 'control',
                ],
            ],
            [
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function ($model) use ($reference) {
                    return Html::a(Html::encode($model->name), ['update', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id, 'id' => $model->id]);
                },
            ],
            [
                'attribute'      => 'sort',
                'contentOptions' => [
                    'class' => 'text-center',
                ],
                'options'        => [
                    'width' => '105px',
                ],
            ],
            [
                'attribute'      => 'active',
                'filter'         => [0 => Yii::t('app/reference', 'filter-active-yes'), 1 => Yii::t('app/reference', 'filter-active-no')],
                'format'         => 'boolean',
                'contentOptions' => [
                    'class' => 'text-center',
                ],
                'options'        => [
                    'width' => '100px',
                ],
            ],
            [
                'format'         => 'raw',
                'value'          => function ($model) use ($reference) {
                    return $model->active ? Html::a(Yii::t('app/distribution', 'Start'), ['process', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id, 'id' => $model->id], ['class' => 'btn btn-info btn-xs']) : '&nbsp;';
                },
                'options'        => [
                    'width' => '90px',
                ],
                'contentOptions' => [
                    'class' => 'control',
                ],
            ],
        ],
    ]); ?>
</div>
