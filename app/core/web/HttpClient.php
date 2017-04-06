<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 05.04.17
 * Time: 18:09
 */

namespace app\core\web;

use app\core\io;
use yii\helpers\StringHelper;
use yii\helpers\FileHelper;
use yii\web\Cookie;
use yii\web\CookieCollection;
use yii\web\HeaderCollection;

class HttpClient
{
    const HTTP_1_0 = "1.0";
    const HTTP_1_1 = "1.1";
    const HTTP_GET = "GET";
    const HTTP_POST = "POST";
    const HTTP_PUT = "PUT";
    const HTTP_HEAD = "HEAD";
    const HTTP_PATCH = "PATCH";

    const BUF_READ_LEN = 16384;
    const BUF_POST_LEN = 131072;

    protected $proxyHost;
    protected $proxyPort;
    protected $proxyUser;
    protected $proxyPassword;

    protected $responseHeaders;
    protected $responseCookies;
    protected $requestHeaders;
    protected $requestCookies;
    protected $result = '';
    protected $waitResponse = true;
    protected $redirect = true;
    protected $redirectMax = 5;
    protected $sslVerify = true;

    protected $status = 0;

    protected $redirectCount = 0;
    protected $compress = false;
    protected $version = self::HTTP_1_0;
    protected $requestCharset = '';

    protected $outputStream;
    protected $effectiveUrl;

    protected $resource;
    protected $socketTimeout = 30;
    protected $streamTimeout = 60;
    protected $error = [];

    public function __construct(array $options = [])
    {
        $this->requestHeaders = new HeaderCollection();
        $this->responseHeaders = new HeaderCollection();
        $this->requestCookies = new CookieCollection();
        $this->responseCookies = new CookieCollection();
    }

    public function download($url, $file_path)
    {
        $dir = StringHelper::dirname($file_path);
        FileHelper::createDirectory($dir);

        $file = new io\File($file_path);
        $handler = $file->open("w+");

        if ($handler !== false) {
            $this->setOutputStream($handler);
            $res = $this->query(self::HTTP_GET, $url);
            if ($res) {
                $res = $this->readBody();
            }

            $this->disconnect();
            fclose($handler);
            return $res;
        }

        return false;
    }

    /**
     * Sets the response output to the stream instead of the string result. Useful for large responses.
     * Note, the stream must be readable/writable to support a compressed response.
     * Note, in this mode the result string is empty.
     *
     * @param resource $handler File or stream handler.
     * @return void
     */
    public function setOutputStream($handler)
    {
        $this->outputStream = $handler;
    }

    public function query($method, $url, $entityBody = null)
    {
        $this->effectiveUrl = $url;
        if (is_array($entityBody)) {
            $entityBody = http_build_query($entityBody, "", "&");
        }

        $this->redirectCount = 0;

        while (true) {
            //Only absoluteURI is accepted
            //Location response-header field must be absoluteURI either
            $parsedUrl = new Uri($this->effectiveUrl);
            if ($parsedUrl->getHost() == '') {
                $this->error["URI"] = "Incorrect URI: " . $this->effectiveUrl;
                return false;
            }

            //just in case of serial queries
            $this->disconnect();

            if ($this->connect($parsedUrl) === false) {
                return false;
            }

            $this->sendRequest($method, $parsedUrl, $entityBody);
            if (!$this->waitResponse) {
                $this->disconnect();
                return true;
            }

            if (!$this->readHeaders()) {
                $this->disconnect();
                return false;
            }

            if ($this->redirect && ($location = $this->responseHeaders->get("Location")) !== null && $location <> '') {
                //we don't need a body on redirect
                $this->disconnect();

                if ($this->redirectCount < $this->redirectMax) {
                    $this->effectiveUrl = $location;
                    if ($this->status == 302 || $this->status == 303) {
                        $method = self::HTTP_GET;
                    }
                    $this->redirectCount++;
                } else {
                    $this->error["REDIRECT"] = "Maximum number of redirects (" . $this->redirectMax . ") has been reached at URL " . $url;
                    trigger_error($this->error["REDIRECT"], E_USER_WARNING);
                    return false;
                }
            } else {
                //the connection is still active to read the response body
                break;
            }
        }

        return true;
    }

    /**
     * @param $name
     * @param $value
     * @param bool $replace
     */
    public function setHeader($name, $value, $replace = true)
    {
        if ($replace == true || $this->requestHeaders->get($name) === null) {
            $this->requestHeaders->set($name, $value);
        }
    }

    /**
     * Sets Basic Authorization request header field.
     *
     * @param string $user Username.
     * @param string $pass Password.
     * @return void
     */
    public function setAuthorization($user, $pass)
    {
        $this->setHeader("Authorization", "Basic " . base64_encode($user . ":" . $pass));
    }

