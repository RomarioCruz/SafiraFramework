<?php

namespace Safira\Routing;

class Router {

    protected $parameters = array();
    private $params;

    public function __construct() {
        $this->setParams();
    }

    protected function go($data) {
        header("Location: /" . $data);
    }

    public function setUrlParameter($name, $value) {
        $this->parameters[$name] = $value;
        return $this;
    }

    protected function getUrlParameters() {
        $params = "";
        foreach ($this->parameters as $name => $value) {
            $params .= $name . '/' . $value . '/';
        }
        return $params;
    }

    public function goToController($controller) {
        $this->go($controller . '/' . $this->getUrlParameters());
    }

    public function goToAction($action) {
        $this->go($this->getCurrentController() . '/' . $action . '/' . $this->getUrlParameters());
    }

    public function goToControllerAction($controller, $action) {
        $this->go($controller . '/' . $action . '/' . $this->getUrlParameters());
    }

    public function goToCurrentControllerAction() {
        $url = $this->getCurrentController() . '/' . $this->getCurrentAction();
        if($this->getIdFromRoute()){
            $url .= "/" . $this->getIdFromRoute();
            
        }
        $this->go($url);
    }

    public function goToIndex() {
        $this->goToController('index');
    }

    public function goToRoot() {
        $this->go('');
    }

    public function goToUrl($url) {
        header("Location: " . $url);
    }

    public function getIdFromRoute() {
        $url = explode('/', $_GET['url']);
        unset($url[0], $url[1]);

        if (end($url) == null) {
            $id = null;
        } else {
            $id = end($url);
        }

        return $id;
    }

    public function getCurrentController() {
        $url = explode('/', $_GET['url']);
        $controller = $url[0];

        return $controller;
    }

    public function getCurrentAction() {
        $url = explode('/', $_GET['url']);

        if (!isset($url[1]) || $url[1] == '') {
            $action = 'index';
        } else {
            $action = $url[1];
        }

        return $action;
    }

    private function setParams() {
        $url = explode('/', $_GET['url']);
        unset($url[0], $url[1]);

        if (end($url) == null) {
            array_pop($url);
        }

        $i = 0;
        if (!empty($url)) {
            foreach ($url as $val) {
                if ($i % 2 == 0) {
                    $ind[] = $val;
                } else {
                    $value[] = $val;
                }
                $i++;
            }
        } else {
            $ind = array();
            $value = array();
        }

        if (!empty($ind) && !empty($value) && count($ind) == count($value)) {
            $this->params = array_combine($ind, $value);
        } else {
            $this->params = array();
        }
    }

    /**
     * Retorna os parâmetros passados na url
     * @param array $name
     * @return array
     */
    public function getParam($name = null) {
        if ($name != null) {
            if (array_key_exists($name, $this->params)) {
                return $this->params[$name];
            } else {
                return false;
            }
        } else {
            return $this->params;
        }
    }

}
