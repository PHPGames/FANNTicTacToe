<?php

namespace PHPGames;

use PHPGames\AI\ANN\Trainer;
use PHPGames\AI\TicTacToeAI;
use PHPGames\Command\TicTacToeSelfPlay;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ANNTicTacToeSelfPlay extends Application
{
    protected function getCommandName(InputInterface $input)
    {
        return 'tic_tac_toe_self_play';
    }

    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $file = '/tmp/self_play_ann.net';
        $ai = new TicTacToeAI(null, $file);

        $logger = new Logger('ANNTicTacToeSelfPlay');
        $logger->pushHandler(new StreamHandler('/tmp/machine_vs_machine.log'), Logger::DEBUG);
        $trainer = new Trainer($ai);

        $defaultCommands[] = new TicTacToeSelfPlay($ai, $trainer, $logger);

        return $defaultCommands;
    }

    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}
