<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.04.17
 * Time: 18:08
 */

namespace app\core\io;

class File extends FileEntry
{
    /** @var resource */
    protected $filePointer;

    /**
     * File constructor.
     * @param $path
     */
    public function __construct($path)
    {
        parent::__construct($path);
    }

    /**
     * @param $mode
     * @return resource
     * @throws FileOpenException
     */
    public function open($mode)
    {
        $this->filePointer = fopen($this->path, $mode . 'b');
        if (!$this->filePointer) {
            throw new FileOpenException($this->originalPath);
        }

        return $this->filePointer;
    }
}