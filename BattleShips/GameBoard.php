<?php
/**
 * Created by JetBrains PhpStorm.
 * User: georgiev
 * Date: 3/12/14
 * Time: 9:29 PM
 * To change this template use File | Settings | File Templates.
 */

namespace BattleShips;

class GameBoard
{
    protected $_rows;
    protected $_cols;
    protected $_rowsLetterMap;

    public function __construct($rows, $cols)
    {
        $this->_cols = $cols;
        $this->_rows = $rows;
        $this->_rowsLetterMap = range('A', 'Z');
    }

    public function getRowsLetterMap()
    {
        return $this->_rowsLetterMap;
    }

}
