<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 04.04.17
 * Time: 1:31
 */

/**
 * @var $this yii\web\View
 * @var $model app\models\distribution\DistributionPart
 * @var $reference app\models\reference\Reference
 * @var $name string
 * @var $value mixed
 */

use yii\helpers\Html;

$list = [
    'name'    => 'Название',
    'section' => 'Раздел родитель',
];
if ($reference->catalog) {
    $list['current_props'] = 'Исходные свойства';
    $list['price'] = 'Диапазон цены';
}
?>
<div class="condition-line line" data-content="condition">
    <div class="clearfix form-inline">
        <div class="form-group">
            <?= Html::dropDownList(null, $name, $list, ['class' => 'form-control chooser', 'prompt' => ['text' => '-- Условие --', 'options' => ['class' => 'prompt']]]) ?>
        </div>
        <div class="form-group condition-value value-box">
            <?
            if ($value !== null) {
                echo $this->render('_condition_value', [
                    'model'     => $model,
                    'condition' => $name,
                    'reference' => $reference,
                    'value'     => $value,
                ]);
            }
            ?>
        </div>
        <?= Html::a('Удалить', 'javascript:void(0)', ['class' => 'btn btn-danger btn-sm pull-right remove-line']); ?>
    </div>
</div>
