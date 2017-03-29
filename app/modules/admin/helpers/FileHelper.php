<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 18.03.17
 * Time: 19:04
 */

namespace app\modules\admin\helpers;

/**
 * Class FileHelper
 * @package app\modules\admin\helpers
 */
class FileHelper extends \yii\helpers\FileHelper
{
    /**
     * @param $path
     * @return mixed|string
     */
    public static function getFileName($path)
    {
        $path = rtrim($path, '\0.\\/+ ');
        $path = str_replace('\\', '/', $path);
        $path = rtrim($path, '/');

        $p = strrpos($path, '/');
        if ($p !== false)
            return substr($path, $p + 1);

        return $path;
    }
}