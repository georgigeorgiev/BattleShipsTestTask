<?php

namespace BattleShips\Controllers;

use BattleShips\Adapters\ConsoleAdapter;
use BattleShips\BattleField;
use BattleShips\ShipFactory;

class ConsoleController extends GameController
{
    public function init()
    {
        $this->_ioAdapter = ConsoleAdapter::getInstance();
        parent::init();
    }
}