<?php
/**
 * AbstractPlayer.php
 *
 * Ariel Ferrandini <arielferrandini@gmail.com>
 * 03/10/14
 */ 

namespace PHPGames\Game\Player;


use PHPGames\Exception\InvalidPlayerNameException;

abstract class AbstractPlayer implements Player
{
    /**
     * @var string
     */
    protected $name;
    protected $token;

    /**
     * @param $name
     * @throws InvalidPlayerNameException
     */
    public function __construct($name, $token)
    {
        if (empty($name)) {
            throw new InvalidPlayerNameException($name);
        }

        $this->name = $name;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getName ()
    {
        return $this->name;
    }
}
