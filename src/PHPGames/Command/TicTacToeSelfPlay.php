<?php

namespace PHPGames\Command;

use PHPGames\AI\ANN\Trainer;
use PHPGames\Console\Helper\Board;
use PHPGames\Console\Helper\HAL;
use PHPGames\Exception\ExitGameException;
use PHPGames\Game\Game;
use PHPGames\Game\Player\FANNPlayer;
use PHPGames\Game\TicTacToeGame;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TicTacToeSelfPlay extends TicTacToe
{
    const HUMAN_PLAYER = 1;
    const COMPUTER_PLAYER = 2;

    protected function configure()
    {
        $this
            ->setName('tic_tac_toe_self_play')
            ->setDescription('Play Tic Tac Toe with a un-learned AI')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Player Name, otherwise, Dave',
                $this->humanName

            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->humanName = $input->getArgument('name');

        $hal = new HAL($output);
        $hal->sayHello();

        $p1 = new FANNPlayer($this->ai, 'X');
        $p2 = new FANNPlayer($this->ai, 'O');

        $board = new Board($output, $this->getApplication()->getTerminalDimensions(), 3);
        $game = new TicTacToeGame($p1, $p2);


        $playerMove = 0;
        $this->logger->debug('BEGIN');
        try {
            $games = 1000000;
            while (true) {
                if (!$playerMove) {
                    $this->logger->debug('----- P1 MOVE -----');
                    $this->computerP1Move($p1, $game, $board);
                    $this->logger->debug('----- P1 MOVE -----');
                } else {
                    $this->logger->debug('----- P2 MOVE -----');
                    $this->computerMove($p2, $game, $board);
                    $this->logger->debug('----- P2 MOVE -----');
                }

                if ($game->isFinished()) {
                    switch ($game->getResult()) {
                        case TicTacToeGame::RESULT_PLAYER_1_WON:
                            $this->logger->debug('Result: Player 1 won.');
                            break;
                        case TicTacToeGame::RESULT_PLAYER_2_WON:
                            $this->logger->debug('Result: Player 2 won.');
                            break;
                        case TicTacToeGame::RESULT_TIE:
                            $this->logger->debug('Result: Tie.');
                            break;
                    }

                    $game = $this->handleEndOfMatch($game, $p1, $p2, $board);
                    $games--;
                    if (0 == $games) {
                        throw new ExitGameException("End of games");
                    }
                    $this->logger->debug('---------------- END GAME ------------------');
                } else {
                    $playerMove = !$playerMove;
                }

            }
        } catch (\Exception $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
        }

        $this->ai->save();
    }

    protected function computerP1Move($p1, $game, $board)
    {
        $game->player1Move();
        $p1Move = $p1->getLastMove();
        $board->updateGame($p1Move->getX(), $p1Move->getY(), self::HUMAN_PLAYER);
        $this->trainer->recordMove($p1Move->getX(), $p1Move->getY(), Trainer::HUMAN);
    }


    protected function handleEndOfMatch(Game $game, FANNPlayer $p1, FANNPlayer $p2, Board $board)
    {
        $this->trainAI($this->gameStatus[$game->getResult()]);
        $p1->resetBoard();
        $p2->resetBoard();
        $newGame = $this->newGame($board, $p1, $p2);

        return $newGame;
    }
}
