<?php

namespace Lib\Resource;

/**
 * Class Configs
 * @package Lib\Resource
 */
class Configs extends AbstractResource
{
    /**
     * @return \Lib\Configs
     */
    public static function createResource()
    {
        return new \Lib\Configs();
    }


}