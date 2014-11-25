<?php

namespace PHPGames;

use PHPGames\AI\ANN\Trainer;
use PHPGames\AI\TicTacToeAI;
use PHPGames\Command\TicTacToe;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ANNTicTacToe extends Application
{
    protected function getCommandName(InputInterface $input)
    {
        return 'tic_tac_toe';
    }

    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $file = '/tmp/ann.net';
        $ai = new TicTacToeAI(null, $file);

        $logger = new Logger('ANNTicTacToe');
        $logger->pushHandler(new StreamHandler('/tmp/human_vs_machine.log'), Logger::DEBUG);
        $trainer = new Trainer($ai);

        $defaultCommands[] = new TicTacToe($ai, $trainer, $logger);

        return $defaultCommands;
    }

    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}
