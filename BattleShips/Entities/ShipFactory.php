<?php

namespace BattleShips\Entities;

class ShipFactory
{
    const BATTLESHIP = 'Battleship';
    const DESTROYER = 'Destroyer';

    public static function create($ship)
    {
        switch ($ship) {
            case self::BATTLESHIP :
                $ship = new Ship(self::BATTLESHIP, 5);
                break;
            case self::DESTROYER :
                $ship = new Ship(self::DESTROYER, 4);
                break;
        }

        return $ship;
    }
}
