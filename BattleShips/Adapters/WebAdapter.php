<?php

namespace BattleShips\Adapters;

class WebAdapter implements IOAdapterInterface
{
    const TEMPLATE_FILE = 'BattleShips/Templates/index.html';
    const NEW_LINE = "<br />\n";
    const SPACE = '&nbsp;';
    const USER_INPUT_FORM = '<form method="post"><input type="text" name="user_command" /></form>';
    const CONTENT_TAG = '[CONTENT]';

    private $_output;
    private static $_instance;

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new WebAdapter();
        }

        return self::$_instance;
    }

    private function __construct()
    {
    }

    public function writeLine($line)
    {
        $this->_output .= $line . self::NEW_LINE;
    }

    public function write($string)
    {
        $this->_output .= $string;
    }

    public function writeSpace()
    {
        $this->_output .= self::SPACE;
    }

    public function writeNewLine()
    {
        $this->_output .= self::NEW_LINE;
    }

    public function requestInput($requestMessage)
    {
        $this->_output .= $requestMessage . self::USER_INPUT_FORM;
    }

    public function displayPage()
    {
        $html = file_get_contents(self::TEMPLATE_FILE);
        $html = str_replace(self::CONTENT_TAG, $this->_output, $html);
        echo $html;
    }
}
