<?php

namespace Safira\Init;

use Safira\Http\PageError;

abstract class Bootstrap {

    private $url;
    private $explode;
    protected $controller;
    protected $action;
    protected $params;
    private $pageError;

    public function __construct() {
        $this->setUrl();
        $this->setExplode();
        $this->setController();
        $this->setAction();
        $this->init();
        $this->run();
    }

    abstract protected function init();

    private function setUrl() {
        $_GET['url'] = (isset($_GET['url']) ? $_GET['url'] : 'index/index');
        $this->url = $_GET['url'];
    }

    private function setExplode() {
        $this->explode = explode('/', $this->url);
    }

    private function setController() {
        $this->controller = $this->explode[0];
    }

    public function getController() {
        return $this->controller;
    }

    protected function setAction() {
        $ac = (!isset($this->explode[1]) || $this->explode[1] == null || $this->explode[1] == "index" ? "indexAction" : $this->explode[1] . 'Action');
        $this->action = $ac;
    }
    
    public function getAction() {
        return $this->action;
    }

    public function run() {
        $class = "app\\Controllers\\" . ucfirst($this->controller) . 'Controller';

        if (class_exists($class)) {
            $controller = new $class;
            $action = $this->action;
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                $this->getPageError()->goPage(404);
            }
        } else {
            $this->getPageError()->goPage(404);
        }
    }

    public function getPageError() {
        return new PageError();
    }

}
