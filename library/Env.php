<?php

namespace Lib;

/**
 * Class Env
 * @package Lib
 */
final class Env
{
    const TESTING_ENVIRONMENT = 'testing';
    const DEVELOPMENT_ENVIRONMENT = 'development';
    const PRODUCTION_ENVIRONMENT = 'production';

    /** @var  string */
    protected static $env;

    /**
     * @return null|string
     * @throws \Exception
     */
    public static function get()
    {
        if (null == self::$env) {
            self::$env = self::detectEnvironment();
        }
        return self::$env;
    }

    /**
     * @return bool
     */
    public static function isTesting()
    {
        return self::get() === self::TESTING_ENVIRONMENT;
    }

    /**
     * @return bool
     */
    public static function isDevelop()
    {
        return self::get() === self::DEVELOPMENT_ENVIRONMENT;
    }

    /**
     * @return bool
     */
    public static function isProduction()
    {
        return self::get() === self::PRODUCTION_ENVIRONMENT;
    }

    /**
     * @return null|string
     * @throws \Exception
     */
    private static function detectEnvironment()
    {
        if (php_sapi_name() == 'cli') {
            $env = getenv("APPLICATION_ENV") ? getenv("APPLICATION_ENV") : null;
        } else {
            $env = isset($_SERVER["APPLICATION_ENV"]) ? $_SERVER["APPLICATION_ENV"] : null;
        }

        if (!$env || !in_array($env, ['testing', 'development', 'production'])) {
            throw new \Exception('Application env not defined');
        }

        return $env;
    }
}