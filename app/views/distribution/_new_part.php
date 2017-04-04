<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 03.04.17
 * Time: 21:21
 */
/**
 * @var $this yii\web\View
 * @var $model app\models\distribution\DistributionPart
 * @var $reference app\models\reference\Reference
 */

use yii\helpers\Json;
use yii\helpers\Html;

if ($model->isNewRecord) {
    $model->id = substr(md5(uniqid("", true)), 0, 5);
    $data = [];
} else {
    $data = unserialize($model->data);
}

$params = [
    'part'    => [
        'id' => $model->id,
    ],
    'visual'  => [
        'id'        => 'part-' . $model->id,
        'condition' => 'part-conditions-box-' . $model->id,
        'action'    => 'part-actions-box-' . $model->id,
    ],
    'buttons' => [
        'remove'    => 'part-remove-btn-' . $model->id,
        'condition' => 'part-condition-btn-' . $model->id,
        'action'    => 'part-action-btn-' . $model->id,
    ],
    'data'    => [
        'url'          => '/distribution/part-service',
        'reference_id' => $reference->id,
    ],
];
$arJSParams = [
    'addCondition' => '#part-condition-btn-' . $model->id,
    'addOperation' => '#part-action-btn-' . $model->id,
    'obCondition'  => '#part-conditions-box-' . $model->id,
    'obOperation'  => '#part-actions-box-' . $model->id,
    'removePart'   => '#part-remove-btn-' . $model->id,
    'url'          => '/distribution/part-service',
    'id'           => $model->id,
    'reference_id' => $reference->id,
];
?>
    <div class="panel panel-default distribution-part" id="<?= $params['visual']['id'] ?>">
        <div class="panel-heading form-inline text-right">
            <?= Html::activeCheckbox($model, '[' . $model->id . ']active'); ?>
        </div>
        <div class="panel-body">
            <div class="conditions" id="<?= $params['visual']['condition']; ?>">
                <h4 class="panel-title">Фильтр</h4>
                <?
                foreach ($data['condition'] as $key => $value) {
                    echo $this->render('_condition_line', [
                        'reference' => $reference,
                        'model'     => $model,
                        'name'      => $key,
                        'value'     => $value,
                    ]);
                }
                ?>
            </div>
            <hr class="xs">
            <div class="actions" id="<?= $params['visual']['action']; ?>">
                <h4 class="panel-title">Действия</h4>
                <?
                foreach ($data['operation'] as $key => $value) {
                    echo $this->render('_action_line', [
                        'model' => $model,
                        'reference' => $reference,
                        'name'  => $key,
                        'value' => $value,
                    ]);
                }
                ?>
            </div>
        </div>
        <div class="panel-footer">
            <a class="btn btn-default" id="<?= $params['buttons']['condition'] ?>">Добавить условие</a>
            <a class="btn btn-default" id="<?= $params['buttons']['action'] ?>">Добавить действие</a>
            <?= Html::a('Удалить итерацию', 'javascript:void(0);', ['class' => 'btn btn-danger', 'id' => $params['buttons']['remove']]); ?>
        </div>
    </div>
<?
$this->registerJs('$("#' . $params['visual']['id'] . '").distribution(' . Json::encode($arJSParams) . ');');
?>