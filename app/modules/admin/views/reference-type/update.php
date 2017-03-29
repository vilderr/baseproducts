<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 09.03.17
 * Time: 13:35
 * @var $this yii\web\View
 * @var $model app\modules\admin\models\ReferenceType
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Редактирование типа справочников "'.$model->id.'"';
?>
<div class="reference-type-update">
    <?php $form = ActiveForm::begin(); ?>

    <p><label>ID:</label> <?=$model->id;?></p>

    <?= $form->field($model, 'name')->textInput() ?>
    <?= $form->field($model, 'sort')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Отмена', ['/admin/reference-type'], ['class' => 'btn btn-default']);?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
