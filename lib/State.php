<?php

/**
 * Store global variables about the games: iterations, scores
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class State
{
    /**
     * Incremented each times the script tries to play a move
     *
     * @var int
     */
    public static $iterations = 0;

    /**
     * Store the number of game-overs
     *
     * @var int
     */
    public static $gameOvers = 0;

    /**
     * When an iteration is skipped because a similar played iteration was found through symmetry
     *
     * @var int
     */
    public static $skippedSymmetry = 0;

    /**
     * Number of game played indexed by their scores
     *
     * @var array
     */
    public static $scores = [];

    /**
     * Timestamp for the beginning of the search
     *
     * @var int
     */
    public static $startedTime;

    /**
     * Storing all the moves already played, used to skip iterations through symmetry
     *
     * @var array
     */
    protected static $stateHashes = [];

    /**
     * Increment the scores by providing a game results with a number of remaining stones
     *
     * @param int $remainingStones
     */
    public static function incrementScore($remainingStones)
    {
        if (!isset(self::$scores[$remainingStones])) {
            self::$scores[$remainingStones] = 0;
        }
        self::$scores[$remainingStones]++;
    }

    /**
     * Check if the same state of the board was already played
     *
     * @param Board $board
     *
     * @return bool
     */
    public static function boardAlreadyPlayed(Board $board)
    {
        $hashes = $board->getHashes();
        foreach ($hashes as $hash) {
            if (array_key_exists($hash, self::$stateHashes)) {
                return true;
            }
        }

        self::$stateHashes[reset($hashes)] = 1;

        return false;
    }
}
