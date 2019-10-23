<?php

namespace Destiny;

use Destiny\Collections\InventoryCollection;
use Destiny\Collections\ActivityCollection;
use Destiny\Collections\ProgressionCollection;

class Character
{
    public $inventory;
    public $activities;
    public $progressions;

    public function __construct($oCharacter, $aInventory = null, $oActivities = null, $oProgressions = null)
    {
        foreach($oCharacter as $key => $value)
        {
            $this->{$key} = $value;
        }

        $this->inventory = new InventoryCollection($aInventory);
        $this->activities = new ActivityCollection($oActivities);
        $this->progressions = new ProgressionCollection($oProgressions);
    }
}
