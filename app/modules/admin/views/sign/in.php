<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 15:36
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Вход в панель администратора';
?>
<div class="container">
    <div id="wrapper" class="col-md-4 col-md-offset-4 vertical-align-parent">
        <div class="vertical-align-child">
            <div class="panel">
                <div class="panel-heading text-center">
                    Администратор
                </div>
                <div class="panel-body">
                    <?php foreach(Yii::$app->session->getAllFlashes() as $key => $message) : ?>
                        <div class="alert alert-<?= $key ?>"><?= $message ?></div>
                    <?php endforeach; ?>
                    <?php $form = ActiveForm::begin([
                        'fieldConfig' => [
                            'template' => "{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}"
                        ]
                    ]); ?>
                    <?= $form->field($model, 'username')->textInput(['placeholder' => 'Логин', 'autofocus' => true]) ?>
                    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Пароль']) ?>
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                    <div style="color:#999;margin:1em 0">
                        <a href="<?= Url::to('restore-password')?>">Забыли пароль?</a>
                    </div>
                    <div class="form-group">
                        <?= Html::submitButton('Вход', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>