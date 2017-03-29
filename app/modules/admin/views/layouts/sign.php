<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 15:36
 */
use yii\helpers\Html;
use app\modules\admin\assets\SignAsset;

$asset = SignAsset::register($this);
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
<div class="container">
    <?= $content ?>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>