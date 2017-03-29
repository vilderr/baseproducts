<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 21.03.17
 * Time: 23:09
 */

namespace app\assets\reference;


use yii\web\AssetBundle;

/**
 * Class ReferenceAsset
 * @package app\assets\reference
 */
class ReferenceAsset extends AssetBundle
{
    public $sourcePath = 'app/media';

    public $js = [
        'js/reference/referenceProp.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}