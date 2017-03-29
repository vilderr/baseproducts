<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 08.03.17
 * Time: 12:15
 */

namespace app\assets;


use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@vendor/fortawesome/font-awesome';
    public $css = [ 'css/font-awesome.css',];
}