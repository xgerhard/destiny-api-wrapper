<?php

namespace Destiny;

use Destiny\Model;
use Destiny\Collections\CharacterCollection;

class Player extends Model
{
    public function fromBuilder(PlayerBuilder $builder, $aProperties = [])
    {
        $aProperties['account'] = [
            'displayName' => $builder->getDisplayName(),
            'membershipType' => $builder->getMembershipType(),
            'membershipId' => $builder->getMembershipId()
        ];

        parent::__construct($aProperties);
        return $this;
    }

    public function getCharacters()
    {
        return new CharacterCollection($this->properties['characters']);
    }

    public function fromApi($oPlayer)
    {
        foreach($oPlayer as $key => $value)
        {
            $this->{$key} = $value;
        }
        $this->characters = new CharacterCollection($this, $oDestinyApi);
        return $this;
    }
}