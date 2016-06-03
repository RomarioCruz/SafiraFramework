<?php

namespace Safira\Http;

class HttpRequest {

    public function isPost() {
        if ($_POST) {
            return true;
        }

        return false;
    }

    public function getPost($field = null) {
        if ($field) {
            if(isset($_POST[$field])) {
                return $_POST[$field];
            }
            
            return false;
        }

        return $_POST;
    }

    public function isGet() {
        if ($_GET) {
            return true;
        }

        return false;
    }

    public function getGet($field = null) {
        if ($field) {
            if(isset($_GET[$field])) {
                return $_GET[$field];
            }
            
            return false;
        }

        return $_GET;
    }

}
