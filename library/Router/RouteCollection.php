<?php

namespace Lib\Router;

use Traversable;

/**
 * Class RouteCollection
 * @package Lib\Router
 */
class RouteCollection implements \Countable, \IteratorAggregate
{
    private $routes = [];
    private $paramPatternsMap = [
        'not_required_params' => '/::(.+?)\//',
        'required_params' => '/:(.+?)\//',
    ];

    /**
     * @param array $routes
     */
    public function __construct(array $routes = [])
    {
        if (!empty($routes)) {
            foreach ($routes as $name => $route) {
                $this->add($name, $route);
            }
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->routes);
    }

    public function add($name, $route)
    {

        $this->routes[$name] = array_merge($route, $this->extractParams($route['route']));
        return $this;
    }

    /**
     * @param string $name
     * @return null|mixed
     */
    public function get($name)
    {
        return $this->has($name) ? $this->routes[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->routes[$name]);
    }

    /**
     * @param array|string $routes
     */
    public function remove($routes)
    {
        foreach ((array)$routes as $name) {
            unset($this->routes[$name]);
        }
    }

    private function extractParams($route)
    {
        $result = [];
        foreach ($this->paramPatternsMap as $key => $pattern) {
            if (preg_match_all($pattern, $route, $matches)) {
//                $result[$key] = array_fill_keys($matches[1], '');
                $result[$key] = array_fill_keys(array_filter($matches[1], function($item){
                    return substr($item, 0, 1) !== ':';
                }), null);
            }
        }
        return $result;
    }
}