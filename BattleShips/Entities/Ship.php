<?php

namespace BattleShips\Entities;

/**
 * Class Ship
 * @package BattleShips
 */
class Ship
{
    private $_name;
    private $_length;
    private $_sectors;
    public $shots = 0;

    public function __construct($name, $length)
    {
        $this->_name = $name;
        $this->_length = $length;
    }

    public function setLength($length)
    {
        $this->_length = $length;
    }

    public function getLength()
    {
        return $this->_length;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setSector($row, $col)
    {
        $this->_sectors[$row][$col] = 1;
    }

    public function checkSector($row, $col)
    {
        if(isset($this->_sectors[$row][$col]))

            return true;

        return false;
    }

    public function isSunk()
    {
        if ($this->shots < $this->_length)
            return false;

        return true;
    }
}
