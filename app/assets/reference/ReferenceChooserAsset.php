<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 22.03.17
 * Time: 0:12
 */

namespace app\assets\reference;

use yii\web\AssetBundle;

/**
 * Class ReferenceChooserAsset
 * @package app\assets\reference
 */
class ReferenceChooserAsset extends AssetBundle {

    public $sourcePath = 'app/media';
    public $js         = [
        'js/reference/reference.chooser.js'
    ];
    public $depends    = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}