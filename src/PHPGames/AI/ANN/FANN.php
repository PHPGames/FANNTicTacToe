<?php

namespace PHPGames\AI\ANN;

interface FANN
{
    public function run($input);
    public function trainOnFile($filename, $maxEpochs, $epochsBetweenReports, $desiredError);
}
