<?php

namespace BattleShips\Adapters;

interface IOAdapterInterface
{
    public function writeLine($line);

    public function write($string);

    public function writeSpace();

    public function writeNewLine();

    public function requestInput($requestMessage);

    public function flush();
}
