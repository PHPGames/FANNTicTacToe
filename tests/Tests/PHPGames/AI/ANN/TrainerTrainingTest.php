<?php

namespace Tests\PHPGames\AI\ANN;

use PHPGames\AI\ANN\Trainer;

class TrainerTrainingTest extends \PHPUnit_Framework_TestCase
{
    public function testTrainerTrainsWhenHumanWonAndPlayedFirst()
    {
        $trainer = new Trainer();
        $match = [
            [-1, 0, 0, 0, 0, 0, 0, 0, 0],
            [-1, 0, 0, 0, 0, 0, 0, 0, 1],
            [-1, -1, 0, 0, 0, 0, 0, 0, 1],
            [-1, -1, 0, 1, 0, 0, 0, 0, 1],
            [-1, -1, -1, 1, 0, 0, 0, 0, 1],
        ];
        //lost match
        $expectedTraining = [
            0 => [
                'input' => [-1, 0, 0, 0, 0, 0, 0, 0, 0],
                'output' => [1, 0, 0, 0, 0, 0, 0, 0, -1]
            ],
            2 => [
                'input' => [-1, -1, 0, 0, 0, 0, 0, 0, 1],
                'output' => [1, 1, 0, -1, 0, 0, 0, 0, -1]
            ],
            4 => [
                'input' => [-1, -1, -1, 1, 0, 0, 0, 0, 1],
                'output' => [1, 1, 1, -1, 0, 0, 0, 0, -1]
            ],
        ];
        $training = $trainer->createTraining($match, Trainer::MATCH_HUMAN_WON);

        $this->assertEquals($expectedTraining, $training);
    }

    public function testTrainerTrainsWhenHumanWonAndPlayedSecond()
    {
        $trainer = new Trainer();
        $match = [
            [1, 0, 0, 0, 0, 0, 0, 0, 0],
            [1, 0, 0, 0, 0, 0, 0, 0, -1],
            [1, 0, 0, 1, 0, 0, 0, -1, -1],
            [1, 0, 0, 1, 0, 0, -1, -1, -1],
        ];
        //lost match
        $expectedTraining = [
            0 => [
                'input' => [1, 0, 0, 0, 0, 0, 0, 0, 0],
                'output' => [-1, 0, 0, 0, 0, 0, 0, 0, 1]
            ],
            2 => [
                'input' => [1, 0, 0, 1, 0, 0, 0, -1, -1],
                'output' => [-1, 0, 0, -1, 0, 0, 1, 1, 1]
            ],
        ];
        $training = $trainer->createTraining($match, Trainer::MATCH_HUMAN_WON);

        $this->assertEquals($expectedTraining, $training);
    }

    public function testTrainerTrainsWhenHumanLostAndPlayedSecond()
    {
        $trainer = new Trainer();
        $match = [
            [1, 0, 0, 0, 0, 0, 0, 0, 0],
            [1, 0, 0, 0, 0, 0, 0, 0, -1],
            [1, 0, 0, 1, 0, 0, 0, -1, -1],
            [1, 0, 0, 1, 0, -1, 1, -1, -1],
        ];
        $expectedTraining = [
            0 => [
                'input' => [1, 0, 0, 0, 0, 0, 0, 0, 0],
                'output' => [1, 0, 0, 0, 0, 0, 0, 0, -1]
            ],
            2 => [
                'input' => [1, 0, 0, 1, 0, 0, 0, -1, -1],
                'output' => [1, 0, 0, 1, 0, -1, 1, -1, -1]
            ],
        ];
        $training = $trainer->createTraining($match, Trainer::MATCH_AI_WON);

        $this->assertEquals($expectedTraining, $training);
    }

