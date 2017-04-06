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

    public static function checkDirPath($path)
    {
        $path = str_replace(["\\", "//"], "/", $path);

        //remove file name
        if (substr($path, -1) != "/") {
            $p = strrpos($path, "/");
            $path = substr($path, 0, $p);
        }

        $path = rtrim($path, "/");

        if ($path == "") {
            //current folder always exists
            return true;
        }

        if (!file_exists($path)) {
            return self::createDirectory($path);
        }

        return is_dir($path);
    }

    public static function getName($path)
    {
        $path = self::normalizePath($path);

        $p = strrpos($path, DIRECTORY_SEPARATOR);
        if ($p !== false)
            return substr($path, $p + 1);

        return $path;
    }

    public static function getDirectory($path)
    {
        return substr($path, 0, -strlen(self::getName($path)) - 1);
    }
}