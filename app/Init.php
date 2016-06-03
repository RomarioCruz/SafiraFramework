<?php

namespace app;

use Safira\Init\Bootstrap;
use Safira\Auth\AuthManager;
use Safira\Routing\Router;
use Safira\Helpers\Util;
use Safira\Message\FlashMessenger;

class Init extends Bootstrap {

    protected $auth;
    private $router;

    protected function getAuth() {
        if (!$this->auth) {
            $this->auth = new AuthManager();
        }

        return $this->auth;
    }

    protected function getRouter() {
        if (!$this->router) {
            $this->router = new Router();
        }

        return $this->router;
    }

    public function init() {
        $this->getAuth()->setLoginController("auth")
                ->checkLogin('redirect');
    }

}
