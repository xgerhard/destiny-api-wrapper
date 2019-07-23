<?php

namespace Destiny\Collections;

use Destiny\Player;
use Destiny\Api\Client as ApiClient;

class CharacterCollection
{
    private $fetched = false;
    private $components = [200];

    public function __construct(Player $oPlayer, ApiClient $oDestinyApi)
    {
        $this->api = $oDestinyApi;
        $this->player = $oPlayer;
        $this->characters = [];
    }

    public function getCharacters()
    {
        $this->fetch();

        return $this->characters;
    }

    public function current()
    {
        $this->fetch();

        $oLastPlayerCharacter = false;
        foreach($this->characters as $oCharacter)
        {
            if(!$oLastPlayerCharacter || strtotime($oLastPlayerCharacter->dateLastPlayed) < strtotime($oCharacter->dateLastPlayed))
                $oLastPlayerCharacter = $oCharacter;
        }
        return $oLastPlayerCharacter;
    }

    public function fetch($aComponents = [])
    {
        if($this->fetched)
            return;

        $oCharacters = $this->api->getProfile($this->player->membershipType, $this->player->membershipId, (empty($aComponents) ? $this->components : $aComponents));
        if(isset($oCharacters->characters->data) && !empty($oCharacters->characters->data))
        {
            foreach($oCharacters->characters->data as $iMembershipId => $oCharacter)
            {
                $this->characters[$iMembershipId] = $oCharacter;
            }
            return $this->getCharacters();
        }

        if(empty($this->characters))
            throw new NoCharactersFoundException();
    }
}

?>