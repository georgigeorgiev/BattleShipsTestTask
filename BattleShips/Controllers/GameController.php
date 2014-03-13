<?php

namespace BattleShips\Controllers;

use BattleShips\BattleField;
use BattleShips\ShipFactory;

class GameController
{
    const ROWS = 10;
    const COLS = 10;
    const CHEAT_SHOW = 'show';
    const MODE_TRANSPARENT_MAP = 0;
    const MODE_MASKED_MAP = 1;
    const RESULT_MISS = 0;
    const RESULT_HIT = 1;
    const RESULT_SUNK = 2;

    protected $_shots;
    /** @var  \BattleShips\BattleField $_battleField */
    protected $_battleField;
    protected $_ships;
    /** @var  \BattleShips\Adapters\IOAdapterInterface $_ioAdapter */
    protected $_ioAdapter;
    protected $_allowedCommands;
    protected $_totalShipSectors;
    protected $_lettersMap;

    public function init()
    {
        $this->_battleField = new BattleField(self::ROWS, self::COLS);
        $this->_lettersMap = $this->_battleField->getRowsLetterMap();
        $this->_ships = array(
            ShipFactory::create(ShipFactory::BATTLESHIP),
            ShipFactory::create(ShipFactory::DESTROYER),
            ShipFactory::create(ShipFactory::DESTROYER)
        );

        try {
            $this->_totalShipSectors = $this->positionShips();
        } catch (\LogicException $e) {
            $this->_ioAdapter->writeLine('Error in configuration. Bye.');
            exit;
        }

        $this->_generateShotCodes();
        $this->drawField(self::MODE_TRANSPARENT_MAP);
    }

    public function positionShips()
    {
        if (empty($this->_ships)) throw new \LogicException('No ships for positioning');

        $shipSectors = 0;
        foreach ($this->_ships as &$ship) {
            /** @var \BattleShips\Ship $ship */
            $this->_battleField->positionShip($ship);
            $shipSectors += $ship->getLength();
        }

        return $shipSectors;
    }

    public function processShot($rowLabel, $colLabel)
    {
        $rowKey = array_search($rowLabel, $this->_lettersMap) + 1;
        $colKey = $colLabel;

        if (isset($this->_shots[$rowKey][$colKey])) return false; //Already shot there - skipping

        $this->_shots[$rowKey][$colKey] = 1;

        if (isset($this->_battleField->_field[$rowKey][$colKey])) {
            return ($this->_isShipSunk($rowKey, $colKey)) ?  self::RESULT_SUNK : self::RESULT_HIT;
        }

        return self::RESULT_MISS;

    }

    public function drawField($mode = self::MODE_MASKED_MAP)
    {
        // Row 1 labels - 1,2,3...,0
        $this->_ioAdapter->writeSpace();
        for ($i = 1; $i <= self::COLS; $i++) {
            $this->_ioAdapter->write(substr($i, -1)); //col labels are displayed as one digit
        }

        $this->_ioAdapter->writeNewLine();

        // Rest of the rows with label in front
        for ($i = 1; $i <= self::ROWS; $i++) {
            $this->_ioAdapter->write($this->_lettersMap[$i - 1]); //Current row label

            for ($j = 1; $j <= self::COLS; $j++) {
                if (isset($this->_battleField->_field[$i][$j]) && isset($this->_shots[$i][$j])) {
                    //Displaying positioned and hit ship part
                    $this->_ioAdapter->write($this->_battleField->_field[$i][$j]);

                } elseif (isset($this->_shots[$i][$j])) {
                    //Displaying missed shot
                    $this->_ioAdapter->write(BattleField::MISSED_SHOT);

                } elseif (isset($this->_battleField->_field[$i][$j])) {
                    //Displaying ship part
                    $this->_ioAdapter->write(
                        ($mode === self::MODE_TRANSPARENT_MAP) ? $this->_battleField->_field[$i][$j] : BattleField::WATER
                    );
                } else {
                    ($mode === self::MODE_TRANSPARENT_MAP) ? $this->_ioAdapter->writeSpace() : $this->_ioAdapter->write(BattleField::WATER);
                }
            }
            $this->_ioAdapter->writeNewLine();
        }
    }

    protected function _isShipSunk($rowKey, $colKey)
    {
        $isSunk = false;
        foreach ($this->_ships as &$ship) {
            /** @var \BattleShips\Ship $ship */
            if ($ship->checkSector($rowKey, $colKey)) {
                $ship->shots += 1;
                $isSunk = $ship->isSunk();
            }
        }

        return $isSunk;
    }

    protected function _generateShotCodes()
    {
        for ($i = 1; $i <= self::ROWS; $i++) {
            for ($j = 1; $j <= self::COLS; $j++) {
                $this->_allowedCommands[] = $this->_lettersMap[$i - 1] . $j;
            }
        }

        $this->_allowedCommands[] = self::CHEAT_SHOW;
    }

}