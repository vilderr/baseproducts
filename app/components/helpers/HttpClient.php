<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.04.17
 * Time: 17:01
 */

namespace app\components\helpers;

use yii\base\Model;

class HttpClient extends Model
{
    public function download($url, $filePath)
    {
        $dir = FileHelper::getDirectory($filePath);
        FileHelper::createDirectory($dir);
    }
}