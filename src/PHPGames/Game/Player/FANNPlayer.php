<?php

namespace PHPGames\Game\Player;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPGames\AI\AI;
use PHPGames\Game\Move;

class FANNPlayer extends AbstractPlayer
{
    protected $name = 'HAL 1000';

    /**
     * @var AI
     */
    private $ai;

    private $lastMove;
    private $AIBoard = [0, 0, 0, 0, 0, 0, 0, 0, 0];
    private $logger;

    /**
     * @param AI $ai
     * @param    $token
     * @throws \PHPGames\Exception\InvalidPlayerNameException
     */
    public function __construct(AI $ai, $token)
    {
        $this->ai = $ai;
        $this->logger = new Logger('FANN.Player');
        $this->logger->pushHandler(new StreamHandler('/tmp/fann_player.log'), Logger::DEBUG);
        parent::__construct($this->name, $token);
    }

    public function resetBoard()
    {
        $this->AIBoard = [0, 0, 0, 0, 0, 0, 0, 0, 0];
    }

    public function getLastMove()
    {
        return $this->lastMove;
    }

    public function getNextMove($board)
    {
        $this->logger->info('-----------------------');
        $this->logger->info('---- START AI MOVE ----');

        $this->updateFromMatrixBoard($board);

        $this->logger->debug(sprintf('>>> fann_input: %s', http_build_query($this->AIBoard, null, ', ')));

        $aiOutput = $this->ai->run($this->AIBoard);

        arsort($aiOutput);

        $this->logger->debug(sprintf('<<< fann_output:'));

        foreach ($aiOutput as $index => $value) {
            $this->logger->debug(sprintf('   [%d] = %.30f', $index, $value));
        }

        $this->logger->info('>>> get first valid move');

        foreach ($aiOutput as $index => $value) {
            $isValid = $this->validateMove($index);
            $moveIndex = $index;
            if ($isValid) {
                break;
            }
        }

        $coords = $this->getBoardXYFromIndex($moveIndex);

        $this->lastMove = new Move($coords[0], $coords[1]);
        $this->logger->info(sprintf('--> do_move: %d,%d', $this->lastMove->getX(), $this->lastMove->getY()));

        $this->logger->info('----- END AI MOVE -----');
        $this->logger->info('-----------------------');

        return $this->lastMove;
    }

    protected function getBoardXYFromIndex($index)
    {
        $map = [
            0 => [0, 0],
            1 => [0, 1],
            2 => [0, 2],
            3 => [1, 0],
            4 => [1, 1],
            5 => [1, 2],
            6 => [2, 0],
            7 => [2, 1],
            8 => [2, 2]
        ];

        return $map[$index];
    }

    protected function getBoardIndexFromXY($x, $y)
    {
        $map = [[0, 1, 2], [3, 4, 5], [6, 7, 8]];

        return $map[$x][$y];
    }

    public function move($board)
    {
        $board[$this->lastMove->getX()][$this->lastMove->getY()] = $this->token;
        return $board;
    }

    private function updateFromMatrixBoard($board)
    {
        foreach ($board as $r => $row) {
            foreach ($row as $c => $col) {
                if ($this->token !== $col && $col !== 0) {
                    //human
                    $this->AIBoard[$this->getBoardIndexFromXY($r, $c)] = -1;
                } elseif ($this->token === $col && $col !== 0) {
                    $this->AIBoard[$this->getBoardIndexFromXY($r, $c)] = 1;
                }
            }
        }
    }

    private function validateMove($moveIndex)
    {
        if ($this->AIBoard[$moveIndex] == 0) {
            $this->logger->info(sprintf('  ✔  valid_move: %d', $moveIndex));

            return true;
        }

        $this->logger->info(sprintf('  ✘  invalid_move: %d', $moveIndex));

        return false;
    }
}
