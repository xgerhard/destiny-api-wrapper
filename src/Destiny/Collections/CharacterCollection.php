<?php

namespace Destiny\Collections;

use Destiny\Character;
use Destiny\Collection;

class CharacterCollection extends Collection
{
    private $characters = [];

    public function __construct($oCharacters)
    {
        $aCharacters = [];
        if($oCharacters && isset($oCharacters->data))
        {
            foreach($oCharacters->data as $iCharacterId => $oCharacter)
            {
                $aCharacters[$iCharacterId] = new Character((array) $oCharacter);
            }
        }
        $this->characters = $aCharacters;
    }

    /**
     * Get all characters
     *
     * @return array [Destiny\Character]
     */
    public function getAll()
    {
        return $this->characters;
    }

    /**
     * Get current / last played characters
     *
     * @return object Destiny\Character
     */
    public function getCurrent()
    {
        $oLastPlayerCharacter = false;
        foreach($this->characters as $oCharacter)
        {
            if(!$oLastPlayerCharacter || strtotime($oLastPlayerCharacter->dateLastPlayed) < strtotime($oCharacter->dateLastPlayed))
                $oLastPlayerCharacter = $oCharacter;
        }
        return $oLastPlayerCharacter;
    }
}

?>