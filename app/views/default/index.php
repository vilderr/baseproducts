<?php
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

$arFile = File::makeArray('http://static.quiksilver.com/www/store.quiksilver.eu/html/images/catalogs/global/roxy-products/all/default/large/erjdp03094_suntripperscropped,w_bla6_frt2.jpg');
echo '<pre>'; print_r($arFile); echo '</pre>';

