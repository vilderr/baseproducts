<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 18.03.17
 * Time: 22:21
 */

namespace app\modules\admin\assets\reference;


use yii\web\AssetBundle;

class ReferenceAsset extends AssetBundle
{
    public $sourcePath = 'app/modules/admin/media';

    public $js = [
        'js/referenceProp.js',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END,
    ];
}