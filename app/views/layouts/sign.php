<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 14:34
 */

use yii\helpers\Html;
use app\assets\SignAsset;

SignAsset::register($this);
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
