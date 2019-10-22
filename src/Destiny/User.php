<?php

namespace Destiny;

use Destiny\Api\Client as ApiClient;
use Destiny\Collections\ProfileCollection;

class User
{
    public $profiles;

    public function __construct($oUser, ApiClient $oDestinyApi)
    {
        $aKeys = ['membershipId', 'uniqueName', 'displayName'];

        foreach($oUser as $key => $value)
        {
            if(in_array($key, $aKeys))
                $this->{$key} = $value;
        }

        $this->profiles = new ProfileCollection($this, $oDestinyApi);
    }
}