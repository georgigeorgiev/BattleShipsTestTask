<?php

namespace BattleShips;

/**
 * Class ConsoleController
 * @package BattleShips
 */
class ConsoleController {
    const CHEAT_SHOW = 'SHOW';
    private static $_adapter;
    private static $_allowedCommands;
    private static $_battleField;
    private static $_totalShipSectors;
    private static $_totalHitShots = 0;
    private static $_totalShots = 0;

    public static function init(){
        self::$_adapter = ConsoleAdapter::getInstance();
        self::$_battleField = BattleField::getInstance(self::$_adapter);

        self::$_battleField->setShips(array(
            new Ship('Battleship', 5),
            new Ship('Destroyer', 4),
            new Ship('Destroyer', 4)
        ));

        try {
            self::$_totalShipSectors = self::$_battleField->positionShips();
        } catch (\LogicException $e) {
            self::$_adapter->writeLine('Error in configuration. Bye.');
            exit;
        }

        $lettersMap = self::$_battleField->getLettersMap();

        //Generating allowed commands
        self::_generateShotCodes($lettersMap, BattleField::ROWS, BattleField::COLS);
        self::$_allowedCommands[] = self::CHEAT_SHOW;

        self::_requestAndProcessUserInput();
    }

    private static function _requestAndProcessUserInput(){
        self::$_battleField->drawField();
        $userInput = self::$_adapter->requestInput('Enter coordinates (row, col), e.g. A5 = ');

        try {
            $result = self::_processUserInput($userInput);

            self::$_adapter->writeNewLine();

            if($result===BattleField::RESULT_MISS)
                self::_processMissShot();
            elseif($result===BattleField::RESULT_HIT)
                self::_processHitShot();
            elseif($result===BattleField::RESULT_SUNK)
                self::_processSunkShot();

        } catch (\UnexpectedValueException $e) {
            self::$_adapter->writeLine("*** Error ***");
        }

        if(self::$_totalHitShots < self::$_totalShipSectors)
            self::_requestAndProcessUserInput();
        else
            self::$_adapter->writeLine("Well done! You completed game in " . self::$_totalShots . " shots.");
    }

    private static function  _processUserInput($getUserInput){

        $getUserInput = trim(strtoupper($getUserInput));
        if(!in_array($getUserInput, self::$_allowedCommands)){
            throw new \UnexpectedValueException('Not allowed user input');
        }

        $result = false;

        if($getUserInput == self::CHEAT_SHOW){
            self::$_battleField->drawField(BattleField::MODE_TRANSPARENT_MAP);
        }else{
            preg_match("/[A-Z]{1,}/", $getUserInput, $rowLabel);
            preg_match("/[0-9]{1,}/", $getUserInput, $colLabel);

            $result = self::$_battleField->processShot($rowLabel[0], $colLabel[0]);
        }

        return $result;
    }

    private static function _processHitShot(){
        self::$_totalHitShots += 1;
        self::$_totalShots += 1;
        self::$_adapter->writeLine("Right on the target!");
    }

    private static function _processMissShot(){
        self::$_totalShots += 1;
        self::$_adapter->writeLine("Missed!");
    }

    private static function _processSunkShot(){
        self::$_totalShots += 1;
        self::$_totalHitShots += 1;
        self::$_adapter->writeLine("Sunk!");
    }


    private static function _generateShotCodes($rowLabels, $numRows, $numCols){
        for ($i = 1; $i <=$numRows; $i++){
            for ($j = 1; $j <=$numCols; $j++){
                self::$_allowedCommands[] = $rowLabels[$i - 1] . $j;
            }
        }
    }
}