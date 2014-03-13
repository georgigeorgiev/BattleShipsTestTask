<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
function __autoload($class)
{
    $parts = explode('\\', $class);
    require implode('/', $parts). '.php';
}

use \BattleShips\Adapters\WebAdapter;

$adapter = WebAdapter::getInstance();

$adapter->write('Hello World!');
$adapter->writeSpace();
$adapter->writeLine('And here goes some test. After that new line.');
$adapter->writeLine('Welcome from the second line');
$adapter->requestInput('Tell me something: ');

$adapter->displayPage();
