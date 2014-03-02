Main Documentation
==================

Overview
--------

BattleShips is a simple console application which implements logic of the game with same name. This app is written in php
object-oriented model with different level of abstraction presented of main game controller, output adapter and
entity objects with their corresponding actions. Game can be easily extended to web and other UIs.

Class structure
---------------

ConsoleController - the main game controller

ConsoleAdapter - adapter for the console UI

BattleField - the class of the main game entity - the field

Ship - the class of the ship entity


Additional notes
----------------

Right now the game is written with static size of the field and static number of ships but that constants can be easily
adjusted to allow bigger field with more and different ships. To be achieved this - need to adjust constants ROWS and
COLS in BattleField class or change them to properties that loads from configuration on class instantiation. Same about
the ships created and passed in ConsoleController::init() .

