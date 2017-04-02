<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 27.03.17
 * Time: 10:07
 */

namespace app\components\helpers;

/**
 * Class FileHelper
 * @package app\components\helpers
 */
class FileHelper extends \yii\helpers\FileHelper
{
    /**
     * @param $path
     * @return mixed
     */
    public static function getFileName($path)
    {
        $arPath = explode('/', $path);
        if (count($arPath) > 0) {
            return array_pop($arPath);
        }

        return $path;
    }
}