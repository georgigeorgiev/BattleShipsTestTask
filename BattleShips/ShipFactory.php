<?php
/**
 * Created by JetBrains PhpStorm.
 * User: georgiev
 * Date: 3/12/14
 * Time: 9:47 PM
 * To change this template use File | Settings | File Templates.
 */

namespace BattleShips;

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
