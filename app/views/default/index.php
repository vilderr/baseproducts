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

//$arFile = File::makeArray('http://i.otto.ru/i/otto/13688522?$formatz$');
//echo '<pre>'; print_r($arFile); echo '</pre>';

//$extension = \yii\helpers\FileHelper::getExtensionsByMimeType($arFile['type']);
//$extension = pathinfo('http://i.otto.ru/i/otto/13688522?$formatz$', PATHINFO_EXTENSION);

//echo $extension;
//echo '<pre>'; print_r($extension); echo '</pre>';


$from = "http://image01.bonprix.ru/bonprixbilder/429x60770/1459330988/15045961-pJYZXWVt.jpg";
$to = Yii::$app->getBasePath().'/upload';
exec("cd $to && /usr/bin/wget $from > /dev/null &", $result, $error);
echo $result;


/*
$start_time = time();
$interval = 30;

$elements = ReferenceElement::find()->limit(10)->where(['reference_id' => '3', 'shop' => 'Bonprix', 'detail_picture' => null])->all();
foreach ($elements as $element) {
    $arFile = File::makeArray($element->picture_src);
    if (is_array($arFile)) {
        $element->detail_picture = File::saveFile($arFile, 'reference');
    } else {
        $element->reference_section_id = 32;
    }

    $element->save(false);

    if ($interval > 0 && (time() - $start_time) > $interval)
        break;
}
*/

/*
$file = 'http://image01.bonprix.ru/bonprixbilder/429x600/1443443500/14194024-lOlBBOi7.jpg';
$path = Yii::$app->getBasePath().'/upload/image.jpg';

$result = File::downloadFile($file, $path);
echo $result;
*/

//echo \yii\helpers\Html::img(Yii::$app->getBasePath().'/upload/reference/838/8385c65d6051d568879fbd91876a4c87');