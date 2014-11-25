<?php
/**
 * HumanPlayer.php
 *
 * Ariel Ferrandini <arielferrandini@gmail.com>
 * 03/10/14
 */ 

namespace PHPGames\Game\Player;


use PHPGames\Game\Move;

class HumanPlayer extends AbstractPlayer
{
    private $nextMove;

    /**
     * Returns the next move.
     *
     * @return Move
     */
    public function getNextMove ($board)
    {
        return $this->nextMove;
    }

    public function move($board)
    {
        $board[$this->nextMove->getX()][$this->nextMove->getY()] = $this->token;
        return $board;
    }

    public function setNextMove(Move $move)
    {
        $this->nextMove = $move;
    }
}
