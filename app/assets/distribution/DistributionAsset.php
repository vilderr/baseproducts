<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 03.04.17
 * Time: 19:37
 */

namespace app\assets\distribution;


use yii\web\AssetBundle;

/**
 * Class DistributionAsset
 * @package app\assets\distribution
 */
class DistributionAsset extends AssetBundle
{
    public $sourcePath = 'app/media';

    public $js = [
        'js/distribution/script.js',
    ];

    public $css = [
        'css/distribution/styles.css',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
    ];
}