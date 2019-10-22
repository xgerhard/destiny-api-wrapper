<?php

namespace Destiny;

use Destiny\Player;
use Destiny\User;
use Destiny\Api\Client as ApiClient;
use Destiny\Exceptions\PlayerNotFoundException;
use Destiny\Exceptions\UserNotFoundException;
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
            if($iMembershipType)
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

    /**
     * Search Destiny user by UniqueName
     *
     * @param string $strUser
     *
     * @return object Destiny\User
     */
    public function searchUser($strUser, $bLinkedPlayers = false)
    {
        $aUsers = $this->api->searchUser($strUser);
        if(!empty($aUsers))
        {
            foreach($aUsers as $oUser)
            {
                if(strtolower($strUser) == strtolower($oUser->uniqueName))
                    return new User($oUser, $this->api);
            }
        }
        throw new UserNotFoundException($strUser);
    }

    /**
     * Search Destiny player by displayName or UniqueName
     *
     * @param string $strUser
     *
     * @return object Destiny\User
     */
    public function searchPlayerUser($strUser, $iMembershipType = null)
    {
        try
        {
            return $this->searchPlayer($strUser, $iMembershipType);
        }
        catch(PlayerNotFoundException $e)
        {
            try
            {
                return $this->searchUser($strUser, true)->profiles->getCurrent();
            }
            catch(UserNotFoundException $e)
            {
                throw new PlayerNotFoundException($strUser);
            }
        }
    }
}