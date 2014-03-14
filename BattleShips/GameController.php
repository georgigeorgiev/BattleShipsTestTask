<?php

namespace BattleShips;

use BattleShips\Adapters\IOAdapterInterface;
use BattleShips\Entities\BattleField;
use BattleShips\Entities\ShipFactory;
use BattleShips\Entities\Ship;

abstract class GameController
{
    const ROWS = 10;
    const COLS = 10;
    const CHEAT_SHOW = 'SHOW';
    const MODE_TRANSPARENT_MAP = 0;
    const MODE_MASKED_MAP = 1;

    protected $_shots;
    /** @var  BattleField $_battleField */
    protected $_battleField;
    protected $_ships;
    /** @var  IOAdapterInterface $_ioAdapter */
    protected $_ioAdapter;
    protected $_allowedCommands;
    protected $_totalHitShots = 0;
    protected $_totalShipSectors = 0;
    protected $_totalShots = 0;
    protected $_lettersMap;
    protected $_userInput;

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
            $this->_positionShips();
        } catch (\LogicException $e) {
            $this->_ioAdapter->writeLine('Error in configuration. Bye.');
            $this->_ioAdapter->flush();
        }

        $this->_generateShotCodes();
        $this->gamePlay();

    }

    public function gamePlay()
    {
        $draw = true;
        try {
            $draw = $this->_processUserInput();
        } catch (\UnexpectedValueException $e) {
            $this->_ioAdapter->writeLine("*** Error ***");
        }

        if($draw) $this->_drawField();

        if ($this->_totalHitShots < $this->_totalShipSectors) {
            $this->_requestUserInput();
            $this->gamePlay();
        } else {
            $this->_gameDone();
        }
    }

    protected function _processUserInput()
    {
        if (isset($this->_userInput)) {
            $getUserInput = trim(strtoupper($this->_userInput));
            if (!in_array($getUserInput, $this->_allowedCommands)) {
                throw new \UnexpectedValueException('Not allowed user input');
            }

            if ($getUserInput == self::CHEAT_SHOW) {
                $this->_drawField(self::MODE_TRANSPARENT_MAP);

                return false;
            } else {
                preg_match("/[A-Z]{1,}/", $getUserInput, $rowLabel);
                preg_match("/[0-9]{1,}/", $getUserInput, $colLabel);

                $rowKey = array_search($rowLabel[0], $this->_lettersMap) + 1;
                $colKey = $colLabel[0];

                if (isset($this->_shots[$rowKey][$colKey])) return true; //Already shot there - skipping

                $this->_shots[$rowKey][$colKey] = 1;
                $this->_totalShots += 1;
                $result = $this->_battleField->checkSector($rowKey, $colKey);

                if ($result == BattleField::RESULT_HIT) {
                    $this->_totalHitShots += 1;
                    if($this->_isShipSunk($rowKey, $colKey))
                        $this->_ioAdapter->writeLine("Sunk!");
                    else
                        $this->_ioAdapter->writeLine("Right on the target!");
                } else {
                    $this->_ioAdapter->writeLine("Missed!");
                }
            }
        }

        return true;
    }

    protected function _requestUserInput()
    {
        $result = $this->_ioAdapter->requestInput('Enter coordinates (row, col), e.g. A5 = ');
        if($result) $this->_userInput = $result;
    }

    protected function _positionShips()
    {
        if (empty($this->_ships)) throw new \LogicException('No ships for positioning');

        foreach ($this->_ships as &$ship) {
            /** @var Ship $ship */
            $this->_battleField->positionShip($ship);
            $this->_totalShipSectors += $ship->getLength();
        }
    }

    protected function _drawField($mode = self::MODE_MASKED_MAP)
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
            /** @var Ship $ship */
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

    protected function _setIOAdapter(IOAdapterInterface $ioAdapter)
    {
        $this->_ioAdapter = $ioAdapter;
    }

    protected function _gameDone()
    {
        $this->_ioAdapter->writeLine("Well done! You completed game in " . $this->_totalShots . " shots.");
    }
}
