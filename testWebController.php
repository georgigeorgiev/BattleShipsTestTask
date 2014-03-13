<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
function __autoload($class)
{
    $parts = explode('\\', $class);
    require implode('/', $parts). '.php';
}

use BattleShips\Controllers\WebController;

session_start();

$controller = WebController::getInstance();
$controller->init();

$_SESSION[$controller::SESSION_OBJECT_KEY] = $controller;