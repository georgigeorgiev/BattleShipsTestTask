<?php

namespace BattleShips\Adapters;

interface IOAdapterInterface
{
    public static function getInstance();

    public function writeLine($line);

    public function write($string);

    public function writeSpace();

    public function writeNewLine();

    public function requestInput($requestMessage);
}
