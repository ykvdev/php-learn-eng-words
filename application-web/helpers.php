<?php

/**
 * @return array
 */
function config()
{
    return \Lib\Resource\Configs::getInstance()->getConfigs();
}

/**
 * @return \Lib\Mysql
 */
function mysqldb()
{
    return \Lib\Resource\Mysql::getInstance();
}

/**
 * @return \Lib\FlashMessenger
 */
function fm()
{
    return \Lib\Resource\FlashMessenger::getInstance();
}

function redirect($route, array $params = [], $injectLang = false)
{
    if (strpos($route, 'http')) {
        $url = uri()->fromUrl($route);
    } else {
        $url = uri($route, $params, $injectLang);
    }
    header('Location: ' . $url);
    exit;
}

function redirectToReferer()
{
    $referer = $_SERVER['HTTP_REFERER'] ?: '/';
    $referer = str_replace(["http://{$_SERVER['HTTP_HOST']}", "https://{$_SERVER['HTTP_HOST']}"], '', $referer);
    redirect($referer);
}

/**
 * Fast function for get url with lang
 * @param string|null $route - i.e. home
 * @param array $params
 * @param bool $injectLang
 * @return string|\Lib\Router\UrlGenerator - i.e. /en/page/etc/
 */
function uri($route = null, array $params = [], $injectLang = false)
{
    $generator = router()->getUrlGenerator();
    if (null == $route) {
        return $generator;
    }
    return $generator->fromRoute($route, $params, $injectLang);
}

/**
 * @param string|null $route
 * @param array $params
 * @param bool $injectLang
 * @return string
 */
function url($route = null, array $params = [], $injectLang = false) {
    return currentUrl() . uri($route, $params, $injectLang);
}

/**
 * @param string $viewAlias
 * @param array|object $vars
 * @return \Lib\ViewRenderer
 */
function view($viewAlias, $vars = [])
{
    return (new \Lib\ViewRenderer(config()['view_renderer']))
        ->setViewAlias($viewAlias)->setVars($vars);
}

/**
 * @param string|array $value
 * @return string|array
 */
function escape($value)
{
    if (is_string($value)) {
        return htmlspecialchars($value);
    } elseif (is_array($value)) {
        foreach ($value as $k => $v) {
            $value[$k] = escape($v);
        }

        return $value;
    } else {
        return $value;
    }
}

/**
 * @return string
 */
function pageJs()
{
    /** @var \Lib\PageJs $pageJs */
    $pageJs = \Lib\Resource\PageJs::getInstance();
    return $pageJs->requireJs();
}

/**
 * @return string
 */
function currentUrl()
{
    return (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'];
}

/**
 * @return \Lib\Router
 */
function router()
{
    return \Lib\Resource\Router::getInstance();
}