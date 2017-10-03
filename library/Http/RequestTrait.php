<?php namespace Lib\Http;

/**
 * Class RequestTrait
 * @package Lib\Http
 */
trait RequestTrait
{

    /**
     * @param $name
     * @return bool
     */
    protected function hasQueryParam($name)
    {
        return array_key_exists($name, $_GET);
    }

    /**
     * @param null|string $key
     * @param null|mixed $default
     * @return mixed
     */
    protected function getQueryParams($key = null, $default = null)
    {
        if (null == $key) {
            array_walk_recursive($_GET, function (&$val) {
                $val = is_string($val) ? trim($val) : $val;
            });
            return $_GET;
        }

        $val = isset($_GET[$key]) && !empty($_GET[$key]) ? $_GET[$key] : $default;
        $val = is_string($val) ? trim($val) : $val;

        return $val;
    }

    /**
     * @param null|string $key
     * @param null|mixed $default
     * @return mixed
     */
    protected function getPostParams($key = null, $default = null)
    {
        if (null == $key) {
            array_walk_recursive($_POST, function (&$val) {
                $val = is_string($val) ? trim($val) : $val;
            });
            return $_POST;
        }

        $val = isset($_POST[$key]) ? $_POST[$key] : $default;
        $val = is_string($val) ? trim($val) : $val;

        return $val;
    }

    /**
     * @param null|string $key
     * @param null|mixed $default
     * @return mixed
     */
    protected function getFilesParams($key = null, $default = null)
    {
        if (null == $key) {
            return $_FILES;
        }
        return isset($_FILES[$key]) && !empty($_FILES[$key]) ? $_FILES[$key] : $default;
    }

    /**
     * @param null|string $key
     * @param mixed $default
     * @return null|string
     */
    protected function getCookieParams($key = null, $default = null)
    {
        if (null == $key) {
            array_walk_recursive($_COOKIE, function (&$val) {
                $val = is_string($val) ? trim($val) : $val;
            });
            return $_COOKIE;
        }
        $val = isset($_COOKIE[$key]) && !empty($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
        $val = is_string($val) ? trim($val) : $val;
        return $val;
    }

    /**
     * @return array
     */
    protected function getAllParams()
    {
        return array_merge($this->getQueryParams(), $this->getPostParams());
    }

    /**
     * @return bool
     */
    protected function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Alias for isAjaxRequest method
     *
     * @return bool
     */
    protected function isXMLHttpRequest()
    {
        return $this->isAjaxRequest();
    }

    /**
     * @return bool
     */
    protected function isPostRequest()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    protected function isGetRequest()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }
}