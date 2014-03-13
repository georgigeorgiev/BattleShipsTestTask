<?php

namespace BattleShips;

/**
 * Class BattleField
 * @package BattleShips
 */
class BattleField extends GameBoard
{
    const WATER = '.';
    const SHIP_PART = 'X';
    const MISSED_SHOT = '-';
    const HORIZONTAL_ID = 0;
    const VERTICAL_ID = 1;

    public $_field;

    /**
     * Positioning single ship randomly on available space
     * @param \BattleShips\Ship $ship
     * @throws \LogicException
     */
    public function positionShip(&$ship)
    {
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
    }

    /**
     * Scanning field for available ship space
     * Returning available start points for drawing ships
     * @param $shipLength
     * @return mixed
     */
    private function _scanField($shipLength)
    {
        $startPoints[self::HORIZONTAL_ID] = array();
        $startPoints[self::VERTICAL_ID] = array();

        for ($i = 1; $i <= $this->_rows; $i++) {
            for ($j = 1; $j <= $this->_cols; $j++) {
                if (($j + $shipLength) > $this->_cols) // If there isn't enough room to place that ship horizontally
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

        for ($j = 1; $j <= $this->_cols; $j++) {
            for ($i = 1; $i <= $this->_rows; $i++) {
                if (($i + $shipLength) > $this->_rows) // If there is enough room to place that ship vertically
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
}
