<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 09.03.17
 * Time: 10:43
 */

namespace app\widgets\menu\assets;


use yii\web\AssetBundle;

class MenuAsset extends AssetBundle
{
    public $basePath = 'app/widgets/menu';
    public $sourcePath = 'app/widgets/menu/media';

    public $css = [
        'css/style.css',
    ];

    public $js = [
        'js/script.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'app\assets\FontAwesomeAsset'
    ];
}