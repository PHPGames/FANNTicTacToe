<?php
/**
 * AbstractPlayerTest.php
 *
 * Ariel Ferrandini <arielferrandini@gmail.com>
 * 03/10/14
 */
namespace Tests\PHPGames\Game;

class AbstractPlayerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $stub = $this->getMockForAbstractClass(
            'PHPGames\Game\Player\AbstractPlayer',
            array('name' => 'Player 1', 'token' => 'X')
        );

        $stub->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('Player 1'))
        ;

        $this->assertEquals('Player 1', $stub->getName());
    }

    /**
     * @expectedException \PHPGames\Exception\InvalidPlayerNameException
     */
    public function testInvalidNameException()
    {
        $this->getMockForAbstractClass('PHPGames\Game\Player\AbstractPlayer', array('name' => '', 'token' => 'X'));
    }
}
