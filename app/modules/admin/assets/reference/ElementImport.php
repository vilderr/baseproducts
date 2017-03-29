<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 18.03.17
 * Time: 22:09
 */

namespace app\modules\admin\assets\reference;

use yii\web\AssetBundle;

/**
 * Class ElementImport
 * @package app\modules\admin\assets\reference
 */
class ElementImport extends AssetBundle
{
    public $sourcePath = 'app/modules/admin/media';

    public $js = [
        'js/reference/import.js',
    ];
}