    /**
     * @param $method
     * @param Uri $url
     * @param null $entityBody
     */
    protected function sendRequest($method, Uri $url, $entityBody = null)
    {
        $this->status = 0;
        $this->result = '';
        $this->responseHeaders->removeAll();
        $this->responseCookies->removeAll();

        if ($this->proxyHost <> '') {
            $path = $url->getLocator();
            if ($this->proxyUser <> '') {
                $this->setHeader("Proxy-Authorization", "Basic " . base64_encode($this->proxyUser . ":" . $this->proxyPassword));
            }
        } else {
            $path = $url->getPathQuery();
        }

        $request = $method . " " . $path . " HTTP/" . $this->version . "\r\n";

        $this->setHeader("Host", $url->getHost());
        $this->setHeader("Connection", "close", false);
        $this->setHeader("Accept", "*/*", false);
        $this->setHeader("Accept-Language", "en", false);

        if (($user = $url->getUser()) <> '') {
            $this->setAuthorization($user, $url->getPass());
        }

        $cookies = $this->cookiesToString();
        if ($cookies <> '') {
            $this->setHeader("Cookie", $cookies);
        }

        if ($this->compress) {
            $this->setHeader("Accept-Encoding", "gzip");
        }

        if (!is_resource($entityBody)) {
            if ($method == self::HTTP_POST) {
                //special processing for POST requests
                if ($this->requestHeaders->get("Content-Type") === null) {
                    $contentType = "application/x-www-form-urlencoded";
                    if ($this->requestCharset <> '') {
                        $contentType .= "; charset=" . $this->requestCharset;
                    }
                    $this->setHeader("Content-Type", $contentType);
                }
            }

            if ($entityBody <> '' || $method == self::HTTP_POST) {
                //HTTP/1.0 requires Content-Length for POST
                if ($this->requestHeaders->get("Content-Length") === null) {
                    $this->setHeader("Content-Length", StringHelper::byteLength($entityBody));
                }
            }
        }

        $request .= $this->headersToString();
        $request .= "\r\n";

        $this->send($request);

        if (is_resource($entityBody)) {
            //PUT data can be a file resource
            while (!feof($entityBody)) {
                $this->send(fread($entityBody, self::BUF_POST_LEN));
            }
        } elseif ($entityBody <> '') {
            $this->send($entityBody);
        }
    }

    /**
     * @return bool
     */
    protected function readHeaders()
    {
        $headers = "";
        while (!feof($this->resource)) {
            $line = fgets($this->resource, self::BUF_READ_LEN);
            if ($line == "\r\n") {
                break;
            }

            if ($this->streamTimeout > 0) {
                $info = stream_get_meta_data($this->resource);
                if ($info['timed_out']) {
                    $this->error['STREAM_TIMEOUT'] = "Stream reading timeout of " . $this->streamTimeout . " second(s) has been reached";
                    return false;
                }
            }

            if ($line === false) {
                $this->error['STREAM_READING'] = "Stream reading error";
                return false;
            }
            $headers .= $line;
        }

        $this->parseHeaders($headers);

        return true;
    }

    protected function readBody()
    {
        if ($this->responseHeaders->get("Transfer-Encoding") == "chunked") {
            while (!feof($this->resource)) {
                $line = fgets($this->resource, self::BUF_READ_LEN);
                if ($line == "\r\n") {
                    continue;
                }
                if (($pos = strpos($line, ";")) !== false) {
                    $line = substr($line, 0, $pos);
                }

                $length = hexdec($line);
                while ($length > 0) {
                    $buf = $this->receive($length);
                    if ($this->streamTimeout > 0) {
                        $info = stream_get_meta_data($this->resource);
                        if ($info['timed_out']) {
                            $this->error['STREAM_TIMEOUT'] = "Stream reading timeout of " . $this->streamTimeout . " second(s) has been reached";
                            return false;
                        }
                    }
                    if ($buf === false) {
                        $this->error['STREAM_READING'] = "Stream reading error";
                        return false;
                    }
                    $length -= StringHelper::byteLength($buf);
                }
            }
        } else {
            while (!feof($this->resource)) {
                $buf = $this->receive();
                if ($this->streamTimeout > 0) {
                    $info = stream_get_meta_data($this->resource);
                    if ($info['timed_out']) {
                        $this->error['STREAM_TIMEOUT'] = "Stream reading timeout of " . $this->streamTimeout . " second(s) has been reached";
                        return false;
                    }
                }
                if ($buf === false) {
                    $this->error['STREAM_READING'] = "Stream reading error";
                    return false;
                }
            }
        }

        if ($this->responseHeaders->get("Content-Encoding") == "gzip") {
            $this->decompress();
        }

        return true;
    }

