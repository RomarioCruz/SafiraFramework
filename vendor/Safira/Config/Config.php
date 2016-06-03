<?php

namespace Safira\Config;

class Config {

    public static function auth() {
        return include __DIR__ . '/../../../config/auth.php';
    }

    public static function database() {
        return include __DIR__ . '/../../../config/database.php';
    }

}
