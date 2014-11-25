<?php
/**
 * MoveTest.php
 *
 * Ariel Ferrandini <arielferrandini@gmail.com>
 * 03/10/14
 */ 

namespace Tests\PHPGames\Game;


use PHPGames\Game\Move;

class MoveTest extends \PHPUnit_Framework_TestCase
{
    public function testValidMoves()
    {
        foreach ($this->getValidMoves() as $move) {
            $moveObject = new Move($move[0], $move[1]);

            $this->assertEquals($move[0], $moveObject->getX());
            $this->assertEquals($move[1], $moveObject->getY());
        }
    }

    public function testInvalidMoves()
    {
        foreach ($this->getInvalidMoves() as $move) {
            try {
                new Move($move[0], $move[1]);
            } catch (\Exception $e) {
                $this->assertEquals(sprintf('The move [%d,%d] is not valid.', $move[0], $move[1]), $e->getMessage());
            }
        }
    }

    private function getValidMoves()
    {
        return array(
            array(0, 0),
            array(0, 1),
            array(0, 2),
            array(1, 0),
            array(1, 1),
            array(1, 2),
            array(2, 0),
            array(2, 1),
            array(2, 2),
        );
    }

    private function getInvalidMoves()
    {
        return array(
            array(-1, 0),
            array(0, -1),
            array(-1, -1),
            array(3, 0),
            array(0, 3),
            array(3, 3)
        );
    }
}
 