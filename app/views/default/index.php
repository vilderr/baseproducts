<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 14:20
 * @var $this yii\web\View
 */

$this->title = Yii::t('app', 'Admin Panel');

$sect = \app\models\reference\ReferenceType::getReferenceMenu();

echo '<pre>'; print_r($sect); echo '</pre>';