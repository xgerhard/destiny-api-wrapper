<?php

namespace Destiny;

class Character
{
    public function __construct($oCharacter)
    {
        foreach($oCharacter as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
}
