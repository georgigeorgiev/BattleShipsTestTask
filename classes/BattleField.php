<?php

namespace BattleShips;

/**
 * Class BattleField
 * @package BattleShips
 */
class BattleField {
    const ROWS = 10;
    const COLS = 10;

    const WATER = '.';
    const SHIP_PART = 'X';
    const MISSED_SHOT = '-';

    const HORIZONTAL_ID = 0;
    const VERTICAL_ID = 1;

    const DEBUG_MODE = false;
    const MODE_TRANSPARENT_MAP = 0;
    const MODE_MASKED_MAP = 1;

    const RESULT_MISS = 0;
    const RESULT_HIT = 1;
    const RESULT_SUNK = 2;

    private $_lettersMap = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    private $_ships;
    private static $_instance;
    private $_field;
    private $_shots;
    private $_outputAdapter;


    /**
     * Singleton class instance
     * @param $outputAdapter
     * @return BattleField
     */
    public static function getInstance($outputAdapter){
        if(!isset(self::$_instance)){
            self::$_instance = new BattleField($outputAdapter);
        }

        return self::$_instance;
    }

    private function  __construct($outputAdapter){
        $this->_outputAdapter = $outputAdapter;
        $this->_field = array();
        $this->_shots = array();
    }

    /**
     * Positioning all the ships on the field
     * Return the number of allocated sectors
     * @return int
     * @throws \LogicException
     */
    public function positionShips(){
        if(empty($this->_ships))
            throw new \LogicException('No ships for positioning');
        $shipSectors = 0;
        foreach ($this->_ships as &$ship) {
            $this->_positionShip($ship);
            $shipSectors += $ship->getLength();
        }

        return $shipSectors;
    }

    /**
     * Processing shot - determine miss, hit or sunk
     * @param $rowLabel
     * @param $colLabel
     * @return bool|int
     */
    public function processShot($rowLabel, $colLabel){
        $rowKey = array_search($rowLabel, $this->_lettersMap) + 1;
        $colKey = $colLabel;

        if(isset($this->_shots[$rowKey][$colKey]))
            return false; //Already shot there - skipping

        $this->_shots[$rowKey][$colKey] = 1;

        if (isset($this->_field[$rowKey][$colKey])){
            if($this->_isShipSunk($rowKey, $colKey)) //We hit a ship, is it sunk now?
                return self::RESULT_SUNK;

            return self::RESULT_HIT;
        }


        return self::RESULT_MISS;

    }

    /**
     * Drawing the field during game play
     * in two modes - transparent and masked
     * @param int $mode
     */
    public function drawField($mode = self::MODE_MASKED_MAP){
        if(self::DEBUG_MODE)
            $mode = self::MODE_TRANSPARENT_MAP;

        // Row 1 labels - 1,2,3...,0
        $this->_outputAdapter->writeSpace();
        for ($i = 1; $i <= self::COLS; $i++) {
            $this->_outputAdapter->write(substr($i, -1)); //col labels are displayed as one digit
        }

        $this->_outputAdapter->writeNewLine();

        // Rest of the rows with label in front
        for ($i = 1; $i <= self::ROWS; $i++) {
            $this->_outputAdapter->write($this->_lettersMap[$i - 1]); //Current row label

            for ($j = 1; $j <= self::COLS; $j++) {
                if (isset($this->_field[$i][$j]) && isset($this->_shots[$i][$j])) {
                    //Displaying positioned and hit ship part
                    $this->_outputAdapter->write($this->_field[$i][$j]);

                }elseif (isset($this->_shots[$i][$j])) {
                    //Displaying missed shot
                    $this->_outputAdapter->write(self::MISSED_SHOT);

                }elseif (isset($this->_field[$i][$j])) {
                    //Displaying ship part
                    if($mode === self::MODE_TRANSPARENT_MAP)
                        $this->_outputAdapter->write($this->_field[$i][$j]);
                    elseif($mode === self::MODE_MASKED_MAP)
                        $this->_outputAdapter->write(self::WATER);

                } else {
                    //Displaying water
                    if($mode === self::MODE_TRANSPARENT_MAP)
                        $this->_outputAdapter->writeSpace();
                    elseif($mode === self::MODE_MASKED_MAP)
                        $this->_outputAdapter->write(self::WATER);

                }
            }
            $this->_outputAdapter->writeNewLine();
        }
    }

