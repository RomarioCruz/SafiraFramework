<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

$init = new \app\Init();
$auth = new \Safira\Auth\AuthManager();
