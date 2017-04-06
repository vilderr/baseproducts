<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.04.17
 * Time: 14:24
 */

namespace app\models\file;

use app\components\helpers\FileHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\httpclient\Client;
use app\core\web\HttpClient;

/**
 * Class File
 * @package app\models\file
 */
class File extends BaseFile
{
    /**
     * @param $path
     * @return bool
     */
    public static function checkIsImage($path)
    {
        if ($path == '') {
            return false;
        }

        if (preg_match('#^php://filter#i', $path)) {
            return false;
        }

        $file_type = self::getFileType($path);

        if ($file_type == 'IMAGE')
            return true;

        return false;
    }

    /**
     * @param $arFile
     * @param $save_path
     * @param bool $bSkipExt
     * @return bool|mixed
     */
    public static function saveFile($arFile, $save_path = 'reference', $bSkipExt = false)
    {
        if (!$arFile) {
            return false;
        }

        $strFileName = self::getFileName($arFile['name']);

        $arFile['original_name'] = $strFileName;
        $strFileName = self::transformName($strFileName, $bSkipExt);

        if ($arFile['type'] == 'image/pjpeg' || $arFile['type'] == 'image/jpg') {
            $arFile['type'] = 'image/jpeg';
        }

        $upload_dir = 'upload';
        $strFileExt = ($bSkipExt == true || ($ext = pathinfo($strFileName, PATHINFO_EXTENSION)) == '' ? '' : '.' . $ext);
        while (true) {
            if (substr($save_path, -1, 1) <> '/')
                $save_path .= '/' . substr($strFileName, 0, 3);
            else
                $save_path .= substr($strFileName, 0, 3) . '/';

            if (!self::fileExists(Yii::$app->getBasePath() . '/' . $upload_dir . '/' . $save_path . '/' . $strFileName))
                break;

            $strFileName = md5(uniqid('', true)) . $strFileExt;
        }

        $arFile['subdir'] = $save_path;
        $arFile['name'] = $strFileName;
        $strDirName = Yii::$app->getBasePath() . '/' . $upload_dir . '/' . $save_path . '/';
        $strDbFileNameX = $strDirName . $strFileName;

        FileHelper::createDirectory($strDirName);

        if (self::is_set($arFile, 'content')) {
            $f = fopen($strDbFileNameX, 'ab');
            if (!$f)
                return false;
            if (fwrite($f, $arFile['content']) === false)
                return false;
            fclose($f);
        } elseif (
            !copy($arFile['tmp_name'], $strDbFileNameX)
            && !move_uploaded_file($arFile['tmp_name'], $strDbFileNameX)
        ) {
            return false;
        }

        @chmod($strDbFileNameX, 0644);

        if ($arFile['type'] == '' || !is_string($arFile['type'])) {
            $arFile['type'] = 'application/octet-stream';
        }

        $ID = self::doInsert([
            'name'          => $arFile['name'],
            'original_name' => $arFile['original_name'],
            'size'          => $arFile['size'],
            'type'          => $arFile['type'],
            'subdir'        => $arFile['subdir'],
            'external_id'   => isset($arFile['external_id']) ? $arFile['external_id'] : md5(mt_rand()),
        ]);

        return $ID;
    }

    /**
     * @param $arFields
     * @return mixed
     */
    protected static function doInsert(array $arFields)
    {
        $file = new static();
        $file->attributes = $arFields;

        $file->save(false);

        return $file->id;
    }


    /**
     * @param $a
     * @param bool $k
     * @return bool
     */
    public static function is_set(&$a, $k = false)
    {
        if ($k === false)
            return isset($a);

        if (is_array($a))
            return array_key_exists($k, $a);

        return false;
    }

    /**
     * @param $name
     * @param bool $bSkipExt
     * @return mixed|string
     */
    protected static function transformName($name, $bSkipExt = false)
    {
        $fileName = self::getFileName($name);
        if ($bSkipExt == false && strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) == 'jpe') {
            $fileName = substr($fileName, 0, -4) . '.jpg';
        }

        $fileName = self::removeScriptExtension($fileName);

        $fileName = md5(uniqid('', true)) . ($bSkipExt == true || ($ext = pathinfo($fileName, PATHINFO_EXTENSION)) == '' ? '' : '.' . $ext);

