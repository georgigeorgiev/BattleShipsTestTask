<?php

namespace BattleShips;

/**
 * Class ConsoleAdapter
 * @package BattleShips
 */
class ConsoleAdapter
{
    private static $_instance;

    /**
     * Singleton class instance
     * @return ConsoleAdapter
     */
    public static function getInstance()
    {
        if (self::$_instance==null) {
            self::$_instance = new ConsoleAdapter();
        }

        return self::$_instance;
    }

    private function __construct()
    {
    }

    public function writeLine($line)
    {
        echo $line . "\n";
    }

    public function write($line)
    {
        echo $line;
    }

    public function writeSpace()
    {
        echo ' ';
    }

    public function writeNewLine()
    {
        echo "\n";
    }

    public function requestInput($requestMessage)
    {
        echo $requestMessage;
        $handle = fopen ("php://stdin","r");
        $userInput = fgets($handle);
        fclose($handle);

        return $userInput;
    }
}
