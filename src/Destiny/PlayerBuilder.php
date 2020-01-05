<?php

namespace Destiny;

use Destiny\Player;
use Destiny\Exceptions\InvalidArgumentException;
use Destiny\Definitions\MembershipTypeDefinition;
use Destiny\Definitions\ComponentTypeDefinition;

class PlayerBuilder
{
    private $client;
    private $displayName;
    private $membershipType;
    private $membershipId;
    private $components = [];

    public function withClient(DestinyClient $client)
    {
        $this->client = $client;

        return $this;
    }

    public function withDisplayName($strDisplayName)
    {
        $this->displayName = $strDisplayName;

        return $this;
    }

    public function withMembershipType($iMembershipType)
    {
        $this->membershipType = $iMembershipType;

        return $this;
    }

    public function withMembershipId($strMembershipId)
    {
        $this->membershipId = $strMembershipId;

        return $this;
    }

    public function withComponents($component)
    {
        if(is_array($component))
            $this->components = array_merge($this->components, $component);
        else
            $this->components[] = $component;

        return $this;
    }

    public function build()
    {
        $this->validate();

        $oProfile = $this->client->api->getProfile(
            $this->getMembershipType(),
            $this->getMembershipId(),
            $this->getComponents()
        );

        $oPlayer = new Player;
        return $oPlayer->fromBuilder($this, (array) $oProfile);
    }

    public function validate()
    {
        if($this->membershipType === null)
            throw new InvalidArgumentException('A membershipType must be set');
        else
        {
            $oMembershipTypeDefinition = new MembershipTypeDefinition;
            if(!in_array($this->membershipType, array_values($oMembershipTypeDefinition->getOptions())))
                throw new InvalidArgumentException('Invalid membershipType provided');
        }

        if($this->membershipId === null)
            throw new InvalidArgumentException('A membershipId must be set');

        if(!empty($this->components))
        {
            $oComponentTypeDefintion = new ComponentTypeDefinition;
            $aComponents = $oComponentTypeDefintion->getOptions();

            foreach($this->components as $i => $component)
            {
                if(!in_array($component, array_values($aComponents)))
                {
                    if(isset($aComponents[strtoupper($component)]))
                        $this->components[$i] = $aComponents[strtoupper($component)];
                    else
                        throw new InvalidArgumentException('Invalid componentType provided');
                }
            }
            $this->components = array_unique($this->components);
        }
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function getMembershipType()
    {
        return $this->membershipType;
    }

    public function getMembershipId()
    {
        return $this->membershipId;
    }

    public function getComponents()
    {
        return $this->components;
    }
}
?>