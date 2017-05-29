<?php

/**
 * Represents a fixed state of the board, each iteration has a different board
 * 
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class Board
{
    const BOARD_SIZE = 3;
    const SIDE_SIZE = 2;
    const STONE = 1;
    const EMPTY_SLOT = 0;

    /** @var array */
    public $board;

    /**
     * Initialize board with stones and empty slot in the center
     */
    public function __construct()
    {
        for ($x = -self::BOARD_SIZE; $x <= self::BOARD_SIZE; $x++) {
            for ($y = -self::BOARD_SIZE; $y <= self::BOARD_SIZE; $y++) {
                if (abs($x) >= self::SIDE_SIZE && abs($y) >= self::SIDE_SIZE) {
                    continue;
                }
                $this->board[$x][$y] = self::STONE;
            }
        }

        $this->board[0][0] = self::EMPTY_SLOT;
    }


    /**
     * Actually play a move on the board, duplicating it's state for branching
     * If no more playable stones, either exit because it's a win or return the number of remaining stones
     *
     * @param PlayableStone   $playableStone
     * @param PlayableStone[] $playStack
     */
    public function play(PlayableStone $playableStone, array $playStack = [])
    {
        $board = clone $this; // Clone the last state of the board

        State::$iterations++;
        $board->playStone($playableStone);

        if (State::boardAlreadyPlayed($board)) {
            State::$skippedSymmetry++;

            return;
        }

        $playStack[] = $playableStone;

        // Win
        if ($board->gameWon()) {
            State::incrementScore(1);
            Debug::displayState(1);
            Debug::drawReplay($playStack);

            return;
        }

        // Game Over
        $newPlayableStones = $board->findPlayableStones();
        if (0 === count($newPlayableStones)) {
            State::$gameOvers++;
            $remainingStones = $board->countStones();
            State::incrementScore($remainingStones);
            if ($remainingStones < 3) { // Change this to set the minimum score to display the result
                Debug::drawBoard($board);
                Debug::displayState($remainingStones);
            }

            return;
        }

        foreach ($newPlayableStones as $newPlayableStone) {
            $board->play($newPlayableStone, $playStack);
        }
    }


    /**
     * @param Coordinate $coordinate
     *
     * @return bool
     */
    public function isInBoard(Coordinate $coordinate)
    {
        return array_key_exists($coordinate->x, $this->board)
        && array_key_exists($coordinate->y, $this->board[$coordinate->x]);
    }

    /**
     * @param Coordinate $coordinate
     *
     * @return bool
     */
    public function hasStone(Coordinate $coordinate)
    {
        if (!$this->isInBoard($coordinate)) {
            return false;
        }

        return self::STONE === $this->board[$coordinate->x][$coordinate->y];
    }

    /**
     * Count remaining stones on board
     *
     * @return int
     */
    public function countStones()
    {
        $stoneCount = 0;
        foreach ($this->board as $x => $row) {
            /** @var array $row */
            foreach ($row as $y => $stone) {
                if (self::STONE === $stone) {
                    $stoneCount++;
                }
            }
        }

        return $stoneCount;
    }

    /**
     * Check the win condition: only one remaining stone
     *
     * @return bool
     */
    public function gameWon()
    {
        return $this->countStones() === 1;
    }

    /**
     * Find all empty slots
     *
     * @return Coordinate[]
     */
    public function findEmptySlots()
    {
        $emptySlots = [];
        foreach ($this->board as $x => $row) {
            /** @var array $row */
            foreach ($row as $y => $stone) {
                if (self::EMPTY_SLOT === $stone) {
                    $emptySlots[] = new Coordinate($x, $y);
                }
            }
        }

        return $emptySlots;
    }

    /**
     * Find all playable stones
     *
     * @return PlayableStone[]
     */
    public function findPlayableStones()
    {
        $playableStones = [];
        foreach ($this->findEmptySlots() as $empty) {
            foreach (Coordinate::$directions as $directionName => $direction) {
                $removable = new Coordinate(
                    $empty->x + $direction[0],
                    $empty->y + $direction[1]
                );
                if (!$this->hasStone($removable)) {
                    continue;
                }
                $playable = new Coordinate(
                    $removable->x + $direction[0],
                    $removable->y + $direction[1]
                );
                if (!$this->hasStone($playable)) {
                    continue;
                }
                $playableStones[] = new PlayableStone($playable, $removable, $empty, $directionName);
            }
        }

        return $playableStones;
    }

    /**
     * Update the state of the board with by applying a playable move, no checks are performed
     *
     * @param PlayableStone $playableStone
     */
    public function playStone(PlayableStone $playableStone)
    {
        $this->board[$playableStone->playable->x][$playableStone->playable->y] = self::EMPTY_SLOT;
        $this->board[$playableStone->removable->x][$playableStone->removable->y] = self::EMPTY_SLOT;
        $this->board[$playableStone->empty->x][$playableStone->empty->y] = self::STONE;
    }

    /**
     * Return all board variations through rotation and symmetry
     *
     * @return Board[]
     */
    public function getVariations()
    {
        $rotate1 = $this->rotate();
        $rotate2 = $rotate1->rotate();
        $rotate3 = $rotate2->rotate();
        $reversed = $this->reverse();
        $reversed2 = $reversed->rotate();
        $reversed3 = $reversed2->rotate();
        $reversed4 = $reversed3->rotate();

        return [
            $rotate1,
            $rotate2,
            $rotate3,
            $reversed,
            $reversed2,
            $reversed3,
            $reversed4
        ];
    }

    /**
     * Get the current hash plus all it's variations
     *
     * @return array
     */
    public function getHashes()
    {
        $hashes = [
            $this->getHash(),
        ];

        foreach ($this->getVariations() as $variation) {
            $hashes[] = $variation->getHash();
        }

        return $hashes;
    }

    /**
     * Hash the current state of the board to easily identify already played states
     *
     * @return string
     */
    public function getHash()
    {
        $string = '';
        foreach ($this->board as $row) {
            /** @var array $row */
            $string .= implode('', $row);
        }

        return md5($string);
    }

    /**
     * Return a new board rotated by 90°
     *
     * @return Board
     */
    public function rotate()
    {
        $board = clone $this;
        foreach ($this->board as $x => $row) {
            /** @var array $row */
            foreach ($row as $y => $item) {
                $board->board[-$y][$x] = $item;
            }
        }

        return $board;
    }

    /**
     * Mirror the board along it's x axis
     *
     * @return Board
     */
    public function reverse()
    {
        $board = clone $this;
        foreach ($this->board as $x => $row) {
            /** @var array $row */
            foreach ($row as $y => $item) {
                $board->board[-$x][$y] = $item;
            }
        }

        return $board;
    }
}
