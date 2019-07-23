<?php

namespace Destiny\Exceptions;

class PlayerNotFoundException extends \Exception
{
    public function __construct($strDisplayName)
    {
        parent::__construct(sprintf('Player not found: %s', $strDisplayName));
    }
}