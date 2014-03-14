<?php

namespace BattleShips\Controllers;

use BattleShips\Adapters\WebAdapter;

class WebController extends GameController
{
    const SESSION_OBJECT_KEY = 'battleshipssessionkey';

    private static $_instance;

    public static function getInstance()
    {
        if (self::$_instance == null && isset($_SESSION[self::SESSION_OBJECT_KEY])) {
            self::$_instance = $_SESSION[self::SESSION_OBJECT_KEY];
        } elseif (self::$_instance == null) {
            self::$_instance = new WebController();
        }

        return self::$_instance;
    }

    private function __construct()
    {
    }

    public function init()
    {
        if (!isset($_SESSION[self::SESSION_OBJECT_KEY])) {
            $this->_setIOAdapter(WebAdapter::getInstance());
            parent::init();
        } else {

        }
        $this->_ioAdapter->displayPage();
    }
}
