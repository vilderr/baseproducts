<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 23.03.17
 * Time: 11:31
 */

/**
 * @var $this yii\web\View
 * @var $model app\models\reference\Reference
 * @var $searchModel app\models\reference\search\ReferenceSectionSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $section app\models\reference\ReferenceSection
 */

use yii\helpers\Html;

$this->title = $model->name . ': ' . $section->name . ': ' . Yii::t('app/reference', 'Sections');
$this->params['breadcrumbs'][] = ['label' => $model->referenceType->name, 'url' => ['index', 'type' => $model->referenceType->id]];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['section', 'type' => $model->referenceType->id, 'reference_id' => $model->id]];
if (!$section->isNewRecord) {
    foreach ($section->parents()->all() as $parent) {
        $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => ['section', 'type' => $model->referenceType->id, 'reference_id' => $model->id, 'reference_section_id' => $parent->id]];
    }
}
$this->params['breadcrumbs'][] = ['label' => $section->name];
?>
<div class="reference-section-index">
    <? echo $this->render('_search', [
        'model'     => $searchModel,
        'reference' => $model,
        'section'   => $section,
    ]); ?>
    <p>
        <?= Html::a(Yii::t('app/reference', 'create-section-btn'), ['create-section', 'type' => $model->reference_type_id, 'reference_id' => $model->id, 'reference_section_id' => $section->id], ['class' => 'btn btn-success']); ?>
        <? if (!$section->isNewRecord): ?>
            <?= Html::a(Yii::t('app/reference', 'up-folder-btn'), ['section', 'type' => $model->reference_type_id, 'reference_id' => $model->id, 'reference_section_id' => $section->reference_section_id], ['class' => 'btn btn-default']); ?>
        <? endif; ?>
        <?= Html::a(Yii::t('app/reference', 'Elements'), ['element', 'type' => $model->reference_type_id, 'reference_id' => $model->id, 'reference_section_id' => $section->id], ['class' => 'btn btn-default']); ?>
    </p>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'       => '{items}{summary}{pager}',
        'columns'      => [
            [
                'attribute' => 'id',
                'options'   => [
                    'width' => '80px',
                ],
            ],
            [
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return Html::a($model->name, ['section', 'type' => $model->reference->reference_type_id, 'reference_id' => $model->reference->id, 'reference_section_id' => $model->id]);
                },
            ],
            [
                'attribute' => 'active',
                'filter'    => [0 => Yii::t('app/reference', 'filter-active-yes'), 1 => Yii::t('app/reference', 'filter-active-no')],
                'format'    => 'boolean',
                'options'   => [
                    'width' => '110px',
                ],
            ],
            [
                'attribute' => 'sort',
                'options'   => [
                    'width' => '110px',
                ],
            ],
            [
                'class'          => 'yii\grid\ActionColumn',
                'contentOptions' => [
                    'class' => 'text-center',
                ],
                'template'       => '{update} {delete}',
                'buttons'        => [
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fa fa-pencil"></i>', ['update-section', 'type' => $model->reference->reference_type_id, 'reference_id' => $model->reference->id, 'reference_section_id' => $model->id]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fa fa-trash"></i>', ['delete-section', 'type' => $model->reference->reference_type_id, 'reference_id' => $model->reference->id, 'reference_section_id' => $model->id], ['data-method' => 'post', 'data-confirm' => 'Будут удалены все внутрилежащие разделы и элементы. Вы хотите удалить раздел?']);
                    },
                ],
                'options'        => [
                    'width' => '60px',
                ],
            ],
        ],
    ]); ?>
</div>
