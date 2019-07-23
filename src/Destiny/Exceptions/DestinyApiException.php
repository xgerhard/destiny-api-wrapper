<?php

namespace Destiny\Exceptions;

class DestinyApiException extends \Exception
{
    public function __construct($strMessage)
    {
        parent::__construct(sprintf('Destiny API error: %s', $strMessage));
    }
}