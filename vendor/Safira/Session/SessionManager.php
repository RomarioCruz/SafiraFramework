<?php

namespace Safira\Session;

class SessionManager {

    public function setSession($name, $value) {
        $_SESSION[$name] = $value;
        return $this;
    }

    public function getSession($name) {
        return $_SESSION[$name];
    }

    public function destroySession($name) {
        unset($_SESSION[$name]);
        return $this;
    }

    public function hasSession($name) {
        return isset($_SESSION[$name]);
    }

}
