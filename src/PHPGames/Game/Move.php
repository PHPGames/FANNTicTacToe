<?php
/**
 * Move.php
 *
 * Ariel Ferrandini <arielferrandini@gmail.com>
 * 03/10/14
 */ 

namespace PHPGames\Game;


use PHPGames\Exception\InvalidMoveException;

class Move
{
    /**
     * @var int
     */
    private $x;

    /**
     * @var int
     */
    private $y;

    /**
     * @param int $x
     * @param int $y
     * @throws InvalidMoveException
     */
    public function __construct($x, $y)
    {
        if ($x<0 || $x>2 || $y<0 || $y>2) {
            throw new InvalidMoveException($x, $y);
        }

        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }
}
 