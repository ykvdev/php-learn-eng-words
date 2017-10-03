<?php

namespace Lib\Resource;

/**
 * Class Mysql
 * @package Web\Services\Application\Resources
 */
class Mysql extends AbstractResource
{
    public static function createResource()
    {
        return new \Lib\Mysql(config()['mysql']['default']);
    }
}