<?php

namespace Destiny;

use Destiny\Player;
use Destiny\Api\Client as ApiClient;
use Destiny\Exceptions\PlayerNotFoundException;
use Destiny\Exceptions\InvalidPlayerParametersException;

class Client
{
    public $api; // Destiny\Api\Client

    public function __construct($strApiKey)
    {
        $this->api = new ApiClient($strApiKey);
    }

    /**
     * Loads player based on membershipId & membershipType
     *
     * @param array $aPlayer [membershipId, membershipType]
     *
     * @return object Destiny\Player
     */
    public function loadPlayer($aPlayer)
    {
        if(!isset($aPlayer['membershipId']) || !isset($aPlayer['membershipType']))
            throw new InvalidPlayerParametersException();

        return new Player($aPlayer, $this->api);
    }

    /**
     * Search Destiny player by DisplayName
     *
     * @param string $strDisplayName
     * @param int $iMembershipType
     *
     * @return object Destiny\Player
     */
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