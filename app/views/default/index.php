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


//$image = fopen(Yii::$app->getBasePath().'/upload/image.jpg', 'w+b');
//$loaded = HttpRequest::get('https://blackstarshop.ru/image/catalog2/women/LA1817-500/LA1817-500d.png')->body();

//echo '<pre>'; print_r($loaded); echo '</pre>';

//$arFile = File::makeArray('https://blackstarshop.ru/image/catalog2/women/LA1817-500/LA1817-500d.png');
//echo '<pre>'; print_r($arFile); echo '</pre>';
$file = Yii::$app->getBasePath().'/upload/image.png';
$url = 'https://blackstarshop.ru/image/catalog2/women/LA1817-500/LA1817-500d.png';
$start = microtime(true);
curl_download($url, $file);
$end = microtime(true);
$time = $end - $start;
printf('Время работы скрипта: %.3F сек.', $time);

function curl_download($url, $file)
{
    // открываем файл, на сервере, на запись
    $dest_file = @fopen($file, "w");

    // открываем cURL-сессию
    $resource = curl_init();

    // устанавливаем опцию удаленного файла
    curl_setopt($resource, CURLOPT_URL, $url);

    // устанавливаем место на сервере, куда будет скопирован удаленной файл
    curl_setopt($resource, CURLOPT_FILE, $dest_file);

    // заголовки нам не нужны
    curl_setopt($resource, CURLOPT_HEADER, 0);

    // выполняем операцию
    curl_exec($resource);

    // закрываем cURL-сессию
    curl_close($resource);

    // закрываем файл
    fclose($dest_file);
}

