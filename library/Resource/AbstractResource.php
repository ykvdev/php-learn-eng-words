<?php

namespace Lib\Resource;

/**
 * Class AbstractResource
 * @package Lib\Resource
 */
abstract class AbstractResource implements ResourceInterface
{
    /** @var  static */
    protected static $instances;

    /**
     * Return instance as Singleton
     * @return mixed
     */
    public static function getInstance()
    {
        $resource = get_called_class();
        if (!isset(static::$instances[$resource])) {
            static::$instances[$resource] = static::createInstance();
        }
        return static::$instances[$resource];
    }

    /**
     * Create new instance
     *
     * @return static
     */
    public static function createInstance()
    {
        return static::createResource();
    }
}