<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 27.03.17
 * Time: 10:36
 */

namespace app\assets\reference;

use yii\web\AssetBundle;

/**
 * Class ImportAsset
 * @package app\assets\reference
 */
class ImportAsset extends AssetBundle
{
    public $sourcePath = 'app/media';

    public $js = [
        'js/reference/import.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
    ];
}