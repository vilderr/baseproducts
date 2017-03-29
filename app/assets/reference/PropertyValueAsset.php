<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 25.03.17
 * Time: 17:59
 */

namespace app\assets\reference;


use yii\web\AssetBundle;

class PropertyValueAsset extends AssetBundle
{
    public $sourcePath = 'app/media';

    public $js = [
        'js/reference/property.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}