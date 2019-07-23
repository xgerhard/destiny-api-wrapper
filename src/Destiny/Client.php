<?php

namespace Destiny;

use Destiny\Api\Client as ApiClient;
use Destiny\Exceptions\PlayerNotFoundException;
use Destiny\Player;

class Client
{
    private $api; // Destiny\Api\Client

    public function __construct($strApiKey)
    {
        $this->api = new ApiClient($strApiKey);
    }

    public function searchPlayer($strDisplayName, $iMembershipType = null)
    {
        $aPlayers = $this->api->searchDestinyPlayer($strDisplayName, $iMembershipType);
        if(!empty($aPlayers))
        {
            if(count($aPlayers) > 1 && $iMembershipType)
            {
                $aTempPlayers = [];
                foreach($aPlayers as $oPlayer)
                {
                    if(strtolower($strDisplayName) == strtolower($oPlayer->displayName) && $iMembershipType == $oPlayer->membershipType)
                        $aTempPlayers[] = $oPlayer;
                }

                if(!empty($aTempPlayers))
                    return new Player(end($aTempPlayers), $this->api);
            }
            return new Player($aPlayers[0], $this->api);
        }
        throw new PlayerNotFoundException($strDisplayName);
    }
}