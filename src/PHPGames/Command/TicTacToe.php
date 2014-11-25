<?php

namespace PHPGames\Command;

use PHPGames\AI\AI;
use PHPGames\AI\ANN\Trainer;
use PHPGames\Console\Helper\Board;
use PHPGames\Console\Helper\HAL;
use PHPGames\Exception\ExitGameException;
use PHPGames\Exception\InvalidMoveException;
use PHPGames\Game\Game;
use PHPGames\Game\Move;
use PHPGames\Game\Player\FANNPlayer;
use PHPGames\Game\Player\HumanPlayer;
use PHPGames\Game\Player\Player;
use PHPGames\Game\TicTacToeGame;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Monolog\Logger;

class TicTacToe extends Command
{
    const HUMAN_PLAYER = 1;
    const COMPUTER_PLAYER = 2;

    private $input;
    private $output;

    protected $humanName = 'Dave';
    protected $gameStatus = [
        TicTacToeGame::RESULT_PLAYER_2_WON => Trainer::MATCH_AI_WON,
        TicTacToeGame::RESULT_PLAYER_1_WON => Trainer::MATCH_HUMAN_WON,
        TicTacToeGame::RESULT_TIE => Trainer::MATCH_TIE
    ];
    protected $ai;
    protected $logger;
    protected $trainer;

    public function __construct(AI $ai, Trainer $trainer, Logger $logger, $name = null)
    {
        parent::__construct($name);
        $this->ai = $ai;
        $this->logger = $logger;
        $this->trainer = $trainer;
    }

    protected function configure()
    {
        $this
            ->setName('tic_tac_toe')
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
        $this->input = $input;
        $this->output = $output;

        $this->humanName = $input->getArgument('name');

        $hal = new HAL($output);
        $hal->sayHello();
        $helper = $this->getHelper('question');
        $helper->ask($this->input, $this->output, new ConfirmationQuestion(''));

        $p1 = new HumanPlayer($this->humanName, 'X');
        $p2 = new FANNPlayer($this->ai, 'O');

        $board = new Board($output, $this->getApplication()->getTerminalDimensions(), 3);
        $game = new TicTacToeGame($p1, $p2);


        $question = $this->createCoordinatesQuestion();
        $playerMove = 0;
        $this->logger->info('--------------- START GAME ------------------');
        try {
            while (true) {
                if (!$playerMove) {
                    $this->handleHumanMove($question, $p1, $game, $board);
                } else {
                    $this->computerMove($p2, $game, $board);
                }

                if ($game->isFinished()) {
                    $this->logger->info('---------------- END GAME ------------------');
                    $game = $this->handleEndOfMatch($game, $p2, $p1, $board);
                } else {
                    $playerMove = !$playerMove;
                }
            }
        } catch (ExitGameException $e ) {
            $this->output->writeln("<question>I knew it!</question>");
        } catch (\Exception $e) {
            $this->output->writeln('Unexpected <error>' . $e->getMessage() . '</error>');
        } finally {
            $this->ai->save();
        }
    }

    private function humanMove($p1, $coord, $game, $board)
    {
        $p1->setNextMove(new Move((int)$coord[0], (int)$coord[1]));
        $game->player1Move();

        $this->logger->debug(sprintf('  Human move (%s,%s)', $coord[0], $coord[1]));

        $board->updateGame($coord[0], $coord[1], self::HUMAN_PLAYER);
        $this->trainer->recordMove($coord[0], $coord[1], Trainer::HUMAN);
    }

    protected function computerMove($p2, $game, $board)
    {
        $game->player2Move();
        $p2Move = $p2->getLastMove();

        $this->logger->debug(sprintf('  AI    move (%s,%s)', $p2Move->getX(), $p2Move->getY()));

        $board->updateGame($p2Move->getX(), $p2Move->getY(), self::COMPUTER_PLAYER);
        $this->trainer->recordMove($p2Move->getX(), $p2Move->getY(), Trainer::AI);
    }

    protected function handleEndOfMatch(Game $game, FANNPlayer $p2, HumanPlayer $p1, Board $board)
    {
        $this->trainAI($this->gameStatus[$game->getResult()]);
        $p2->resetBoard();
        $newGame = $this->newGame($board, $p1, $p2);
        $this->writeResult($game);
        $this->askToPlayAgain();

        return $newGame;
    }

    protected function trainAI($gameStatus)
    {
        $training = $this->trainer->createTrainingFromRecord($gameStatus);
        $this->trainer->train($this->ai, $training, 64);//50000
    }

    private function askToPlayAgain()
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Play again? <comment>(y/n)</comment><info>[y]</info>', true);
        if (!$helper->ask($this->input, $this->output, $question)) {
            throw new ExitGameException();
        }
    }

    private function handleHumanMove(Question $question, Player $p1, Game $game, Board $board)
    {
        $helper = $this->getHelper('question');
        $count = 0;
        while (true) {
            $humanMove = $helper->ask($this->input, $this->output, $question);
            $coord     = explode(',', $humanMove);
            try {
                $this->humanMove($p1, $coord, $game, $board);
            } catch (InvalidMoveException $e) {
                $this->writeInvalidMoveMessage($count, $humanMove);
                $count++;
                continue;
            }
            break;
        }
    }

    private function writeResult(Game $game)
    {
        $message = 'Winner was %s';

        if ($game->getResult() === TicTacToeGame::RESULT_PLAYER_1_WON) {
            $message = sprintf($message, 'Player 1');
        } elseif ($game->getResult() === TicTacToeGame::RESULT_PLAYER_2_WON) {
            $message = sprintf($message, 'Player 2');
        } else {
            $message = sprintf($message, 'Tie!');
        }

        $this->logger->info($message);

        $this->output->writeln($message);
    }

    private function writeInvalidMoveMessage($retries, $humanMove)
    {
        $message = sprintf("<comment>Move %s is an invalid move, choose another one</comment>", $humanMove);
        if ($retries > 2) {
            $message = sprintf("<comment>Are you kidding me, again %s?</comment>", $humanMove);
        }

        $this->output->writeln($message);
    }

    private function createCoordinatesQuestion()
    {
        $question = new Question('Enter the coordinates <info>(e.g: 0,0)</info>: ', null);
        $question->setValidator(
            function ($answer) {
                if (!preg_match('/\d,\d/', $answer)) {
                    throw new \RuntimeException('Invalid format, they should be in the format x,y');
                }
                return $answer;
            }
        );

        $question->setAutocompleterValues(['0,0', '0,1', '0,2', '1,0', '1,1', '1,2', '2,0', '2,1', '2,2']);

        return $question;
    }

    protected function newGame($board, $p1, $p2)
    {
        $board->initGame();
        $game = new TicTacToeGame($p1, $p2);
        $this->ai->save('/tmp/ann.net');
        $this->trainer->resetMovesRecord();

        return $game;
    }
}
