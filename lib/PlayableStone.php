<?php

/**
 * Stores a potential move
 * 
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class PlayableStone
{
    /** @var Coordinate */
    public $playable;

    /** @var Coordinate */
    public $removable;

    /** @var Coordinate */
    public $empty;

    /** @var string */
    public $direction;

    /**
     * @param Coordinate $playable
     * @param Coordinate $removable
     * @param Coordinate $empty
     * @param string     $direction
     */
    public function __construct(Coordinate $playable, Coordinate $removable, Coordinate $empty, $direction)
    {
        $this->playable = $playable;
        $this->removable = $removable;
        $this->empty = $empty;
        $this->direction = $direction;
    }
}
