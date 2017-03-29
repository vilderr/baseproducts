<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 10.03.17
 * Time: 11:56
 * @var $this yii\web\View
 * @var $reference app\modules\admin\models\Reference
 * @var $section app\modules\admin\models\ReferenceSection
 * @var $searchModel app\modules\admin\models\search\ReferenceSectionSearch
 * @var $dataProvider \app\modules\admin\components\ActiveDataProvider
 *
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

$reference = Yii::$app->controller->reference;

$this->title = $reference->name.': Разделы';
$route = '/'.Yii::$app->controller->getRoute();

$params = Yii::$app->request->queryParams;
$labels = $searchModel->attributeLabels();

$this->params['breadcrumbs'][] = ['label' => $reference->name, 'url' => ['index', 'reference_id' => $reference->id]];

if(!$section->isNewRecord)
{
    $this->title = $reference->name.': '.$section->name.': Разделы';

    foreach ($section->parents()->all() as $parent)
    {
        $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => ['index', 'reference_id' => $reference->id, 'reference_section_id' => $parent->id]];
    }

    $this->params['breadcrumbs'][] = ['label' => $section->name, 'url' => ['index', 'reference_id' => $reference->id, 'reference_section_id' => $section->id]];
}

$this->params['breadcrumbs'][] = ['label' => 'Разделы']
?>
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Фильтр</h3>
            </div>
            <div class="panel-body">
            <?= Html::beginForm($route, 'get', ['id' => 'section-filter', 'class' => 'form-horizontal']);?>
                <?=Html::csrfMetaTags()?>
                <?=Html::hiddenInput('reference_id', $reference->id);?>
                <?
                if (isset($params['per-page']))
                {
                    echo Html::hiddenInput('per-page', $params['per-page']);
                }
                ?>
                <div class="form-group">
                    <label for="reference-section-name" class="col-sm-3 control-label"><?=$labels['name']?></label>
                    <div class="col-sm-7">
                        <?=Html::textInput('name', $searchModel->name, ['class' => 'form-control', 'id' => 'reference-section-name']);?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="reference-section-active" class="col-sm-3 control-label"><?=$labels['active']?></label>
                    <div class="col-sm-7">
                            <?=Html::dropDownList('active', $searchModel->active, $searchModel::getActiveArray(), ['class' => 'form-control', 'id' => 'reference-section-active']);?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="reference-section-id" class="col-sm-3 control-label"><?=$labels['reference_section_id']?></label>
                    <div class="col-sm-7">
                        <?=Html::dropDownList('reference_section_id', $searchModel->reference_section_id, ArrayHelper::merge([0 => '-- Верхний уровень --'], $section::getTree()), ['class' => 'form-control', 'id' => 'reference-section-id']);?>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <?=Html::submitButton('Применить', ['class' => 'btn btn-primary']);?>
                <?=Html::a('Отменить', [$route, 'reference_id' => $reference->id,'reference_section_id' => $section->id], ['class' => 'btn btn-default'])?>
            </div>
            <? Html::endForm();?>
        </div>
    </div>
</div>
<p>
    <?= Html::a('<i class="fa fa-plus"></i> Добавить раздел', ['create', 'reference_id' => $reference->id, 'reference_section_id' => $section->id], ['class' => 'btn btn-success']) ?>
    <?
        if(!$section->isNewRecord)
            echo Html::a('<i class="fa fa-plus"></i> На уровень вверх', ['index', 'reference_id' => $reference->id, 'reference_section_id' => $section->reference_section_id], ['class' => 'btn btn-default']);
    ?>
    <?= Html::a('Элементы', ['/admin/reference-element/index', 'reference_id' => $reference->id, 'reference_section_id' => $section->id], ['class' => 'btn btn-default']) ?>
</p>
<br>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout'       => '{items}{summary}{pager}',
    'columns' => [
        [
            'attribute' => 'id',
            'label'     => 'ID',
        ],
        [
            'attribute' => 'name',
            'label'     => 'Название',
            'format'    => 'raw',
            'value'     => function ($model) use ($reference) {
                return Html::a(Html::encode($model->name), ['/admin/reference-section', 'reference_id' => $reference->id, 'reference_section_id' => $model->id]);
            },

        ],
        [
            'attribute' => 'sort',
            'label'     => 'Сортировка',
        ],
        [
            'attribute' => 'active',
            'label'     => 'Активность',
            'filter'    => [
                0 => 'Нет',
                1 => 'Да',
            ],
            'format'    => 'boolean',
        ],
        [
            'class'          => 'yii\grid\ActionColumn',
            'header'         => 'Действия',
            'contentOptions' => [
                'class' => 'text-center',
            ],
            'template'       => '{update} {delete}',
            'buttons'        => [
                'update' => function ($url, $model) use ($reference) {
                    return Html::a('<i class="fa fa-pencil"></i>', ['update', 'reference_id' => $reference->id, 'reference_section_id' => $model->id]);
                },
                'delete' => function ($url, $model) use ($reference) {
                    return Html::a('<i class="fa fa-trash"></i>', ['delete', 'reference_id' => $reference->id, 'id' => $model->id], ['data-method' => 'post', 'data-confirm' => 'Будут удалены все внутрилежащие элементы. Вы хотите удалить раздел?']);
                },
            ],
            'options'        => [
                'width' => '85px',
            ],
        ],
    ],
]);
