<?php

namespace Destiny\Collections;

use Destiny\Player;
use Destiny\Character;
use Destiny\Api\Client as ApiClient;

class CharacterCollection
{
    private $fetched = false;
    private $components = [200, 201, 202, 204, 205];

    public function __construct(Player $oPlayer, ApiClient $oDestinyApi)
    {
        $this->api = $oDestinyApi;
        $this->player = $oPlayer;
        $this->characters = [];
    }

    /**
     * Get all characters
     *
     * @return array [Destiny\Character]
     */
    public function getAll($aComponents = [])
    {
        $this->fetch($aComponents);

        return $this->characters;
    }

    /**
     * Get current / last played characters
     *
     * @return object Destiny\Character
     */
    public function getCurrent($aComponents = [])
    {
        $this->fetch($aComponents);

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
        // Only need to fetch the getProfile request once
        if($this->fetched)
            return;

        $oCharacters = $this->api->getProfile($this->player->membershipType, $this->player->membershipId, (empty($aComponents) ? $this->components : $aComponents));
        if(isset($oCharacters->characters->data) && !empty($oCharacters->characters->data))
        {
            foreach($oCharacters->characters->data as $iMembershipId => $oCharacter)
            {
                $this->characters[$iMembershipId] = new Character(
                    $oCharacter,
                    [
                        'equipment' => (isset($oCharacters->characterEquipment->data->{$iMembershipId}->items) ? $oCharacters->characterEquipment->data->{$iMembershipId}->items : null),
                        'instances' => (isset($oCharacters->itemComponents->instances->data) ? $oCharacters->itemComponents->instances->data : null),
                        'sockets' => (isset($oCharacters->itemComponents->sockets->data) ? $oCharacters->itemComponents->sockets->data : null)
                    ],
                    (isset($oCharacters->characterProgressions->data->{$iMembershipId}) ? $oCharacters->characterProgressions->data->{$iMembershipId} : null),
                    (isset($oCharacters->characterActivities->data->{$iMembershipId}) ? $oCharacters->characterActivities->data->{$iMembershipId} : null)
                );
            }
            $this->fetched = true;
            return $this->getAll();
        }

        if(empty($this->characters))
            throw new NoCharactersFoundException();
    }
}

?>