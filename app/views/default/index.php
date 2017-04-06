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


$image = fopen(Yii::$app->getBasePath().'/upload/image.jpg', 'w+b');
$loaded = HttpRequest::get('https://blackstarshop.ru/image/catalog2/women/LA1817-500/LA1817-500d.png')->body();

echo '<pre>'; print_r($loaded); echo '</pre>';

//$arFile = File::makeArray('https://blackstarshop.ru/image/catalog/new-catalog/Women/LA1816-53466.png');
//echo '<pre>'; print_r($arFile); echo '</pre>';

