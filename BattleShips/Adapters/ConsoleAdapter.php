<?php

namespace BattleShips\Adapters;


class ConsoleAdapter implements IOAdapterInterface
{
    private static $_instance;

    public static function getInstance()
    {
        if (self::$_instance == null) {
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

    public function write($string)
    {
        echo $string;
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
        $handle = fopen("php://stdin", "r");
        $userInput = fgets($handle);
        fclose($handle);

        return $userInput;
    }
}
