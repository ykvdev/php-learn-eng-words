<?php

namespace Web;

use Lib\Env;

class Bootstrap
{
    /** @var \Lib\Controller\AbstractAction $action */
    private $action;

    public function run()
    {
        session_start();
        $this->dispatchWithCatchExceptions();
        $this->renderView();
    }

    private function dispatchWithCatchExceptions()
    {
        try {
            $this->dispatch();
        } catch (\Lib\ProcessException $e) {
            fm()->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            if (Env::isTesting()) {
                $trace = nl2br($e->getTraceAsString());
                die("{$e->getMessage()}<br>{$trace}");
            } else {
                fm()->addErrorMessage('Произошла внутренняя ошибка');
            }
        }
    }

    private function dispatch()
    {
        router()->setUri($_SERVER['REQUEST_URI'])->parse();
        $actionClassName = router()->getActionClassName();
        $this->action = new $actionClassName();
        $this->action->run();
    }

    private function renderView()
    {
        // redirect to prev page is need
        if ($this->action->isReturnToPrevPage()) {
            redirectToReferer();
        }

        // render view
        $view = $this->action->getView();
        if (!$view->getViewAlias()) {
            $view->setViewAlias(router()->getViewAlias());
        }

        if (!$view->getLayoutAlias()) {
            $view->useDefaultLayout();
        }
        echo $view;
    }
}