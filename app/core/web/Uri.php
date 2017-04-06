<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.04.17
 * Time: 19:52
 */

namespace app\core\web;

/**
 * Class Uri
 * @package app\core\web
 */
class Uri
{
    protected $scheme;
    protected $host;
    protected $port;
    protected $user;
    protected $pass;
    protected $path;
    protected $query;
    protected $fragment;

    /**
     * Uri constructor.
     * @param $url
     */
    public function __construct($url)
    {
        if (strpos($url, "/") === 0) {
            $url = "/" . ltrim($url, "/");
        }

        $parsedUrl = parse_url($url);

        if ($parsedUrl !== false) {
            $this->scheme = (isset($parsedUrl["scheme"]) ? strtolower($parsedUrl["scheme"]) : "http");
            $this->host = $parsedUrl["host"];
            if (isset($parsedUrl["port"])) {
                $this->port = $parsedUrl["port"];
            } else {
                $this->port = ($this->scheme == "https" ? 443 : 80);
            }
            if (isset($parsedUrl["user"]))
                $this->user = $parsedUrl["user"];
            if (isset($parsedUrl["pass"]))
                $this->pass = $parsedUrl["pass"];
            $this->path = ((isset($parsedUrl["path"]) ? $parsedUrl["path"] : "/"));
            if (isset($parsedUrl["query"]))
                $this->query = $parsedUrl["query"];
            if (isset($parsedUrl["fragment"]))
                $this->fragment = $parsedUrl["fragment"];
        }
    }

    /**
     * Return the URI without a fragment.
     * @return string
     */
    public function getLocator()
    {
        $url = "";
        if ($this->host <> '') {
            $url .= $this->scheme . "://" . $this->host;

            if (($this->scheme == "http" && $this->port <> 80) || ($this->scheme == "https" && $this->port <> 443)) {
                $url .= ":" . $this->port;
            }
        }

        $url .= $this->getPathQuery();
        return $url;
    }

    /**
     * Returns the host.
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Returns the scheme.
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Returns the port number.
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Returns the path with the query.
     * @return string
     */
    public function getPathQuery()
    {
        $pathQuery = $this->path;
        if($this->query <> "")
        {
            $pathQuery .= '?'.$this->query;
        }
        return $pathQuery;
    }

    /**
     * Returns the user.
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Returns the password.
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * Sets the host
     * @param string $host Host name.
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }
}