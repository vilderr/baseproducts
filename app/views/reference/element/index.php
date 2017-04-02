<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 23.03.17
 * Time: 22:56
 */

/**
 * @var $this yii\web\View
 * @var $reference app\models\reference\Reference
 * @var $section app\models\reference\ReferenceSection
 * @var $searchModel app\models\reference\search\ReferenceElementSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use app\widgets\grid\View;

$this->title = $reference->name . ': ' . $section->name . ': ' . Yii::t('app/reference', 'Elements');
$this->params['breadcrumbs'][] = ['label' => $reference->referenceType->name, 'url' => ['index', 'type' => $reference->referenceType->id]];
$this->params['breadcrumbs'][] = ['label' => $reference->name, 'url' => ['element', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id]];
if (!$section->isNewRecord) {
    foreach ($section->parents()->all() as $parent) {
        $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => ['element', 'type' => $reference->referenceType->id, 'reference_id' => $reference->id, 'reference_section_id' => $parent->id]];
    }
}
$this->params['breadcrumbs'][] = ['label' => $section->name];
?>
<div class="reference-element-index">
    <? echo $this->render('_search', [
        'model'     => $searchModel,
        'reference' => $reference,
        'section'   => $section,
    ]); ?>
    <p>
        <?= Html::a(Yii::t('app/reference', 'create-element-btn'), ['create-element', 'type' => $reference->reference_type_id, 'reference_id' => $reference->id, 'reference_section_id' => $section->id], ['class' => 'btn btn-success']); ?>
        <?= Html::a(Yii::t('app/reference', 'Sections'), ['section', 'type' => $reference->reference_type_id, 'reference_id' => $reference->id, 'reference_section_id' => $section->id], ['class' => 'btn btn-default']); ?>
    </p>
    <?
    $catalogColumns = $reference->catalog ? [
        [
            'attribute' => 'current_props',
            'format'    => 'ntext',
        ],
        [
            'attribute' => 'price',
            'options'   => [
                'width' => '110px',
            ],
        ],
        [
            'attribute' => 'oldprice',
            'options'   => [
                'width' => '110px',
            ],
        ],
        [
            'attribute' => 'discount',
            'options'   => [
                'width' => '110px',
            ],
        ],
    ] : [];
    ?>
    <?= View::widget([
        'dataProvider'     => $dataProvider,
        'layout'           => '{items}{summary}{pager}',
        'pager'            => [
            'class' => \app\widgets\pager\ModernLinkPager::className(),
        ],
        'showFooter'       => true,
        'footerRowOptions' => [
            'class' => 'active',
        ],
        'options'          => [
            'tag'    => 'form',
            'id'     => 'reference-' . $reference->id . '-elements-list',
            'method' => 'post',
        ],
        'actions'          => [
            'DEA' => 'Деактивировать',
            'ACT' => 'Активировать',
            'MOV' => 'Перенести в раздел',
        ],
        'columns'          => \yii\helpers\ArrayHelper::merge(
            [
                [
                    'class'   => \yii\grid\CheckboxColumn::className(),
                    'options' => [
                        'width' => '50px',
                    ],
                ],
                [
                    'attribute' => 'id',
                    'options'   => [
                        'width' => '85px',
                    ],
                ],
                [
                    'attribute' => 'name',
                    'format'    => 'raw',
                    'value'     => function ($model) use ($reference) {
                        return Html::a($model->name, ['update-element', 'type' => $reference->reference_type_id, 'reference_id' => $reference->id, 'id' => $model->id]);
                    },
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
                    'attribute'      => 'sort',
                    'contentOptions' => [
                        'class' => 'text-center',
                    ],
                    'options'        => [
                        'width' => '105px',
                    ],
                ],
            ],
            $catalogColumns,
            [
                [
                    'class'          => 'yii\grid\ActionColumn',
                    'contentOptions' => [
                        'class' => 'text-center',
                    ],
                    'template'       => '{delete}',
                    'buttons'        => [
                        'delete' => function ($url, $model) {
                            return Html::a('<i class="fa fa-trash"></i>', ['delete-element', 'type' => $model->reference->reference_type_id, 'reference_id' => $model->reference->id, 'id' => $model->id], ['data-method' => 'post', 'data-confirm' => 'Вы действительно хотите удалить элемент?']);
                        },
                    ],
                    'options'        => [
                        'width' => '50px',
                    ],
                ],
            ]
        ),
    ]); ?>

    <?
    $requestUrl = Yii::$app->request->url;
    $script = <<< JS
    var form = $("#reference-$reference->id-elements-list");
    var button = $(form).find('button[name="group-action"]');
    var actionSelect = $(form).find('select[name="action-select"]');
    var actionBox = $(form).find('#action-box');
    
    $(form).on('submit', function(e) {
        
        if(!button.attr('disabled'))
        {
            return true;
        }
        
        return false;    
    });
    
    $(form).on('change', 'input[type="checkbox"], select[name="action"]', function() {
       var keys = $(form).yiiGridView('getSelectedRows');
       
       $(button).prop('disabled', keys.length === 0);
    });
    
    $(form).on('change', 'select[name="action"]', function(e) {
        var target = e.target;
        var actionValue = target.value;
        
        if (actionValue == 'MOV') {
            var request = $.ajax({
                url: '$requestUrl',
                data: {
                    'LOAD_TREE': 'Y'
                },
                type: 'post'
            });
            
            request.done(function(content) {
                $(actionBox).append(content);
            });
        } else {
            $(actionBox).html('');
        }
    });
JS;
    $this->registerJs($script);
    ?>
</div>
