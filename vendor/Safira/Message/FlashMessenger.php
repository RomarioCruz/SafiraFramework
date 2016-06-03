<?php

namespace Safira\Message;

use Safira\Session\SessionManager;

class FlashMessenger {

    protected static $sessionManager;

    public function addSuccessMessage($msg) {
        if (self::$sessionManager === null) {
            self::$sessionManager = new SessionManager;
        }
        
        $alertMsg = "<div class='alert alert-success' role='alert'>";
        $alertMsg .= $msg;
        $alertMsg .= "</div>";

        self::$sessionManager->setSession('msg', $alertMsg);
    }

    public static function addErrorMessage($msg) {
        if (self::$sessionManager === null) {
            self::$sessionManager = new SessionManager;
        }
        
        $alertMsg = "<div class='alert alert-danger' role='alert'>";
        $alertMsg .= $msg;
        $alertMsg .= "</div>";
        
        self::$sessionManager->setSession('msg', $alertMsg);
    }

    public static function hasMessage($name) {
        if (isset($_SESSION[$name])) {
            return true;
        } else {
            return false;
        }
    }

    public static function getMessage($name) {
        $msg = '';
        if ($_SESSION[$name]) {
            $msg = $_SESSION[$name];
            unset($_SESSION[$name]);
        }
        return $msg;
    }

}
