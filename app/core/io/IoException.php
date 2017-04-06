<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.04.17
 * Time: 19:10
 */

namespace app\core\io;

use Exception;

class IoException extends \Exception
{
    /**
     * @var string
     */
    protected $path;

    /**
     * IoException constructor.
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message = "", $path = "", \Exception $previous = null)
    {
        parent::__construct($message, 120, $previous);
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }
}

class InvalidPathException extends IoException
{
    public function __construct($path, \Exception $previous = null)
    {
        $message = sprintf("Path '%s' is invalid.", $path);
        parent::__construct($message, $path, $previous);
    }
}

class FileOpenException extends IoException
{
    public function __construct($path, \Exception $previous = null)
    {
        $message = sprintf("Cannot open the file '%s'.", $path);
        parent::__construct($message, $path, $previous);
    }
}