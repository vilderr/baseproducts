<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 09.03.17
 * Time: 12:21
 * @var $this yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Типы справочников';
?>
<p>
    <?= Html::a('Создать новый тип', ['create'], ['class' => 'btn btn-success']) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout'       => '{items}{summary}{pager}',
    'columns'      => [
        [
            'attribute' => 'id',
            'format' => 'raw',
            'value' => function($model, $key, $index, $column) {
                return Html::a(Html::encode($model->name), ['/admin/reference', 'type' => $model->id]);
            },
        ],
        'name',
        'sort',
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
            'options'  => [
                'width' => '70px',
            ],
        ],
    ],
]);
