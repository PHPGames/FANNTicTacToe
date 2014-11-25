<?php

namespace PHPGames\AI\ANN;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Trainer
{
    const AI = 1;
    const HUMAN = -1;
    const NEUTRAL = 0;
    const BAD_AI_MOVE = -1;
    const GOOD_AI_MOVE = 1;
    const NOT_POSSIBLE_MOVE = -1;
    const MATCH_AI_WON = 1;
    const MATCH_HUMAN_WON = -1;
    const MATCH_TIE = 0;

    private $trainingsFolder = '/tmp/';
    private $logger;
    
    private $recordedMoves  = [];
    private $recordedBoards = [];

    public function __construct()
    {
        $this->logger = new Logger('fann.trainer');
        $this->logger->pushHandler(new StreamHandler('/tmp/fann_player.log'), Logger::DEBUG);
    }

    public function createTraining(array $match, $matchResult)
    {
        $isAIfirstPlayer = $this->recordedMoves[0]['player'] === self::AI;
        $training = [];

        $finalMatch = $this->simulateLastMove($match);
        $moves = count($finalMatch);

        $startAt = $moves - 1;
        $endAt = $isAIfirstPlayer ? 1 : 0;

        if (!$isAIfirstPlayer && $matchResult == self::MATCH_HUMAN_WON) {
            $startAt -= 2;
        }

        $this->logger->info('START TRAINING AT ' . $startAt);

        $lastWeight = 0;
        for ($move = $startAt; $move >= $endAt; $move--) {
            $board = $finalMatch[$move];

            $isOddMove = $move % 2;

            if (!$isAIfirstPlayer && !$isOddMove && $move < 8) {
                $training[$move]['input'] = $board;
                $outputMove = $move + 1;
            } elseif ($isAIfirstPlayer && $isOddMove) {
                $training[$move]['input'] = $board;
                $outputMove = $move - 1;
            } else {
                continue;
            }

            $training[$move]['output'] = $this->createOutput($outputMove, $board, $matchResult, $lastWeight);

            if ($outputMove >= 0) {
                $recordedMove = $this->recordedMoves[$outputMove]['position'];
                $lastWeight   = $training[$move]['output'][$recordedMove];
            }
        }

        if ($isAIfirstPlayer) {
            $training[0]['input'] = [0, 0, 0, 0, 0, 0, 0, 0, 0];
            $training[0]['output'] = $this->createOutput(1, $training[0]['input'], $matchResult, $lastWeight);
        }

        ksort($training);

        $this->logger->debug('-------------------------');
        $this->logger->debug('---- CREATE TRAINING ----');
        $this->logger->debug('  MOVE   INPUT                      OUTPUT');
        foreach ($training as $move => $arrayTraining) {
            $input = str_pad('('.implode(',', $arrayTraining['input']).')', 24, ' ', STR_PAD_RIGHT);
            $output = str_pad('('.implode(',', $arrayTraining['output']).')', 46, ' ', STR_PAD_RIGHT);
            $this->logger->debug(sprintf('  [%d]  | %s | %s', $move, $input, $output));
        }

        return $training;
    }

    private function createOutput($move, $board, $matchResult, $lastWeight)
    {
        $alpha = $this->calculateAlpha();
        $recordedMove = $this->recordedMoves[$move]['position'];
        $isLastMove = ($move >= count($this->recordedMoves) - 2);

        $this->logger->error("IS LAST? $move " . ($isLastMove ? 'YES' : 'NO'));

        $neutral_weight = $weight = 0.5;

        $calculatedWeight = $weight + ($alpha * ($lastWeight - $weight));
        if ($isLastMove) {
            $playerMove = $this->recordedMoves[$move]['player'];
            $calculatedWeight = $this->calculateWeightForLastMove($playerMove, $matchResult, $calculatedWeight);
        }

        $output = [];
        foreach ($board as $position => $player) {
            $output[$position] = $neutral_weight;

            if ($recordedMove === $position) {
                $output[$position] = $calculatedWeight;
            }
        }

        return $output;
    }

    private function calculateAlpha()
    {
        $trainings = (int)file_get_contents('/tmp/trainings');
        return 1 / (1 + exp(-$trainings)) - 0.5; // * log($trainings + 10);
    }

    private function calculateWeightForLastMove($playerMove, $matchResult, $previous)
    {
        $calculatedWeight = $previous;
        if (self::AI === $playerMove && self::MATCH_AI_WON === $matchResult) {
            $calculatedWeight = 1;
        } elseif (self::AI === $playerMove && self::MATCH_HUMAN_WON === $matchResult) {
            $calculatedWeight = 0;
        } elseif (self::HUMAN === $playerMove && self::MATCH_HUMAN_WON === $matchResult) {
            $calculatedWeight = 1;
        } elseif (self::HUMAN === $playerMove && self::MATCH_AI_WON === $matchResult) {
            $calculatedWeight = 0;
        }

        return $calculatedWeight;
    }

    private function simulateLastMove($match)
    {
        $moves = count($match);

        if ($moves % 2 > 0) {
            $moves++;
            $match[$moves - 1] = $match[$moves - 2];
        }

        return $match;
    }

    public function train(FANN $ann, $training, $epochs)
    {
        $fileName = $this->createTrainingFile($training);
        $rto = $ann->trainOnFile($fileName, $epochs, 0, 0.0000001);
        $this->logger->debug('Trained: ' . ($rto ? 'yes' : 'no'));

        $this->logger->debug('-- END CREATE TRAINING --');
        $this->logger->debug('-------------------------');
    }

    private function createTrainingFile($training)
    {
        $fileName = $this->trainingsFolder . 'training_' . time() . '.dat';
        $trainFile = fopen($fileName, 'w+');
        $numberOfTrainings = count($training);
        $input = 9;
        $output = 9;

        fputs($trainFile, sprintf('%d %d %d' . PHP_EOL, $numberOfTrainings, $input, $output));
        foreach ($training as $move) {
            fputs($trainFile, implode(' ', $move['input']) . PHP_EOL);
            fputs($trainFile, implode(' ', $move['output']) . PHP_EOL);
        }
        fclose($trainFile);

        return $fileName;
    }

    public function recordMove($x, $y, $player)
    {
        $tempMoves = $this->recordedBoards;
        $newMove = array_pop($tempMoves);
        $index = $this->getBoardIndexFromXY($x, $y);

        if ($newMove === null) {
            $newMove = [0, 0, 0, 0, 0, 0, 0, 0, 0];
        }

        $this->logger->debug('record_last_move: ' . http_build_query($newMove, null, ', '));

        $newMove[$index] = $player;
        $this->recordedMoves[] = ['position' => $index, 'player' => $player];
        $this->recordedBoards[] = $newMove;
    }

    protected function getBoardIndexFromXY($x, $y)
    {
        $map = [[0, 1, 2], [3, 4, 5], [6, 7, 8]];

        return $map[$x][$y];
    }

    public function createTrainingFromRecord($winner)
    {
        return $this->createTraining($this->recordedBoards, $winner);
    }

    public function resetMovesRecord()
    {
        $this->recordedMoves  = [];
        $this->recordedBoards = [];
    }
}
