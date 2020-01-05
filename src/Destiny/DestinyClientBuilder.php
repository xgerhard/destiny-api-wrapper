<?php

namespace Destiny;

use Destiny\DestinyClient;
use Destiny\Exceptions\InvalidArgumentException;

class DestinyClientBuilder
{
    private $apiKey;

    public function withApiKey($strApiKey)
    {
        $this->apiKey = $strApiKey;

        return $this;
    }

    public function build()
    {
        $this->validate();

        return new DestinyClient($this);
    }

    public function validate()
    {
        if($this->apiKey === null)
            throw new InvalidArgumentException('An API key must be set');
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }
}
?>