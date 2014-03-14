<?php

namespace BattleShips\Adapters;

class ConsoleAdapter implements IOAdapterInterface
{
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

    public function flush()
    {
        exit;
    }
}
