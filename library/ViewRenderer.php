<?php

namespace Lib;

/**
 * Class ViewRenderer
 * @package Lib
 * @property bool $needReloadParent
 */
class ViewRenderer {
    private $config;

    private $pageTitle;

    private $layoutsPath;

    private $layoutAlias;

    private $viewsPath;

    private $viewAlias;

    private $vars = [];

    private $jsVars = [];

    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * @param mixed $layoutsPath
     */
    public function setLayoutsPath($layoutsPath)
    {
        $this->layoutsPath = $layoutsPath;
        return $this;
    }

    public function getLayoutsPath()
    {
        return $this->layoutsPath;
    }

    /**
     * @param mixed $layoutAlias
     */
    public function setLayoutAlias($layoutAlias)
    {
        $this->layoutAlias = $layoutAlias;
        return $this;
    }

    public function getLayoutAlias()
    {
        return $this->layoutAlias;
    }

    public function useDefaultLayout() {
        $this->layoutAlias = $this->config['default_layout_alias'];
    }

    /**
     * @param mixed $viewsPath
     */
    public function setViewsPath($viewsPath)
    {
        $this->viewsPath = $viewsPath;
        return $this;
    }

    public function getViewsPath()
    {
        return $this->viewsPath;
    }

    /**
     * @param mixed $viewAlias
     */
    public function setViewAlias($viewAlias)
    {
        $this->viewAlias = $viewAlias;
        return $this;
    }

    public function getViewAlias()
    {
        return $this->viewAlias;
    }

    /**
     * @param array|object $vars
     */
    public function setVars($vars)
    {
        if(is_object($vars) && $vars instanceof self) {
            $this->vars = array_merge($this->vars, $vars->vars);
            $this->jsVars = array_merge($this->jsVars, $vars->jsVars);
        } elseif(is_array($vars)) {
            $this->vars = array_merge($this->vars, $vars);
        }

        return $this;
    }

    /**
     * @param array $vars
     * @return $this
     */
    public function setJsVars($vars)
    {
        $this->jsVars = array_merge($this->jsVars, $vars);

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function addJsVar($name, $value) {
        $this->jsVars[$name] = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPageTitle($value) {
        $this->pageTitle = $value;

        return $this;
    }

    public function getTranslatedPageTitle() {
        return t($this->config['page_title_translate_key'], $this->pageTitle);
    }

    /**
     * @return array
     */
    public function getJsVars() {
        return $this->jsVars;
    }

    public function __set($varName, $varValue) {
        $this->vars[$varName] = $varValue;
    }

    public function __get($varName) {
        if(isset($this->vars[$varName])) {
            return escape($this->vars[$varName]);
        } else {
            return null;
        }
    }

    public function raw($varName) {
        if(isset($this->vars[$varName])) {
            return $this->vars[$varName];
        } else {
            return null;
        }
    }

    public function __toString() {
        $viewHtml = $this->prepareParams()
            ->checkParams()
            ->render($this->getViewFullPath());

        if($this->layoutAlias && $this->layoutsPath) {
            $this->vars['content'] = $viewHtml;
            $viewHtml = $this->render($this->getLayoutFullPath());
        }

        return $viewHtml;
    }

    private function prepareParams() {
        $this->layoutsPath = $this->layoutsPath ?: $this->config['layouts_path'];

        $this->viewsPath = $this->viewsPath ?: $this->config['views_path'];
        if(!is_file($this->getViewFullPath())) {
            $this->viewsPath = $this->config['base_path'];
        }

        return $this;
    }

    private function checkParams() {
        if($this->layoutsPath && $this->layoutAlias && !is_file($this->getLayoutFullPath())) {
            trigger_error("Layout by alias {$this->layoutAlias} not found");
        }

        if(!$this->viewsPath) {
            trigger_error('Layouts path must be specified');
        }

        if(!$this->viewAlias) {
            trigger_error('Layout alias must be specified');
        }

        if(!is_file($this->getViewFullPath())) {
            trigger_error("View by alias {$this->viewAlias} not found");
        }

        return $this;
    }

    private function render($viewPath) {
        ob_start();
        require $viewPath;
        return ob_get_clean();
    }

    private function getLayoutFullPath() {
        return "{$this->layoutsPath}/{$this->layoutAlias}.phtml";
    }

    private function getViewFullPath() {
        return "{$this->viewsPath}/{$this->viewAlias}.phtml";
    }
}