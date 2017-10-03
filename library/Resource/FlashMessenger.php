<?php

namespace Lib\Resource;

/**
 * Class FlashMessenger
 * @package Web\Services\Application\Resources
 */
class FlashMessenger extends AbstractResource
{
    /**
     * @return \Lib\FlashMessenger
     */
    public static function createResource()
    {
        return new \Lib\FlashMessenger();
    }
}