    /**
     * Letters map property getter
     * @return array
     */
    public function getLettersMap(){
        return $this->_lettersMap;
    }

    /**
     * Ship entity setter
     * @param $ships
     */
    public function setShips($ships){
        $this->_ships = $ships;
    }

    /**
     * Positioning single ship randomly on available space
     * @param $ship
     * @throws \LogicException
     */
    private function _positionShip(&$ship){
        //Get all available start points for the ship
        $startPoints = $this->_scanField($ship->getLength());

        if(!empty($startPoints[self::HORIZONTAL_ID]) && !empty($startPoints[self::VERTICAL_ID]))
            $choiceOrientation = rand(self::HORIZONTAL_ID, self::VERTICAL_ID);
        elseif(!empty($startPoints[self::HORIZONTAL_ID]))
            $choiceOrientation = self::HORIZONTAL_ID;
        elseif(!empty($startPoints[self::VERTICAL_ID]))
            $choiceOrientation = self::VERTICAL_ID;
        else
            throw new \LogicException("Can't place the ships. Too much ships on a small field.");


        $rowKeysArr = array_keys($startPoints[$choiceOrientation]);
        $rowKey = array_rand($rowKeysArr);
        $rowNum = $rowKeysArr[$rowKey];

        $colKeysArr = array_keys($startPoints[$choiceOrientation][$rowNum]);
        $colKey = array_rand($colKeysArr);
        $colNum = $colKeysArr[$colKey];

        for ($i = 0; $i < $ship->getLength(); $i++) {
            if ($choiceOrientation === self::HORIZONTAL_ID) {
                $this->_field[$rowNum][$colNum + $i] = self::SHIP_PART;
                $ship->setSector($rowNum, $colNum + $i);
            } else {
                $this->_field[$rowNum + $i][$colNum] = self::SHIP_PART;
                $ship->setSector($rowNum + $i, $colNum);
            }
        }

        if(self::DEBUG_MODE){ //If debug mode is on - output some info about ship position
            $this->_outputAdapter->writeLine(
                sprintf("orientation: %s, row: %s, col: %s\n%s", $choiceOrientation, $rowNum, $colNum, print_r($ship))
            );
        }

    }

    /**
     * Scanning field for available ship space
     * Returning available start points for drawing ships
     * @param $shipLength
     * @return mixed
     */
    private function _scanField($shipLength){
        $startPoints[self::HORIZONTAL_ID] = array();
        $startPoints[self::VERTICAL_ID] = array();

        for ($i = 1; $i <= self::ROWS; $i++) {
            for ($j = 1; $j <= self::COLS; $j++) {
                if (($j + $shipLength) > self::COLS) // If there isn't enough room to place that ship horizontally
                continue 2;

                for ($k = 0; $k < $shipLength; $k++) {
                    if (isset($this->_field[$i][$j + $k])) { //If there is already allocated sector in that row
                        $j += $k + 1; //move cursor to the col after allocated sector
                        continue 2; //proceed scan at that col
                    }
                }
                $startPoints[self::HORIZONTAL_ID][$i][$j] = 1;
            }
        }

        for ($j = 1; $j <= self::COLS; $j++) {
            for ($i = 1; $i <= self::ROWS; $i++) {
                if (($i + $shipLength) > self::ROWS) // If there is enough room to place that ship vertically
                continue 2;

                for ($k = 0; $k < $shipLength; $k++) {
                    if (isset($this->_field[$i + $k][$j])) { //If there is already allocated sector in that col
                        $i += $k + 1; //move cursor to the row after allocated sector
                        continue 2; //proceed scan at that row
                    }
                }
                $startPoints[self::VERTICAL_ID][$i][$j] = 1;
            }
        }

        return $startPoints;
    }

    /**
     * Ship sunk checker
     * Did we shot all the parts of a ship
     * @param $rowKey
     * @param $colKey
     * @return bool
     */
    private function _isShipSunk($rowKey, $colKey){
        $isSunk = false;
        foreach ($this->_ships as &$ship) {
            if($ship->checkSector($rowKey, $colKey)){
                $ship->shots += 1;
                $isSunk = $ship->isSunk();
            }
        }

        return $isSunk;
    }
}