<?php
/**
 * HumanPlayerTest.php
 *
 * Ariel Ferrandini <arielferrandini@gmail.com>
 * 03/10/14
 */ 

namespace Tests\PHPGames\Game;

use PHPGames\Game\Move;
use PHPGames\Game\Player\HumanPlayer;

class HumanPlayerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetNextMove()
    {
        $player = new HumanPlayer('Pepe', 'X');
        $player->setNextMove(new Move(1, 1));

        $move = $player->getNextMove([]);

        $this->assertInstanceOf('PHPGames\Game\Move', $move);
    }
}
