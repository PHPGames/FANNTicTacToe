<?php

namespace PHPGames\AI;

use PHPGames\AI\ANN\FANNStandard;

class TicTacToeAI extends FANNStandard implements AI
{
    const ANN_TYPE_STANDARD = 'standard';
    private $fann;

    public function __construct($type = self::ANN_TYPE_STANDARD, $file = '')
    {
        $this->fann = parent::__construct(9, 9, 3, 27, $file);
    }
}
