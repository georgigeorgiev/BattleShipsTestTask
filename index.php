<?php

if (!defined("STDIN"))
    die("Please run this from the console! <br /> $ php index.php");

function __autoload($class)
{
    $parts = explode('\\', $class);
    require 'BattleShips/'. end($parts) . '.php';
}

use BattleShips\ConsoleController;

ConsoleController::init();
