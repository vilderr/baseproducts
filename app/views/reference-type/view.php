<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\reference\Reference;

/**
 * @var $this yii\web\View
 * @var $model app\models\reference\ReferenceType
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel app\models\reference\search\ReferenceSearch
 **/

$this->title = $model->name . ': ' . Yii::t('app/reference', 'References');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app/reference', 'Reference Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reference-type-view">

    <?= $this->render('/reference/_search', [
        'model'       => $model,
        'searchModel' => $searchModel,
    ]); ?>

    <p>
        <?= Html::a(Yii::t('app/reference', 'create-reference-btn'), ['create-reference', 'reference_type_id' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'       => '{items}{summary}{pager}',
        'columns'      => [
            'id',
            [
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function ($referenceModel) {
                    return Html::a(Html::encode($referenceModel->name), ['update-reference', 'id' => $referenceModel->id]);
                },
            ],
            //'created_at:datetime',
            //'updated_at:datetime',
            [
                'attribute' => 'active',
                'filter'    => Reference::getStatusArray(),
                'format'    => 'boolean',
            ],
            'sort',
            [
                'attribute' => 'catalog',
                'filter'    => [0 => Yii::t('app/reference', 'filter-active-no'), 1 => Yii::t('app/reference', 'filter-active-yes')],
                'format'    => 'boolean',
            ],
            [
                'class'          => 'yii\grid\ActionColumn',
                'template'       => '{delete}',
                'options'        => [
                    'width' => '50px',
                ],
                'contentOptions' => [
                    'class' => 'text-center',
                ],
                'buttons'        => [
                    'update' => function ($url, $referenceModel) use ($model) {
                        return Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>', ['update-reference', 'reference_type_id' => $model->id, 'id' => $referenceModel->id]);
                    },
                    'delete' => function ($url, $referenceModel) use ($model) {
                        return Html::a('<i class="fa fa-trash" aria-hidden="true"></i>', ['/reference/delete', 'id' => $referenceModel->id], ['data-method' => 'post', 'data-confirm' => Yii::t('app/reference', 'Confirm delete "{item}"?', ['item' => $referenceModel->name])]);
                    },
                ],
            ],
        ],
    ]) ?>

</div>
