<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 14:13
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\widgets\menu\Menu;

AppAsset::register($this);
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
    <div class="wrapper">
        <div class="header">
            <div class="logo">
                <?= Html::a('BaseProducts.ru', Yii::$app->homeUrl, ['id' => 'home-logo']); ?>
            </div>
            <div class="nav">
                <a href="<?= Url::to(['/sign/out']) ?>" class="pull-right"><i class="glyphicon glyphicon-log-out"></i>
                    Выход</a>
            </div>
        </div>
        <div class="main">
            <div class="box sidebar">
                <?= Menu::widget(
                    [
                        'id'    => 'admin-menu',
                        'items' => [
                            [
                                'label' => 'Каталог товаров',
                                'url'   => Url::to(['/reference', 'type' => 'catalog']),
                            ],
                            [
                                'label' => 'Свойства товаров',
                                'url'   => Url::to(['/reference', 'type' => 'props']),
                            ],
                            [
                                'label' => 'Импорт товаров',
                                'url'   => Url::to(['/reference/import', 'type' => 'catalog', 'reference_id' => 3]),
                            ],
                            [
                                'label' => 'Типы справочников',
                                'url'   => Url::toRoute('/reference-type'),
                            ],
                        ],
                    ]
                ); ?>
            </div>
            <div class="box content">
                <div class="page-title"><?= $this->title ?></div>
                <div class="container-fluid">
                    <?= Breadcrumbs::widget([
                        'options'  => ['class' => 'breadcrumb small'],
                        'homeLink' => ['label' => 'Главная', 'url' => '/'],
                        'links'    => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]) ?>
                    <?php foreach (Yii::$app->session->getAllFlashes() as $key => $message) : ?>
                        <p class="bg-<?= $key ?> text-<?= $key ?> small notify"><?= $message ?></p>
                    <?php endforeach; ?>
                    <?= $content ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
