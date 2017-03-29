<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 21.03.17
 * Time: 13:40
 */

namespace app\assets;


use yii\web\AssetBundle;

/**
 * Class SignAsset
 * @package app\assets
 */
class SignAsset extends AssetBundle
{
    public $sourcePath = 'app/media';

    public $css = [
        'css/sign.css',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}