    protected function decompress()
    {
        if (is_resource($this->outputStream)) {
            $compressed = stream_get_contents($this->outputStream, -1, 10);
            $compressed = StringHelper::byteSubstr($compressed, 0, -8);
            if ($compressed <> '') {
                $uncompressed = gzinflate($compressed);

                rewind($this->outputStream);
                $len = fwrite($this->outputStream, $uncompressed);
                ftruncate($this->outputStream, $len);
            }
        } else {
            $compressed = StringHelper::byteSubstr($this->result, 0, -8);
            if ($compressed <> '') {
                $this->result = gzinflate($compressed);
            }
        }
    }

    /**
     * @return string
     */
    protected function cookiesToString()
    {
        $str = "";
        foreach ($this->requestCookies as $name => $value) {
            $str .= ($str == "" ? "" : "; ") . rawurlencode($name) . "=" . rawurlencode($value);
        }
        return $str;
    }

    protected function headersToString()
    {
        $str = "";
        foreach ($this->requestHeaders as $name => $header) {
            foreach ($header as $value) {
                $str .= $name . ": " . $value . "\r\n";
            }
        }
        return $str;
    }

    /**
     * Returns URL of the last redirect if request was redirected, or initial URL if request was not redirected.
     * @return string
     */
    public function getEffectiveUrl()
    {
        return $this->effectiveUrl;
    }

    /**
     * @param Uri $url
     * @return bool
     */
    protected function connect(Uri $url)
    {
        if ($this->proxyHost <> '') {
            $proto = "";
            $host = $this->proxyHost;
            $port = $this->proxyPort;
        } else {
            $proto = ($url->getScheme() == "https" ? "ssl://" : "");
            $host = $url->getHost();
            $url->setHost($host);
            $port = $url->getPort();
        }

        $context = $this->createContext();
        if ($context) {
            $res = stream_socket_client($proto . $host . ":" . $port, $errno, $errstr, $this->socketTimeout, STREAM_CLIENT_CONNECT, $context);
        } else {
            $res = stream_socket_client($proto . $host . ":" . $port, $errno, $errstr, $this->socketTimeout);
        }

        if (is_resource($res)) {
            $this->resource = $res;

            if ($this->streamTimeout > 0) {
                stream_set_timeout($this->resource, $this->streamTimeout);
            }

            return true;
        }

        if (intval($errno) > 0) {
            $this->error["CONNECTION"] = "[" . $errno . "] " . $errstr;
        } else {
            $this->error["SOCKET"] = "Socket connection error.";
        }

        return false;
    }

    /**
     * @return resource
     */
    protected function createContext()
    {
        $contextOptions = [];
        if ($this->sslVerify === false) {
            $contextOptions["ssl"]["verify_peer_name"] = false;
            $contextOptions["ssl"]["verify_peer"] = false;
            $contextOptions["ssl"]["allow_self_signed"] = true;
        }
        $context = stream_context_create($contextOptions);

        return $context;
    }

    /**
     * @param $data
     * @return int
     */
    protected function send($data)
    {
        return fwrite($this->resource, $data);
    }

    /**
     * @param null $bufLength
     * @return string
     */
    protected function receive($bufLength = null)
    {
        if ($bufLength === null) {
            $bufLength = self::BUF_READ_LEN;
        }

        $buf = stream_get_contents($this->resource, $bufLength);
        if ($buf !== false) {
            if (is_resource($this->outputStream)) {
                //we can write response directly to stream (file, etc.) to minimize memory usage
                fwrite($this->outputStream, $buf);
                fflush($this->outputStream);
            } else {
                $this->result .= $buf;
            }
        }

        return $buf;
    }

    protected function disconnect()
    {
        if ($this->resource) {
            fclose($this->resource);
            $this->resource = null;
        }
    }

    protected function parseHeaders($headers)
    {
        foreach (explode("\n", $headers) as $k => $header) {
            if ($k == 0) {
                if (preg_match('#HTTP\S+ (\d+)#', $header, $find)) {
                    $this->status = intval($find[1]);
                }
            } elseif (strpos($header, ':') !== false) {
                list($headerName, $headerValue) = explode(':', $header, 2);
                if (strtolower($headerName) == 'set-cookie') {
                    if (($pos = strpos($headerValue, ';')) !== false && $pos > 0) {
                        $cookie = trim(substr($headerValue, 0, $pos));
                    } else {
                        $cookie = trim($headerValue);
                    }
                    $arCookie = explode('=', $cookie, 2);

                    $this->responseCookies->add(new Cookie(['name' => $arCookie[0], 'value' => $arCookie[1]]));
                }
                $this->responseHeaders->add($headerName, trim($headerValue));
            }
        }
    }
}