<?php

namespace BattleShips;

use BattleShips\Adapters\WebAdapter;

class WebController extends GameController
{
    const SESSION_OBJECT_KEY = 'battleshipssessionkey';

    private static $_instance;

    public static function getInstance()
    {
        if (self::$_instance == null && isset($_SESSION[self::SESSION_OBJECT_KEY])) {
            self::$_instance = $_SESSION[self::SESSION_OBJECT_KEY]; // Load instance from session
        } elseif (self::$_instance == null) {
            self::$_instance = new WebController(); // Creating new instance
        }

        return self::$_instance;
    }

    private function __construct()
    {
    }

    public function init()
    {
        if (!isset($_SESSION[self::SESSION_OBJECT_KEY])) {
            $this->_setIOAdapter(new WebAdapter());
            parent::init();
        } else {
            $this->_userInput = isset($_POST['user_command']) ? $_POST['user_command'] : '';
            parent::gamePlay();
        }
    }

    protected function _requestUserInput()
    {
        $_SESSION[self::SESSION_OBJECT_KEY] = self::$_instance; // Save instance
        parent::_requestUserInput();

        $this->_ioAdapter->flush();
    }

    protected function _gameDone()
    {
        parent::_gameDone();
        $this->_ioAdapter->flush();
    }
}
