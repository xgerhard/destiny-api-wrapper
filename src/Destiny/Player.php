<?php

namespace Destiny;

use Destiny\Api\Client as ApiClient;
use Destiny\Collections\CharacterCollection;

class Player
{
    public $characters;

    public function __construct($oPlayer, ApiClient $oDestinyApi)
    {
        foreach($oPlayer as $key => $value)
        {
            $this->{$key} = $value;
        }

        $this->characters = new CharacterCollection($this, $oDestinyApi);
    }
}