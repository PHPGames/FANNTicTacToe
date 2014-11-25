<?php
/**
 * InvalidPlayerNameException.php
 *
 * Ariel Ferrandini <arielferrandini@gmail.com>
 * 03/10/14
 */ 
namespace PHPGames\Exception;


class InvalidPlayerNameException extends \Exception
{
    public function __construct ($name)
    {
        parent::__construct(sprintf('The player name provided "%s" is not valid.', $name));
    }

}
 