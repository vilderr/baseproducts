<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.03.17
 * Time: 15:10
 */

namespace app\modules\admin\assets;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $sourcePath = 'app/modules/admin/media';
    public $css = [
        'css/admin.css',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_END,
    ];
}