<?php

namespace PHPGames\Game\Player;


use PHPGames\Game\Move;

interface Player
{
    /**
     * Returns the player name
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the next move.
     *
     * @param int $x
     * @param int $y
     * @return Move
     */
    public function getNextMove($board);

    public function move($board);
}
