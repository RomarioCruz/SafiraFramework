<?php

namespace Safira\Http;

class PageError {

    public function goPage($error) {
        if (file_exists(__DIR__ . "/../Mvc/View/error/{$error}.phtml")) {
            include_once __DIR__ . "/../Mvc/View/error/{$error}.phtml";
        } else {
            header('Location: /');
        }
    }

}
