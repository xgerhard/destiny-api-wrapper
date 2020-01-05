<?php

namespace Destiny\Exceptions;

class InvalidArgumentException extends \Exception
{
    public function __construct($strMessage)
    {
        parent::__construct(sprintf('Invalid argument error: %s', $strMessage));
    }
}