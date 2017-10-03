<?php

namespace Lib;

class FlashMessenger
{
    const SUCCESSFUL = 'successful';
    const ERROR = 'error';
    const INFO = 'info';

    private $identifier = 'flash-messenger';

    private $classes = [
        self::SUCCESSFUL => 'alert alert-success',
        self::ERROR => 'alert alert-danger',
        self::INFO => 'alert alert-info'
    ];

    public function display()
    {
        $view = '<div class="%s"> %s</div>';
        $markup = '';
        if (!isset($_SESSION[$this->identifier])) {
            return $markup;
        }

        $namespaces = array_keys($_SESSION[$this->identifier]);
        $messages = array_reduce($namespaces, function (array $result, $namespace) {
            $result[$namespace] = $this->getMessages($namespace);
            return $result;
        }, []);


        foreach ($messages as $namespace => $message) {
            $class = isset($this->classes[$namespace]) ? $this->classes[$namespace] : $this->classes[self::INFO];
            foreach($message as $value){
                $markup .= sprintf($view, $class, $value);
            }
        }

        return $markup;
    }

    public function addMessage($messages, $namespace = self::INFO)
    {
        $messages = (array)$messages;

        foreach ($messages as $message) {
            $_SESSION[$this->identifier][$namespace][] = $message;
        }
        return $this;
    }

    public function addSuccessfulMessage($message)
    {
        $this->addMessage($message, self::SUCCESSFUL);
        return $this;
    }

    public function addErrorMessage($message)
    {
        $this->addMessage($message, self::ERROR);
        return $this;
    }

    public function hasMessages($namespace = self::INFO)
    {
        return !empty($_SESSION[$this->identifier][$namespace]);
    }

    public function hasSuccessfulMessages()
    {
        return $this->hasMessages(self::SUCCESSFUL);
    }

    public function hasErrorMessages()
    {
        return $this->hasMessages(self::ERROR);
    }

    public function getMessages($namespace = self::INFO)
    {
        $messages = [];
        if ($this->hasMessages($namespace)) {
            $messages = $_SESSION[$this->identifier][$namespace];
            unset($_SESSION[$this->identifier][$namespace]);
        }
        return $messages;
    }

    public function getSuccessfulMessages()
    {
        return $this->getMessages(self::SUCCESSFUL);
    }

    public function getErrorMessages()
    {
        return $this->getMessages(self::ERROR);
    }

    public function getInfoMessages() {
        return $this->getMessages(self::INFO);
    }
}