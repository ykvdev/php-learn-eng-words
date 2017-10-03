<?php

namespace Lib\Resource;

use Lib\Router\RouteCollection;

/**
 * Class Routes
 * @package Lib\Resource
 */
class Routes extends AbstractResource
{
    /**
     * @return RouteCollection
     */
    public static function createResource()
    {
        return new RouteCollection(config()['routes']);
    }
}