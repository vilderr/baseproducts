<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.04.17
 * Time: 18:53
 */

namespace app\core\io;

use yii\helpers\FileHelper;

abstract class FileSystemEntry
{
    protected $path;
    protected $originalPath;

    public function __construct($path)
    {
        if ($path == '')
            throw new InvalidPathException($path);

        $this->originalPath = $path;
        $this->path = FileHelper::normalizePath($path);

        if ($this->path == '')
            throw new InvalidPathException($path);
    }
}