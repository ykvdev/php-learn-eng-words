<?php

namespace Lib;

use Lib\Router\RouteCollection;
use Lib\Router\UrlGenerator;

class Router {
    /** @var RouteCollection  */
    private $routeCollection;

    /** @var string */
    private $controllersNs;

    private $availableLangs = [];

    private $uri;

    private $routeName;

    private $routeData = [];

    private $urlGenerator;

    public function __construct(RouteCollection $routeCollection, $controllersNs, $availableLangs = []) {
        $this->routeCollection = $routeCollection;
        $this->controllersNs = $controllersNs;
        $this->availableLangs = $availableLangs;
        $this->urlGenerator = new UrlGenerator($routeCollection);
    }

    public function getUrlGenerator()
    {
        return $this->urlGenerator;
    }

    public function setUri($uri) {
        $this->uri = $uri;

        return $this;
    }


    public function getUri()
    {
        return $this->uri;
    }

    public function parse() {
        $this->cleanUri()
            ->cutLang();

        foreach($this->routeCollection as $name => $route) {
            if ($this->parseSimpleRoute($name, $route) || $this->parseParametrizedRoute($name, $route)) {
                $this->initGetParams();
                break;
            }
        }

        if(!$this->routeData) {
            $this->routeName = 'not-found';
            $this->routeData = $this->routeCollection->get($this->routeName);
        }

        return $this;
    }

    public function getControllerName() {
        $controller = $this->routeData['defaults']['controller'];
        $controller = str_replace("-", " ", $controller);
        $controller = ucwords($controller);
        $controller = str_replace(" ", "", $controller);
        return $controller;
    }

    public function getActionName() {
        $action = $this->routeData['defaults']['action'];
        $action = str_replace("-", " ", $action);
        $action = ucwords($action);
        $action = str_replace(" ", "", $action);
        return lcfirst($action)."Action";
    }

    public function getActionClassName() {
        $controllerName = $this->getControllerName();
        $actionName = ucfirst($this->getActionName());
        return $this->controllersNs . '\\' . $controllerName . '\\' . $actionName;
    }

    public function getRouteName() {
        return $this->routeName;
    }

    public function isRoute($name){
        return $this->getRouteName() === $name;
    }

    public function getRoute($name = null)
    {
        $name = $name ? : $this->getRouteName();
        return $this->routeCollection->get($name);
    }

    public function getViewAlias() {
        $controller = strtolower($this->routeData['defaults']['controller']);
        $action = strtolower($this->routeData['defaults']['action']);
        return "{$controller}/{$action}";
    }

    public function isUsersOnlyAccess() {
        return isset($this->routeData['access']) && $this->routeData['access'] == 'users';
    }

    public function isGuestsOnlyAccess() {
        return isset($this->routeData['access']) && $this->routeData['access'] == 'guests';
    }

    private function cleanUri() {
        $this->uri = $this->uri == '/' ? '/' : parse_url($this->uri)['path'];

        return $this;
    }

    private function cutLang() {
        if(!$this->availableLangs) {
            return;
        }

        $uriParts = explode('/', $this->uri);
        if(in_array($uriParts[1], $this->availableLangs)) {
            $this->uri = str_replace("/{$uriParts[1]}", '', $this->uri);
        }

        return $this;
    }

    private function parseSimpleRoute($name, array $route) {
        $pattern = $route['route'];
        if(strstr($pattern, ':') === false && $this->uri == $pattern) {
            $this->routeName = $name;
            $this->routeData = $route;
            return true;
        } else {
            return false;
        }
    }

    private function parseParametrizedRoute($name, $route) {
        $pattern = $route['route'];
        $uriParts = explode('/', $this->uri);
        $patternParts = explode('/', $pattern);
        $availableRout = true;
        foreach($patternParts as $partNumber => $patternPartValue) {
            if(!isset($uriParts[$partNumber]) && strstr($patternPartValue, '::') === false && !empty($patternPartValue)) {
                $availableRout = false;
                break;
            }

            $uriPart = isset($uriParts[$partNumber]) ? $uriParts[$partNumber] : null;
            if(substr($patternPartValue, 0, 1) == ':') {
                $paramName = str_replace(':', '', $patternPartValue);
                $_GET[$paramName] = trim($uriPart);
            } elseif ($patternPartValue != $uriPart) {
                $availableRout = false;
                break;
            }
        }

        if($availableRout) {
            $this->routeName = $name;
            $this->routeData = $route;
            return true;
        } else {
            return false;
        }
    }

    private function initGetParams() {
        if(isset($this->routeData['params'])) {
            foreach($this->routeData['params'] as $k => $v) {
                $_GET[$k] = $v;
            }
        }
    }
}