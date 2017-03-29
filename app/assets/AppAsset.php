<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class AppAsset
 * @package app\assets
 */
class AppAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = 'app/media';
    /**
     * @var array
     */
    public $css = [
        'css/main.css',
    ];
    /**
     * @var array
     */
    public $js = [];
    /**
     * @var array
     */
    public $depends = [
        //'yii\web\YiiAsset',
        'app\assets\FontAwesomeAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
