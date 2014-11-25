<?php

namespace Tests\PHPGames\AI\ANN;

use PHPGames\AI\ANN\Trainer;

class TrainerTrainANNTest extends \PHPUnit_Framework_TestCase
{
    public $trainerFileName = '/tmp/';

    public function tearDown()
    {
        $this->trainerFileName = '/tmp/';
    }

    public function testTrainerTrainsANN()
    {
        $trainer = new Trainer();

        $match = [
            [-1, 0, 0, 0, 0, 0, 0, 0, 0],
            [-1, 0, 0, 0, 0, 0, 0, 0, 1],
            [-1, -1, 0, 0, 0, 0, 0, 0, 1],
            [-1, -1, 0, 1, 0, 0, 0, 0, 1],
            [-1, -1, -1, 1, 0, 0, 0, 0, 1],
        ];

        $training = $trainer->createTraining($match, true);
        $epochs = 50;

        $expectedFileExtension = 'dat';
        $expectedLines = count($training) * 2 + 1;
        $expectedFirstLine = sprintf('%d %d %d' . PHP_EOL, $expectedLines / 2, 9, 9);
        $expectedElementsPerLine = 9;
        $ann = $this->getMockForAbstractClass('PHPGames\AI\ANN\FANN');
        $ann
            ->expects($this->once())
            ->method('trainOnFile')
            ->with($this->callback($this->createTrainOnFileParamsCallback($training)));

        $trainer->train($ann, $training, $epochs);

        $this->assertFileExists($this->trainerFileName);
        $lines = [];
        $fh = fopen($this->trainerFileName, 'r');
        while ($line = fgets($fh)) {
            $lines[] = $line;
            if (count($lines) > 1) {
                $elements = explode(' ', $line);
                $this->assertCount($expectedElementsPerLine, $elements);
            }
        }

        fclose($fh);
        $this->assertEquals($expectedFileExtension, pathinfo($this->trainerFileName, PATHINFO_EXTENSION));
        $this->assertEquals($expectedLines, count($lines));
        $this->assertEquals($expectedFirstLine, $lines[0]);
        unlink($this->trainerFileName);
    }

    private function createTrainOnFileParamsCallback($training)
    {
        return function ($subject) use ($training) {
            $this->trainerFileName = $subject;

            return true;
        };
    }
}
