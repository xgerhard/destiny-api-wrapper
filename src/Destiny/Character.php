<?php

namespace Destiny;

class Character extends Model
{
    public function __construct($aProperties)
    {
        parent::__construct($aProperties);
    }

    public function getHistoricalStats()
    {
        return isset($this->properties['historicalStats']) ? new StatCollection($this->properties['historicalStats']) : null;
    }
}
