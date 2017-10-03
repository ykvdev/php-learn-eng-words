<?php

namespace Lib\Router;

/**
 * Class UrlGenerator
 * @package Lib\Router
 */
class UrlGenerator
{
    /** @var RouteCollection */
    private $routes;

    /**
     * @param RouteCollection $routes
     */
    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @param string $name
     * @param array $params
     * @param bool $injectLang
     * @return string
     */
    public function fromRoute($name, array $params = [], $injectLang = false)
    {
        if (!$this->routes->has($name)) {
            return $name;
        }

        $route = $this->routes->get($name);
        $this->applyNotRequiredParams($route, $params);
        $this->applyRequiredParams($route, $params);
        return $route['route'];
    }

    public function fromUrl($url)
    {
        return $url;
    }

    /**
     * @param array $route
     * @param array $params
     */
    private function applyRequiredParams(&$route, array $params)
    {
        $required = isset($route['required_params']) ? $route['required_params'] : [];
        foreach ($required as $name => $value) {
            if(isset($params[$name])){
                $route['route'] = str_replace(":{$name}", $params[$name], $route['route']);
            } elseif(isset($route['defaults'][$name])) {
                $route['route'] = str_replace(":{$name}", $route['defaults'][$name], $route['route']);
            } else {
                trigger_error("Not found required route params '{$name}'");
            }
        }
    }

    /**
     * @param string $route
     * @param array $params
     */
    private function applyNotRequiredParams(&$route, array $params)
    {
        if(isset($route['not_required_params'])){
            $params = array_merge($route['not_required_params'], $params);
        }
        foreach ($params as $name => $value) {
            $value = empty($value) ? '' : $value . '/';
            $route['route'] = str_replace("::{$name}/", $value, $route['route']);
        }
    }

    /**
     * @param string $pattern
     * @param bool $injectLang
     */
    private function injectLang(&$pattern, $injectLang)
    {
        if(config()['translator']['auto_inject_to_url'] || (bool)$injectLang){
            $pattern = '/' . translator()->getCurrentLang() . $pattern;
        }
    }
}