<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\reference\Reference;

/* @var $this yii\web\View */
/* @var $searchModel app\models\reference\search\ReferenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $referenceType app\models\reference\ReferenceType */

$this->title = $referenceType->name . ': ' . Yii::t('app/reference', 'References');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reference-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'       => '{items}{summary}{pager}',
        'columns'      => [
            [
                'attribute' => 'id',
                'options'   => [
                    'width' => '50px',
                ],
            ],
            [
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function ($model) use ($referenceType) {
                    return Html::a(Html::encode($model->name), ['reference/section/index', 'type' => $referenceType->id, 'reference_id' => $model->id]);
                },
            ],
            'code',
            'xml_id',
            [
                'attribute' => 'active',
                'filter'    => Reference::getStatusArray(),
                'format'    => 'boolean',
            ],
            'sort',
        ],
    ]); ?>
</div>
