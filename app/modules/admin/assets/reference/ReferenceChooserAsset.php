<?php

namespace app\modules\admin\assets\reference;

use yii\web\AssetBundle;
/**
 * Asset bundle for reference chooser javascript files.
 *
 * @author VILDERR
 */
class ReferenceChooserAsset extends AssetBundle {

    public $sourcePath = 'app/modules/admin/media';
    public $js         = [
        'js/reference/reference.chooser.js'
    ];
    public $depends    = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
