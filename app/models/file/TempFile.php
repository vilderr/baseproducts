<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.04.17
 * Time: 15:01
 */

namespace app\models\file;


class TempFile
{
    private static $arFiles = [];

    public static function getAbsoluteRoot()
    {

    }

    public static function getFileName($file_name = '')
    {
        $dir_name = self::getAbsoluteRoot();
    }


}