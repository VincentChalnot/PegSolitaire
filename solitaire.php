<?php

require_once __DIR__.'/lib/Board.php';
require_once __DIR__.'/lib/Coordinate.php';
require_once __DIR__.'/lib/Debug.php';
require_once __DIR__.'/lib/PlayableStone.php';
require_once __DIR__.'/lib/State.php';


$board = new Board();
State::$startedTime = time();

echo "Starting search...\n";

$playableStones = $board->findPlayableStones();

foreach ($playableStones as $playableStone) {
    $board->play($playableStone);
}

echo "End of test\n";
Debug::displayState();
