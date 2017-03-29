<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 09.03.17
 * Time: 12:57
 * @var $this yii\web\View
 * @var $referenceType \app\modules\admin\models\ReferenceType
 * @var $dataProvider \yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = $referenceType->name.': Справочники';
?>
<p>
    <?= Html::a('Создать справочник', ['create', 'type' => $referenceType->id], ['class' => 'btn btn-success']) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout'       => '{items}{summary}{pager}',
    'columns' => [
        'id',
        'name',
        'code',
        'xml_id',
        'sort',
        [
            'attribute' => 'active',
            'filter' => [0 => 'Нет', 1 => 'Да'],
            'format' => 'boolean',
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
            'options'  => [
                'width' => '70px',
            ],
        ],
    ],
]);