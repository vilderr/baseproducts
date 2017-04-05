<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.04.17
 * Time: 14:24
 */

namespace app\models\file;

/**
 * Class File
 * @package app\models\file
 */
class File extends BaseFile
{
    public static function makeArray($path, $external_id = '')
    {
        $arFile = [];

        // for exists files
        if (intval($path) > 0) {

        }

        $path = preg_replace("#(?<!:)[\\\\\\/]+#", "/", $path);

        // for agressive compounding
        if (strlen($path) == 0 || $path == "/") {
            return NULL;
        }

        if (preg_match("#^php://filter#i", $path)) {
            return NULL;
        }

        if (preg_match("#^(http[s]?)://#", $path)) {

            $temp_path = '';
            $urlComponents = parse_url($path);
            if ($urlComponents && strlen($urlComponents['path']) > 0) {
                $temp_path = File::getTempName(basename($urlComponents['path']));
            } else {
                $temp_path = File::getTempName(basename($path));
            }

        } elseif (preg_match("#^(ftp[s]?|php)://#", $path)) {

        } else {

        }
    }

    public static function getTempName($file_name = '')
    {
        if(($pos = strpos($file_name, "?")) !== false)
        {
            $file_name = substr($file_name, 0, $pos);
        }

        return CTempFile::GetFileName($file_name);
    }
}