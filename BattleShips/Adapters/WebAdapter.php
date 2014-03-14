<?php

namespace BattleShips\Adapters;

class WebAdapter implements IOAdapterInterface
{
    const TEMPLATE_FILE = 'BattleShips/Templates/index.html';
    const NEW_LINE = "<br />";
    const SPACE = '&nbsp;';
    const USER_INPUT_FORM = '<form method="post">%s<input type="text" name="user_command" /><input type="submit" /></form>';
    const CONTENT_TAG = '[CONTENT]';

    private $_output;

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
        $this->_output .= sprintf(self::USER_INPUT_FORM, $requestMessage) . self::NEW_LINE;
    }

    public function flush()
    {
        $this->_displayPage();
        $this->_output = '';
        exit;
    }

    private function _displayPage()
    {
        $html = file_get_contents(self::TEMPLATE_FILE);
        $html = str_replace(self::CONTENT_TAG, $this->_output, $html);
        echo $html;
    }

}
