<?php

namespace BattleShips\Controllers;

use BattleShips\Adapters\ConsoleAdapter;

class ConsoleController extends GameController
{
    private static $_instance;

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new ConsoleController();
        }

        return self::$_instance;
    }

    private function __construct()
    {
    }

    public function init()
    {
        $this->_setIOAdapter(ConsoleAdapter::getInstance());
        parent::init();
    }
}
