<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 09.03.17
 * Time: 13:16
 * @var $this yii\web\View
 * @var $model app\modules\admin\models\ReferenceType
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Новый тип справочников';
?>
<div class="reference-type-create">
    <? $form = ActiveForm::begin();?>

    <?= $form->field($model, 'id')->textInput(['maxlength' => true])?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'sort')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', ['/admin/reference-type'], ['class' => 'btn btn-default']);?>
    </div>

    <? ActiveForm::end();?>
</div>
