<?php
function __autoload($class)
{
    $parts = explode('\\', $class);
    require implode('/', $parts) . '.php';
}

use BattleShips\ConsoleController;
use BattleShips\WebController;

session_start();

$controller = (defined("STDIN")) ? ConsoleController::getInstance() : WebController::getInstance();
$controller->init();
