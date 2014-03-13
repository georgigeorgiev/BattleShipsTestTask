<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
function __autoload($class)
{
    $parts = explode('\\', $class);
    require implode('/', $parts). '.php';
}

use BattleShips\Controllers\ConsoleController;

$controller = new ConsoleController();
$controller->init();
