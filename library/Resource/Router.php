<?php

namespace Lib\Resource;

class Router extends AbstractResource
{
    /**
     * @return \Lib\Router
     */
    public static function createResource()
    {
        return new \Lib\Router(Routes::getInstance(), '\Web\Controllers');
    }
}