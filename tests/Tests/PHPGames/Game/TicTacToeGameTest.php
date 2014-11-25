<?php

namespace Tests\PHPGames\Game;

use PHPGames\Game\Move;
use PHPGames\Game\TicTacToeGame;

class TicTacToeGameTest extends \PHPUnit_Framework_TestCase
{
    public function testGameAdmitsTwoPlayers()
    {
        $player1 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');
        $player2 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');
        $game = new TicTacToeGame($player1, $player2);
        $this->assertCount(2, $game->players());
    }

    public function testGameHasEmptyBoard()
    {
        $expectedBoard = [
            [0, 0, 0],
            [0, 0, 0],
            [0, 0, 0],
        ];

        $player1 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');
        $player2 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');
        $game = new TicTacToeGame($player1, $player2);

        $this->assertEquals($expectedBoard, $game->getBoard());
    }

    public function testGameRegistersPlayerOneMoveInBoard()
    {
        $expectedBoard = [
            [1, 0, 0],
            [0, 0, 0],
            [0, 0, 0],
        ];

        $player1 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');
        $player1->expects($this->once())->method('move')->willReturn($expectedBoard);
        $player1->expects($this->once())->method('getNextMove')->willReturn(new Move(0, 0));
        $player2 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');
        $game = new TicTacToeGame($player1, $player2);
        $game->player1Move();

        $this->assertEquals($expectedBoard, $game->getBoard());
    }

    public function testGameRegistersPlayerTwoMoveInBoard()
    {
        $expectedBoardP1 = [
            [1, 0, 0],
            [0, 0, 0],
            [0, 0, 0],
        ];
        $expectedBoard = [
            [1, 2, 0],
            [0, 0, 0],
            [0, 0, 0],
        ];

        $player1 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');
        $player1->expects($this->once())->method('getNextMove')->willReturn(new Move(0, 0));
        $player1->expects($this->once())->method('move')->willReturn($expectedBoardP1);
        $player2 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');
        $player2->expects($this->once())->method('getNextMove')->willReturn(new Move(0, 1));
        $player2->expects($this->once())->method('move')->willReturn($expectedBoard);
        $game = new TicTacToeGame($player1, $player2);
        $game->player1Move();
        $game->player2Move();

        $this->assertEquals($expectedBoard, $game->getBoard());
    }

    /**
     * @expectedException PHPGames\Exception\InvalidMoveException
     */
    public function testPlayer2CantPlaceInSameCoordsThanPlayer1()
    {
        $expectedBoardP1 = [
            [1, 0, 0],
            [0, 0, 0],
            [0, 0, 0],
        ];

        $player1 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');
        $player1->expects($this->once())->method('getNextMove')->willReturn(new Move(0, 0));
        $player1->expects($this->once())->method('move')->willReturn($expectedBoardP1);

        $player2 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');
        $player2->expects($this->once())->method('getNextMove')->willReturn(new Move(0, 0));
        $player2->expects($this->never())->method('move');

        $game = new TicTacToeGame($player1, $player2);
        $game->player1Move();
        $game->player2Move();
    }

    /**
     * @expectedException PHPGames\Exception\InvalidMoveException
     */
    public function testPlayer1MoveIsOutOfBounds()
    {
        $expectedBoardP1 = [
            [1, 0, 0],
            [0, 0, 0],
            [0, 0, 0],
        ];

        $player1 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');
        $player1->expects($this->never())->method('getNextMove')->willReturn(new Move(3, 3));
        $player1->expects($this->never())->method('move')->willReturn($expectedBoardP1);

        $player2 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');

        $game = new TicTacToeGame($player1, $player2);
        $game->player1Move();
    }
    /**
     * @dataProvider endedGamesProvider
     */
    public function testPlayer1MovesAndGameIsFinished($expectedBoard, $totalMoves)
    {
        $emptyBoard =  [
                    [1, 2, 0],
                    [1, 0, 2],
                    [1, 0, 0],
                ];
        $player1 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');
        $player1->expects($this->any())->method('getNextMove')->willReturn(new Move(2, 2));
        $player1->expects($this->any())->method('move')->willReturn($emptyBoard);
        $player1->expects($this->at($totalMoves))->method('move')->willReturn($expectedBoard);

        $player2 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');

        $game = new TicTacToeGame($player1, $player2);
        for ($x = 1; $x <= $totalMoves; $x++) {
            $game->player1Move();
        }

        $this->assertTrue($game->isFinished());
    }

    public function endedGamesProvider()
    {

        return [
            [
                [
                    [1, 2, 0],
                    [1, 0, 2],
                    [1, 0, 0],
                ],
                5
            ],
            [
                [
                    [2, 1, 2],
                    [0, 1, 2],
                    [0, 1, 0],
                ],
                6
            ],
            [
                [
                    [1, 2, 0],
                    [0, 1, 2],
                    [0, 2, 1],
                ],
                6
            ],
            [
                [
                    [0, 2, 1],
                    [0, 1, 2],
                    [1, 2, 0],
                ],
                6
            ],
            [
                [
                    [1, 1, 2],
                    [2, 1, 1],
                    [1, 2, 2],
                ],
                8
            ],
        ];
    }

    /**
     * @dataProvider wonGamesProvider
     */
    public function testGameIsWon($expectedBoard)
    {
        $player1 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');
        $player1->expects($this->any())->method('getNextMove')->willReturn(new Move(2, 2));
        $player1->expects($this->any())->method('move')->willReturn($expectedBoard);

        $player2 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');

        $game = new TicTacToeGame($player1, $player2);
        $game->player1Move();

        $this->assertEquals(TicTacToeGame::RESULT_PLAYER_1_WON, $game->getResult());
    }

    public function wonGamesProvider()
    {

        return [
            [
                [
                    [1, 2, 0],
                    [1, 0, 2],
                    [1, 0, 0],
                ],
            ],
            [
                [
                    [2, 1, 2],
                    [0, 1, 2],
                    [0, 1, 0],
                ],
            ],
            [
                [
                    [1, 2, 0],
                    [0, 1, 2],
                    [0, 2, 1],
                ],
            ],
            [
                [
                    [0, 2, 1],
                    [0, 1, 2],
                    [1, 2, 0],
                ],
            ],
        ];
    }

    /**
     * @dataProvider tieGamesProvider
     */
    public function testGameIsTie($expectedBoard, $totalMoves)
    {
        $this->markTestSkipped(
            "We should implement functional test completing a game with human players to control the movements"
        );
        $emptyBoard =  [
            [1, 2, 0],
            [1, 0, 2],
            [0, 0, 0],
        ];
        $player1 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');
        $player1->expects($this->any())->method('getNextMove')->willReturn(new Move(2, 2));
        $player1->expects($this->any())->method('move')->willReturn($emptyBoard);

        $player2 = $this->getMockForAbstractClass('PHPGames\Game\Player\Player');

        $game = new TicTacToeGame($player1, $player2);
        for ($x = 1; $x <= $totalMoves - 1; $x++) {
            $game->player1Move();
        }

        $player1->expects($this->at($totalMoves))->method('move')->willReturn($expectedBoard);
        $game->player1Move();

        $this->assertEquals(TicTacToeGame::RESULT_TIE, $game->getResult());
    }

    public function tieGamesProvider()
    {
        return [
            [
                [
                    [1, 1, 2],
                    [2, 1, 1],
                    [1, 2, 2],
                ],
                8
            ]
        ];
    }
}
