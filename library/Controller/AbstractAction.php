<?php

namespace Lib\Controller;

use Lib\Http\RequestTrait;

/**
 * Class AbstractActionController
 * @package Lib\Controller
 */
abstract class AbstractAction
{
    use RequestTrait;

    /**
     * @var \Lib\ViewRenderer
     */
    protected $view;
    protected $returnToPrevPage = false;

    public function __construct()
    {
        $this->view = new \Lib\ViewRenderer(config()['view_renderer']);
    }

    abstract public function run();


    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }

    public function getView()
    {
        return $this->view;
    }

    public function sendJsonResponse(JsonResponse $response)
    {
        echo $response;
        exit;
    }

    public function setReturnToPrevPage()
    {
        $this->returnToPrevPage = true;
    }

    /**
     * @return bool
     */
    public function isReturnToPrevPage()
    {
        return $this->returnToPrevPage;
    }
}