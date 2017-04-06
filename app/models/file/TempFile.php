<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.04.17
 * Time: 15:01
 */

namespace app\models\file;

use Yii;
use yii\base\Model;

class TempFile extends Model
{
    private static $arFiles = [];

    /**
     * @return string
     */
    public static function getAbsoluteRoot()
    {
        return rtrim(Yii::$app->getBasePath(), '/') . '/upload/tmp';
    }

    /**
     * @param string $file_name
     * @return string
     */
    public static function getFileName($file_name = '')
    {
        $dir_name = self::getAbsoluteRoot();
        $i = 0;

        while (true) {
            $i++;

            if ($file_name == '/')
                $dir_add = md5(mt_rand());
            elseif ($i < 25)
                $dir_add = substr(md5(mt_rand()), 0, 3);
            else
                $dir_add = md5(mt_rand());

            $temp_path = $dir_name . '/' . $dir_add . '/' . $file_name;

            if (!file_exists($temp_path)) {
                if (empty(self::$arFiles))
                    register_shutdown_function([self::className(), 'cleanup']);

                self::$arFiles[$temp_path] = $dir_name . '/' . $dir_add;

                return $temp_path;
            }
        }
    }

    /**
     * php shutdown cleanup temppath
     */
    public static function cleanup()
    {
        foreach (self::$arFiles as $temp_path => $temp_dir) {
            if (file_exists($temp_path)) {
                if (is_file($temp_path)) {
                    unlink($temp_path);
                    @rmdir($temp_dir);
                } elseif (
                    substr($temp_path, -1) == '/'
                    && is_dir($temp_path)
                ) {
                    TempFile::_absolute_path_recursive_delete($temp_path);
                }
            }
        }
    }

    /**
     * @param $path
     * @return bool
     */
    private static function _absolute_path_recursive_delete($path)
    {
        if (strlen($path) == 0 || $path == '/')
            return false;

        $f = true;
        if (is_file($path) || is_link($path)) {
            if (@unlink($path))
                return true;
            return false;
        } elseif (is_dir($path)) {
            if ($handle = opendir($path)) {
                while (($file = readdir($handle)) !== false) {
                    if ($file == '.' || $file == '..')
                        continue;

                    if (!TempFile::_absolute_path_recursive_delete($path . '/' . $file))
                        $f = false;
                }
                closedir($handle);
            }
            if (!@rmdir($path))
                return false;
            return $f;
        }
        return false;
    }
}