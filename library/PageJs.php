<?php

namespace Lib;

class PageJs {
    private $config;

    public function __construct($config) {
        $this->config = $config;
    }

    public function requireJs() {
        $scriptName = router()->getRouteName() . '.js';
        $scriptPath = $this->config['scripts_path'] . '/' . $scriptName;
        if(is_file($scriptPath)) {
            $requirePath = $this->config['require_path'] . '/' . $scriptName;
            return sprintf('<script type="text/javascript" src="%s"></script>', $requirePath);
        } else {
            return '';
        }
    }
}