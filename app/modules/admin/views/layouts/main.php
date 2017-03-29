<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 15:09
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\admin\assets\AdminAsset;
use app\modules\admin\models\Reference;
use yii\widgets\Breadcrumbs;
use app\widgets\menu\Menu;

$asset = AdminAsset::register($this);
$module = $this->context->module->id;
?>
<?php $this->beginPage() ?>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div id="admin-body">
    <div class="">
        <div class="wrapper">
            <div class="header">
                <div class="logo">
                    Tuppo.pro
                </div>
                <div class="nav">
                    <a href="<?= Url::to(['/admin/sign/out']) ?>" class="pull-right"><i class="glyphicon glyphicon-log-out"></i> Выход</a>
                </div>
            </div>
            <div class="main">
                <div class="box sidebar">
                    <?= Menu::widget(
                        [
                            'id'    => 'admin-menu',
                            'items' => Reference::menuItems()
                        ]
                    ); ?>
                </div>
                <div class="box content">
                    <div class="page-title">
                        <?= $this->title ?>
                    </div>
                    <div class="container-fluid">
                        <?= Breadcrumbs::widget([
                                'homeLink' => ['label' => 'Главная', 'url' => '/admin/index'],
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        ]) ?>
                        <?php foreach(Yii::$app->session->getAllFlashes() as $key => $message) : ?>
                            <div class="alert alert-<?= $key ?>"><?= $message ?></div>
                        <?php endforeach; ?>
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
