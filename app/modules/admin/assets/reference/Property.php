<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 16.03.17
 * Time: 21:05
 */

namespace app\modules\admin\assets\reference;

use yii\web\AssetBundle;

/**
 * Class Property
 * @package app\modules\admin\assets\reference
 */
class Property extends AssetBundle
{
    public $sourcePath = 'app/modules/admin/media';

    public $js = [
        'js/reference/property.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}