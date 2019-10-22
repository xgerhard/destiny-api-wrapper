<?php

namespace Destiny\Collections;

use Destiny\Player;
use Destiny\User;
use Destiny\Api\Client as ApiClient;

class ProfileCollection
{
    private $fetched = false;

    public function __construct(User $oUser, ApiClient $oDestinyApi)
    {
        $this->api = $oDestinyApi;
        $this->user = $oUser;
        $this->profiles = [];
    }

    /**
     * Get all profiles
     *
     * @return array [Destiny\Player]
     */
    public function getAll()
    {
        $this->fetch();

        return $this->profiles;
    }

    /**
     * Get current / last played profile
     *
     * @return object Destiny\Player
     */
    public function getCurrent()
    {
        $this->fetch();

        $oLastPlayerProfile = false;
        foreach($this->profiles as $oProfile)
        {
            if(!$oLastPlayerProfile || strtotime($oLastPlayerProfile->dateLastPlayed) < strtotime($oProfile->dateLastPlayed))
                $oLastPlayerProfile = $oProfile;
        }
        return $oLastPlayerProfile;
    }

    public function fetch($aComponents = [])
    {
        // Only need to fetch once
        if($this->fetched)
            return;

        $oProfiles = $this->api->getLinkedProfiles(254, $this->user->membershipId);
        if(isset($oProfiles->profiles) && !empty($oProfiles->profiles))
        {
            foreach($oProfiles->profiles as $i => $oProfile)
            {
                $this->profiles[$oProfile->membershipId] = new Player($oProfile, $this->api);
            }
            $this->fetched = true;
            return $this->getAll();
        }

        if(empty($this->profiles))
            throw new NoProfilesFoundException($this->user->displayName);
    }
}

?>