<?php

/**
 * Used to draw the board in ASCII or to output informations about the game
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class Debug
{
    /**
     * Draw board for debugging
     *
     * @param Board $board
     */
    public static function drawBoard(Board $board)
    {
        echo "\n";
        for ($x = -Board::BOARD_SIZE; $x <= Board::BOARD_SIZE; $x++) {
            for ($y = -Board::BOARD_SIZE; $y <= Board::BOARD_SIZE; $y++) {
                $coordinate = new Coordinate($x, $y);
                if ($board->isInBoard($coordinate)) {
                    echo $board->hasStone($coordinate) ? 'o' : '·';
                } else {
                    echo ' ';
                }
            }
            echo "\n";
        }
    }

    /**
     * @param Board           $board
     * @param PlayableStone[] $playableStones
     */
    public static function drawPlayableStones(Board $board, array &$playableStones)
    {
        echo "\n";
        for ($x = -Board::BOARD_SIZE; $x <= Board::BOARD_SIZE; $x++) {
            for ($y = -Board::BOARD_SIZE; $y <= Board::BOARD_SIZE; $y++) {
                $coordinate = new Coordinate($x, $y);
                if ($board->isInBoard($coordinate)) {
                    $hasPlayableStone = false;
                    foreach ($playableStones as $playableStone) {
                        if ($coordinate->equals($playableStone->playable)) {
                            echo $playableStone->direction;
                            $hasPlayableStone = true;
                            break;
                        }
                    }
                    if (!$hasPlayableStone) {
                        echo $board->hasStone($coordinate) ? 'o' : '·';
                    }
                } else {
                    echo ' ';
                }
            }
            echo "\n";
        }
    }

    /**
     * @param int $remainingStones
     */
    public static function displayState($remainingStones = null)
    {
        if ($remainingStones) {
            if (1 === $remainingStones) {
                echo " ============= WIN ! =============\n\n";
            } else {
                echo "Game Over: {$remainingStones} remaining stones\n";
            }
        }
        echo 'Iterations: '.State::$iterations.' - Games played: '.State::$gameOvers;
        echo ' - Skipped through symmetry: '.State::$skippedSymmetry."\n";
        echo "Game Over stats:\n";
        foreach (State::$scores as $remaining => $count) {
            echo "  - {$count} games with {$remaining} stones\n";
        }
        echo 'Elapsed time: '.(time() - State::$startedTime)."s\n";
        echo "\n\n";
    }

    /**
     * @param PlayableStone[] $playStack
     */
    public static function drawReplay(array $playStack)
    {
        $replayBoard = new Board();
        Debug::drawBoard($replayBoard);
        foreach ($playStack as $playedStone) {
            $replayBoard->playStone($playedStone);

            /** @noinspection DisconnectedForeachInstructionInspection */
            Debug::drawBoard($replayBoard);
        }
    }
}
