<?php
error_reporting(E_ALL);
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 14:20
 * @var $this yii\web\View
 */

$this->title = Yii::t('app', 'Admin Panel');

use app\models\file\File;
use app\models\reference\ReferenceElement;
use  yii\helpers\FileHelper;
use app\core\http\HttpRequest;

$arFile = File::makeArray('https://blackstarshop.ru/image/catalog2/women/LA1817-500/LA1817-500d.png');
echo '<pre>'; print_r($arFile); echo '</pre>';
