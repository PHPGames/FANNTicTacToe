<?php

namespace PHPGames\Game;

use PHPGames\Exception\InvalidMoveException;
use PHPGames\Game\Player\Player;

class TicTacToeGame implements Game
{
    const RESULT_PLAYER_1_WON = 1;
    const RESULT_PLAYER_2_WON = 2;
    const RESULT_TIE = 3;

    const RESULT_IN_PROGRESS = 0;

    private $result = self::RESULT_IN_PROGRESS;
    private $player1;
    private $player2;
    private $board = [
            [0, 0, 0],
            [0, 0, 0],
            [0, 0, 0],
        ];
    private $movesLeft = 9;

    public function __construct(Player $player1, Player $player2)
    {
        $this->player1 = $player1;
        $this->player2 = $player2;
    }

    public function players()
    {
        return [$this->player1, $this->player2];
    }

    public function getBoard()
    {
        return $this->board;
    }

    public function player1Move()
    {
        $this->validateMove($this->player1->getNextMove($this->board));
        $this->movesLeft--;
        $this->board = $this->player1->move($this->board);
        $this->updateResult($this->player1);
    }

    public function player2Move()
    {
        $this->validateMove($this->player2->getNextMove($this->board));
        $this->movesLeft--;
        $this->board = $this->player2->move($this->board);
        $this->updateResult($this->player2);
    }

    private function validateMove(Move $move)
    {
        if ($this->board[$move->getX()][$move->getY()] !== 0) {
            throw new InvalidMoveException($move->getX(), $move->getY());
        }

        if (!isset($this->board[$move->getX()][$move->getY()])) {
            throw new InvalidMoveException($move->getX(), $move->getY());
        }
    }

    private function updateResult(Player $player)
    {
        if ($this->isBoardWithWinnerCondition()) {
            if ($player === $this->player1) {
                $this->result = self::RESULT_PLAYER_1_WON;
            } else {
                $this->result = self::RESULT_PLAYER_2_WON;
            }
        } elseif (0 === $this->movesLeft) {
            $this->result = self::RESULT_TIE;
        }
    }

    public function getResult()
    {
        return $this->result;
    }

    private function isBoardWithWinnerCondition()
    {
        if ($this->board[0][0] === $this->board[0][1] && $this->board[0][1] === $this->board[0][2] && $this->board[0][0] !== 0) {
            return true;
        }

        if ($this->board[1][0] === $this->board[1][1] && $this->board[1][1] === $this->board[1][2] && $this->board[1][0] !== 0) {
            return true;
        }

        if ($this->board[2][0] === $this->board[2][1] && $this->board[2][1] === $this->board[2][2] && $this->board[2][0] !== 0) {
            return true;
        }

        if ($this->board[2][0] === $this->board[1][0] && $this->board[1][0] === $this->board[0][0] && $this->board[2][0] !== 0) {
            return true;
        }

        if ($this->board[2][1] === $this->board[1][1] && $this->board[1][1] === $this->board[0][1] && $this->board[2][1] !== 0) {
            return true;
        }

        if ($this->board[2][2] === $this->board[1][2] && $this->board[1][2] === $this->board[0][2] && $this->board[2][2] !== 0) {
            return true;
        }

        if ($this->board[0][0] === $this->board[1][1] && $this->board[1][1] === $this->board[2][2] && $this->board[0][0] !== 0) {
            return true;
        }

        if ($this->board[0][2] === $this->board[1][1] && $this->board[1][1] === $this->board[2][0] && $this->board[0][2] !== 0) {
            return true;
        }

        return false;
    }

    public function isFinished()
    {
        switch ($this->getResult()) {
            case self::RESULT_PLAYER_1_WON:
            case self::RESULT_PLAYER_2_WON:
            case self::RESULT_TIE:
                return true;

            default:
                return false;
        }
    }
}
