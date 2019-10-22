<?php

namespace Destiny\Exceptions;

class UserNotFoundException extends \Exception
{
    public function __construct($strUser)
    {
        parent::__construct(sprintf('User not found: %s', $strUser));
    }
}