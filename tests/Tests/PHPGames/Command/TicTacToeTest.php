<?php
/*

namespace Tests\PHPGames\Command;

use PHPGames\Command\TicTacToe;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class TicTacToeTest extends \PHPUnit_Framework_TestCase
{
    private $app;
    private $aiMock;

    public function setUp()
    {
        $this->aiMock = $this->getMockForAbstractClass('PHPGames\AI\AI');
        $this->app = new Application();
        $this->app->add(new TicTacToe($this->aiMock));
    }

    public function tearDown()
    {
        $this->aiMock = null;
        $this->app = null;
    }

    public function testSayHelloToDave()
    {
        $this->aiMock->expects($this->once())->method('run');
        $command = $this->app->find('tic_tac_toe');
        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream('0\\n'));

        $commandTester->execute([]);
        $display = $commandTester->getDisplay();

        $this->assertRegExp('/Hello Dave/', $display);
    }

    public function testSayHelloToUsername()
    {
        $this->aiMock->expects($this->once())->method('run');
        $command = $this->app->find('tic_tac_toe');
        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream('0\\n'));

        $commandTester->execute(['name' => 'Eduardo']);
        $display = $commandTester->getDisplay();

        $this->assertRegExp('/Hello Eduardo/', $display);
    }

    public function testAskForCoordinatesFromAListOfOptions()
    {
        $this->aiMock->expects($this->once())->method('run');
        $command = $this->app->find('tic_tac_toe');
        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream('0\\n'));

        $commandTester->execute([]);

        $display = $commandTester->getDisplay();
        $numberOfX = preg_match('/X/', $display);

        $this->assertCount(2, $numberOfX);
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}
*/
