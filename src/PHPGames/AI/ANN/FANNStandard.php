<?php

namespace PHPGames\AI\ANN;

class FANNStandard implements FANN
{
    protected $ann;

    private $file;

    public function __construct($inputNeurons, $outputNeurons, $hiddenLayers, $hiddenNeurons, $file = '')
    {
        $this->file = $file;
        if (!empty($this->file) && is_readable($this->file)) {
            $this->ann = fann_create_from_file($this->file);
        }

        if (!$this->ann) {

            /**
             * layers
             * layer 1 (input layer)
             * layer 2 (hidden layer 1)
             * .
             * .
             * layer 5 (output layer)
             */
            $layers = $this->createLayersArray($inputNeurons, $outputNeurons, $hiddenLayers, $hiddenNeurons);

            $this->ann = fann_create_standard_array(count($layers), $layers);
        }

        fann_set_activation_function_hidden($this->ann, FANN_SIGMOID_SYMMETRIC);
        fann_set_activation_function_output($this->ann, FANN_SIGMOID);
        fann_set_training_algorithm($this->ann, FANN_TRAIN_BATCH);
    }

    private function createLayersArray($inputLayer, $outputLayer, $hiddenLayers, $hiddenNeurons)
    {
        $layers = [$inputLayer];
        for ($i = 0; $i < $hiddenLayers; $i++) {
            $layers[] = $hiddenNeurons;
        }

        $layers[] = $outputLayer;

        return $layers;
    }

    public function run($input)
    {
        return fann_run($this->ann, $input);
    }

    public function trainOnFile($filename, $maxEpochs, $epochsBetweenReports, $desiredError)
    {
        $this->updateTrainingsCount();
        return fann_train_on_file($this->ann, $filename, $maxEpochs, $epochsBetweenReports, $desiredError);
    }

    private function updateTrainingsCount()
    {
        $trainingsFile = '/tmp/trainings';

        $trainings = 0;
        if (file_exists($trainingsFile) && is_readable($trainingsFile)) {
            $trainings = (int)file_get_contents('/tmp/trainings');
        }

        $trainings++;
        file_put_contents($trainingsFile, $trainings);
    }

    public function save()
    {
        fann_save($this->ann, $this->file);
    }

    public function test(array $input, $desiredOutput)
    {
        return fann_test($this->ann, $input, $desiredOutput);
    }
}
