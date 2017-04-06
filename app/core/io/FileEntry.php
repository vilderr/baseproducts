<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.04.17
 * Time: 18:53
 */

namespace app\core\io;


class FileEntry extends FileSystemEntry
{
    public function __construct($path)
    {
        parent::__construct($path);
    }
}