<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 04.04.17
 * Time: 1:32
 */

/**
 * @var $this yii\web\View
 * @var $var string
 * @var $model app\models\distribution\DistributionPart
 * @var $reference app\models\reference\Reference
 * @var $name string
 * @var $value mixed
 */

use yii\helpers\Html;

$list = [
    'active'  => 'Активность',
    'section' => 'Перенести в раздел',
];

?>
<div class="action-line line" data-content="operation">
    <div class="clearfix form-inline">
        <div class="form-group">
            <?= Html::dropDownList(null, $name, $list, ['class' => 'form-control chooser', 'prompt' => ['text' => '-- Действие --', 'options' => ['class' => 'prompt']]]); ?>
        </div>
        <div class="form-group action-value value-box">
            <?
            if ($value !== null) {
                echo $this->render('_operation_value', [
                    'model'     => $model,
                    'reference' => $reference,
                    'operation' => $name,
                    'value'     => $value,
                ]);
            }
            ?>
        </div>
        <?= Html::a('Удалить', 'javascript:void(0)', ['class' => 'btn btn-danger btn-sm pull-right remove-line']); ?>
    </div>
</div>
