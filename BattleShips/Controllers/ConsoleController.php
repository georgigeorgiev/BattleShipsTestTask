<?php

namespace BattleShips\Controllers;

use BattleShips\Adapters\ConsoleAdapter;

class ConsoleController extends GameController
{
    public function init()
    {
        $this->_ioAdapter = ConsoleAdapter::getInstance();
        parent::init();
    }
}
