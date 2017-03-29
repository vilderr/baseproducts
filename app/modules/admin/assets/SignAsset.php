<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 15:37
 */

namespace app\modules\admin\assets;

use yii\helpers\Html;
use yii\web\AssetBundle;

class SignAsset extends AssetBundle
{
    public $sourcePath = 'app/modules/admin/media';

    public $css = [
        'css/sign.css',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}