    public function testTrainerTrainsWhenHumanLostAndPlayedFirst()
    {
        $trainer = new Trainer();
        $match = [
            [-1, 0, 0, 0, 0, 0, 0, 0, 0],
            [-1, 0, 0, 0, 0, 0, 0, 0, 1],
            [-1, -1, 0, 0, 0, 0, 0, 0, 1],
            [-1, -1, 1, 0, 0, 0, 0, 0, 1],
            [-1, -1, 1, -1, 0, 0, 0, 0, 1],
            [-1, -1, 1, -1, 0, 1, 0, 0, 1],
        ];

        $expectedTraining = [
            0 => [
                'input' => [-1, 0, 0, 0, 0, 0, 0, 0, 0],
                'output' => [-1, 0, 0, 0, 0, 0, 0, 0, 1]
            ],
            2 => [
                'input' => [-1, -1, 0, 0, 0, 0, 0, 0, 1],
                'output' => [-1, -1, 1, 0, 0, 0, 0, 0, 1]
            ],
            4 => [
                'input' => [-1, -1, 1, -1, 0, 0, 0, 0, 1],
                'output' => [-1, -1, 1, -1, 0, 1, 0, 0, 1]
            ],
        ];
        $training = $trainer->createTraining($match, Trainer::MATCH_AI_WON);

        $this->assertEquals($expectedTraining, $training);
    }

    public function testTrainerTrainsWhenTie()
    {
        $trainer = new Trainer();
        $match = [
            [-1, 0, 0, 0, 0, 0, 0, 0, 0],
            [-1, 0, 0, 0, 0, 0, 0, 0, 1],

            [-1, -1, 0, 0, 0, 0, 0, 0, 1],
            [-1, -1, 0, 1, 0, 0, 0, 0, 1],

            [-1, -1, 0, 1, -1, 0, 0, 0, 1],
            [-1, -1, 1, 1, -1, 0, 0, 0, 1],

            [-1, -1, 1, 1, -1, -1, 0, 0, 1],
            [-1, -1, 1, 1, -1, -1, 0, 1, 1],

            [-1, -1, 1, 1, -1, -1, -1, 1, 1],
        ];

        $expectedTraining = [
            0 => [
                'input' => [-1, 0, 0, 0, 0, 0, 0, 0, 0],
                'output' => [1, 0, 0, 0, 0, 0, 0, 0, 1]
            ],
            2 => [
                'input' => [-1, -1, 0, 0, 0, 0, 0, 0, 1],
                'output' => [1, 1, 0, 1, 0, 0, 0, 0, 1]
            ],
            4 => [
                'input' => [-1, -1, 0, 1, -1, 0, 0, 0, 1],
                'output' => [1, 1, 1, 1, 1, 0, 0, 0, 1]
            ],
            6 => [
                'input' => [-1, -1, 1, 1, -1, -1, 0, 0, 1],
                'output' => [1, 1, 1, 1, 1, 1, 0, 1, 1]
            ],
            8 => [
                'input' => [-1, -1, 1, 1, -1, -1, -1, 1, 1],
                'output' => [1, 1, 1, 1, 1, 1, 1, 1, 1]
            ],
        ];
        $training = $trainer->createTraining($match, Trainer::MATCH_TIE);

        $this->assertEquals($expectedTraining, $training);
    }

    public function testRegisterMove()
    {
        $trainer = new Trainer();

        $trainer->recordMove(0, 0, Trainer::HUMAN);
        $trainer->recordMove(2, 2, Trainer::AI);
        $trainer->recordMove(0, 1, Trainer::HUMAN);
        $trainer->recordMove(1, 0, Trainer::AI);
        $trainer->recordMove(0, 2, Trainer::HUMAN);

        $expectedTraining = [
            0 => [
                'input' => [-1, 0, 0, 0, 0, 0, 0, 0, 0],
                'output' => [1, 0, 0, 0, 0, 0, 0, 0, -1]
            ],
            2 => [
                'input' => [-1, -1, 0, 0, 0, 0, 0, 0, 1],
                'output' => [1, 1, 0, -1, 0, 0, 0, 0, -1]
            ],
            4 => [
                'input' => [-1, -1, -1, 1, 0, 0, 0, 0, 1],
                'output' => [1, 1, 1, -1, 0, 0, 0, 0, -1]
            ],
        ];

        $training = $trainer->createTrainingFromRecord(Trainer::MATCH_HUMAN_WON);

        $this->assertEquals($expectedTraining, $training);
    }

    protected function indexToXY($index)
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
}
