<?php

namespace Destiny\Exceptions;

class InvalidPlayerParametersException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Invalid player parameters provided, membershipId & membershipType are required');
    }
}