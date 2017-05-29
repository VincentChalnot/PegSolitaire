<?php

/**
 * Represents a position on the board, it can be a stone, a empty slot or even a position outside the board
 * 
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class Coordinate
{
    const DIR_NORTH = 'N';
    const DIR_SOUTH = 'S';
    const DIR_EAST = 'E';
    const DIR_WEST = 'W';

    /** @var int */
    public $x;

    /** @var int */
    public $y;

    /** @var array */
    public static $directions = [
        self::DIR_NORTH => [1, 0],
        self::DIR_SOUTH => [-1, 0],
        self::DIR_EAST => [0, -1],
        self::DIR_WEST => [0, 1],
    ];

    /**
     * @param int $x
     * @param int $y
     */
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @param Coordinate $coordinate
     *
     * @return bool
     */
    public function equals(Coordinate $coordinate)
    {
        return $coordinate->x === $this->x && $coordinate->y === $this->y;
    }
}
