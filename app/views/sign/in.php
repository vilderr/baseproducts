<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 14:33
 * @var $this yii\web\View
 * @var $model app\models\user\forms\LoginForm
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Yii::t('app', 'admin-panel-title');
?>
<div class="container">
    <div id="wrapper" class="col-md-4 col-md-offset-4 vertical-align-parent">
        <div class="vertical-align-child">
            <div class="panel">
                <div class="panel-heading text-center">
                    <?=Yii::t('app/user', 'login-form-title');?>
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
                    <?= $form->field($model, 'username')->textInput(['placeholder' => Yii::t('app/user', 'login'), 'autofocus' => true]) ?>
                    <?= $form->field($model, 'password')->passwordInput(['placeholder' => Yii::t('app/user', 'password')]) ?>
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                    <div style="color:#999;margin:1em 0">
                        <a href="<?= Url::to('restore-password')?>"><?=Yii::t('app/user', 'forgot-password')?></a>
                    </div>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app/user', 'login-btn-title'), ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>