        return $fileName;
    }

    /**
     * @param $check_name
     * @return string
     */
    public static function removeScriptExtension($check_name)
    {
        $arExtensions = explode(',', 'php,php3,php4,php5,php6,phtml,pl,asp,aspx,cgi,dll,exe,ico,shtm,shtml,fcg,fcgi,fpl,asmx,pht,py,psp,var');
        $name = self::getFileName($check_name);

        $arParts = explode('.', $name);
        foreach ($arParts as $i => $part) {
            if ($i > 0 && ArrayHelper::isIn(strtolower(rtrim($part, '\0.\\/+ ')), $arExtensions))
                unset($arParts[$i]);
        }

        $path = substr(rtrim($check_name, '\0.\\/+ '), 0, -strlen($name));

        return $path . implode('.', $arParts);
    }

    /**
     * @param $path
     * @param bool $mimetype
     * @param string $external_id
     * @return array|null
     */
    public static function makeArray($path, $mimetype = false, $external_id = '')
    {
        $arFile = [];

        // for exists files
        if (intval($path) > 0) {
            if (($res = parent::findOne($path)) !== null) {
                $ar = $res->attributes;

                $arFile['name'] = (strlen($ar['original_name']) > 0 ? $ar['original_name'] : $ar['NAME']);
                $arFile['size'] = $ar['size'];
                $arFile['type'] = $ar['type'];
                $arFile['tmp_name'] = Yii::$app->getBasePath() . '/upload/' . $ar['subdir'] . '/' . $ar['name'];
                $arFile['external_id'] = $external_id != '' ? $external_id : $ar['external_id'];

                return $arFile;

            } else {
                return NULL;
            }

        } elseif (!self::checkIsImage($path)) {
            return NULL;
        }

        $path = preg_replace('#(?<!:)[\\\\\\/]+#', '/', $path);

        // for agressive compounding
        if (strlen($path) == 0 || $path == '/') {
            return NULL;
        }

        if (preg_match('#^php://filter#i', $path)) {
            return NULL;
        }

        if (preg_match('#^(http[s]?)://#', $path)) {
            $temp_path = '';
            $urlComponents = parse_url($path);
            if ($urlComponents && strlen($urlComponents['path']) > 0) {
                $temp_path = File::getTempName(StringHelper::basename($urlComponents['path']));
            } else {
                $temp_path = File::getTempName(StringHelper::basename($path));
            }


            /*
            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl($path)
                ->send();
            if ($response->isOk) {
                $dir = StringHelper::dirname($temp_path);
                FileHelper::createDirectory($dir);

                $handler = fopen($temp_path, 'w+b');
                fwrite($handler, $response->content);
                fflush($handler);
                fclose($handler);

                $arFile = self::makeArray($temp_path);
            } else {
                return NULL;
            }
            */


            $ob = new HttpClient();
            if ($ob->Download($path, $temp_path)) {
                $arFile = static::makeArray($temp_path);
            }
            else {
                return NULL;
            }


        } elseif (preg_match('#^(ftp[s]?|php)://#', $path)) {

            if ($fp = fopen($path, 'rb')) {
                $content = '';
                while (!feof($fp))
                    $content .= fgets($fp, 4096);

                if (strlen($content) > 0) {
                    $temp_path = self::getTempName(StringHelper::basename($path));
                    if (self::rewriteFile($temp_path, $content))
                        $arFile = self::makeArray($temp_path);
                }

                fclose($fp);
            }

        } else {
            if (!file_exists($path)) {
                if (file_exists(Yii::$app->getBasePath() . $path))
                    $path = Yii::$app->getBasePath() . $path;
                else
                    return NULL;
            }

            if (is_dir($path))
                return NULL;

            $arFile['name'] = StringHelper::basename($path);
            $arFile['size'] = filesize($path);
            $arFile['tmp_name'] = $path;
            $arFile['type'] = $mimetype;
            if (strlen($arFile['type']) <= 0)
                $arFile['type'] = FileHelper::getMimeType($path);
        }

        if (strlen($arFile['type']) <= 0)
            $arFile['type'] = 'unknown';

        if (!isset($arFile['external_id']) && ($external_id != '')) {
            $arFile['external_id'] = $external_id;
        }

        return $arFile;
    }

    /**
     * @param string $file_name
     * @return string
     */
    public static function getTempName($file_name = '')
    {
        if (($pos = strpos($file_name, '?')) !== false) {
            $file_name = substr($file_name, 0, $pos);
        }

        return TempFile::getFileName($file_name);
    }

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

    /**
     * @param $path
     * @return bool
     */
    public static function fileExists($path)
    {
        return file_exists($path) && is_file($path);
    }

    /**
     * @param $abs_path
     * @param $strContent
     * @return bool
     */
    public static function rewriteFile($abs_path, $strContent)
    {
        $dir = StringHelper::dirname($abs_path);
        FileHelper::createDirectory($dir);

        if (file_exists($abs_path) && !is_writable($abs_path))
            @chmod($abs_path, 0644);
        $fd = fopen($abs_path, 'wb');
        if (!fwrite($fd, $strContent)) return false;
        @chmod($abs_path, 0644);
        fclose($fd);
        return true;
    }

    /**
     * @param $path
     * @return string
     */
    public static function getFileType($path)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'bmp':
            case 'png':
                $type = 'IMAGE';
                break;
            case 'swf':
                $type = 'FLASH';
                break;
            case 'html':
            case 'htm':
            case 'asp':
            case 'aspx':
            case 'phtml':
            case 'php':
            case 'php3':
            case 'php4':
            case 'php5':
            case 'php6':
            case 'shtml':
            case 'sql':
            case 'txt':
            case 'inc':
            case 'js':
            case 'vbs':
            case 'tpl':
            case 'css':
            case 'shtm':
                $type = 'SOURCE';
                break;
            default:
                $type = 'UNKNOWN';
        }

        return $type;
    }
}