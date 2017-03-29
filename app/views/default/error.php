<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 14:12
 */

use yii\helpers\Html;
$this->title = $name;
?>
<h1><?= Html::encode($this->title) ?></h1>

<div class="alert alert-danger">
    <?= nl2br(Html::encode($message)) ?>
</div>

<p>
    The above error occurred while the Web server was processing your request.
</p>
<p>
    Please contact us if you think this is a server error. Thank you.
</p>
