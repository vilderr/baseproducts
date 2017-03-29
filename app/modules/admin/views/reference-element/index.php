<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 11.03.17
 * Time: 16:19
 * @var $this yii\web\View
 * @var $reference app\modules\admin\models\Reference
 * @var $section app\modules\admin\models\ReferenceSection
 * @var $searchModel \app\modules\admin\models\search\ReferenceElementSearch
 * @var $dataProvider \app\modules\admin\components\ActiveDataProvider
 */

use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\helpers\Html;

$reference = Yii::$app->controller->reference;

$this->title = $reference->name.': Элементы';
$route = '/'.Yii::$app->controller->getRoute();
$labels = $searchModel->attributeLabels();

$this->params['breadcrumbs'][] = ['label' => $reference->name, 'url' => ['index', 'reference_id' => $reference->id]];
if(!$section->isNewRecord)
{
    $this->title = $reference->name.': '.$section->name.': Элементы';

    foreach ($section->parents()->all() as $parent)
    {
        $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => ['index', 'reference_id' => $reference->id, 'reference_section_id' => $parent->id]];
    }

    $this->params['breadcrumbs'][] = ['label' => $section->name, 'url' => ['index', 'reference_id' => $reference->id, 'reference_section_id' => $section->id]];
}
$this->params['breadcrumbs'][] = ['label' => 'Элементы']
?>

<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Фильтр</h3>
            </div>
            <div class="panel-body">
                <?= Html::beginForm($route, 'get', ['id' => 'element-filter', 'class' => 'form-horizontal']);?>
                <?=Html::csrfMetaTags()?>
                <?=Html::hiddenInput('reference_id', $reference->id);?>
                <?
                if (isset($params['per-page']))
                {
                    echo Html::hiddenInput('per-page', $params['per-page']);
                }
                ?>
                <div class="form-group">
                    <label for="reference-element-name" class="col-sm-2 control-label"><?=$labels['name']?></label>
                    <div class="col-sm-8">
                        <?=Html::textInput('name', $searchModel->name, ['class' => 'form-control', 'id' => 'reference-element-name']);?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="reference-section-id" class="col-sm-2 control-label"><?=$labels['reference_section_id']?></label>
                    <div class="col-sm-4">
                        <?=Html::dropDownList('reference_section_id', $searchModel->reference_section_id, ArrayHelper::merge([0 => '-- Верхний уровень --'], $section::getTree()), ['class' => 'form-control', 'id' => 'reference-section-id']);?>
                    </div>
                    <div class="col-sm-4"><label><?=Html::checkbox('include_subsections', $searchModel->include_subsections);?> Включая подразделы</label></div>
                </div>
                <div class="form-group">
                    <label for="reference-element-active" class="col-sm-2 control-label"><?=$labels['active']?></label>
                    <div class="col-sm-4">
                        <?=Html::dropDownList('active', $searchModel->active, $searchModel::getActiveArray(), ['class' => 'form-control', 'id' => 'reference-element-active']); ?>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <?=Html::submitButton('Применить', ['class' => 'btn btn-primary']);?>
            </div>
            <? Html::endForm();?>
        </div>
    </div>
</div>
<p>
    <?= Html::a('<i class="fa fa-plus"></i> Добавить элемент', ['create', 'reference_id' => $reference->id, 'reference_section_id' => $section->id], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Разделы', ['/admin/reference-section/index', 'reference_id' => $reference->id, 'reference_section_id' => $section->id], ['class' => 'btn btn-default']) ?>
</p>
<?
/*
$i = 1;
while($i < 50001)
{
    $model = new ReferenceElement(['reference_id' => $reference->id]);
    $model->name = 'Новый элемент '.$i;
    $model->reference_section_id = $section->id;
    if($model->validate())
    {
        $model->save();
    }
    $i++;
}
*/
/*
$model = new ReferenceElement(['reference_id' => $reference->id]);
$model::deleteAll(['reference_section_id' => $section->id]);
*/
?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout'       => '{items}{summary}{pager}',
    'columns' => [
        [
            'attribute' => 'id',
            'options'   => [
                'width' => '85px',
            ],
        ],
        [
            'attribute' => 'name',
            'label'     => 'Название',
            'format'    => 'raw',
            'value'     => function ($model) use ($reference, $section) {
                return Html::a(Html::encode($model->name), ['update', 'reference_id' => $reference->id, 'reference_section_id' => $section->id, 'id' => $model->id]);
            },
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
        'price',
        'oldprice',
        'discount',
        'shop',
        [
            'class'          => 'yii\grid\ActionColumn',
            'header'         => 'Действия',
            'contentOptions' => [
                'class' => 'text-center',
            ],
            'template'       => '{delete}',
            'buttons'        => [
                'delete' => function ($url, $model) use ($reference) {
                    return Html::a('<i class="fa fa-trash"></i>', ['delete', 'reference_id' => $reference->id, 'id' => $model->id], ['data-method' => 'post', 'data-confirm' => 'Вы действительно хотите удалить элемент?']);
                },
            ],
            'options'        => [
                'width' => '85px',
            ],
        ],
    ],
]);
