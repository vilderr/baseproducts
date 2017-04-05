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

$arFile = File::makeArray('http://static.quiksilver.com/www/store.quiksilver.eu/html/images/catalogs/global/roxy-products/all/default/hi-res/erjjk03089_steffijk,w_kvj0_frt1.jpg');