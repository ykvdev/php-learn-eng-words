<?php

namespace Lib\Resource;

class PageJs extends AbstractResource
{
    public static function createResource()
    {
        return new \Lib\PageJs(config()['page_js']);
    }
}