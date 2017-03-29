<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\reference\search\ReferenceTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/reference', 'Reference Types');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reference-type-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app/reference', 'create-reference-type-btn'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'       => '{items}{summary}{pager}',
        'columns'      => [

            'id',
            [
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return Html::a(Html::encode($model->name), ['view', 'id' => $model->id]);
                },
            ],
            'sort',
            [
                'class'          => 'yii\grid\ActionColumn',
                'template'       => '{update} {delete}',
                'options'        => [
                    'width' => '70px',
                ],
                'contentOptions' => [
                    'class' => 'text-center',
                ],
                'buttons'        => [
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>', ['update', 'id' => $model->id]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fa fa-trash" aria-hidden="true"></i>', ['delete', 'id' => $model->id], ['data-method' => 'post', 'data-confirm' => Yii::t('app/reference', 'Confirm delete "{item}"?', ['item' => $model->name])]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
